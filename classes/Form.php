<?php
	class Form extends Crud {

		protected $_table = 'forms';

		public function __construct($r = null) {
			parent::__construct($r);
		}

		public function getList($name){
			$parameters = [];
			if ($name) {
				$parameters[] = $name;
				$q = "Select * from forms where  ref_name = ?  order by id desc";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function checker($ref_name , $ref_id){
			$parameters = [];
			if ($ref_name && $ref_id) {

				$parameters[] = $ref_id;
				$parameters[] = $ref_name;

				$q = "Select count(*) as cnt from forms where ref_id = ? and ref_name = ? ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return ($data->first()->cnt) ? true : false;
				}
			}
		}

		public function lastID($ref_name){
			$parameters = [];
			if ($ref_name) {


				$parameters[] = $ref_name;

				$q = "Select ref_id from forms where ref_name = ? order by ref_id desc limit 1 ";

				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return ($data->first()->ref_id) ? $data->first()->ref_id : 0;
				}
			}
		}
	}