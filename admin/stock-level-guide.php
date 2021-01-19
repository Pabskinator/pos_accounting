<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$dt_from = strtotime(date('F Y'));
	$dt_to = strtotime(date('F Y') . "1 month -1 min");



	$dt_from_last_year = strtotime(date('m/d/Y H:i:s',$dt_from) . "-1 year");
	$dt_to_last_year = strtotime(date('m/d/Y H:i:s',$dt_to) . "-1 year");
	$dt_from_last_year_2 = strtotime(date('m/d/Y H:i:s',$dt_from) . "-2 year");
	$dt_to_last_year_2 = strtotime(date('m/d/Y H:i:s',$dt_to) . "-2 year");

	$sales = new Sales();
	$sales_whole =  $sales->summaryByItem($dt_to_last_year,$dt_from);
	$sales_cur =  $sales->summaryByItem($dt_from,$dt_to);
	$sales_last_year = $sales->summaryByItem($dt_from_last_year,$dt_to_last_year);
	$sales_last_year_2 = $sales->summaryByItem($dt_from_last_year_2,$dt_to_last_year_2);

	$inv = new Inventory();

	$inventory = $inv->get_audit_record($user->data()->company_id,0,10000);

	$arr_item = [];

	$cur  = [];

	$list = [];

	$list_2 = [];

	$whole = [];

	$arrinv = [];




	if($inventory){

		foreach($inventory as $i){
			if(!in_array($i->item_code,$arr_item)) $arr_item[] = $i->item_code;

			$arrinv[$i->item_code] = $i->qty;

		}

	}
	if($sales_cur){

		foreach($sales_cur as $st){
			if(!in_array($st->item_code,$arr_item)) $arr_item[] = $st->item_code;

			$cur[$st->item_code] = $st->totalquantity;

		}

	}
	if($sales_whole){

		foreach($sales_whole as $st){
			if(!in_array($st->item_code,$arr_item)) $arr_item[] = $st->item_code;

			$whole[$st->item_code] = ($st->totalquantity / 12) ; // avg year

		}

	}
	if($sales_last_year){

		foreach($sales_last_year as $st){
			if(!in_array($st->item_code,$arr_item)) $arr_item[] = $st->item_code;

			$list[$st->item_code] = $st->totalquantity;

		}

	}

	if($sales_last_year_2){

		foreach($sales_last_year_2 as $st){

			if(!in_array($st->item_code,$arr_item)) $arr_item[] = $st->item_code;

			$list_2[$st->item_code] = $st->totalquantity;

		}
	}


?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Stock Level Guide </h1>

	</div>

	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading"></div>
				<div class="panel-body">
					<?php
						echo date('m/d/Y H:i:s A',$dt_from) . "-" .date('m/d/Y H:i:s A',$dt_to);
						echo "<br>" . date('m/d/Y H:i:s A',$dt_from_last_year) . "-" .date('m/d/Y H:i:s A',$dt_to_last_year);
						echo "<br>" . date('m/d/Y H:i:s A',$dt_from_last_year_2) . "-" .date('m/d/Y H:i:s A',$dt_to_last_year_2);
						echo "<br>";
						echo count($arr_item) . " items";
					?> <br>
					<table class='table table-bordered table-condensed'>
						<thead>
						<tr>
							<th>Item Code</th>
							<th>Inventory</th>
							<th>This month sales</th>
							<th>Avg</th>
							<th>Last year</th>
							<th>2 Years Ago</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
							<?php

								if($arr_item){
									foreach($arr_item as $a){
										$current = isset($cur[$a]) ? $cur[$a] : 0;
										$last_year = isset($list[$a]) ? $list[$a] : 0;
										$last_year_2 = isset($list_2[$a]) ? $list_2[$a] : 0;
										$wholeyear = isset($whole[$a]) ? $whole[$a] : 0;
										$qty = isset($arrinv[$a]) ? $arrinv[$a] : 0;

										echo "<tr>
										<td style='border-top:1px solid #ccc;'>$a</td>
										<td  style='border-top:1px solid #ccc;'>" . formatQuantity($qty) . " </td>
										<td  style='border-top:1px solid #ccc;'>" . formatQuantity($current) . " </td>
										<td  style='border-top:1px solid #ccc;'>" . formatQuantity($wholeyear) . " </td>
										<td  style='border-top:1px solid #ccc;'>" . formatQuantity($last_year). "</td>
										<td  style='border-top:1px solid #ccc;'>" . formatQuantity($last_year_2). "</td>
										<td  style='border-top:1px solid #ccc;'></td>
										</tr>";

									}
								}
							?>
						</tbody>

					</table>

				</div>
			</div>
		</div>

	</div> <!-- end page content wrapper-->

	<script>
		$(function(){


		})
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>