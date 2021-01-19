<?php
	class Supplier_payment extends Crud {

		protected $_table = 'supplier_payments';

		public function __construct($s=null){
			parent::__construct($s);
		}

		public function paidExpense($expense_id=0){
			$parameters = array();
			if($expense_id) {

				$parameters[] = $expense_id;

				$q = " SELECT sum(amount) as amount from supplier_payments where expense_id = ? ";
				$data = $this->_db->query($q, $parameters);

				// return results if there is any
				if($data->count()) {
					return $data->first();
				}

			}
		}

		public function payments($order_id){
			$parameters = array();
			if($order_id) {

				$parameters[] = $order_id;

				$q = " SELECT e.*, se.payable_to, se.amount as total_amount
 						from supplier_payments e
 						left join supplier_expenses se on se.id = e.expense_id
 						where e.supplier_order_id = ? order by e.expense_id desc ";

				$data = $this->_db->query($q, $parameters);

				// return results if there is any
				if($data->count()) {
					return $data->results();
				}

			}
		}

	}
?>