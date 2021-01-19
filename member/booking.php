<?php
	include 'service/connection.php';
	session_start();
	if(!$_SESSION['member_id']){
		header("Location: login.php");
		exit();
	}
	$date_one = date('m/01/Y');
	$date_two = date('m/d/Y', strtotime($date_one . " 1 month -1 min"));
	$arr_calender = [];
	$q = "Select s.* ,os.name from class_schedules s left join offered_services os on os.id = s.class_id where 1 = 1 and s.is_active = 1";
	$result = $mysqli->query($q);
	$arr_calender = [];
	$arr_class_name= [];
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		// select class here
		$arr_calender[$row['day_of_the_week']][] =['name' => $row['name'],'time_of_the_day' =>$row['time_of_the_day'], 'id' => $row['id'], 'is_pt' => $row['is_pt'], 'class_type' => $row['class_type']];	
		if(!in_array($row['name'], $arr_class_name)){
			$arr_class_name[$row['class_id']] = $row['name'];
		}
	}
	$my_request = "Select m.* , os.name, ch.name as coach_name from member_service_request m left join offered_services os on os.id = m.class_id  left join coaches ch on ch.id = m.coach_id where m.member_id = $_SESSION[member_id]";
	$result_request = $mysqli->query($my_request);
	$num_rows_req = $result_request->num_rows;
	$arr_req = [];

	if($num_rows_req > 0){
			while($row = $result_request->fetch_array(MYSQLI_ASSOC)){

				$ex = explode('-',$row['time_of_the_day']);
				$hr_from = date('h:i A',strtotime($ex[0]));
				$hr_to = date('h:i A',strtotime($ex[1]));

				$arr_status = ['','Pending','Processed'];
				$arr_req[] = [
					'title' => $row['name'],
					'start' => date('Y-m-d',$row['schedule_date']),
					'reserved' => $hr_from . "-" . $hr_to,

					'coach_name' => $row['coach_name']
				];
			}
	}

	function date_compare($a, $b)
	{

	}

	function getClassesSchedule($mysqli){

		$q  = "Select s.* ,os.name, ch.name as coach_name from class_schedules s left join offered_services os on os.id = s.class_id left join coaches ch on ch.id = s.coach_id where 1 = 1 and s.is_active = 1";
		$result = $mysqli->query($q);
		$arr_os = [];
		$arr_key = [];
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			// select class here
			$arr_os[$row['name']][$row['day_of_the_week']][] =['time_of_the_day' =>$row['time_of_the_day'] , 'id' => $row['id'], 'is_pt' => $row['is_pt'], 'class_type' => $row['class_type'], 'coach_name' => $row['coach_name']];
			$arr_key[$row['name']] = $row['day_of_the_week'];
		}
		foreach($arr_key as $key => $val){
			// select class here
			usort($arr_os[$key][$val],function($a,$b){
				$a_ex = explode("-",$a['time_of_the_day']);
				$b_ex = explode("-",$b['time_of_the_day']);

				$t1 = strtotime($a_ex[0]);
				$t2 = strtotime($b_ex[0]);
				return $t1 - $t2;
			});
		}




		$arr_days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

	

		echo "<div id='no-more-tables'>";
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
							echo "<small style='display:block;'>
										$hr_from - $hr_to
										<small style='display:block;' class='red-text'>".$classtype[$a['class_type']]."- ".$is_pt[$a['is_pt']]."</small>
										<small style='display:block;' class='red-text'>Coach: $a[coach_name]</small>
										</small> ";
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
?>

<?php include_once 'includes/member/page_head.php'; ?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css">

<?php include_once 'includes/member/sidebar.php'; ?>

<main>
		<div class="main_heading">
			<div id='con_member_since'><h4 class='white-text'>Member Since: January 01, 2017</h4></div>
			<div id='con_sessions' class='white-text'>

			</div>

		</div>
	<div id="main" class='grey lighten-5 z-depth-1'>
	 <div class="fixed-action-btn">
	<a id='btnAddBooking' class="btn-floating btn-large waves-effect waves-light red"><i class="material-icons">add</i></a>
	</div>
		<?php 
			

			$start_date = strtotime($date_one);
			$end_date = strtotime($date_two);
			$arrjson = [];
			while($start_date <= $end_date){
				$date_cur = date('m/d/Y', $start_date);
				if(isset($arr_calender[date('l', $start_date)])){
					$arr_cur = $arr_calender[date('l', $start_date)];
					if(count($arr_cur)){
						foreach($arr_cur as $class_s){
							$arrjson[] = ['title' => $class_s['name'] . " " . $class_s['time_of_the_day'], 'start' => $date_cur];
						}
					}
				}
				
				$start_date	 = strtotime(date('m/d/Y', $start_date) . " 1 day");
			}
			
		?>
		<h4>Class Schedule</h4>

		<?php 
			getClassesSchedule($mysqli);
		?>
		<h4>Book a Class Now</h4>
	
		<div id="calendar"></div>
	</div>
</main>
<footer>

</footer>
<!-- Modal Structure -->
  <div id="modal1" class="modal">
    <div class="modal-content large">
    
       <form  >
            <h3  class='center-align'>Booking</h3>
	       <div class="input-field col s12">
		       <i class="material-icons prefix white-text">list</i>
		       <select name="booking_type" id="booking_type"  required>
			       <option value="2">Private Training</option>
			       <option value="1">Class</option>

		       </select>
		       <label for="booking_type">Type</label>
	       </div>
            <div class="input-field col s12">
                <i class="material-icons prefix white-text">book</i>
                <select name="booking_class" id="booking_class"  required>
                  	<?php foreach($arr_class_name as $sid => $sname){
                  		?>
                  		<option value="<?php echo $sid; ?>"><?php echo $sname; ?></option>
                  		<?php

                  		}?>
                </select>
	           <label for="booking_class">Class</label>
              </div>
              <div class="input-field col s12">
                <i class="material-icons prefix white-text">date</i>
                <input id="booking_date" type="text" required>
                <label for="booking_date">Date</label>
              </div>
            	
            	<div class="input-field col s12">
            	<i class="material-icons prefix white-text">time</i>
                 <select name="booking_time" id="booking_time"  required>
                  	
                </select>
                <label for="booking_class">Time</label>
              </div>
    
              
            </form>
    </div>
    <div class="modal-footer">
     	<div class="input-field col s12 center-align ">
                 <button class="waves-effect waves-light red btn" id='btnMemberBooking'>Book Now</button>
      	</div>
    </div>
  </div>
          

<script src="js/jquery.js"></script>
<script src="js/materialize.min.js"></script>
<script src="js/moment.js"></script>
<script src="js/fullCalendar.js"></script>

<script>
	$(function(){

		$('.button-collapse').sideNav();
		 $('select').material_select();
		 $('.modal').modal();
		 $('#booking_date').pickadate({
			selectMonths: true,
			selectYears: 15,
			format: 'mm/dd/yyyy',
			closeOnSelect: true,
			onSet: function (ele) {
				if(ele.select){
					this.close();
					getAvailableTime();
				}
			}
		});
		$('body').on('change','#booking_class',function(){
			getAvailableTime();
		});
		$('body').on('click','#btnMemberBooking',function(){
			var class_id = $('#booking_class').val();
			var dt = $('#booking_date').val();
			var tm = $('#booking_time').val();
			var type = $('#booking_type').val();
			var remarks = '';
			var con = $(this);
			con.attr('disabled',true);
			con.html('Loading...');
			if(class_id && dt && tm){
				$.ajax({
	              url:'service/service.php',
	              type:'POST',
	              data: {functionName:'submitSchedule',dt:dt,class_id:class_id,tm:tm,remarks:remarks,type:type},
	              success: function(data){
	                  if(data){
	          
	                    Materialize.toast("Request submitted successfully. Thank you.",2000,"green lighten-2",function(){
	                    	location.reload();
		                    con.attr('disabled',false);
		                    con.html('BOOK NOW');
	                    });

	                  } else {
	                     Materialize.toast("Invalid data.",2000,"red lighten-2");
		                  con.attr('disabled',false);
		                  con.html('BOOK NOW');
	                  }
	              },
	              error:function(){
		              con.attr('disabled',false);
		              con.html('BOOK NOW');
	              }
	              });	
			} else {
				  Materialize.toast("Invalid request.",2000,"red lighten-2");
				con.attr('disabled',false);
				con.html('BOOK NOW');
			}
		});
		function getAvailableTime(){
			var dt = $('#booking_date').val();
			var class_id = $('#booking_class').val();
			var type = $('#booking_type').val();
			if(dt && class_id){
				$.ajax({
              url:'service/service.php',
              type:'POST',
              data: {functionName:'getAvailableTime',dt:dt,class_id:class_id,type:type},
              success: function(data){
                  if(data){
          
                  	$('#booking_time').html(data);
                  	 $('#booking_time').material_select();

                  } else {
                     Materialize.toast("No schedule for that date.",2000,"red lighten-2");
                     $('#booking_date').val('')
                     $('#booking_time').html('');
                     $('#booking_time').material_select();
                  }
              },
              error:function(){
                  
              }
              });
			}
		}

		var date_req = '<?php echo json_encode($arr_req);?>';
		var dreq = [];

		try{
			dreq = JSON.parse(date_req);
		}catch(e){

		}

		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek'
			},
		
			navLinks: true, // can click day/week names to navigate views
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			events: dreq,
			 eventRender: function(event, element) { 
           	 element.find('.fc-title').append("<br/>" + event.reserved + "<br/>" + event.coach_name);
        	},
        	eventConstraint: {
            start: moment().format('YYYY-MM-DD'),
            end: '2100-01-01' // hard coded goodness unfortunately
       		 },
   		  dayClick: function(date, jsEvent, view) { 
            alert('Clicked on: ' + date.getDate()+"/"+date.getMonth()+"/"+date.getFullYear());  
       	 }
		});
		/* 

{
					title: 'Boxing Class',
					start: '2017-01-12',
					reserved: 'Reserved: 6' ,
					confirmed: 'Confirmed 4'

				},
				{
					title: 'Muay Thai Class',
					start: '2017-01-17',
					reserved: 'Reserved: 8' ,
					confirmed: 'Confirmed 2'
				},
			
				{
					title: 'Wrestling Class',
					start: '2017-01-23',
					reserved: 'Reserved: 5' ,
					confirmed: 'Confirmed 6'
				},
				{
					title: 'Boxing class ',
					start: '2017-01-23',
					reserved: 'Reserved: 3' ,
					confirmed: 'Confirmed 3'
				},
				{
					title: 'Boxing Class',
					start: '2017-01-25',
					reserved: 'Reserved: 2' ,
					confirmed: 'Confirmed 3'
				},
				{
					title: 'Private Training',
					start: '2017-01-27',
					reserved: '' ,
					confirmed: ''
				}
		*/

	});
	$('body').on('click','#btnAddBooking',function(){
		$('#modal1').modal('open');
	});
</script>


<?php include_once 'includes/member/page_tail.php';  ?>
