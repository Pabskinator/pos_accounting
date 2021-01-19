<?php


	function __getMemberItemAdjustment($member_id,$item_id,$qty){
		$remarks = "";
		$alladj = 0;
		if($member_id){
			$memberTerms = new Member_term();
			$memadj =$memberTerms->getAdjustment($member_id,$item_id);
			$total_member_adjustment = 0;

			if(count($memadj)){
				$alladjInd = 0;
				$alladjAbove = 0;
				foreach($memadj as $m){
					$madj = $m->adjustment;
					if(!$madj) {
						continue;
					}
					if($m->remarks){
						$remarks .= $m->remarks . "***";
					}

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
				$remarks = rtrim($remarks,'***');
				if($alladjAbove){
					$alladj = $alladjAbove;
				} else if($alladjInd){
					$alladj = $alladjInd;
				}
			}
		}
		return ['alladj' => $alladj,'remarks' => $remarks];
	}


	function getAdjustmentPrice($branch_id=0,$item_id = 0, $member_id = 0,$qty=0){
		$adjustment_class = new Item_price_adjustment();
		$branch_id_to=0;

		if($branch_id && $item_id && $member_id && $qty){
			$is_ret = true;
		} else {
			$is_ret = false;
			$branch_id = Input::get('branch_id');
			$item_id = Input::get('item_id');
			$member_id = Input::get('member_id');
			$qty = Input::get('qty');
			$branch_id_to = Input::get('branch_id_to');
		}

		$price_group_id = Input::get('price_group_id');
		$nadj = 0;
		$alladj = 0;
		$ctr_item = 1;
		$_SESSION['cart_item_counter'] = $ctr_item;
		$valid = 0;
		$final_message = "";
		$remaining = 0;

		if($branch_id && $item_id && $qty){

			$availability = getReservedStocks($item_id,$branch_id,$qty);
			if($availability && $availability['message']){
				if(!$availability['success']){
					$final_message =  $availability['message'];
				} else {
					$valid = 1;
				}
				$remaining = $availability['remaining'];
			}
			if(Configuration::getValue('strict_order') == 2){
				$valid = 1;
			}


			$member_adjustment = __getMemberItemAdjustment($member_id,$item_id,$qty);

			$alladj = $member_adjustment['alladj'];
			$remarks_for_adjustment = $member_adjustment['remarks'];

			$adj = $adjustment_class->getAdjustment($branch_id,$item_id);

			if(isset($adj->adjustment)){
				$nadj += $adj->adjustment;
			}


			if($price_group_id){
				$adj_price_group = $adjustment_class->getAdjustmentPriceGroup($item_id,$price_group_id);
				if(isset($adj_price_group->adjustment)){
					$nadj += $adj_price_group->adjustment;
				}
			}


			$prod = new Product();
			$price = $prod->getPrice($item_id);

			$branch_discount = new Branch_discount();
			$b_disc = $branch_discount->getDiscount($branch_id,$branch_id_to);
			$b_disc_amount = 0;

			if(isset($b_disc->discount) && !empty($b_disc->discount)){
				$b_disc_amount = $b_disc->discount / 100;

				$b_disc_amount = $price->price * $b_disc_amount;
			}

			if($b_disc_amount){
				$b_disc_amount  = ($b_disc_amount * $qty) * -1;
				$alladj += $b_disc_amount;
			}


			if(Configuration::getValue('discount_by_category') == 1){
				$memdis = new Member_category_discount();
				$memcategdis= $memdis->hasDiscount($item_id,$member_id);
				$totaladd = 0;

				$computed_price = $price->price;
				if(isset($memcategdis->discount_1)  && $memcategdis->discount_1){
					$discount_1 = $memcategdis->discount_1 * 0.01;
					$toadd = $computed_price * $discount_1;
					$totaladd += $toadd;
					$computed_price = $computed_price - $toadd;
					if(isset($memcategdis->discount_2)  && $memcategdis->discount_2){
						$discount_2 = $memcategdis->discount_2 * 0.01;
						$toadd = $computed_price * $discount_2;
						$computed_price = $computed_price - $toadd;
						$totaladd += $toadd;
						if(isset($memcategdis->discount_3)  && $memcategdis->discount_3){
							$discount_3 = $memcategdis->discount_3 * 0.01;
							$toadd = $computed_price * $discount_3;
							$computed_price = $computed_price - $toadd;
							$totaladd += $toadd;
							if(isset($memcategdis->discount_4)  && $memcategdis->discount_4){
								$discount_4 = $memcategdis->discount_4 * 0.01;
								$toadd = $computed_price * $discount_4;
								$totaladd += $toadd;
							}
						}

					}
				}
				$totaladd = $qty * $totaladd;
				$totaladd = $totaladd * -1;
				$alladj += $totaladd;
			}

		}


		// get item freebie of item
		$item_freebie = new Item_freebie();

		$freebies = $item_freebie->getFreebies($item_id, $qty,$branch_id);

		$arr_freebies = [];
		if($freebies){
			foreach($freebies as $fr){
				$nadj_free =0;
				$adj_free = $adjustment_class->getAdjustment($branch_id,$fr->item_id);
				if(isset($adj_free->adjustment)){
					$nadj_free += $adj_free->adjustment;
				}

				if($price_group_id){
					$adj_price_group_free = $adjustment_class->getAdjustmentPriceGroup($fr->item_id,$price_group_id);
					if(isset($adj_price_group_free->adjustment)){
						$nadj_free += $adj_price_group_free->adjustment;
					}
				}

				$fr->price += $nadj_free;
				$total = $fr->qty * $fr->price;

				$need_qty = $fr->need_qty;
				$discount = $fr->discount / 100;
				$total = $total * $discount;
				$total = number_format($total,2,".","");

				$multiplier =  $qty / $need_qty ;

				$multiplier = floor($multiplier);

				$fr->qty = $fr->qty *  $multiplier;

				$total = ($total * $multiplier);

				$arr_freebies[] = [
					'item_id' => $fr->item_id,
					'item_code' => $fr->item_code,
					'description' => $fr->description,
					'qty' => formatQuantity($fr->qty),
					'price' => $fr->price,
					'inv_qty' => formatQuantity($fr->inv_qty),
					'price' => $fr->price,
					'total' => $total
				];
			}
		}

		$group_adjustment_optional = [];
		if(Configuration::getValue('group_adjustment_optional') == 1){
			$grp_adj_opt = new Item_group_adjustment();
			$grp_list = $grp_adj_opt->getAdjustment($item_id);
			if($grp_list){
				$group_adjustment_optional = [];
				foreach($grp_list as $grp_item){
					$group_adjustment_optional[] = $grp_item;
				}

			}
		}

		$_SESSION['cart_item_counter'] = ($_SESSION['cart_item_counter'])? $_SESSION['cart_item_counter'] : 1;
		if($is_ret){
			return  $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
		} else {
			$output = $nadj . "||" . $alladj. "||".$valid. "||" . $_SESSION['cart_item_counter'] . "||" .$remaining. "||" . $final_message;
			echo json_encode(['data' => $output, 'freebies' => $arr_freebies, 'group_adjustment' => $group_adjustment_optional,'adjustment_remarks' => $remarks_for_adjustment]);
		}

	}