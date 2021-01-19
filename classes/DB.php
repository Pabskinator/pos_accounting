<?php
class DB {
	private static $_instance = null;
	private $_pdo,
			$_query,
			$_error = false,
			$_results,
			$_counts = 0,
		$_lastinsertedid=0;
		private function __construct(){
				try {
					$this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';charset=utf8;dbname='.Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));
					$this->_pdo->exec('SET time_zone = "+8:00"');
				} catch (PDOException $e){

					die($e->getMessage());
				}
		}
public static function getInstance(){
	if (!isset(self:: $_instance)){
		self::$_instance = new DB();

	}
	return self::$_instance;

}
public  function query($sql='',$params = array(),$lastInsert= false){

	$this->_error = false;
	//$this->_pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
	//echo $sql;
	//print_r($params);
	if ($this->_query=$this->_pdo->prepare($sql)){
		$x =1 ;

		if (count($params)){
			foreach($params as $param){
					$this->_query ->bindValue($x,$param);
					$x++;
			}

		}

		if ($this->_query->execute()){
			$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
			$this->_counts = $this->_query->rowCount();
			if($lastInsert == true){
				$this->_lastinsertedid = $this->_pdo->lastInsertId();
			}
		} else {

			$this->_error = true;
			//print_r($this->_pdo->errorInfo());
		}

	}
	return $this;

}

private function action($action,$table,$where = array(),$compid=0){
	$withcompanyid = array('users','branches','categories','characteristics','items','sales','units','queus');
	if($compid && in_array($table,$withcompanyid)){
		$wherecompany = " and company_id = " . $compid;
	}else {
		$wherecompany ='';
	}

	if (count($where) === 3){
		$operators = array ('=','>','<','>=','<=');
		$field = $where[0];
		$operator = $where[1];
		$value = $where[2];
		if (in_array($operator, $operators)){
			$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ? $wherecompany";
			if (!$this->query($sql,array($value))->error()){
				return $this;

			}
		}
	}
return false;
}
public function get($table, $where,$c){
	return $this->action('Select *', $table, $where,$c);
}

public function delete($table,$where,$c=0){
return $this->action('Delete', $table, $where,$c);

}
public function insert($table, $fields = array()){

		$keys = array_keys($fields);
		$values = null;
		$x = 1;
		foreach ($fields as $field){
			$values .= "?";
			if ($x < count($fields)){
				$values .= ', ';

			}
			$x++;
		}

		 $sql = "insert into {$table} (`".implode('`,`',$keys)."`) values ($values)";
		if(!$this->query($sql, $fields)->error()){
			$this->_lastinsertedid = $this->_pdo->lastInsertId();
			return true;
		} else {
			return false;

		}

}
public function update($table, $id,$fields){
$set = '';
$x =1;
foreach($fields as $name=> $value){
	$set .= "{$name} = ?";
	if ($x < count($fields)){
		$set .= ',';
	}
$x ++;
}
$sql = "update {$table} set  {$set} where id = {$id}";
if (!$this->query($sql,$fields)->error()){
return true;
} else {
	return false;
}
}

public function results(){
	return $this->_results;

}
public function lastInsertedId(){
		return $this->_lastinsertedid;
}
public function first(){
	return $this->_results[0];
}
public function error(){
	return  $this->_error;

}
public function count(){
	return $this->_counts;

}

}


?>