<?php

	class Account_title extends Crud {
		protected $_table = 'account_titles';
		public function __construct($a = null) {
			parent::__construct($a);
		}

		/**
		 * Get all account titles
		 * @param integer $company_id
		 *
		 * @return array list
		 */
		public function getAcc($company_id = 0) {
			if($company_id) {



				$parameters[] = $company_id;

				$this->where("company_id=?");

				$this->where("and is_active=1");

				return $this->select("id,name,parent_id")
				     ->from("account_titles")
					 ->orderBy("parent_id asc")
					 ->get($parameters)
					 ->all();



			}
		}

		/**
		 * Check if account title has parent
		 *
		 * @param int $id
		 * @param int $company_id
		 *
		 * @return boolean
		 *
		 **/
		public function hasChild($company_id = 0, $id = 0) {
			if($company_id && $id) {



				$parameters[] = $company_id;
				$parameters[] = $id;
				$this->where("company_id=? and is_active=1 and parent_id=?");

				$data = $this->select("id")
					->from("account_titles")
					->get($parameters)
					->first();

				if(isset($data->id)) {

					return true;
				}

				return false;
			}
		}
	}

?>