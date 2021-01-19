<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");

	$user = new User();
	$company_id = $user->data()->company_id;
	if(function_exists($functionName) && $company_id) {
		$functionName($company_id);
	}

	function getPriceAdjustment($cid){
		$item_id = Input::get('item_id');
		$qty = Input::get('qty');
		$member_id = Input::get('member_id');
		$price_group_id = Input::get('price_group_id');
		$branch_id = Input::get('branch_id');

		$branch_adjustment = 0;
		$member_adjustment = 0;
		$price_group_adjustment = 0;

		if($branch_id){
			$branch_adjustment = getBranchAdjustment($branch_id,$item_id);
		}

		if($member_id){
			$member_adjustment = getMemberAdjustment($member_id,$branch_id,$item_id,$qty);
			if($member_adjustment){
				$member_adjustment = $member_adjustment / $qty;
			}
		}

		if($price_group_id){
			$price_group_adjustment = getPriceGroupAdjustment($price_group_id,$item_id);
		}

		$all_adjustment = $branch_adjustment + $price_group_adjustment +$member_adjustment;

		echo $all_adjustment;

	}

	function getPriceGroupAdjustment($price_group_id,$item_id){
		$adj = 0;
		if($price_group_id){
			$adjustment_class = new Item_price_adjustment();
			$adj_price_group = $adjustment_class->getAdjustmentPriceGroup($item_id,$price_group_id);
			if(isset($adj_price_group->adjustment)){
				$adj = $adj_price_group->adjustment;
			}
		}
		return $adj;
	}

	function getBranchAdjustment($b, $i){
		$adjustment_class= new Item_price_adjustment();
		$adj = $adjustment_class->getAdjustment($b,$i);
		if(isset($adj->adjustment)){
			return $adj->adjustment;
		} else {
			return  0;
		}
	}

	function getMemberAdjustment($member_id,$item_id,$qty){
		if($member_id){
			$memberTerms = new Member_term();
			$memadj =$memberTerms->getAdjustment($member_id,$item_id);
			$total_member_adjustment = 0;
			$alladj = 0;
			if(count($memadj)){
				$alladjInd = 0;
				$alladjAbove = 0;
				foreach($memadj as $m){
					$madj = $m->adjustment;

					if($m->type == 1){ // for every
						if($qty < 1 && $qty != 0){
							if($m->qty == 1){
								$x = $qty / $m->qty;
							} else {
								$x = 0;
							}

						} else {
							$x = floor($qty / $m->qty);
						}

						$madj = $madj * $x;
						$total_member_adjustment += $madj;
						$alladjInd += $madj;
					} else if ($m->type == 2){ // above qty

						if($qty >= $m->qty){
							if($m->discount_type == 0){
								$alladjAbove += $madj;
								$total_member_adjustment += $madj;
							} else {
								$madj = $madj * $qty;
								$alladjAbove += $madj;
								$total_member_adjustment += $madj;
							}
						}


					}
				}
				if($alladjAbove){
					$alladj = $alladjAbove;
				} else if($alladjInd){
					$alladj = $alladjInd;
				}
			}
		}
		return $alladj;

	}


	function saveQuotation(){
		$form = json_decode(Input::get('form'));
		$items = json_decode(Input::get('items'));



		if(!$form->member_id){
			$form->member_id = 0;
		}
		$quotation = new Quotation();
		$user = new User();
		$form->contact_person = ($form->contact_person) ? $form->contact_person : '';
		$quotation->create(
			[
				'quotation_for' => $form->remarks,
				'company_name' => $form->client_name,
				'member_id' => $form->member_id,
				'address' => $form->address,
				'quote_date' => $form->date,
				'contact_person' => $form->contact_person,
				'contact_number' => $form->contact_number,
				'validity' => $form->validity,
				'payment_terms' => $form->payment_terms,
				'availability' => $form->availability,
				'extra_note' => $form->note,
				'prepared_by' => $form->prepared_by,
				'checked_by' => $form->checked_by,
				'approved_by' => $form->approved_by,
				'received_by' => $form->received_by,
				'user_id' => $user->data()->id,
				'status' =>1,

			]

		);

		$lastid = $quotation->getInsertedId();

		foreach($items as $item){
			$quotation_item = new Quotation_item();
			$quotation_item->create(
				[
					'qty' => $item->qty,
					'item_id' => $item->item_id,
					'quotation_id' => $lastid,
					'unit_name' => $item->unit,
					'unit_qty' => $item->computed_qty,
					'adjustment' => $item->price_adjustment,
					'price' => $item->price,
					'description' => $item->description,
				]
			);
		}
		echo $lastid;


	}
	function updateQuotation(){
		$form = json_decode(Input::get('form'));
		$items = json_decode(Input::get('items'));





		$lastid = $form->id_number;
		if($lastid && is_numeric($lastid)){

			$q = new Quotation_item();

			$q->deleteItems($lastid);

			foreach($items as $item){
				$quotation_item = new Quotation_item();
				$quotation_item->create(
					[
						'qty' => $item->qty,
						'item_id' => $item->item_id,
						'quotation_id' => $lastid,
						'unit_name' => $item->unit,
						'unit_qty' => $item->computed_qty,
						'adjustment' => $item->price_adjustment,
						'price' => $item->price
					]
				);
			}

		} else {
			echo "failed'";
		}



	}

	function reprintItems(){

		$id = Input::get('id');
		$quotation = new Quotation($id);
		$quotation_item = new Quotation_item();

		//	form: {id_number:'Auto',prepared_by:'',received_by:'',approved_by:'',checked_by:'',client_name:'',address:'',contact_person:'',contact_number:'',date:'',validity:'30',remarks:'',note:'',payment_terms:'80% down payment, 20% upon delivery',availability:'1-2 weeks upon receipt of down payment',member_id:'',	price_group_id:'0'},
		//item:{ item_id:'', unit:'', item_code:'',description:'',qty:'',price:'',computed_qty:'',total:'', price_label:'', total_label:'',adj:''},

		$arr  = $quotation_item->getItems($id);
		$items = [];


		foreach($arr as $i){
			$per_unit = $i->unit_qty / $i->qty;
			$desc = $i->description;
			if($i->item_id == -1){
				$desc = $i->qdesc;
			}
			$items[] = [
				'item_id' => $i->item_id,
				'unit' => $i->unit_name,
				'description' => $desc,
				'item_code' => $i->item_code,
				'price' => $i->price,
				'computed_qty' => $i->unit_qty,
				'price_adjustment' => $i->adjustment,
				'qty' => $i->qty,
				'total' => $i->price  * $i->qty,
				'price_label' => number_format($i->price* $per_unit,2),
				'adj' => number_format($i->adjustment,2),
				'total_label' => number_format(($i->price* $per_unit) * $i->qty,2),
			];
		}

		$form = [
			'id_number' => $id,
			'prepared_by' => $quotation->data()->prepared_by,
			'received_by' => $quotation->data()->received_by,
			'approved_by' => $quotation->data()->approved_by,
			'checked_by' => $quotation->data()->checked_by,
			'client_name' => $quotation->data()->company_name,
			'address' => $quotation->data()->address,
			'availability' => $quotation->data()->availability,
			'contact_person' => $quotation->data()->contact_person,
			'contact_number' => $quotation->data()->contact_number,
			'date' => $quotation->data()->quote_date,
			'validity' => $quotation->data()->validity,
			'remarks' => $quotation->data()->quotation_for,
			'payment_terms' => $quotation->data()->payment_terms,
			'note' => $quotation->data()->extra_note,
		];


		echo json_encode(['form' => $form, 'items' => $items]);


	}

	function updateQuotationStatus(){
		$id = Input::get('id');
		$status = Input::get('status');

		$quotation = new Quotation();
		$quotation->update(['status' => $status],$id);
		echo "Request processed successfully.";
	}


	function getOrderedItems(){

		$id = Input::get('id');
		$quotation_item = new Quotation_item();
		$arr  = $quotation_item->getItems($id);

		$items = [];

		foreach($arr as $i){

			if($i->item_id != -1){
				$items[] = [
					'item_id' => $i->item_id,
					'unit' => $i->unit_name,
					'description' => $i->description,
					'item_code' => $i->item_code,
					'price' => $i->price,
					'computed_qty' => formatQuantity($i->unit_qty),
					'qty' =>formatQuantity($i->qty),
					'total' => $i->price * $i->qty,
					'price_label' => number_format($i->price,2),
					'adj' => number_format($i->adjustment,2),
					'total_label' => number_format($i->price * $i->unit_qty,2),
				];
			}

		}

		echo json_encode($items);


	}

	function submitOrder(){
		$id = Input::get('id');
		$member_id = Input::get('member_id');
		$items = json_decode(Input::get('items'));
		$branch_id = Input::get('branch_id');
		$remarks = Input::get('remarks');



		$notValid = "";
		$isValid = true;
		foreach($items as $item){
			// check inventory again

			if($member_id){
				$data = getAdjustmentPriceGlobal($branch_id,$item->item_id,$member_id,$item->computed_qty);
				$split = explode("||",$data);
				if(isset($split) && $split[2] == 0){
					$notValid .= $item->item_code . " doesn't have inventory as of now. Other user just got it first. <br>";
					$isValid = false;
				}
			}
		}

		if($isValid){

			$order = new Wh_order();
			$now = time();
			$user = new User();

			$order->create(array(
				'branch_id' => $branch_id,
				'member_id' => $member_id,
				'to_branch_id' => $branch_id,
				'remarks' => $remarks,
				'client_po' => '',
				'created' => $now,
				'company_id' => $user->data()->company_id,
				'user_id' => $user->data()->id,
				'is_active' => 1,
				'status' => 1,
				'stock_out' => 0,
				'for_pickup' => 0,
				'is_reserve' => 0
			));

		 	$order_id = $order->getInsertedId();

				foreach($items as $item){

					$order_details = new Wh_order_details();

					$product = new Product($item->item_id);

					$price = $product->getPrice($item->item_id);

					$qty = $item->computed_qty;

					$alladj = $item->adj * $qty;

					$order_details->create(array(
						'wh_orders_id' => $order_id,
						'item_id' => $item->item_id,
						'price_id' => $price->id,
						'qty' => $qty,
						'created' => $now,
						'modified' => $now,
						'company_id' => $user->data()->company_id,
						'is_active' => 1,
						'member_adjustment' => $alladj
					));
				}

			$quotation = new Quotation();
			$quotation->update(['order_id' => $order_id,'status' => 4],$id);

			echo "Order's submitted successfully;";

		} else {
			echo $notValid;
		}




	}

