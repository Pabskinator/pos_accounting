<?php 
	include 'service/connection.php';

	function getClassesSchedule($mysqli){
		$q  = "Select s.* ,os.name from class_schedules s left join offered_services os on os.id = s.class_id where 1 = 1 and s.is_active = 1";
		$result = $mysqli->query($q);
		$arr_os = [];
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			// select class here
			$arr_os[$row['name']][$row['day_of_the_week']][] =['time_of_the_day' =>$row['time_of_the_day'] , 'id' => $row['id'], 'is_pt' => $row['is_pt'], 'class_type' => $row['class_type']];	
		}
		$arr_days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

	

		echo "<div id='no-more-tables' style='padding:10px;overflow-x:auto;'>";
		echo "<table class=''>";
		echo "<thead>";
			echo "<tr>";
			echo "<th>Class</th>";
			foreach($arr_days as $day){
				echo "<th>$day</th>";
			}
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
				$classtype = ['','Turf Area','Matted Area'];
				$is_pt = ['','Class','PT'];
			foreach($arr_os as $class_name => $aos){
				echo "<tr>";
				echo "<td data-title='Class' style='border:1px solid #ccc;' class='red-text'>$class_name</td>";
				foreach($arr_days as $d){
					$cur = isset($aos[$d]) ? $aos[$d] : [];
					echo "<td data-title='".$d."' style='border:1px solid #ccc;' >";
					if(count($cur)){
						foreach($cur as $a){
							$ex = explode('-',$a['time_of_the_day']);
							$hr_from = date('h:i A',strtotime($ex[0]));
							$hr_to = date('h:i A',strtotime($ex[1]));
							echo "<small style='display:block;'>$hr_from - $hr_to <small style='display:block;' class='red-text'>".$classtype[$a['class_type']]."- ".$is_pt[$a['is_pt']]."</small></small> ";
						}
					}
					echo "</td>";
				}
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			echo "</div>";
	} 

	getClassesSchedule($mysqli);

?>