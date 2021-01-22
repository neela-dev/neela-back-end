<?php

	require_once('conn/Db.php');
	
	class Model {
		#-.method to create user.
		public function create_user($email,$pass,$name,$type,$is_child) {
	
			$model = new Model(); 
			
			$uuid  = $model->local_generate_uid();
			$email = $model->sanitize($email);
			$pass  = $model->sanitize($pass);
			$name  = $model->sanitize($name);
			$type  = $model->sanitize($type);
			
			
			$duplicate = $model->local_duplicate_email($email);
			
			if($duplicate == 0){
				
				$db = new database();
			
				$hashed_password = password_hash(trim($pass),PASSWORD_DEFAULT);
				
				$sql = "INSERT 
						INTO 
					   `tbl_user` 
					   (`uid`,`email`,`paswd`,`type`,`child_ref_no`,`date_created`)
						VALUES
					   (:s_0,:s_1,:s_2,:s_3,:s_4,NOW())
						ON
						DUPLICATE KEY UPDATE `date_modified` = NOW()";
						
				$db->query($sql);
				$db->bind(':s_0', trim($uuid));
				$db->bind(':s_1', trim($email));
				$db->bind(':s_2', trim($hashed_password));
				$db->bind(':s_3', trim($type));
				$db->bind(':s_4', trim($is_child));
				
				$db->execute();
				
				$id = $db->lastInsertId();
				
				$db->close();
				
				#-.routine call.
				$api_key = $model->local_user_extra($uuid,$name);
				
				$obj = array();

				if($id>0) {
					$obj["RESULT"]  = "Success";
					$obj["DATA"]    =  array("NAME"=>$name,"API_KEY"=>$api_key);
					$obj["MESSAGE"] = "User has been created";
				}else{
					$obj["RESULT"]  = "Fail";
					$obj["DATA"]    =  array("NAME"=>"0","API_KEY"=>"0");
					$obj["MESSAGE"] = "The operation has failed";	
				}
			}else{
					$obj["RESULT"]  = "Fail";
					$obj["DATA"]    =  array("NAME"=>"0","API_KEY"=>"0");
					$obj["MESSAGE"] = strtoupper($email)." is in use, kindly use a different email";				
			}
			
			return $obj;
		}
		
		#-.method to validate user.
		public function user_authentication($email,$paswd){
			
			$db = new database();
			$model = new Model(); 
						
			$email = $model->sanitize($email);
			$paswd = $model->sanitize($paswd);

			#-.routine call.
			$jdata = $model->local_get_user_data($email);
			
			$obj = array();

			if(!empty($jdata)){
				foreach($jdata as $db_info){
					$is_active = 0;
					if(password_verify($paswd,$db_info['paswd'])){
						$is_active = $model->local_is_account_validated($db_info['uid']);
						if($is_active == 1){
							$obj["RESULT"]  = "Success";
							$obj["DATA"]    = array("NAME" => $db_info['entity_name'],"UID" => $db_info['uid']);
							$obj["MESSAGE"] = "Login is successful";
						}else{
							$obj["RESULT"]  = "Fail";
							$obj["DATA"]    = array("NAME" => 0,"UID" => 0);
							$obj["MESSAGE"] = "Please verify your account before attempting to login";
						}
					}else{
						$obj["RESULT"]  = "Fail";
						$obj["DATA"]    = array("NAME" => 0,"UID" => 0);
						$obj["MESSAGE"] = "Login is unsuccessful";
					}				
				}
			}else{
				$obj["RESULT"]  = "Fail";
				$obj["DATA"]    = array("NAME" => 0,"UID" => 0);
				$obj["MESSAGE"] = "User does not exist";			
			}
			
			$db->close();

			return $obj;
		}
		
		#-.method account verification.
		public function account_verfication($email,$apikey){
			
			$db    = new database();
			$model = new Model(); 
			
			$email  = $model->sanitize($email);
			$apikey = $model->sanitize($apikey);
			
			$sql = "UPDATE 
			       `tbl_user` A 
				    JOIN
				   `tbl_user_extra` B
				    ON
				    A.`uid` = B.`user_uid`
                    SET
					A.`is_active` = 1
				    WHERE 
				    A.`email` = :s_1 AND B.`api_key` = :s_2";
					
			$db->query($sql);			  
			$db->bind(':s_1', trim($email));
			$db->bind(':s_2', trim($apikey));
			
			$db->execute();
			
			$db->close();
			
			$obj = array();
			$obj["RESULT"] = "Success";
			$obj["MESSAGE"] = "Account verification was successful";
			
			return $obj;
		}
		
		#-.method.
		public function create_campaign($name,$start_date,$end_date) {
			
			$model = new Model(); 
		
			$uuid  = "cJ".sha1(md5((rand()*5)+1000));
			$name  = $model->sanitize($name);
			$start = $model->sanitize($start_date);
			$end   = $model->sanitize($end_date);
			
			#-.method call.
			$duplicate = $model->local_duplicate_name($name);
			
			if($duplicate == 0){
				
				$db = new database();
							
				$sql = "INSERT 
						INTO 
					   `tbl_campaign`
					   (`uid`,`campaign_name`,`start_time`,`end_time`)
						VALUES
					   (:s_1,:s_2,:s_3,:s_4)
						ON
						DUPLICATE KEY UPDATE `campaign_name` = :s_5";
						
				$db->query($sql);			  
				$db->bind(':s_1', trim($uuid));
				$db->bind(':s_2', trim($name));
				$db->bind(':s_3', trim($start));
				$db->bind(':s_4', trim($end));
				$db->bind(':s_5', trim($name));
				
				$db->execute();
				
				$id = $db->lastInsertId();
				
				$db->close();
				
				$obj = array();
				$obj["RESULT"]  = "Success";
				$obj["DATA"]    = array("UID" => $uuid);
				$obj["MESSAGE"] = "Campaign was created successful";
			}else {
				$obj = array();
				$obj["RESULT"]  = "Fail";
				$obj["DATA"]    = array("UID" => "0");
				$obj["MESSAGE"] = "An active Campaign named ".strtoupper($name)." already exist";				
			}
			
			return $obj;
		}
		
		#-.method to log campaign invitation.
		public function campaign_invitation($uuid,$msisdn,$invitee){
			
			$db    = new database();
			$model = new Model(); 
			
			#-.method call.	
			$client  = $model->_curl_client_post_bonga_api("endpoint","message");
			$uuid    = $model->sanitize($uuid);
			$msisdn  = $model->sanitize($msisdn);
			$invitee = $model->sanitize($invitee);
			
			$sql = "INSERT 
					INTO 
				   `tbl_campaign_invite`
				   (`campaign_uid`,`msisdn`,`invitee`) VALUES (:s_1,:s_2,:s_3)";
					
			$db->query($sql);			  
			$db->bind(':s_1', trim($uuid));
			$db->bind(':s_2', trim($msisdn));
			$db->bind(':s_3', trim($invitee));
			
			$db->execute();
			
			$id = $db->lastInsertId();
			
			$db->close();
			
			if($id > 0) {
				$obj = array();
				$obj["RESULT"]  = "Success";
				$obj["DATA"]    = array("UID" => $uuid);
				$obj["MESSAGE"] = "Invitation was successful";
			}else{
				$obj = array();
				$obj["RESULT"]  = "Fail";
				$obj["DATA"]    = array("UID" => $uuid);
				$obj["MESSAGE"] = "Invitation was unsuccessful";				
			}			
			
			return $obj;			
		}
		
		#-.method to get uid & name.
		function local_get_user_data($email){
			$db = new database();
			$model = new Model();
			
			$email = $model->sanitize($email);
			
			$sql = "SELECT 
					A.`paswd`,B.`entity_name`,A.`uid`
					FROM
					tbl_user A
					INNER JOIN
					tbl_user_extra B
					WHERE
				    A.`email` = :s_1 AND A.`is_suspended` = 0 
					LIMIT 1";
					
			$db->query($sql);
			
			$db->query($sql);			  
			$db->bind(':s_1', trim($email));		
			
			$rows = $db->resultset();
					
			$db->close();

			return $rows;			
		}
		
		#-.method to user is active.
		function local_is_account_validated($uuid){
			
			$db = new database();
			$model = new Model();
			
			$uuid = $model->sanitize($uuid);
			
			$sql = "SELECT 
				   `is_active` AS is_validated
					FROM
					tbl_user
					WHERE
				   `uid` = :s_1 AND `is_suspended` = 0";
					
			$db->query($sql);
			
			$db->query($sql);			  
			$db->bind(':s_1', trim($uuid));		
			
			$rows = $db->resultset();
			
			$validated = '0';
			foreach($rows as $row){
				$validated = $row['is_validated'];
			}
					
			$db->close();

			return $validated;			
		}
		
		#-.method to save user info.
		function local_user_extra($uuid,$name) {
			
			$db    = new database();
			$model = new Model(); 
			
			$uuid = $model->sanitize($uuid);
			$name = $model->sanitize($name);
			$apikey = sha1(md5((rand()*2.5)+1));
			
			$sql = "INSERT 
					INTO 
				   `tbl_user_extra`
				   (`user_uid`,`entity_name`,`api_key`)
					VALUES
				   (:s_1,:s_2,:s_3)
					ON
					DUPLICATE KEY UPDATE `entity_name` = :s_4";
					
			$db->query($sql);			  
			$db->bind(':s_1', trim($uuid));
			$db->bind(':s_2', trim($name));
			$db->bind(':s_3', trim($apikey));
			$db->bind(':s_4', trim($name));
			
			$db->execute();
			
			$id = $db->lastInsertId();
			
			$db->close();
			
			return "aJ".$apikey.":__";
		}
		
		function local_generate_uid(){
			
			$db = new database();
			
			$sql = "SELECT MD5((RAND()*0.005)+1) AS reference FROM DUAL";
			
			$db->query($sql);		
			
			$rows = $db->resultset();

			foreach($rows as $row){
				$custom_uid = $row['reference'];
			}
			
			$db->close();
			
			return $custom_uid;
		}

		function local_duplicate_name($name){
			
			$db = new database();
			
			$sql = "SELECT COUNT(`_id`) AS cnt FROM `tbl_campaign` WHERE `campaign_name` = :s_1 AND `is_deleted` = 0";
			
			$db->query($sql);

			$db->bind(':s_1', trim($name));			
			
			$rows = $db->resultset();

			$cnt = 0;
			foreach($rows as $row){
				$cnt = $row['cnt'];
			}
			
			$db->close();
			
			return $cnt;
		}

		function local_duplicate_email($email){
			
			$db = new database();
			
			$sql = "SELECT COUNT(`_id`) AS cnt FROM `tbl_user` WHERE `email` = :s_1 AND `is_suspended` = 0";
			
			$db->query($sql);

			$db->bind(':s_1', trim($email));			
			
			$rows = $db->resultset();

			$cnt = 0;
			foreach($rows as $row){
				$cnt = $row['cnt'];
			}
			
			$db->close();
			
			return $cnt;
		}		
		
		public function delete_record($id) {}
		
		//--https://app.bongasms.co.ke/api/send-sms-v1
		//--array('serviceID' => '4823','apiClientID' => '274','MSISDN' => '254707132162','txtMessage' => 'Test message','linkID' => '00033110186912628897133007','key' => 'xJrIYubpFsmYXFL','secret' => 'KUQeTX1ASI7v8NBbQHY5OXs1rXUfcA')
		public function _curl_client_post_bonga_api($url,$data){
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $data,
			));

			$response = curl_exec($curl);

			curl_close($curl);
			
			echo($response);			
		}
		
		#-.method php curl.
		public function _curl_client_post($url,$data){
			//-.init curl.
			$ch = curl_init($url);
			
			curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
			curl_setopt($ch,CURLOPT_HEADER,array('Content-Type:application/json'));
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_HEADER,false);
			//-.send request.
			$result = curl_exec($ch);
			curl_close($ch);
			
			return $result;
		}	
		
		#-.do clean up of user input.
		function sanitize($var){
			if(is_array($var)){
				return array_map('sanitize',$var);
			}else{
				if(get_magic_quotes_gpc()){
					$var = stripslashes($var);
				}
				//$var = str_replace("'","\'",$var);
				return $var;
			}
		}
	}
?>