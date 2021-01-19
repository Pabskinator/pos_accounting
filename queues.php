<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	$host = '127.0.0.1';
	$db   = 'apollosy_mp';
	$user = 'apollosy_mp';
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

	$start=15;
	$limit=15;
/*	$q = "

  			SELECT * FROM `uploads` WHERE `ref_table` LIKE 'members' order by id asc limit $start,$limit

 			";*/

	$q = "Select
			s.member_id, from_unixtime(s.sold_date), sum(((s.qtys * pr.price) + s.adjustment + s.member_adjustment)- (s.discount -s.store_discount)) as totalamount
			from sales s
			left join prices pr on pr.id = s.price_id
			left join members m on m.id = s.member_id
			where YEAR(FROM_UNIXTIME(s.sold_date)) = 2016 and s.status = 0 group by s.payment_id
			HAVING totalamount >= 50000
			order by s.sold_date asc limit $start,$limit "

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


<?php

	//$fnger = "Select * from users where id = 1 ";
	//$finger_res = $pdo->query($fnger);
	//$user_1 = $finger_res->fetch();
	//print_r($user_1['lastname']);



	$stmt = $pdo->query($q);


	echo "<div class='row'>";
	$i = 1;
	foreach ($stmt as $row) {
		$member_id = $row['member_id'];
		if($member_id){
			$q2 = "select filename,id from uploads where ref_id = $member_id and ref_table ='members' ";
			$stmt2 = $pdo->query($q2);
			foreach ($stmt2 as $row2) {
			    //	echo "<h5>Num $i - ID = " . $row2['id'] . "</h5><img style='height:250px;width:auto;' src='uploads/" . $row2['filename'] . "'><br>";
				$i++;

			}
		}



	}

	echo "</div>";




?>

</div><br>
</body>
</html>



