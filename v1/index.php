<?php

require_once('../include/Function.php');

require (__DIR__.'/../libs/autoload.php');

date_default_timezone_set('Africa/Nairobi');

#-.=================================
#-.slim settings.
#-.=================================
$app = new Slim\App([
    'settings'=>[
        'displayErrorDetails'=>true
    ]
]);

#-.=================================
#-.default test page.
#-.=================================
$app->get("/",function(){
	print("<kbd>Neela APIs.</kbd>");
});

#-.=================================
#-.route to create an account.
#-.=================================
$app->post('/createuser', function ($request, $response, $args) {
		
	$request_data = $request->getParsedBody();
	
	if(!haveEmptyParameters(array("email","password","name","usertype"),$request, $response)){
	
		$model = new Model();
		
		$email = $request_data['email'];
		$paswd = $request_data['password'];
		$name  = $request_data['name'];
		$utype = $request_data['usertype'];
		
		#-.routine call.
		$feedback = $model->create_user($email,$paswd,$name,$utype,'0');
		
		$response->write(json_encode($feedback));
		
		return $response->withHeader('Content-type','application/json')->withStatus(201);
	}
});

#-.=================================
#-.route to login.
#-.=================================
$app ->post("/userlogin",function($request, $response, $args){
	
	$request_data = $request->getParsedBody();

	if(!haveEmptyParameters(array("email","password"),$request, $response)){
		
		$model = new Model();
		
		$email = $request_data['email'];
		$paswd = $request_data['password'];
		
		#-.routine call.
		$feedback = $model->user_authentication($email,$paswd);
		
		$response->write(json_encode($feedback));
		
		return $response->withHeader('Content-type','application/json')->withStatus(201);
	}
});

#-.=================================
#-.route to verify account.
#-.=================================
$app ->post("/verifyaccount",function($request, $response, $args){
	
	$request_data = $request->getParsedBody();

	if(!haveEmptyParameters(array("email","apikey"),$request, $response)){
		
		$model = new Model();
		
		$email  = $request_data['email'];
		$apikey = $request_data['apikey'];
		
		#-.routine call.
		$feedback = $model->account_verfication($email,$apikey);
		
		$response->write(json_encode($feedback));
		
		return $response->withHeader('Content-type','application/json')->withStatus(201);
	}
});

#-.=================================
#-.route to register campaign.
#-.=================================
$app ->post("/createcampaign",function($request, $response, $args){
	
	$request_data = $request->getParsedBody();

	if(!haveEmptyParameters(array("campaignname","startdate","enddate"),$request, $response)){
		
		$model = new Model();
		
		$name   = $request_data['campaignname'];
		$s_date = $request_data['startdate'];
		$e_date = $request_data['enddate'];
		
		#-.routine call.
		$feedback = $model->create_campaign($name,$s_date,$e_date);
		
		$response->write(json_encode($feedback));
		
		return $response->withHeader('Content-type','application/json')->withStatus(201);
	}
});

#-.=================================
#-.route to campaign invitation.
#-.=================================
$app ->post("/campaigninvite",function($request, $response, $args){
	
	$request_data = $request->getParsedBody();

	if(!haveEmptyParameters(array("campaignuid","msisdn","invitee"),$request, $response)){
		
		$model = new Model();
		
		$uid     = $request_data['campaignuid'];
		$msisdn  = $request_data['msisdn'];
		$invitee = $request_data['invitee'];
		
		#-.routine call.
		$feedback = $model->campaign_invitation($uid,$msisdn,$invitee);
		
		$response->write(json_encode($feedback));
		
		return $response->withHeader('Content-type','application/json')->withStatus(201);
	}
});

#-.=================================
#-.supporting routine.
#-.=================================
function haveEmptyParameters($required_params,$request,$response){
	$error = false;
	$error_params = "";
	$request_params = $request->getParsedBody();
	
	foreach($required_params as $param){
		if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
			$error = true;
			$error_params .= $param.',';
		}
	}
	print($error_params);
	if($error){
		$error_details = array();
		$error_details["STATUS"] = "Fail";
		$error_details["MESAGE"] = "Required params: ".strtoupper(substr($error_params,0,-1))." are missing or empty";
		
		$response->write(json_encode($error_details));
	}
	return $error;
}

try{
	$app->run();
}catch(Exception $e) {
	$obj = array("STATUS" => "FAIL","MESSAGE" => "Action not allowed");
	die(json_encode($obj));
}

?>