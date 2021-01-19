<?php

	class Wh_order_batch extends Crud{

		protected $_table = 'upload_batches';

		public function __construct($w=null){
			parent::__construct($w);
		}

		public function getPending($is_log = 0,$dt_from = 0, $dt_to=0,$sales_type_name='',$status=0){
			$params = [];
			if($is_log){
				$this->where("status = 6");
			} else {
				$this->where("status != 6 ");
			}

			if($dt_from && $dt_to){
				$dt_from = strtotime($dt_from);
				$dt_to = strtotime($dt_to . "1 day -1 min");
				$this->where("and created >= $dt_from and created <= $dt_to");
			}
			if($sales_type_name){
				$this->where("and TRIM(store_type) = ? ");
				$params[] = $sales_type_name;
			}
			if($status){
				$this->where("and status = ? ");
				$params[] = $status;
			}

			return $this->select("*")
				->from("upload_batches")
				->limitBy(2000)
				->get($params)
				->all();

		}


		public function getItems($batch_id){
			$params = [];

			if($batch_id){

				$params[] = $batch_id;

				  return $this->select(
					              " b.batch_name,wh.client_po,wh.payment_id,wh.id as order_id,wh.branch_id,wh.dr,wh.rebate,
					                m.lastname as member_name,m.personal_address,m.salestype,
					                wd.id as details_id,wd.item_id, wd.qty,p.price, wd.price_id, wd.item_id, wd.member_adjustment,
					                i.item_code, i.description, i.item_type, i.has_serial"
				                )
					->from("upload_batches b")
					->join("left join wh_orders wh on wh.batch_id = b.id")
					->join("left join members m on m.id= wh.member_id")
					->join("left join wh_order_details wd on wd.wh_orders_id = wh.id")
					->join("left join prices p on p.id= wd.price_id")
					->join("left join items i on i.id = wd.item_id")
					->where("b.id = ? and wh.status != 5")
					->get($params)
					->all();
			}

		}

		public function getOrders($batch_id){
			$params = [];

			if($batch_id){

				$params[] = $batch_id;

				return $this->select(
					"id,client_po"
				)
					->from("wh_orders")
					->where("batch_id = ? ")
					->get($params)
					->all();
			}

		}

	}
