<?php
	include 'service/connection.php';
	session_start();
	if(!$_SESSION['user_id']){
		header("Location: login.php");
		exit();
	}
	function encrypt_decrypt($action, $string) {
			
			$output = false;

			$encrypt_method = "AES-256-CBC";
			$secret_key = 'pogisi';
			$secret_iv = 'jayson';

				// hash
			$key = hash('sha256', $secret_key);

				// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
			$iv = substr(hash('sha256', $secret_iv), 0, 16);

			if( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
			}

			else if( $action == 'decrypt' ){
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
			}

			return $output;

	}
	function getCertificate($mysqli){
		 $q= 'Select s.*,ct.name as category_name, m.lastname,m.firstname,m.middlename,i.item_code from services s 
		 left join members m on m.id=s.member_id 
		 left join items i on i.id=s.item_id 
		 left join categories ct on ct.id = i.category_id
		 where s.company_id=1 and s.member_id = '.$_SESSION['member_id'].' 
		 and s.consumable_qty = 0 and i.has_certificate = 1';
		$result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		
		if($result->num_rows > 0){
			while($row =  $result->fetch_array(MYSQLI_ASSOC)){
					if($row['completed_date']){
						$completed_date = date('M d, Y',$row['completed_date']);
					} else {
						 $completed_date =  "April 15, 2017";
					}
					$arr[] =  ['category_name' => $row['category_name'], 'name' => $row['lastname'],'item_code' => $row['item_code'] ,'completed_date' => $completed_date];
			}
			return $arr;
		} else {
			return [];
		}	
	}
	function is_decimal( $val )
	{
		return is_numeric( $val ) && floor( $val ) != $val;
	}
	function getTotalCredit($mysqli){
		$q= 'Select sum(amount - amount_paid) as pending_amount from member_credit where member_id = ' . $_SESSION['member_id'];
		$result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		if($result->num_rows > 0){
			if($num_rows == 1){
				$row = $result->fetch_array(MYSQLI_ASSOC);
				return $row['pending_amount'];
			} 
			return '0';
			
		} else {
			return "0";
		}	
	}
	function formatQuantity($v,$noComma = false){
		if(is_decimal($v)){
			if($noComma){
				return (number_format($v,3,'.',''));
			} else {
				return (number_format($v,3));
			}
		} else {
			if($noComma){
				return (number_format($v,0,'.',''));
			} else {
				return (number_format($v));
			}
		}
	}
	function getSessionAttended($mysqli){
		
		 $q= 'Select count(id) as session_attended from service_attendance where member_id = ' . $_SESSION['member_id'];
		$result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		if($result->num_rows > 0){
			if($num_rows == 1){
				$row = $result->fetch_array(MYSQLI_ASSOC);
				return $row['session_attended'];
			} 
			return '0';
			
		} else {
			return "0";
		}	
	}
	function getAttendance($mysqli){
		 $q= 'Select *  from service_attendance where member_id = ' . $_SESSION['member_id'];
		 $result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		if($result->num_rows > 0){
			$arr = [];
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$out = ($row['time_out']) ?  date('m/d/Y H:i:s A',$row['time_out'])  : 'No Out';
				$arr[] = ['time_in' => date('m/d/Y H:i:s A',$row['time_in']) ,'time_out' => $out ];
			}
			return $arr;
		} else {
			return [];
		}	
	}
	function getServices($mysqli){
		$now = time();
		 $q= 'Select s.id, s.start_date,s.end_date,m.lastname,m.firstname,m.middlename,i.item_code from services s left join members m on m.id=s.member_id left join items i on i.id=s.item_id where s.company_id=1 and s.member_id = '.$_SESSION['member_id'].' and s.consumable_qty = 10000 and i.item_type=3 order by s.end_date desc';
		$result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		if($result->num_rows > 0){
			if($num_rows == 1){
				$row = $result->fetch_array(MYSQLI_ASSOC);
				return ['type' => 1, 'item_code' => $row['item_code'] ,'end_date' => date('F d, Y',$row['end_date'])];
			} else {
				$arr = [];
				while($row =  $result->fetch_array(MYSQLI_ASSOC)){
						$arr[] =  ['item_code' => $row['item_code'] ,'end_date' => date('F d, Y',$row['end_date'])];
				}
				return ['type' => 2, 'data' => $arr];
			}
			
		} else {
			return "No enrolled Service";
		}	
	}
	function getConsumables($mysqli){
		$now = time();
		 $q= 'Select s.*, i.item_code, m.lastname from services s left join items i on i.id = s.item_id left join members m on m.id=s.member_id where s.company_id=1 and s.member_id = '.$_SESSION['member_id'].' and s.consumable_qty != 10000 and s.consumable_qty>0 and s.end_date > '.$now;
		$result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		if($num_rows > 0){
			
			
			if($num_rows == 1){
				$row = $result->fetch_array(MYSQLI_ASSOC);
				return ['type' => 1, 'item_code' => $row['item_code'] , 'qty' => $row['consumable_qty']];
			}else {
				$arr = [];
				while($row =  $result->fetch_array(MYSQLI_ASSOC)){
						$arr[] =  ['item_code' => $row['item_code'] ,'qty' => $row['consumable_qty']];
				}
				return ['type' => 2, 'data' => $arr];
			}
		} else {
			return "0";
		}	
	}
	$total_sessions_attended =  getSessionAttended($mysqli);
	$to_assess_arr = [4,9,14,19,24,29,34,39];
?>

<?php include_once 'includes/member/page_head.php'; ?>

<?php include_once 'includes/member/sidebar.php'; ?>

<main>
	<div class="main_heading">
		<div id='con_member_since'><h4 class='white-text'>Member Since: <?php echo $_SESSION['member_since'] ?></h4></div>
		<div id='con_sessions' class='white-text'>
			<h5>Sessions Attended</h5>
			<h3><?php echo $total_sessions_attended; ?></h3>
		</div>
	</div>

	<div id="main">
	<div style='padding:10px;' class="grey lighten-5 z-depth-1">
			<h5 class='black-text'>
				<?php 
					if(in_array($total_sessions_attended,$to_assess_arr)){
						echo "Note: You will have your assessment in your next session.";
					}
				?>
			</h5>
		</div>
		<div style='padding:10px;' class="grey lighten-5 z-depth-1">
			<h5 class='black-text'>Unpaid Bills: <span class='red-text'><?php echo number_format(getTotalCredit($mysqli),2); ?></span> 
			</h5>
		</div>
		<h4>Safehouse Attendance</h4>
		<?php 
			$data_attendance = getAttendance($mysqli);
			if(count($data_attendance) > 0){
				?>
				<table class='bordered striped highlight'>
			<thead>
			<tr>
				<th>#</th>
				<th data-field="time_in">Time In</th>
				<th data-field="time_out">Time Out</th>
			</tr>
			</thead>

			<tbody>
					<?php 
						$ctr = 1;
						foreach ($data_attendance as $att) {
							?>
							<tr>
								<td><?php echo $ctr; ?></td>
								<td><?php echo $att['time_in']; ?></td>
								<td><?php echo $att['time_out']; ?></td>
							</tr>
							<?php
							$ctr ++;
						}
					?>
			</tbody>
		</table>
				<?php
			} else {
				?>
					<div style='padding:10px;' class="grey lighten-5 z-depth-1">
				 <h5 class='black-text'>No record yet.</h5>
			</div>
				<?php
			}
		?>
		

		<!-- Panel -->
		<br>
		<h4>Enrolled services and consumables</h4>
		<div class="row">
			<div class="col m6">
				<div class="card-panel grey darken-4">
				    <h4 class="white-text">Subscription</h4>
				    <br>
				   
				    <?php 
				    	$service_enrolled = getServices($mysqli);

				    	if(!is_array($service_enrolled)){
				    		?>
				    		<h5 class='white-text right-align'><?php echo $service_enrolled?> </h5>
				    		<?php
				    	} else if (isset($service_enrolled['type']) && $service_enrolled['type'] == 1){
				    		?>
				    		 <p class='white-text right-align'><?php echo $service_enrolled['item_code']?> - Valid Until: <?php echo $service_enrolled['end_date']?> </p>
				    		<?php 
				    	} else if (isset($service_enrolled['type']) && $service_enrolled['type'] == 2){
				    		$data_member_subscription = $service_enrolled['data'];
				    		foreach($data_member_subscription as $ds){
				    			echo "<p class='white-text'>$ds[item_code] - Valid Until: ".$ds['end_date']."</p>";
				    		}
				    	}
				    ?>
				</div>
			</div>
			<div class="col m6">
				<div class="card-panel grey darken-4">
				    <h4 class="white-text">Consumables</h4>
				    <br>
				     <?php 
				    	$con_enrolled = getConsumables($mysqli);
				    	
				    	if(!is_array($con_enrolled)){
				    		?>
				    		<h5 class='white-text right-align'><?php echo $con_enrolled?> </h5>
				    		<?php
				    	} else if (isset($con_enrolled['type']) && $con_enrolled['type'] == 1){
				    		?>
				    		 <h5 class='white-text right-align'><?php echo formatQuantity($con_enrolled['qty']); ?> </h5>
				    		<?php 
				    	}else if (isset($con_enrolled['type']) && $con_enrolled['type'] == 2){
				    		$data_member_consumable = $con_enrolled['data'];
				    		foreach($data_member_consumable as $dc){
				    			echo "<p class='white-text'>$dc[item_code] - Sessions Left: ". formatQuantity($dc['qty']) ."</p>";
				    		}
				    	}
				    ?>
				</div>

			</div>
		</div>

		<!-- End panel-->


	<div>
		
		<?php 
			$cerf = getCertificate($mysqli);
			if($cerf){
				?>
				<h4>Certificate</h4>
				<div class="row">
				
				
				<?php
				foreach($cerf as $c){
					//$c[name]  $c[completed_date] 
					$name = encrypt_decrypt('encrypt',$c['name']);
					$dt = encrypt_decrypt('encrypt',$c['completed_date']);
					$item = encrypt_decrypt('encrypt',$c['item_code']);
					$category_name = encrypt_decrypt('encrypt',$c['category_name']);
					echo "<div class='col m4 s12'>";
					echo "<img class='materialboxed' data-caption='$c[item_code]' height='250' src='service/service.php?category_name=$category_name&name=$name&dt=$dt&item=$item&functionName=getCerf'>";
					echo "<br><a class='btn' target='_blanck' href='service/service.php?category_name=$category_name&name=$name&dt=$dt&item=$item&functionName=getCerf'><i class='left material-icons'>&#xE2C4;</i> Download</a> ";
					echo "</div>";
					
				}
				?>
				</div>
				<?php
			}
		?>
	</div>

	


	</div>
</main>
<footer>

</footer>


<script src="js/jquery.js"></script>
<script src="js/materialize.min.js"></script>
<script>
	$(function(){
		$('.button-collapse').sideNav();
		 $('.materialboxed').materialbox();
	});
</script>


<?php include_once 'includes/member/page_tail.php';  ?>
