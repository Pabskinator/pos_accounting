<?php
	include '../tl/db.php';

	$func = $_POST['functionName'];
	if(function_exists($func)){
		$func($conn);
	}

	function saveOrder($conn){

			$now = time();
			$items = $_POST['items'];
			$branch_name = $_POST['branch_name'];
			$query = "Insert into wh_orders (`created`,`walkin_info`,`status`,`remarks`) values ($now,'$items',99,'$branch_name')";
			mysqli_query($conn, $query);
			$last_id = mysqli_insert_id($conn);



			echo "1".str_pad($last_id,5,"0",STR_PAD_LEFT);

	}