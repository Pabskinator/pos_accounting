<?php
	class Crud{
		protected $_db,
			$_data,
			$_table='',
			$_lastInsertedId=0,
			$_select='',
			$_from='',
			$_join='',
			$_where='',
			$_orderBy='',
			$_groupBy='',
			$_result='',
			$_update='',
			$_update_value='',
			$_limitBy='';
		public function __construct($item=null){
			// get instance of the database
			$this->_db = DB::getInstance();

			// if id is pass
			if($item){
				// find that item base on id
				$this->find($item);
			}
		}


		public function create($fields=null){
			// fields is array of items to insert
			// insert to the table that is set to the child class
			if(!$this->_db->insert($this->_table,$fields)){
				// throw error if not inserted
				throw new Exception("There was a problem in adding " . $this->_table);
			}
			// set the last inserted id
			$this->_lastInsertedId = $this->_db->lastInsertedId();
		}
		// get the last inserted id
		public function getInsertedId(){
			return $this->_lastInsertedId;
		}
		// receive id
		public function find($item=null){
			// if id is set
			if($item){
				// get it on the database
				$data = $this->_db->get($this->_table,array('id' ,'=' , $item),0);
				// if it returns any row
				if($data->count()){
					// get the first row
					$this->_data= $data->first();
					return true;
				}
			}
			return false;
		}
		// get the data of the object
		public function data(){
			return $this->_data;
		}
		// check if item exists
		public function exists(){
			return (!empty($this->_data)) ? true: false;
		}

		public function update($fields=array(),$id=null){
				// update item on database
				if(!$this->_db->update($this->_table,$id,$fields)){
				// throw error if there is a problem
				throw new Exception("There's a problem in updating " . $this->_table);
			}
		}
		// get only active items . is_active = 1
		public function get_active($table='',$where=array(),$start=0,$limit=0){

			$parameters = array();
			// if table is set and where is 3
			if($table && count($where) == 3) {
				// get the value
				$parameters[] = $where[2];

				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				// prepare the query
			  $q= "Select * from `$table` where $where[0] $where[1] ? and is_active=1 $l ";

				//submit the query
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){

					return $data->results();
				}
			}
		}

		public function isExists($table='',$where= array(),$company_id){
			$parameters = array();
			if($where){
				$output= '';
				foreach($where as $k => $v){
					$output .= "`$k`=? and";
					$parameters[] = $v;
				}
				$parameters[] = $company_id;
				$q= "Select * from `$table` where $output is_active=1 and company_id=?";
				//submit the query
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				} else {
					return false;
				}
			}
		}
		public function isExist($table='',$item='',$value='',$company_id=0){

			$parameters = array();
			// if table is set and where is 3

				$parameters[]=$value;
				$parameters[]=$company_id;

			// prepare the query
				$q= "Select * from `$table` where $item = ? and is_active=1 and company_id=?";
				//submit the query
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return true;
				} else {
					return false;
				}
		}
		public function select($v="*"){
			$this->_select = "select " . $v;
			return $this;
		}
		public function getSelect(){
			return $this->_select;
		}
		public function from($v){
			$this->_from = " from " . $v;
			return $this;
		}
		public function getFrom(){
			return $this->_from;
		}
		public function resetJoin(){
			$this->_join = '';
		}

		public function join($v){
			$this->_join .= " ". $v. " ";
			return $this;
		}
		public function getJoin(){
			return $this->_join;
		}
		public function resetWhere(){
			$this->_where = "";
		}

		public function where($v){
			if($this->_where){
				$this->_where .= " ". $v. " ";
			} else {
				$this->_where = ' where ' . $v . " ";
			}
			return $this;
		}


		public function getWhere(){
			return $this->_where ;
		}

		public function limitBy($v){
			$this->_limitBy = " LIMIT " . $v. " ";
			return $this;
		}

		public function getLimitBy(){
			return $this->_limitBy;
		}

		public function orderBy($v){
			$this->_orderBy = " ORDER BY " . $v. " ";
			return $this;
		}
		public function groupBy($v){
			$this->_groupBy = " GROUP BY " . $v. " ";
			return $this;
		}
		public function getOrderBy(){
			return $this->_orderBy;
		}
		public function getGroupBy(){
			return $this->_groupBy;
		}
		public function resetQuery(){

			$this->resetWhere();
			$this->resetJoin();
			$this->resetUpdateValue();
		}

		public function getUpdateQuery(){
			return $this->_update . $this->_update_value . $this->_where;
		}

		public function getQuery(){
			return $this->getSelect() .
				$this->getFrom() .
				$this->getJoin() .
				$this->getWhere() .
				$this->getGroupBy() .
				$this->getOrderBy() .
				$this->getLimitBy();
		}

		public function get($parameters=[]){

			$data = $this->_db->query($this->getQuery(), $parameters);

			$this->_result = $data;

			$this->resetQuery();

			return $this;
		}

		public function all(){
			if($this->_result->count()){
				return $this->_result->results();
			}
			return false;
		}

		public function first(){
			if($this->_result->count()){
				return $this->_result->first();
			}
			return false;
		}

		public function updateTable($v){
			$this->_update = "update " .$v. " ";
			return $this;
		}
		public function resetUpdateValue(){
			$this->_update_value  = "";
			return $this;
		}

		public function setValue($v){
			if($this->_update_value == ''){
				$this->_update_value = " set " . $v . " ";
			} else {
				$this->_update_value .= " , " . $v . " ";
			}
		}
		public function updateQuery($parameters){

			$data = $this->_db->query($this->getUpdateQuery(), $parameters);

			$this->_result = $data;
			$this->resetQuery();
			if($this->_result->count()){
				return true;
			}
			return false;
		}
		public function destroy($parameters){
			$q =  "DELETE " . $this->_from . " " . $this->_where;

			$data = $this->_db->query($q, $parameters);
			$this->_result = $data;
			$this->resetQuery();
			if($this->_result->count()){
				return true;
			}
			return false;

		}
	}
?>