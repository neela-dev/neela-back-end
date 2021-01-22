<?php

require_once('inc/config.inc');

class database{
    private $host   = DB_HOST;
    private $user   = DB_USER;
    private $pass   = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $error;
	private $stmt;

    public function __construct(){
        #-Set DSN.
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        #-Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        #-Create a new PDO instance.
        try{
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        #-Catch errors.
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
    }
	#-Prepare.
	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}
	#-Bind.
	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	#-Execute.
	public function execute(){
		return $this->stmt->execute();
	}
	#-Recordset.
	public function resultset(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	#-RecordsetNUM.
	public function resultsetNum(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_NUM);
	}	
	#-Single record.
	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
    #-Row count.
	public function rowCount(){
		return $this->stmt->rowCount();
    }
	#-Last inserted.
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}
	#-Transaction-begin.
	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}
	#-Transaction-end.
	public function endTransaction(){
		return $this->dbh->commit();
	}
	#-Transaction-rollBack.
	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}
	#-Debug dump.
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
	#-Close.
	public function close(){
		return $this->dbh = null;
	}
}
?>