<?php
	include 'service/connection.php';
	session_start();
	if(!$_SESSION['member_id']){
		header("Location: login.php");
		exit();
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
	function getModule($mysqli){
		 $q= 'Select w.description, w.name  from workout_module w left join workout_module_members wm on wm.module_id = w.id  where wm.member_id = ' . $_SESSION['member_id'];
		 $result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		if($result->num_rows > 0){
			$arr = [];
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$arr[] = ['name' => $row['name'] ,'description' => $row['description'] ];
			}
			return $arr;
		} else {
			return [];
		}	
	}
		
	
?>

<?php include_once 'includes/member/page_head.php'; ?>

<?php include_once 'includes/member/sidebar.php'; ?>
<main>
		<div class="main_heading">
			<div id='con_member_since'><h4 class='white-text'>Member Since: <?php echo $_SESSION['member_since'] ?></h4></div>
			<div id='con_sessions' class='white-text'>
				<h5>Sessions Attended</h5>
				<h3><?php echo getSessionAttended($mysqli); ?></h3>
			</div>

		</div>
	<div id="main">
		<?php 
			$myModules = getModule($mysqli);
			if($myModules){
				foreach($myModules as $m){
					echo "<h1>$m[name]</h1>";
					echo $m['description'];
				}
			} else {
				echo "<p class='red-text'>* Module has not been set yet.</p>";
			}
		?>

	</div>
</main>
<footer>

</footer>


<script src="js/jquery.js"></script>
<script src="js/materialize.min.js"></script>
<script>
	$(function(){
		$('.button-collapse').sideNav();
		
	});
</script>


<?php include_once 'includes/member/page_tail.php';  ?>
