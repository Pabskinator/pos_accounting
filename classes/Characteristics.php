<?php
	class Characteristics extends Crud{
		protected $_table = 'characteristics';
		public function __construct($char=null){
			parent::__construct($char);
		}
		public function getChars($id){
			$parameters = array();


			$parameters[] = $id;
			$q= "select * from characteristics where company_id = ? and is_active = 1 order by tag asc, name asc";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}

		}

	}
?>