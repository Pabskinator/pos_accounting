<?php
	class Remarks_list extends Crud{
		protected $_table = 'remarks_list';
		public function __construct($remark=null){
			parent::__construct($remark);
		}
		public function getServices($ref_id = 0,$ref_table =0,$company_id=0){
			$parameters = array();
			$now = time();
			if($company_id && $ref_id && $ref_table){
				$parameters[] = $company_id;
				$parameters[] = $ref_table;
				$parameters[] = $ref_id;
				$q= "Select r.*, u.lastname , u.firstname
					from remarks_list r
					left join users u on u.id = r.user_id
					where r.company_id = ? and r.ref_table = ? and r.ref_id = ? and r.is_active = 1
					order by r.created desc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
	}
?>