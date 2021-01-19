<?php
	//Contact_form_message

	class Contact_form_message extends Crud{
		protected $_table = 'contact_form_messages';
		public function __construct($c=null){
			parent::__construct($c);
		}

		public function countRecord($cid) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;


				$q = "Select count(*) as cnt from contact_form_messages where 1=1 and company_id = ? ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				$q = "Select * from contact_form_messages where 1=1 and company_id = ? order by created desc $l ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}
