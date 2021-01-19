<?php
	include '../ajax/ajax_connection.php';
	$fn = Input::get('functionName');
	$fn();
	function getTypes(){

		$surgery_type = new Surgery_type();
		$list = $surgery_type->get_active('surgery_types',['1','=','1']);
		$arr= [];
		if($list){

			foreach($list as $a){
				$a->created_at = date('m/d/Y',$a->created);
				$arr[] = $a;
			}

		}
		echo json_encode($arr);
	}

	function addType(){
		$form = json_decode(Input::get('form'));
		$surgery_type = new Surgery_type();
		if($form->id && is_numeric($form->id)){
			$surgery_type->update(
				[
					'name' => $form->name,
					'time_required' => $form->time_required,
				], $form->id
			);
		} else {
			if($form->name){

				$surgery_type->create(
					[
						'name' => $form->name,
						'time_required' => $form->time_required,
						'created' => time(),
						'is_active' => 1,
					]
				);
			}
		}
	}


	function getDoctors(){
		$doctor = new Med_doctor();
		$list = $doctor->get_active('med_doctors',[1,'=',1]);
		$arr = [] ;

		if($list){
			foreach($list as $d){
				$arr[] = $d;
			}
		}
		echo json_encode($arr);
	}

	function addDoctorSchedule(){
		$form = json_decode(Input::get('form'));
		$schedules = new Schedule();

		if($form->id && is_numeric($form->id)){
			$schedules->update(
				[
					'doctor_id' => $form->doctor_id,
					'branch_id' => $form->branch_id,
					'day_in_week' => $form->days,
					'time_in' => $form->time_in,
					'time_out' => $form->time_out,
					'is_active' => 1,
				] , $form->id
			);
		} else {
			//	form:{doctor_id:'',id:0,branch_id:'',days:'1',time_in:'',time_out:''},
			$schedules->create(
				[
					'doctor_id' => $form->doctor_id,
					'branch_id' => $form->branch_id,
					'day_in_week' => $form->days,
					'time_in' => $form->time_in,
					'time_out' => $form->time_out,
					'is_active' => 1,
				]
			);
		}
	}

	function getDoctorSchedules(){

		$schedule = new Schedule();
		$list = $schedule->getRecord();
		$arr= [];
		if($list){

			$arr_doctors = [];
			$arr_doc = [];
			foreach($list as $a){
				if(!in_array($a->doctor_name,$arr_doc)){
					$arr_doc[$a->doctor_id]  = $a->doctor_name;
				}
				$arr[$a->doctor_name][$a->day_in_week] = $a->time_in ."-".  $a->time_out;

			}
			$arr_final = [];
			foreach($arr_doc as $id => $doc){

				$arr_days = [];
				for($i=0;$i<=7;$i++){

					$time= isset($arr[$doc][$i]) ? $arr[$doc][$i] : 'N/A';


					$arr_days["".$i.""] =  $time;
				}
				$arr_final[] = array_merge( ['doctor_name' => $doc],$arr_days);

			}



		}
		echo json_encode($arr_final);
	}

	function addAppointment(){
		$form = json_decode(Input::get('form'));
		$appointment = new Appointment();

		$appointment->create(
				[
					'doctor_id' => $form->doctor_id,
					'branch_id' => $form->branch_id,
					'member_id' => $form->member_id,
					'desired_date' => $form->dt,
					'desired_time' => date('H:i:s',strtotime($form->desired_time)),
					'surgery_type' => $form->type_id,
					'status' => 1,
					'created' => time(),
				]
			);
		echo "Processed successfully.";

	}

	function getLatestSched(){
		$doctor_id = Input::get('doctor_id');
		$page = Input::get('page');
		$appointment = new Appointment();
		$limit = 4;
		$start = 0;
		if($page){
			$start = ($page * $limit) * -1;

		}
		$list = $appointment->getDoctorSchedule($doctor_id,$start,$limit);

		$arr = [];

		if($list){
			foreach($list as $a){
				$a->day_of_the_week = date('D',strtotime($a->desired_date));
				$arr[] = $a;
			}
		}
		echo json_encode($arr);
	}

	function getAppointments(){

		$type = Input::get('type');

		$appointment = new Appointment();

		$filter = json_decode(Input::get('filter'));

		if($type == 1){

			$list = $appointment->getAppointments($filter->doctor_id,$filter->status);
			$arr = [];

			if($list){

				foreach($list as $a){

					$a->day_of_the_week = date('D',strtotime($a->desired_date));
					$arr[] = $a;

				}

			}
			echo json_encode($arr);
		} else if ($type == 2){

			$ctr = Input::get('0');
			$dt1 =  date("m/d/Y", strtotime('monday this week'));
			$dt2 =  date("m/d/Y", strtotime('sunday this week'));

			if($ctr){
				$ctr1 = $ctr * 7;

				$dt1 = strtotime($dt1 . "$ctr1 day");
				$dt2 = strtotime(date('m/d/Y',$dt1) . "7 days -1 min");

			} else {
				$dt1 = strtotime($dt1);
				$dt2 = strtotime($dt2 . "1 day -1 min");
			}

			$dt1 =  date("Y-m-d",$dt1);
			$dt2 =  date("Y-m-d",$dt2);

			$list =  $appointment->getAppointmentsWeekly($dt1,$dt2,$filter->branch_id);
			$arr = [];
			if($list){
				$arr_doc = [];

				foreach($list as $a){
					$a->day_of_the_week = date('w',strtotime($a->desired_date));
					$arr[$a->doctor_name][$a->day_of_the_week][] = $a;
					if(!in_array($a->doctor_name,$arr_doc)){
						$arr_doc[] = $a->doctor_name;
					}
				}
				$final = [];
				$statuses = ['','Pending','Confirmed','Reconfirmed','Done','Cancelled'];
				foreach($arr_doc as $doc){
					$ind = [];
					$ind['doctor_name'] = $doc;
					for($i=1 ; $i<= 7 ;$i++){
						if(isset($arr[$doc][$i])){

							$data ="";
							$now = time();

							foreach($arr[$doc][$i] as $a){

								$dt = $a->desired_date . " " . $a->desired_time;
								$dif = $now - strtotime($dt);
								$bdg ="primary";

								if($dif <= 3600 && $dif>= -3600){
									$bdg="success";
								}

								$data .= "<div class='card border-dark mb-1'style='width:200px;font-size:12px;' >
								  <div class='card-header'>".$a->member_name . "</div>
								  <div class='card-body'>
								    ".$dt. "<br>".$a->branch_name."<br><br> <span class='badge badge-primary'>".$statuses[$a->status]."</span></p>
								  </div>
								</div>";

							}
						} else {
							$data = "";
						}
						$ind[$i] = $data;

					}
					$final[] = $ind;
				}

			}
			echo json_encode($final);
		}

	}



	function changeStatus(){
		$id= Input::get('id');
		$status = Input::get('status');
		$app = new Appointment();
		$app->update(['status' => $status],$id);
		echo 1;
	}

	function getUpcoming(){
		$appointment  = new Appointment();
		$dt1 = date('Y-m-d');
		$dt2 = date('Y-m-d',strtotime(date('Y-m-d') . "1 day"));
		$user = new User();
		$branch_id = (Input::get('branch_id')) ? Input::get('branch_id') : $user->data()->branch_id;
		$bn = new Branch($branch_id);
		$list =  $appointment->getUpcoming($dt1,$dt2,$branch_id);
		$arr = [];
		if($list){
			foreach($list as $a){
				$timenow = strtotime(date('Y-m-d H:i:s'));
				$timecur = strtotime($a->desired_date ." " .$a->desired_time);
				if($timenow - 3600 >= $timecur){
					continue;
				}
				$a->day_of_the_week = date('w',strtotime($a->desired_date));
				$arr[] = $a;
			}
		}

		echo json_encode(['list' =>$arr,'branch_name' => $bn->data()->name]);

	}
