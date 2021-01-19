<?php
	class Cr_log extends Crud{
		protected $_table = 'cr_log';
		public function __construct($char=null){
			parent::__construct($char);
		}
		/*
delivery_date,
delivery_receipt,
sales_invoice,
client_name,
receipt_amount,
deduction,
check_cash_amount,
bank_name,
check_no,
check_date,
terms
		 */
		public function isExistsCR(	$cr_number='',
		                               $delivery_date='',
		                               $delivery_receipt='',
		                               $sales_invoice='',
		                               $client_name='',
		                               $receipt_amount='',
		                               $deduction='',
										$paid_amount='',
										$bank_name='',
										$check_no='',
										$check_date='',
										$terms=''
								){

			$parameters = array();

			$parameters[] = $cr_number;
			$parameters[] = trim($delivery_date);
			$parameters[] = trim($delivery_receipt);
			$parameters[] = trim($sales_invoice);
			$parameters[] = trim($client_name);
			$parameters[] = $receipt_amount;
			$parameters[] = $deduction;
			$parameters[] = $paid_amount;
			$parameters[] = trim($bank_name);
			$parameters[] = trim($check_no);
			$parameters[] = trim($check_date);
			$parameters[] = trim($terms);


			$q= "Select * from cr_log where cr_number=? and
				TRIM(delivery_date)=? and
				TRIM(delivery_receipt)=? and
				TRIM(sales_invoice)=? and
				TRIM(client_name)=? and
				receipt_amount=? and
				deduction=? and
				paid_amount=? and
				TRIM(bank_name)=? and
				TRIM(check_no)=? AND
				TRIM(check_date)=? and
				TRIM(terms)=?";

			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return true;
			}
			return false;

			}


		public function checkCR(

		                               $delivery_receipt='',
		                               $sales_invoice='',
		                               $paid_amount='',
		                               $check_no='',
										$cr_num='',
										$member_name=''

		){

			$parameters = array();


			$parameters[] = trim($delivery_receipt);
			$parameters[] = trim($sales_invoice);



			$parameters[] = trim($check_no);
			$whereCr='';
			$whereMember='';
			$whereAmount = '';
			if($paid_amount){
				$parameters[] = $paid_amount;
				$whereAmount = ' and paid_amount=? ';
			}
			if($cr_num){
				$cr_num = trim($cr_num);
				$parameters[] = $cr_num;
				$whereCr = " and TRIM(cr_number) = ? ";
			}

			if($member_name){
				$member_name = trim($member_name);
				$parameters[] = $member_name;
				$whereMember = " and TRIM(client_name) = ? ";
			}



			$q= "Select * from cr_log where
				TRIM(REPLACE(delivery_receipt,'SVC',''))=? and
				TRIM(REPLACE(sales_invoice,'SVC',''))=?  and
				TRIM(check_no)=? $whereAmount $whereCr $whereMember";


			$e = $this->_db->query($q, $parameters);
			if($e->count()){
				return true;
			}
			return false;

		}

		public function getByCR($cr_number='') {
			$parameters = array();

			$parameters[] = $cr_number;

			$q = "Select * from cr_log where cr_number = ?";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}

		public function deleteCr($cr_number='') {
			$parameters = array();
			if($cr_number){
				$parameters[] = $cr_number;

				$q = "Delete from cr_log where cr_number = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
			return false;
		}

		public function approveCRLog($cr_number='') {
			$parameters = array();
			if($cr_number){
				$parameters[] = $cr_number;

				$q = "update cr_log set status = 0 where cr_number = ?";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}

			return false;
		}

		public function byCR($cr_number='') {

			$parameters = array();

			if($cr_number){

				$parameters[] = trim($cr_number);
				 $q = "select * from cr_log where TRIM(cr_number) = ? ";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}

			}

			return false;

		}

		public function deleteDetails($id=0) {
			$parameters = array();
			if($id){
				$parameters[] = $id;

				$q = "Delete from cr_log where id = ? limit 1 ";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
			}
			return false;
		}






	}
?>