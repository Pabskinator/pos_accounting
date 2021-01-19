<?php
	class Supplier_expense extends Crud{

		protected $_table = 'supplier_expenses';

		public function __construct($se=null){
			parent::__construct($se);
		}

		public function getExpense($id, $status = 0) {
			$parameters = array();
			if($id) {
				$parameters[] = $id;
				$whereStatus = "";
				if($status){
					$parameters[] = $status;
					$whereStatus = " and status = ? ";
				}

				$q = "Select * from supplier_expenses where supplier_order_id = ?  $whereStatus order by payable_to ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
		public function getPayExpense($id, $status = 0) {
			$parameters = array();
			if($id) {
				$parameters[] = $id;
				$whereStatus = "";
				if($status){
					$parameters[] = $status;
					$whereStatus = " and e.status = ? ";
				}

				$q = "Select
 					  e.*, p.paid_amount
 					 from supplier_expenses e
 					 left join (select sum(amount) as paid_amount, expense_id from supplier_payments group by expense_id)
 					  p on p.expense_id = e.id
 						where e.supplier_order_id = ?
 					   $whereStatus order by e.payable_to ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
	}