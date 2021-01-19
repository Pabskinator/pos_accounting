<?php
	class Position extends Crud{
		protected $_table='positions';
		public function __construct($position=null){
			parent::__construct($position);
		}
		public function getName($id=0){
			$parameters = array();
			if($id){
				$parameters[] =$id;
				$q= 'Select `position` from `positions`  where  is_active=1 and id=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function getPositions($id=''){
			$parameters = array();
			if($id){
				$exploded = explode(',',$id);
				$lid = "";
				foreach($exploded as $e){
					$e = (int) $e;
					$lid .= $e.",";
				}
				$lid = rtrim($lid,",");
				 $q= "Select `position` from `positions`  where  is_active=1 and id in ($lid)";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}
		public function getAllPositions(){
			$parameters = array();


			$q= "Select * from `positions`  where  is_active=1 order by TRIM(`position`) asc";
			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return $e->results();
			}

		}
	}
?>