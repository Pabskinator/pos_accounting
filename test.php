<?php
	error_reporting(E_ALL);
	 ini_set('display_errors', 1);
	$host = '127.0.0.1';
	$db   = 'apollosy_cebuhiq';
	$user = 'apollosy_cebuhiq';
	$pass = '409186963@StephenWang';
	$charset = 'utf8mb4';

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, $user, $pass, $opt);


	date_default_timezone_set('Asia/Manila');
	$pdo->exec('SET time_zone = "+8:00"');
	/*
	$now = time();
	$q = "Select it.*,p.price from items it
	LEFT JOIN
		( Select a.item_id, a.effectivity, p.price, p.id as price_id from
							(Select p.item_id, max(p.effectivity) as effectivity  from prices p left join items i on i.id=p.item_id  where i.company_id=1  and p.effectivity <= $now group by p.item_id) a
							left join prices p on p.item_id = a.item_id where a.effectivity = p.effectivity) p on p.item_id = it.id
	where it.is_active = 1";

	$q2 = "	Select
				i.* , pg.name as price_group_name
				from item_price_adjustment i
				left join price_groups pg on pg.id = i.price_group_id
				where i.is_active = 1 and i.price_group_id !=0
			";


	$q3 = "	Select * from price_groups where is_active = 1 ";


	$stmt = $pdo->query($q);
	$stmt2 = $pdo->query($q2);
	$stmt3 = $pdo->query($q3); */


	/* sync inventory*/

	$rack_id = 1;
	$branch_id = 1;
	$report_date=  1545408000;

	$q = "

  			Select it.item_code, it.description, i.qty, i.item_id
 			from inventory_ending i
  			left join items it on it.id = i.item_id
 			where i.branch_id = $branch_id and i.report_date = $report_date

 			";

	$q2 = "select * from inventory_monitoring where branch_id = $branch_id  and created >= $report_date order by created asc ";




?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="../css/bootstrap.css">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<br>
<div class="container-fluid">

</div><br>
<?php

	//$fnger = "Select * from users where id = 1 ";
	//$finger_res = $pdo->query($fnger);
	//$user_1 = $finger_res->fetch();
	//print_r($user_1['lastname']);


	$stmt2 = $pdo->query($q2);
	$stmt = $pdo->query($q);

	$arr_inv = [];
	$arr_inv_orig = [];

	foreach ($stmt as $row) {

		if(isset($arr_inv[$row['item_id']])){

			$arr_inv[$row['item_id']] += $row['qty'];
			$arr_inv_orig[$row['item_id']] += $row['qty'];

		} else {

			$arr_inv[$row['item_id']] = $row['qty'];
			$arr_inv_orig[$row['item_id']] = $row['qty'];

		}

	}


	/* echo "<table  class='table'>";
	echo "<tr><th>item</th><th>qty</th></tr>";
	foreach($arr_inv as $id => $qty){
		echo "<tr><td>$id</td><td>$qty</td></tr>";
	}
	echo "</table>"; */


	$add = [];
	$deduct = [];
	$amend = [];

	$last_mon = [];
	$total_add = [];
	$total_deduct = [];
	$arr_item_in_rack = [];

	foreach ($stmt2 as $row2) {

		if(!$row2['item_id']){
			echo $row2['id'];
		}

		$cur_rack_id = $row2['rack_id'];
		$arr_item_in_rack[$row2['item_id']][$cur_rack_id] = 1;


		if($row2['qty_di'] == 1){

			if(isset($arr_inv[$row2['item_id']])){
				$arr_inv[$row2['item_id']] += $row2['qty'];
			} else {
				$arr_inv[$row2['item_id']] = $row2['qty'];
			}

			if(isset($total_add[$row2['item_id']])){
				$total_add[$row2['item_id']] += $row2['qty'];
			} else {
				$total_add[$row2['item_id']] = $row2['qty'];
			}

		} else if ($row2['qty_di'] == 2){

			if(isset($arr_inv[$row2['item_id']])){
				$arr_inv[$row2['item_id']] -= $row2['qty'];
			} else {
				$arr_inv[$row2['item_id']] = 0  - $row2['qty'];
			}

			if(isset($total_deduct[$row2['item_id']])){
				$total_deduct[$row2['item_id']] += $row2['qty'];
			} else {
				$total_deduct[$row2['item_id']] = $row2['qty'];
			}

		} else if ($row2['qty_di'] == 3){

		}

		$last_mon[$row2['item_id']] = $row2['qty'];

	}



	echo "<table  class='table'>";
	echo "<tr><th>item</th><th>Reported</th><th>qty</th><th>add</th><th>deduct</th><th></th></tr>";
	$neg_count = 0;
	$neg_arr = [];
	$ctr = 0;

	foreach($arr_inv as $id => $qty){

		$e_rack_id = 0;

		if(isset($arr_item_in_rack[$id]) && count($arr_item_in_rack[$id]) >0){

			$arr_cur_rack = $arr_item_in_rack[$id];
			foreach($arr_cur_rack as $rid => $one ){
				$e_rack_id = $rid;
			}

			$lbl ="meron-" . $e_rack_id;

		} else {

			$rackql = "Select * from inventories where item_id = $id and branch_id = $branch_id and qty != 0 order by id desc limit 1  ";
			$rack_res = $pdo->query($rackql);

			$r1 = $rack_res->fetch();

			$e_rack_id = $r1['rack_id'];
			$lbl = "wala-".$e_rack_id;

		}

	//del current record
	//	$qdel = " Delete from inventories where branch_id = $branch_id and item_id = $id ";

	// insert record
	//	$qupdate = "INSERT INTO `inventories`(`item_id`, `rack_id`, `qty`, `branch_id`)
	//				VALUES ($id,$rack_id,$qty,$branch_id)";

		/*

		$inv_mon->create(array(
		'item_id' => $item_id,
		'rack_id' => $rack_id,
		'branch_id' => $branch_id,
		'page' => 'admin/addinventory',
		'action' => 'Insert',
		'prev_qty' => $curinventory,
		'qty_di' => 1,
		'qty' => $qty,
		'new_qty' => $newqty,
		'created' => time(),
		 'user_id' => $user->data()->id,
		'remarks' => 'Add Inventory From Borrowed Set Item',
		 'is_active' => 1, 'company_id' => $user->data()->company_id));

		*/



		$prev_qty = isset($last_mon[$id]) ? $last_mon[$id] : 0;
		$orig_qty = isset($arr_inv_orig[$id]) ? $arr_inv_orig[$id] : 0;
		$added = isset($total_add[$id]) ? $total_add[$id] : 0;
		$deducted = isset($total_deduct[$id]) ? $total_deduct[$id] : 0;

		$now = time();

	//	 $inv = "INSERT INTO inventory_monitoring (`item_id`,`rack_id`,`branch_id`,`action`,`prev_qty`,`qty_di`,`qty`,`new_qty`,`created`,`user_id`,`remarks`,`is_active`)
	//			VALUES ($id,$e_rack_id,$branch_id,'Update',$prev_qty,3,$qty,$qty,$now,1,'Update Inventory From Ending Report',1)";


	//	$invmonql = "Select * from inventory_monitoring where item_id = $id and branch_id = $branch_id and rack_id=$e_rack_id order by id desc limit 1  ";
	//	$invmonres = $pdo->query($invmonql);

	//	$inv_mon = $invmonres->fetch();

	//	$prev_qty = (isset($inv_mon['new_qty']) && $inv_mon['new_qty']) ? $inv_mon['new_qty'] : 0;



		$cls =  '';
		if($qty < 0){
			$neg_count++;
			$cls = 'bg-danger';
			$itemql = "Select item_code , description from items where id = $id limit 1  ";
			$item_res = $pdo->query($itemql);

			$it1 = $item_res->fetch();
			$neg_arr[] = ['item_code' => $it1['item_code'], 'description' => $it1['description'],'reported'=> $orig_qty,'added'=>$added,'deducted'=>$deducted,'on_hand'=>$qty];
		}
		$qdel='';
		$qupdate ='';
		$qinventoryupdatefinal = "aaaa";

		if($e_rack_id  && $branch_id && $id && $qty > 0){
			$qinventoryupdatefinal = "INSERT INTO `inventories`( `item_id`, `rack_id`, `qty`, `branch_id`, `critical_level`)
				VALUES ($id,$e_rack_id,$qty,$branch_id,100)";
		//	$pdo->query($qinventoryupdatefinal);
			$ctr++;
		}

		echo "<tr class='$cls'><td>$id - $lbl</td><td>$orig_qty</td><td>$qty</td><td>$added</td><td>$deducted</td><td>$prev_qty</td></tr>";
		echo "<tr class='$cls'><td colspan='6'>$qinventoryupdatefinal</td></tr>";


	}

	echo "</table>";

	echo "<h1>Neg Count: $neg_count CTR = $ctr</h1>";
	echo "<table class='table table-bordered'>";
	echo "<tr><th>Item code</th><th>Description</th><th>Reported</th><th>Added</th><th>Deducted</th><th>On hand</th></tr>";
	foreach($neg_arr as $a){
		echo "<tr><td>$a[item_code]</td><td>$a[description]</td><td>$a[reported]</td><td>$a[added]</td><td>$a[deducted]</td><td>$a[on_hand]</td></tr>";
	}
	echo "</table>";
	?>
	<pre>
		<?php

			foreach($arr_item_in_rack as $iind => $i){
				foreach($i as $r => $v){
					echo "<p>Cnt = ".count($i)."  Item ID $iind Rack $r</p>";
				}
			}

		?>
	</pre>
<?php


/*

	$arr_price_groups = [];
	$arr_adj = [];

	foreach ($stmt3 as $row) {
		$arr_price_groups[$row['id']] = $row['name'];
	}

	foreach ($stmt2 as $row) {
		$arr_adj[$row['price_group_id']][$row['item_id']] = $row['adjustment'];
	}

	echo "<table class='table table-bordered'>";
	echo "<tr>";
	echo "<th>Item</th>";
	echo "<th>Description</th>";
	echo "<th>Price</th>";
	foreach($arr_price_groups as $pg_id => $pg_name){
		echo "<th>$pg_name</th>";
	}

	echo "</tr>";
	foreach ($stmt as $row) {
		$price = number_format($row['price'],2);
		echo "<tr>
					<td>$row[item_code]</td>
					<td>$row[description]</td>
					<td>" . $price ."</td>
					";
		foreach($arr_price_groups as $pg_id => $pg_name){
			$adj = ($arr_adj[$pg_id][$row['id']]) ? $arr_adj[$pg_id][$row['id']] : 0;
			$adjusted = number_format(($row['price'] +$adj),2);
			$dngr = '';
			if($adjusted != $price){
				$dngr = "class='bg-danger'";
			}
			echo "<td $dngr>$adjusted</td>";
		}
		echo "</tr>";
	}
	echo "</table>";

	*/

?>


</body>
</html>



