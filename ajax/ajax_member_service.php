<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	if(function_exists($functionName)) $functionName();

	function getDetails(){
		$id = Input::get('id');

		$assess = new Assessment_list();
		$list = $assess->getDetailsAssessment($id);

		if($list){
			$arr=[];
			foreach($list as $l){
				$arr[$l->grp][] = $l;
			}

			$arr_status = ['','For evaluation','Satisfactory','Excellent'];
			foreach($arr as $a => $i){
				echo "<div class='panel panel-default'>";
				echo "<div class='panel-heading'>";
				echo "<strong>$a</strong>";
				echo "</div>";
				echo "<div class='panel-body'>";
				echo "<table class='table table-bordered'>";
				echo "<tr><th>Drills</th><th>Star</th><th>Duration</th><th>Remarks</th><th>Status</th></tr>";
				foreach($i as $item){
					$style ="style='border-top:1px solid #ccc;'";
					echo "<tr><td $style>$item->aname</td><td $style>$item->rating</td><td $style>$item->duration</td><td $style>$item->remarks</td><td $style>" .$arr_status[$item->status]. "</td></tr>";
				}
				echo "</table>";

				echo "</div>";
				echo "</div>";
			}
			echo "<button class='btn btn-primary'>Print</button>";
		}



	}

	function saveAssessmentData(){

		$data = Input::get('data');

		$member_id = Input::get('member_id');
		$coach_id = Input::get('coach_id');
		$dt = Input::get('dt');
		$disc_id = Input::get('disc_id');
		$goal = Input::get('goal');
		$session_number = Input::get('session_number');
		$recommendation = Input::get('recommendation');
		$analysis = Input::get('analysis');

		$assess = new Assessment();
		$assess->create([
			'member_id' =>  $member_id,
			'coach_id' =>  $coach_id,
			'created' =>  strtotime($dt),
			'disc_id' =>  $disc_id,
			'goals' =>  $goal,
			'recommendation' =>  $recommendation,
			'analysis' =>  $analysis,
			'company_id' =>  1,
			'session_number' =>  $session_number
		]);
		$lastid = $assess->getInsertedId();

		$data = json_decode($data);

		foreach($data as $d){
				$m = new Assessment_member();
			$m->create(
				[
					'assessment_id' => $d->id,
					'parent_id' => $lastid,
					'rating' => $d->star,
					'remarks' => $d->remarks,
					'duration' => $d->duration,
					'status' => $d->status,
					'created' => time(),
					'company_id' =>  1
				]
			);
		}

		echo "Assessment added successfully.";
	}

	function saveAssessment(){
		$member_id = Input::get('member_id');
		$member_remarks = Input::get('member_remarks');
		$ass = new Med_diagnosis();
		$now = time();
		$ass->create([
			'company_id' => 1,
			'created' => $now,
			'is_active' => 1,
			'member_id' => $member_id,
			'remarks' => $member_remarks
		]);
		echo "Information was added successfully";
	}

	function sendEmailNotificationInService(){
		$name = "Juan Dela Cruz";
		 $content = "<html><head><style> p { font-size: 18px; }</style></head><body><div style='width:100%;'><div style='width:600px;margin:0 auto;'><div style='background-image: linear-gradient(141deg,#00806c 0,#00b1b3 71%,#00a8cd 100%);position: relative;height: 80px;'><h1 style='color:white;padding-top:15px;text-align: center;'>Safehouse Fight Academy</h1></div><div><p>Hi <span style='color:red;'> $name</span>! Hope you are doing well. I'm Barbs of Safehouse Fight Academy. We are updating our members profile and notice that you've been off our mats/turf lately.</p><p>We would be happy to hear your feedback regarding your training experience and we are thrilled to have you back to see you progress and achieve your fitness goals!</p><p> All the best,<br> Barbs Ditching <br> Sales Manager <br> 09178276713 | 3512051 <br> </p><hr><div style='color:#999'> DISCLAIMER: This email may contain confidential information intended only for the use of the addressee named above. If you are not the intended recipient of this message you are hereby notified that any use, dissemination, distribution or reproduction of this message is prohibited. If you received this message in error please notify the sender and delete this message immediately. Any views expressed in this message are those of the individual sender and may not necessarily reflect the views of Safehouse Fight Academy.</div><p style='color:red'>*System Generated. Please do not reply.</p></div></div></div></body></html>";
		$email = "rjl.ceniza@gmail.com,barbsolvido@gmail.com";
		$subject = "For approval";
	/*	$res = mail($email,
			$subject,
			$content,
			"From: safehouse.manila@gmail.com" . "\r\n" . 'MIME-Version: 1.0' . "\r\n". "Content-Type: text/html; charset=utf-8",
			"-fsender@example.com"); */

	}

	function getAssesstment(){
		$med = new Med_diagnosis();
		$member_id = Input::get('member_id');
		if($member_id){
			$list = $med->get_record(1,0,500,'',$member_id,0);
			if($list){
				echo " <ul class='collection'>";
				foreach($list as $l){
					echo " <li class='collection-item'>$l->remarks</li>";
				}
				echo "</ul>";
			} else {
				echo "<div class='grey lighten-5 z-depth-1'><br><div ><h4>No record found.</h4><br></div></div>";
			}
		}
	}
	function saveCity(){
		$city = new City_mun();
		$id = Input::get('id');
		$chargeCash = Input::get('chargeCash');
		$chargeBT = Input::get('chargeBT');

		if($id && is_numeric($id) && $chargeCash && is_numeric($chargeCash) && $chargeBT && is_numeric($chargeBT)){
			$city->update(['del_charge_cash' => $chargeCash,'del_charge_bt' => $chargeBT],$id);
			echo "1";
		} else {
			echo "0";
		}
	}
	function addExpiMember(){
		$exp = new Addtl_experience();
		$user = new User();

		$e = Input::get('exp');
		$member_id = Input::get('member_id');
		if(is_numeric($e) && is_numeric($member_id) && $e && $member_id){
			$exp->create(array(
				'created' => time(),
				'member_id' => $member_id,
				'exp' => $e,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
			echo "Updated Successfully.";
		} else {
			echo "Invalid data";
		}

	}
	function getExpiList(){
		$att = new Service_attendance();
		$sum = $att->expiSummary();


		$memberNames = [];
		$memberExpi = [];
		foreach($sum as $s){

			if($s->is_con  == 1){
				$expiGain = $s->cnt * 15;
			} else {
				$expiGain = $s->cnt * 10;
			}
			if(isset($memberExpi[$s->member_id])){
				$memberExpi[$s->member_id]  += $expiGain;
			} else {
				$memberExpi[$s->member_id]  = $expiGain;
			}
			$memberNames[$s->member_id]  = $s->lastname;
		}

		$exp = new Experience_table();
		$expi = $exp->get_active('experience_table',array('1' ,'=','1'));
		$arr_expi_table = [];
		if($expi){
			foreach($expi as $ex){
				$arr_expi_table[$ex->name] = $ex->points_needed;
			}
			asort($arr_expi_table);
		}
		$addtl_arr = [];
		$addtl = $att->addtlExpi();
		if($addtl){
			foreach($addtl as $add){
				$addtl_arr[$add->member_id] = $add->exp;
			}
		}
		$newmemberexpi = [];
		foreach($memberExpi as $mid => $mval){

			$addtl_expi = 0;
			if(isset($addtl_arr[$mid])){
				$addtl_expi =  $addtl_arr[$mid];
			}
			$mval += $addtl_expi;
			$newmemberexpi[$mid] = $mval;
		}
		$memberExpi = $newmemberexpi;
		arsort($memberExpi);


		/*

		if($result->num_rows > 0){
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$exp_value	 = $row['exp'];
				$total_expi_from_attendance	+= $exp_value;
				$dt  = date('m/d/Y',$row['created']);
				$graph_arr[] = ['y' => $dt, 'a' => $total_expi_from_attendance];
			}
		}
		*/

		echo "<table class='table'>";
		echo "<tr><th>Member</th><th>Experience</th><th>Level</th></tr>";
		foreach($memberExpi as $id => $v){
			$prev = '';
			$curlevel = "";

			foreach($arr_expi_table as $indk => $inde){
				if($inde > $v){
					$curlevel = $prev;
					break;
				}
				$prev = $indk;
			}
			echo "<tr><td style='border-top: 1px solid #ccc;'>".$memberNames[$id]."</td><td  style='border-top: 1px solid #ccc;'>$v</td><td  style='border-top: 1px solid #ccc;'>$curlevel</td></tr>";
		}
		echo "</table>";

	}

	function getCerf(){
		//Carregar imagem
		header('Content-Disposition: Attachment;filename=image.png');
		header('Content-type: image/png');
		$rImg = ImageCreateFromJPEG("../css/img/cerf.jpg");

		//Definir cor
		$cor = imagecolorallocate($rImg, 0, 0, 0);

		//Escrever nome
		//imagestring($rImg,1000,500,500,urldecode("JAYSON TEMPORAS"),$cor);

		//Header e output
		//header('Content-type: image/jpeg');
		//imagejpeg($rImg,NULL,100);

		// Set the enviroment variable for GD


		// Name the font to be used (note the lack of the .ttf extension)


		// Create some colors
		$white = imagecolorallocate($rImg, 255, 255, 255);
		$grey = imagecolorallocate($rImg, 128, 128, 128);
		$black = imagecolorallocate($rImg, 0, 0, 0);


		// The text to draw
		$name = 'JAYSON TEMPORAS';
		$award_details = 'Award Details asd asdas das das';
		$extra_details = 'Boxing a';
		$date = 'Jan 01, 2017';
		// Replace path by your own font path
		$font = '../css/fonts/arial.ttf';
		// Get Bounding Box Size
		$name_box = imagettfbbox(25,0,$font,$name);
		$name_width = $name_box[2]-$name_box[0];
		$name_left = ((792 - $name_width) / 2) + 500;


		$award_box = imagettfbbox(15,0,$font,$award_details);
		$award_width = $award_box[2]-$award_box[0];
		$award_left = ((792 - $award_width) / 2) + 500;

		$extra_box = imagettfbbox(15,0,$font,$extra_details);
		$extra_width = $extra_box[2]-$extra_box[0];
		$extra_left = ((237 - $extra_width) / 2) + 830;

		// Add some shadow to the text
		imagettftext($rImg, 25, 0, $name_left, 650, $grey, $font, $name);
		imagettftext($rImg, 25, 0, $name_left, 650, $black, $font, $name);

		imagettftext($rImg, 15, 0, $award_left, 810, $grey, $font, $award_details );
		imagettftext($rImg, 15, 0, $award_left, 810, $black, $font, $award_details);

		imagettftext($rImg, 15, 0, $extra_left, 905, $grey, $font, $extra_details );
		imagettftext($rImg, 15, 0, $extra_left, 905, $black, $font, $extra_details );

		imagettftext($rImg, 15, 0, 580,990, $grey, $font, $date);
		imagettftext($rImg, 15, 0, 580, 990, $black, $font, $date);


		// Using imagepng() results in clearer text compared with imagejpeg()
		imagepng($rImg);

		exit;
	}
		function printCertificate(){

			require('../libs/fpdf17/fpdf.php');




				$pdf = new Cerf();



				$pdf->SetFont('Arial','',8);
				//$pdf->AliasNbPages();
				$pdf->AddPage('L');

				$pdf->Rect(20, 20, 255, 160, 'D');
				$pdf->SetFont('Times','',22);
				$pdf->Ln(14);
				//$pdf->SetFont('Arial','',10);
				$pdf->Cell(280,0,'THIS AWARD CERTIFICATES THAT ',0,0,'C',false);
				$pdf->Line(30, 85, 266, 85);
				$pdf->Ln(15);
				$pdf->SetFont('Arial','',16);
				$pdf->Cell(280,0,'Jayson Temporas',0,0,'C',false);
				$pdf->Ln(5);
				$pdf->SetFont('Times','',20);
				$pdf->Ln(15);
				$pdf->Cell(280,0,'HAS SUCCESSFULLY COMPLETED ',0,0,'C',false);
				$pdf->Line(30, 120, 266, 120);
				$pdf->SetFont('Arial','',14);
				$pdf->Ln(15);
				$pdf->Cell(280,0,'Boxing Session',0,0,'C',false);
				$pdf->Line(100, 140, 196, 140);
				$pdf->SetFont('Arial','',14);
				$pdf->Ln(30);
				$pdf->Cell(280,0,'Jan 1, 2017',0,0,'C',false);
				$pdf->Output();


	}
	function loginMemberService(){
		$user = new User();
		$username = Input::get('username');
		$is_sub = Input::get('is_sub');
		$res = $user->checkCredentialByUsername($username);
		if(!$res){
			die("1");
		} else {
			$member_id = $res->member_id;
			$company_id = $res->company_id;
			if(!$member_id){
				die("2");
			} else {

				// check service enrolled
				$service = new Service();

				if($is_sub == 1) {
					$services = $service->getSubsciption($company_id,$member_id);
					if($services) {
						$has_valid_subs = false;
						$valid_item_id = 0;
						foreach($services as $s){
							if(strpos(strtolower($s->item_code),'annual membership') !== false){

							} else {
								$dayremaining = getDays(date('m/d/Y',$s->end_date));
								if($dayremaining > 0){
									$has_valid_subs = true;
									$valid_item_id = $s->item_id;
								}
							}

						}
						if($has_valid_subs){
							// insert into service attendance
							$service_attendance = new Service_attendance();
							$check_if_signed_in = $service_attendance->alreadySignedIn($member_id);
							if($check_if_signed_in->cnt > 0){
								die("6");
							}
							$now = time();
							$service_attendance->create(array(
								'member_id' => $member_id,
								'company_id' => $company_id,
								'time_in' => $now,
								'is_active' => 1,
								'item_id' => $valid_item_id
							));
							die("5");
						} else {
							die("4");
						}
					} else {
						die("3");
					}
				} else {

					// get consumables
					$services = $service->getServices($company_id,$member_id);
					if($services) {
						echo json_encode(['data' => $services,'member_id' => $member_id]);
					} else {
						die("3");
					}


				}


			}
		}

	}
	function signOut(){
		$member_id = Input::get('member_id');
		$id = Input::get('id');
		$remarks = Input::get('remark');
		$arr_con = Input::get('arr_con');
		$coach_id = Input::get('coach_id');

	//	$user = new User();
		$c = true;// create query to check password if needed
		if($c){
			// update sign out
			$service =new Service_attendance();
			$now = time();
			$service->update(array(
				"remarks" => $remarks,
				"time_out" => $now,
				"coach_id" => $coach_id
			),$id);

			$arr_con = json_decode($arr_con,true);
			if($arr_con){
				$offered_service_history = new Offered_service_history();
				$now = time();
				foreach($arr_con as $s){

					$offered_service_history->create(
						[
							'member_id' => $member_id,
							'company_id' => 1,
							'is_active' => 1,
							'service_id' => $s,
							'created' => $now,
							'att_id' => $id

						]
					);
				}
			}
			echo "0";
		} else {
			echo "1";
		}

	}
	function getMemberBookingRequest(){
		$msr = new Member_service_request();
		$status = Input::get('status') ? Input::get('status') : 1;
		$data = $msr->getRequest($status);
		if($data){

			echo "<table class='table' id='tblForApproval'>";
			echo "<thead>";
			echo "<tr><th>Member</th><th>Service</th><th>Date and Time</th><th>Date requested</th><th>Remarks</th><th></th></tr>";
			echo "</thead>";
			echo "<tbody>";

			foreach($data as $d){
				$sched = date('m/d/Y' ,$d->schedule_date) . " " . $d->time_of_the_day;
				$dt = date('m/d/Y H:i:s A',$d->created);
				$rem = ($d->remarks) ? $d->remarks : "<span class='fa fa-ban'></span>";
				$buttonOk = "";
				$buttonReject = "";
				if($status == 1){
					$buttonOk = "<button data-id='".$d->id."' class='btn btn-default btn-sm btnOk'><span class='glyphicon glyphicon-ok'></span></button>";
					$buttonReject = "<button  data-id='".$d->id."' class='btn btn-danger btn-sm btnReject'><span class='glyphicon glyphicon-remove'></span></button>";

				}
				echo "<tr><td><span class='span-block'><strong>Name:</strong> $d->name</span><span class='span-block'><strong>Contact Number:</strong> $d->contact_number</span><span class='span-block'><strong>Email:</strong> $d->email</span></td><td class='text-danger'>$d->class_name</td><td>$sched</td><td>$dt</td><td>$rem</td><td>$buttonOk $buttonReject</td></tr>";
			}

			echo "</tbody>";
			echo "</table>";

		} else {
			echo "No record found.";
		}
	}

	function getAssessmentForm(){
		$id = Input::get('id');
		$assessment = new Assessment_list();
		$list = $assessment->getAssessment($id);
		if(!$list){
			echo "No form yet";
		} else {
			$arr = [];
			foreach($list as $l){
				$arr[$l->grp][] = $l;
			}

			foreach($arr as $grp => $item){
				echo "<div class='panel panel-default'>";
				echo "<div class='panel-heading'>";
				echo "$grp";
				echo "</div>";
				echo "<div class='panel-body'>";
				echo "<table class='table tbl_assess'>";
				foreach($item as $i){
					echo "<tr data-id='$i->id'>";
					echo "<td style='width:120px;'>$i->name</td>";
					echo "<td><input type='text' placeholder='Star'></td>";
					echo "<td><input type='text' placeholder='Duration'></td>";
					echo "<td><input type='text' placeholder='Remarks'></td>";
					echo "<td><select class='form-control'><option value='1'>For improvement</option><option value='2'>Satisfactory</option><option value='1'>Excellent</option></select></td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "</div>";
				echo "</div>";
			}
		}
	}
	function serviceMemberReport(){
		$msr = new Member_service_request();
		$search = Input::get('search');
		$service_id = Input::get('service_id');
		$item_id = Input::get('item_id');
		$list = $msr->getReport($search,$service_id,$item_id);
		if($list){
			echo "<table class='table'>";
			echo "<thead><tr><th>Member</th><th>Service</th><th></th></tr></thead>";
			echo "<tbody>";
			foreach($list as $l){
				echo "<tr><td class='text-danger' style='border-top: 1px solid #ccc;'><i class='fa fa-user'></i> $l->member_name</td><td style='border-top: 1px solid #ccc;'>$l->name</td><td style='border-top: 1px solid #ccc;'></td></tr>";
			}
			echo "</tbody>";
			echo "</table>";
		}
	}
	function serviceRequestChangeStatus(){
		$status = Input::get('status');
		$id = Input::get('id');
		if($id && is_numeric($id) && $status && is_numeric($status)){
			$msr = new Member_service_request();
			$msr->update(['status' => $status],$id);
			echo "Updated successfully.";
		} else {
			echo "Update failed";
		}

	}
	function resetPassword(){
		$id = Input::get('id');
		$user_cls= new User();
		$pw = md5('password');
		$user_cls->update(['password' => $pw],$id);
		echo "Updated successfully.";
	}
	function deductConsumables(){
		$member_id = Input::get('member_id');
		$ids = Input::get('to_deduct');
		$ss = json_decode($ids,true);
		if($ss){
			$item_id = 0;
			foreach($ss as $s){
				if($s){
					$service = new Service($s);
					$item_id = $service->data()->item_id;
					$remaining = $service->data()->consumable_qty -1;
					$end_date = 0;
					if($remaining == 0){
						$end_date = time();
					}
					$service->update(["consumable_qty" => $remaining,'completed_date' => $end_date],$s);
				}
			}
			$now = time();
			$service_attendance = new Service_attendance();
			$service_attendance->create(array(
				'member_id' => $member_id,
				'company_id' => 1,
				'time_in' => $now,
				'is_active' => 1,
				'is_con' => 1,
				'item_id' => $item_id
			));
			echo "2";
		} else {
			echo "1";
		}

	}

	function saveMeasurements(){
		$arr = json_decode(Input::get('form'),true);
		$final = [];
		foreach($arr as $a){
			if(!$a['value'] || !is_numeric($a['value']) && $a['name'] != 'dt_date'){
				$v = 0;
			} else {
				$v = $a['value'];
			}
			$final[$a['name']] = $v;
		}
		$member_id = $final['member_id'];
		$dt_date = $final['dt_date'];
		if($member_id && $dt_date){
			$height = ($final['height_feet'] * 12) + $final['height_inches'];
			$dt_date = strtotime($dt_date);
			$bm = new Body_measurement();
			$bm->create(array(
				'height' => $height,
				'weight' => $final['weight'],
				'chest' => $final['txt_chest'],
				'l_upperarm' => $final['txt_l_upperarm'],
				'r_upperarm' => $final['txt_r_upperarm'],
				'waist' => $final['txt_waist'],
				'abdomen' => $final['txt_abdomen'],
				'hips' => $final['txt_hips'],
				'l_mid_thigh' => $final['txt_l_mid_thigh'],
				'r_mid_thigh' => $final['txt_r_mid_thigh'],
				'l_calf' => $final['txt_l_calf'],
				'r_calf' => $final['txt_r_calf'],
				'member_id' => $member_id,
				'created' =>$dt_date,
				'is_active' =>1,
				'company_id' => 1
			));

			echo "Record inserted successfully.";
		} else {
			echo 1;
		}

	}


	function updateSubscription(){
	$id = Input::get('id');
	$date_from = Input::get('date_from');
	$date_to = Input::get('date_to');

	$service = new Service();
	$service->update(array('start_date' => strtotime($date_from), 'end_date' => strtotime($date_to)),$id);
	echo "Update complete";

}
	function getSubscription(){
		$user = new User();
		$subs = new Service();

		$services = $subs->getSubsciption($user->data()->company_id);
		if($services){
			?>
			<div id="no-more-tables">
				<table class="table" id='tblSubscription'>
					<thead>
					<tr>
						<th>Subscription ID</th>
						<th>Name</th>
						<th>Item code</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th></th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($services as $s){
							?>
							<tr>
								<td data-title='Ref Id'><?php echo $s->id; ?></td>
								<td data-title='Name'><?php echo ucfirst($s->lastname) . ", " . ucfirst($s->firstname) . " " . ucfirst($s->middlename) ; ?></td>
								<td data-title='Item code'><?php echo $s->item_code; ?></td>
								<td data-title='Start Date'><?php echo date('m/d/Y',$s->start_date); ?></td>
								<td data-title='End Date'><?php echo date('m/d/Y',$s->end_date); ?></td>
								<td data-title='Status' class='text-danger'><?php

										$dayremaining = getDays(date('m/d/Y',$s->end_date));
										//	$dayremaining += 1;
										if($dayremaining > 0){
											if($dayremaining > 1){
												$dlabel = "days";
											} else {
												$dlabel = "day";
											}
											echo $dayremaining . " $dlabel remaining";
										} else {
											echo "Subscription Expired";
										}
									?></td>
								<td>
									<button data-id='<?php echo $s->id; ?>'
									        data-from='<?php echo date('m/d/Y',$s->start_date) ?>'
									        data-to='<?php echo date('m/d/Y',$s->end_date) ?>'
									        class='btn btn-default btnUpdate'>Update</button>

									<button  data-id='<?php echo $s->id; ?>' class='btn btn-default btnDelete'>Delete</button></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-info">There is no current item at the moment...</div>
			<?php
		}
	}

	function deleteSubscription(){
		$id = Input::get('id');
		$service = new Service();
		$service->deleteSubs($id);
		echo "Deleted successfully.";
	}

	function updateConsumable(){
		$id = Input::get('id');
		$qty = Input::get('qty');
		$date = Input::get('date');


		$service = new Service();
		$service->update(array('consumable_qty' => $qty,'end_date'=> strtotime($date)),$id);
		echo "Update complete";

	}

	function getConsumables(){
		$user = new User();
		$subs = new Service();

		$services = $subs->getServices($user->data()->company_id);
		if($services){
			?>
			<div id="no-more-tables">
				<table class="table" id='tblSubscription'>
					<thead>
					<tr>
						<th>Subscription ID</th>
						<th>Name</th>
						<th>Item code</th>
						<th>Consumable Qty</th>
						<th>Expiration date</th>
						<th></th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
						foreach($services as $s){
							$now = strtotime(date('m/d/Y'));
							$lbl = "";
							if($now > $s->end_date){
								$lbl = "<span class='label label-danger'>Subscription Expired</span>";
							}
							?>
							<tr>
								<td data-title='Ref Id'><?php echo $s->id; ?></td>
								<td data-title='Name'><?php echo ucfirst($s->lastname) ; ?></td>
								<td data-title='Item code'><?php echo $s->item_code; ?></td>
								<td data-title='Qty'><?php echo formatQuantity($s->consumable_qty,true); ?></td>
								<td data-title='Expiration'><?php echo date('m/d/Y',$s->end_date); ?></td>
								<td><?php echo $lbl; ?></td>
								<td>
									<button data-date='<?php echo date('m/d/Y',$s->end_date); ?>'
									        data-id='<?php echo $s->id; ?>'
									        data-qty='<?php echo formatQuantity($s->consumable_qty,true) ?>'
									        class='btn btn-default btnUpdate'>
										Update
									</button>

									<button   data-id='<?php echo $s->id; ?>' class='btn btn-default btnDelete'>Delete</button></td>
							</tr>
							<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-info">There is no current item at the moment...</div>
			<?php
		}
	}

	function getConsumablesV2(){
		$user = new User();
		$subs = new Service();

		$services = $subs->getServices($user->data()->company_id);
		$arr = [];
		if($services){

				foreach($services as $s){
						$member_name = $s->lastname;
						$item_code = $s->item_code;
						$total = $s->con_qty - $s->consumable_qty;
						$tocheck = [4,14,19,24,29];
						$tocheckass = [1,5,15,20,25,30];
						$lbl = "";
						$toAssess = 0;
						if(in_array($total,$tocheck)){
							$lbl = "Assessment schedule in the next session.";
						}
						if(in_array($total,$tocheckass)){
							$toAssess = 1;
						}

						$arr[] = [ 'url' => 'assess_member.php?member_id='.$s->member_id, 'member_name' => $member_name,'total' => $total,'item_code' => $item_code,'lbl' => $lbl, 'toAssess' => $toAssess ];
				}

		}
		echo json_encode($arr);
	}


	function deleteConsumable(){
		$id = Input::get('id');
		$service = new Service();
		$service->deleteSubs($id);
		echo "Deleted successfully.";
	}

	function approveMemberCredit(){
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		$member_credit = new Member_credit();
		if(is_numeric($id) && $id){
			$member_credit->update(['status' => 1],$id);
			echo "Approved succesfully";
		} else {
			echo "Failed to process you request. Please try again.";
		}
	}

	function declineMemberCredit(){
		$id = Input::get('id');
		$member_credit_id = Encryption::encrypt_decrypt('decrypt',$id);

		$crud = DB::getInstance();
		if(is_numeric($member_credit_id) && $member_credit_id){
			$member_credit = new Member_credit($member_credit_id);
			$payment = new Payment();
			$payment_id = $member_credit->data()->payment_id;
			$sales = new Sales();
			$st = $sales->getsinglesale($payment_id);
			$payment_dr = $st->dr;
			$payment_inv = $st->invoice;
			$terminal_id = $st->terminal_id;
			//delete credit
			$credit_card = $payment->getCreditCard($payment_id);
			$user = new User();

			if($credit_card){
				foreach($credit_card as $c){

					$amount = $c->amount;
					$user = new User();
					$terminal = new Terminal();
					$terminal_mon = new Terminal_mon();
					$total_amount = $amount;
					$prevamount = $terminal->getTAmount($terminal_id,2);
					$prevamount = ($prevamount->t_amount_cc) ? $prevamount->t_amount_cc:0;
					$to_amount =  $prevamount - $total_amount;
					$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
					$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';

					$terminal->update(array(
						't_amount_cc' => $to_amount
					),$terminal_id);
					$terminal_mon->create( array(
						'terminal_id' => $terminal_id,
						'user_id' => $user->data()->id,
						'from_amount' =>$prevamount,
						'amount' =>$total_amount,
						'to_amount'=>$to_amount,
						'status' => 2,
						'remarks' => "POS $pinv $pdr",
						'is_active' => 1,
						'company_id' => $user->data()->company_id,
						'p_type' => 2,
						'created' => time()
					));
				}
				$crud->delete('credit_card',array('payment_id' ,'=' ,$payment_id));
			}



			// delete cash
			$cash = $payment->getCash($payment_id);
			if($cash){
				foreach($cash as $c){

					$amount = $c->amount;
					$terminal = new Terminal();
					$terminal_mon = new Terminal_mon();
					$total_amount = $amount;
					$prevamount = $terminal->getTAmount($terminal_id,1);
					$prevamount = ($prevamount->t_amount) ? $prevamount->t_amount:0;
					$to_amount =  $prevamount-$total_amount;

					$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
					$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';

					$terminal->update(array(
						't_amount' => $to_amount
					),$terminal_id);
					$terminal_mon->create( array(
						'terminal_id' => $terminal_id,
						'user_id' => $user->data()->id,
						'from_amount' =>$prevamount,
						'amount' =>$total_amount,
						'to_amount'=>$to_amount,
						'status' => 2,
						'remarks' => "Decline Payment $pinv $pdr",
						'is_active' => 1,
						'company_id' => $user->data()->company_id,
						'p_type' => 1,
						'created' => time()
					));
				}

				$crud->delete('cash',array('payment_id' ,'=' ,$payment_id));

			}

			// delete bt
			$bt = $payment->getBT($payment_id);
			if($bt){
				foreach($bt as $c){



					$amount = $c->amount;
					$user = new User();
					$terminal = new Terminal();
					$terminal_mon = new Terminal_mon();
					$total_amount = $amount;
					$prevamount = $terminal->getTAmount($terminal_id,4);
					$prevamount = ($prevamount->t_amount_bt) ? $prevamount->t_amount_bt:0;
					$to_amount =  $prevamount-$total_amount;
					$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
					$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';
					$terminal->update(array(
						't_amount_bt' => $to_amount
					),$terminal_id);
					$terminal_mon->create( array(
						'terminal_id' => $terminal_id,
						'user_id' => $user->data()->id,
						'from_amount' =>$prevamount,
						'amount' =>$total_amount,
						'to_amount'=>$to_amount,
						'status' => 2,
						'remarks' => "POS $pinv $pdr",
						'is_active' => 1,
						'company_id' => $user->data()->company_id,
						'p_type' => 4,
						'created' => time()
					));
				}
				$crud->delete('bank_transfer',array('payment_id' ,'=' ,$payment_id));
			}

			// delete cheque
			$cheque = $payment->getCheque($payment_id);
			if($cheque){
				foreach($cheque as $c){

					$amount = $c->amount;
					$terminal_id = Input::get('terminal_id');
					$user = new User();
					$terminal = new Terminal();
					$terminal_mon = new Terminal_mon();
					$total_amount = $amount;
					$prevamount = $terminal->getTAmount($terminal_id,3);
					$prevamount = ($prevamount->t_amount_ch) ? $prevamount->t_amount_ch:0;
					$to_amount =  $prevamount-$total_amount;
					$pdr = ($payment_dr) ? 'Dr: '.$payment_dr:'';
					$pinv = ($payment_inv) ? 'Inv: '.$payment_inv:'';

					$terminal->update(array(
						't_amount_ch' => $to_amount
					),$terminal_id);
					$terminal_mon->create( array(
						'terminal_id' => $terminal_id,
						'user_id' => $user->data()->id,
						'from_amount' =>$prevamount,
						'amount' =>$total_amount,
						'to_amount'=>$to_amount,
						'status' => 2,
						'remarks' => "POS $pinv $pdr",
						'is_active' => 1,
						'company_id' => $user->data()->company_id,
						'p_type' => 3,
						'created' => time()
					));
				}

				$crud->delete('cheque',array('payment_id' ,'=' ,$payment_id));

			}

			// delete deduction
			$deductions = $payment->getDeduction($payment_id);
			if($deductions){
				$crud->delete('deductions',array('payment_id' ,'=' ,$payment_id));
			}
			// delete consumable
			$consumables = $payment->getConsumable($payment_id);

			if($consumables){
				foreach($consumables as $c){
					$paymentConsumable = new Payment_consumable($c->id);
					$payment_amount = $paymentConsumable->data()->amount;
					$member_id = $paymentConsumable->data()->member_id;
					$concls = new Consumable();
					$res = $concls->updateConsumable($payment_amount,$member_id);
				}
				$crud->delete('payment_consumable',array('payment_id' ,'=' ,$payment_id));
			}

			// delete consumable freebies
			$consumableFreebies = $payment->getConsumableFreebies($payment_id);

			if($consumableFreebies){
				foreach($consumableFreebies as $c){
					$id = $c->id;
					$paymentConsumable = new Payment_consumable_freebies($id);
					$payment_amount = $paymentConsumable->data()->amount;
					$member_id = $paymentConsumable->data()->member_id;
					$concls = new Consumable_freebies();
					$res = $concls->updateConsumable($payment_amount,$member_id);
				}
				$crud->delete('payment_consumable_freebies',array('payment_id' ,'=' ,$payment_id));
			}

			$member_credit->update(['amount_paid' => 0,'json_payment'=> '','status' => 0,'ret_msg'=>'Returned'],$member_credit_id);
			echo "Request returned completed.";

		}
	}

	function getReferrals(){
		$ref = new Referral();
		$list = $ref->get_all();

		$arr = [];

		if($list){
			foreach($list as $a){

                $a->created = date('m/d/Y',$a->created_at);

                $a->old_expiration = date('m/d/Y',$a->old_expiration);
                $a->new_expiration = date('m/d/Y',$a->new_expiration);

				$arr[] = $a;
			}
		}

		echo json_encode($arr);


	}

	function getBooking(){
		$cur = Input::get('cur_week');
		$now = time();
		$nameOfDay = date('l',$now);
		$last_day = strtotime("next sunday");
		if(strtolower($nameOfDay) == 'sunday'){
			$last_day = strtotime(date('m/d/Y'));
		}
		$arr_days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

		if($cur){
			$cur = 7 * $cur;
			$last_day = strtotime(date('m/d/Y',$last_day) . "$cur days");
		}
		$first_day = strtotime(date('m/d/Y',$last_day) . "-6 days");

		$book = new Member_service_request();

		$list = $book->getBookings($first_day,$last_day);
		$arr = [];

		if($list){
			foreach($list as $l){
				$day = date('l',$l->schedule_date);
				$l->readable_date = date('m/d/Y',$l->schedule_date);

				$arr[$day][]= $l;
			}
		}

		$ret_arr = [];
		foreach($arr_days as $ad){
			$daylbl = "No booking";
				if(isset($arr[$ad]) && count($arr[$ad]) > 0){
					$daylbl= "";
					usort($arr[$ad],function($a,$b){
						$a_ex = explode("-",$a->time_of_the_day);
						$b_ex = explode("-",$b->time_of_the_day);

						$t1 = strtotime($a_ex[0]);
						$t2 = strtotime($b_ex[0]);
						return $t1 - $t2;
					});
					foreach($arr[$ad] as $f){
						$ex = explode('-',$f->time_of_the_day);
						$hr_from = date('h:i A',strtotime($ex[0]));
						$hr_to = date('h:i A',strtotime($ex[1]));
						$daylbl .= "<p style='border-bottom: 1px solid #ccc;'><strong>$f->member_name</strong> - $f->class_name <br>$f->readable_date $hr_from - $hr_to <br><span class='text-danger'>$f->coach_name</span></p>";
					}
				}
			$ret_arr[strtolower($ad)] = $daylbl;
		}

		$dt_range = date('m/d/Y',$first_day) . "-" . date('m/d/Y',$last_day);

		echo json_encode(['bookings' => $ret_arr,'range'=> $dt_range]);


	}

	function changeStatusSecondary(){

		$id = Input::get('id');
		$status =Input::get('status');
		$is_cancel =Input::get('is_cancel');
		$is_hold =Input::get('is_hold');
		$remarks =Input::get('remarks');
		$cancel_msg =Input::get('msg');
		$hold_msg =Input::get('hold_msg');

		$remarks = ($remarks) ? $remarks :'';
		$cancel_msg = ($cancel_msg) ? $cancel_msg :'' ;

		$last_status = $status;
		$item_service = new Item_service_request($id);
		$nextStatus =  $status + 1;
		if($is_cancel) $nextStatus = 6;
		if($is_hold) $nextStatus = 5;

		if($last_status == 5){
			$nextStatus = $item_service->data()->last_status;
		}

		$user = new User();
		$log = new Service_date_log();
		$log->create(
			[
				'dt' => time(),
				'service_id' => $id,
				'status' => $nextStatus,
				'remarks' => $remarks,
				'user_id' => $user->data()->id,
			]
		);
		$item_service->update(['second_status'=> $nextStatus,'last_status' => $last_status,'cancel_remarks' => $cancel_msg,'hold_remarks'=>$hold_msg],$id);


	}

	function getServiceRequest(){

		$user = new User();
		$item_service = new Item_service_request();
		$service_list = $item_service->getRequest($user->data()->company_id, 1);

		$for_pullout = [];
		$for_homeservice = [];
		$walk_in_customer = [];
		if($service_list) {
			foreach($service_list as $it) {
				if($it->request_type == 2) {
					$for_pullout[] = $it;
				} else if($it->request_type == 3) {
					$for_homeservice[] = $it;
				} else {
					$walk_in_customer[] = $it;
				}
			}
			$nopending = "";
		} else {
			$nopending = "<h4>No pending item.</h4>";
		}


		$barcodeClass = new Barcode();
		$barcode_format = $barcodeClass->getFormat($user->data()->company_id, "SERVICE");

		$order_styles = $barcode_format->styling;
		$status_arr = ['', // 0
			'Repairing', // 1
			'Good',// 2
			'Repair with warranty',  // 3
			'Repair without warranty', // 4
			'Replacement(Junk)', // 5
			'Replacement(Surplus)', // 6
			'Change Item(Junk)',// 7
			'Change Item(Surplus)', // 8
			'Cancelled', // 9
			'Scheduled', // 10
			'Received', // 11
			'Repairing', // 12
			'Installing', // 13
		];

		//
		//                       _oo0oo_
		//                      o8888888o
		//                      88" . "88
		//                      (| -_- |)
		//                      0\  =  /0
		//                    ___/`---'\___
		//                  .' \\|     |// '.
		//                 / \\|||  :  |||// \
		//                / _||||| -:- |||||- \
		//               |   | \\\  -  /// |   |
		//               | \_|  ''\---/''  |_/ |
		//               \  .-\__  '-'  ___/-. /
		//             ___'. .'  /--.--\  `. .'___
		//          ."" '<  `.___\_<|>_/___.' >' "".
		//         | | :  `- \`.;`\ _ /`;.`/ - ` : | |
		//         \  \ `_.   \_ __\ /__ _/   .-` /  /
		//     =====`-.____`.___ \_____/___.-`___.-'=====
		//                       `=---='
		//


		$secondary = [
			'Service Report Validation Schedule',
			'SO Creation And Dispatching',
			'For Reporting',
			'CCD Verification',
			'Close',
			'Hold',
			'Cancelled'
		];


		$is_aquabest = Configuration::isAquabest();
		?>
		<div class="">

			<?php
				if($service_list) {
					$techcls = new Technician();
					?>
					<div class="btn-group" role="group" aria-label="...">
						<button id='nav_walkin' type="button" class="btn btn-default">Walk In</button>
						<?php if(!Configuration::thisCompany('cebuhiq')){
							?>
							<button id='nav_pullout' type="button" class="btn btn-default">Pullout</button>
							<?php
						} ?>

						<button id='nav_homeservice' type="button" class="btn btn-default">On site service</button>
					</div>
					<div id="con_walkin" style='display:block;padding-top:15px;'>
						<h4>Walk In</h4>
						<?php if($walk_in_customer) {
							?>
							<div id="no-more-tables">
								<table class='table' id='tblForApproval'>
									<thead>
									<tr>
										<th>Details</th>
										<th>Member</th>
										<?php if($is_aquabest){ ?>
											<th>Date Log</th>
										<?php } ?>
										<th>Date Created</th>
										<th>Technician</th>
										<th></th>
									</tr>
									</thead>
									<tbody>
									<?php

										foreach($walk_in_customer as $item) {

											if($item->member_id) {
												$mem = escape($item->mln . ", " . $item->mfn . " " . $item->mmn);
											} else {
												$mem = 'Not available';
											}

											$techids = $item->technician_id;
											$btn_assign_tech = "";
											if($user->hasPermission('ass_tech')){
												$btn_assign_tech = "<button class='btn btn-default btn-sm btnAssignTech' data-id='$item->id'><i class='fa fa-pencil'></i> Assign </button>";
											}
											$alltechnician = "<p class='text-danger'>No technician assign. $btn_assign_tech </p>";
											if($techids) {
												$listech = $techcls->getTech($techids);
												if($listech) {
													$alltechnician = "";
													$arr_tech = [];
													foreach($listech as $l) {
														$arr_tech[] =['text' => $l->name,'id' => $l->id];
														$alltechnician .= "<p class='text-danger'><i class='fa fa-user'></i> $l->name</p>";
													}
													if($user->hasPermission('ass_tech')){
														$editTech = "<button data-tech='".json_encode($arr_tech)."' data-id='$item->id' class='btn btn-default btn-sm btnUpdateTech'><i class='fa fa-pencil'></i> Update </button>";
														$alltechnician .= $editTech;
													}
												}
											}
											?>
											<tr>
												<td data-title="Id">
													<strong>ID:</strong> <?php echo escape($item->id); ?><br>
													<strong>User: </strong><?php echo escape($item->lastname . ", " . $item->firstname . " " . $item->middlename); ?><br>
													<strong>Branch: </strong>

													<?php
														if($item->branch_name){
															echo escape($item->branch_name);
														} else {
															if($user->hasPermission('ass_branch')){
																echo "<button data-id='$item->id' class='btn btn-default btn-sm btnAddBranch'><i class='fa fa-pencil'></i> Assign </button>";
															} else {
																echo "None";
															}

														}


													?>
													<?php if(Configuration::thisCompany('cebuhiq')){
															?>
														<br><strong>RF Id: </strong><?php echo ($item->rf_id) ? ($item->rf_id) : 'NA'; ?>
														<br><strong>Service Ref Number: </strong><?php echo ($item->backload_ref_number) ? ($item->backload_ref_number) : 'NA'; ?>
														<?php
													}?>
													<?php if(Configuration::thisCompany('avision')){
															?>
														<br><strong>Client PO: </strong><?php echo ($item->client_po) ? ($item->client_po) : 'NA'; ?>
														<?php
													}?>



												</td>

												<td data-title="Member">
													<?php echo $mem; ?>
													<small class='text-danger span-block'>
														<?php
															$allstatus = $item_service->getStatuses($item->id);
															$lblstats = "";

															if(count($allstatus)) {
																foreach($allstatus as $ind_stat) {
																	$lblstats .= $status_arr[$ind_stat->status] . ", ";
																}
																echo $lblstats = rtrim($lblstats, ", ");
															}

														?>

													</small>
													<small class='span-block'>
														<?php echo ($item->service_type_name) ? $item->service_type_name : ''; ?>
													</small>

													<?php
														if($is_aquabest){
															?>
															<strong class='text-success span-block'>
																<?php
																	echo strtoupper($secondary[$item->second_status]);
																	if($item->second_status == 6){
																		echo "<br>Remarks: ". $item->cancel_remarks;
																	} else if($item->second_status == 5){
																		echo "<br>Remarks: ". $item->hold_remarks;
																	}

																?>

																<br>
																<?php
																	$item_service_step1 = $item->second_status == 0 && $user->hasPermission('service_step_1');
																	$item_service_step2 = $item->second_status == 1 && $user->hasPermission('service_step_2');
																	$item_service_step3 = $item->second_status == 2 && $user->hasPermission('service_step_3');
																	$item_service_step4 = $item->second_status == 3 && $user->hasPermission('service_step_4');

																	if($item_service_step1 || $item_service_step2 || $item_service_step3 || $item_service_step4 || $item->second_status == 5)
																	{
																		?>
																		<button title='Process' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-primary btn-sm secondary-status-change'><i  class='fa fa-pencil' style='cursor: pointer;'></i></button>
																		<?php
																			if($item->second_status != 6 && $item->second_status != 5 ){
																				?>
																				<button title='Cancel' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-danger btn-sm secondary-status-change-cancel'><i  class='fa fa-close' style='cursor: pointer;'></i></button>
																				<?php
																			} else {

																			}
																		?>

																		<?php
																			if($item->second_status != 5  && $item->second_status != 6){
																				?>
																				<button title='Hold' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-warning btn-sm secondary-status-change-hold'><i  class='fa fa-warning' style='cursor: pointer;'></i></button>
																				<?php
																			}
																		?>


																		<?php
																	}
																?>


															</strong>
															<?php
														}
													?>
													<?php
														if(Configuration::thisCompany('cebuhiq')){
															?>
															<span class='span-block'>Contact Person: <?php echo ($item->contact_person) ? $item->contact_person :'NA'; ?></span>
															<span class='span-block'>Contact Number: <?php echo ($item->contact_number) ? $item->contact_number :'NA'; ?></span>
															<span class='span-block'>Address: <?php echo ($item->contact_address) ? $item->contact_address :'NA'; ?></span>

															<?php
														}
													?>
												</td>
												<?php if($is_aquabest){ ?>
													<td>
													<?php
														$cls_service_date_log = new Service_date_log();
														$service_date_log = $cls_service_date_log->getList($item->id);
														if($service_date_log){
															foreach($service_date_log as $dtlog){
																echo "<p>".$secondary[$dtlog->status]."<br><i class='fa fa-user'></i> <small class='text-danger'>".ucwords($dtlog->firstname . " ". $dtlog->lastname)."</small><br><i class='fa fa-calendar'></i> <small class='text-danger'>".date('m/d/Y h:i:s A',$dtlog->dt)."</small></p>";
															}
														} else {
															echo "<p>N/A</p>";
														}
													?>
													</td>
												<?php } ?>
												<td data-title="Created"><?php echo date('m/d/Y', $item->created); ?></td>
												<td><?php echo $alltechnician; ?></td>
												<td>

													<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnDetails'>
														<i class='fa fa-list'></i> Details
													</button>
													<?php if($user->hasPermission('item_service_rem')){
														?>
														<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnAddRemarks'>
															<i class='fa fa-plus'></i> Remarks
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('measure') && $item->second_status == 2){
														?>
														<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnAddMeasurement'>
															<i class='fa fa-plus'></i> Measurement
														</button>
														<?php
													} ?>
													<?php
														$service_cls  = new Service_request_item();
														$get_stats = $service_cls->stillPending($item->id);

														if($user->hasPermission('item_service_s') &&  $item->item_req_status != 3 && $item->branch_id){
														?>
														<button data-branch_id='<?php echo escape($item->branch_id); ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnRequestItem'>
															<i class='fa fa-share'></i> Request Item
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('item_service_s') && ($item->item_req_status == 1 || $item->item_req_status == 2)){
														?>
														<button data-branch_name='<?php echo $item->branch_name; ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md  btnRequestedItem'>
															<i class='fa fa-share'></i> Requested
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('item_service_ap') && $get_stats->cnt > 0){
														?>
														<button data-branch_name='<?php echo $item->branch_name; ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnReleaseItem'>
															<i class='fa fa-ok'></i> Release Item
														</button>
														<?php
													} ?>

													<?php if($user->hasPermission('item_service_con')){
														?>
														<button data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnCCD'>
															<i class='fa fa-ok'></i> Detailed Info
														</button>
														<?php
													} ?>

												</td>
											</tr>
											<?php
										}
									?>

									</tbody>
								</table>
							</div>									<?php
						} else {
							?>
							<div class="alert alert-info">No request found.</div>									<?php
						} ?>
					</div>
					<div id="con_pullout" style='display:none;padding-top:15px;'>
						<h4>Pull out</h4>
						<?php if($for_pullout) {
							?>
							<div id="no-more-tables">
								<table class='table'  id='tblForApproval'>
									<thead>
									<tr>
										<th>Details</th>

										<th>Member</th>
										<?php if($is_aquabest){ ?>
											<th>Date Log</th>
										<?php } ?>
										<th>Date Pullout</th>
										<th>Technician</th>
										<th></th>
									</tr>
									</thead>
									<tbody>
									<?php
										foreach($for_pullout as $item) {
											if($item->member_id) {
												$mem = escape($item->mln . ", " . $item->mfn . " " . $item->mmn);
											} else {
												$mem = 'Not available';
											}
											$techids = $item->technician_id;
											$btn_assign_tech = "";
											if($user->hasPermission('ass_tech')){
												$btn_assign_tech = "<button class='btn btn-default btn-sm btnAssignTech' data-id='$item->id'><i class='fa fa-pencil'></i> Assign </button>";
											}
											$alltechnician = "<p class='text-danger'>No technician assign. $btn_assign_tech </p>";
											if($techids) {
												$listech = $techcls->getTech($techids);
												if($listech) {
													$alltechnician = "";
													foreach($listech as $l) {
														$alltechnician .= "<p class='text-danger'><i class='fa fa-user'></i> $l->name</p>";
													}
												}
											}
											if($item->pullout_schedule){
												$pullout_date = date('m/d/Y', $item->pullout_schedule);
											} else {
												$pullout_date = "<i class='fa fa-ban'></i>";
											}

											?>
											<tr>
												<td data-title="Id">
													<strong>ID:</strong> <?php echo escape($item->id); ?><br>
													<strong>User: </strong><?php echo escape($item->lastname . ", " . $item->firstname . " " . $item->middlename); ?><br>
													<strong>Branch: </strong>
													<?php
														if($item->branch_name){
															echo escape($item->branch_name);
														} else {
															if($user->hasPermission('ass_branch')){
																echo "<button data-id='$item->id' class='btn btn-default btn-sm btnAddBranch'><i class='fa fa-pencil'></i> Assign </button>";
															} else {
																echo "None";
															}

														}
													?>
													<?php if(Configuration::thisCompany('cebuhiq')){
														?>
														<br><strong>RF Id: </strong><?php echo ($item->rf_id) ? ($item->rf_id) : 'NA'; ?>
														<br><strong>Backload Ref Number: </strong><?php echo ($item->backload_ref_number) ? ($item->backload_ref_number) : 'NA'; ?>

														<?php
													}?>
													<?php if(Configuration::thisCompany('avision')){
														?>
														<br><strong>Client PO: </strong><?php echo ($item->client_po) ? ($item->client_po) : 'NA'; ?>
														<?php
													}?>
												</td>
												<td data-title="Member">
													<?php echo $mem; ?>
													<small class='text-danger span-block'>
														<?php
															$allstatus = $item_service->getStatuses($item->id);
															$lblstats = "";

															if(count($allstatus)) {
																foreach($allstatus as $ind_stat) {
																	$lblstats .= $status_arr[$ind_stat->status] . ", ";
																}
																echo $lblstats = rtrim($lblstats, ", ");
															}

														?>

													</small>
													<small class='span-block'>
														<?php echo ($item->service_type_name) ? $item->service_type_name : ''; ?>
													</small>
													<?php
														if($is_aquabest){
															?>
															<strong class='text-success span-block'>
																<?php echo strtoupper($secondary[$item->second_status]); ?>
																<br>
																<?php
																	$item_service_step1 = $item->second_status == 0 && $user->hasPermission('service_step_1');
																	$item_service_step2 = $item->second_status == 1 && $user->hasPermission('service_step_2');
																	$item_service_step3 = $item->second_status == 2 && $user->hasPermission('service_step_3');
																	$item_service_step4 = $item->second_status == 3 && $user->hasPermission('service_step_4');

																	if($item_service_step1 || $item_service_step2 || $item_service_step3 || $item_service_step4 || $item->second_status == 5)
																	{
																		?>
																		<button title='Process' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-primary btn-sm  secondary-status-change'><i  class='fa fa-pencil' style='cursor: pointer;'></i></button>
																		<?php
																		if($item->second_status != 6 && $item->second_status != 5 ){
																			?>
																			<button title='Cancel' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-danger btn-sm  secondary-status-change-cancel'><i  class='fa fa-close' style='cursor: pointer;'></i></button>
																			<?php
																		}
																		?>

																		<?php
																		if($item->second_status != 5  && $item->second_status != 6){
																			?>
																			<button title='Hold' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-warning btn-sm  secondary-status-change-hold'><i  class='fa fa-warning' style='cursor: pointer;'></i></button>
																			<?php
																		}
																		?>


																		<?php
																	}
																?>


															</strong>
															<?php
														}
													?>
												</td>
												<?php if($is_aquabest){ ?>
													<td>
														<?php
															$cls_service_date_log = new Service_date_log();
															$service_date_log = $cls_service_date_log->getList($item->id);
															if($service_date_log){
																foreach($service_date_log as $dtlog){
																	echo "<p>".$secondary[$dtlog->status]."<br><i class='fa fa-user'></i> <small class='text-danger'>".ucwords($dtlog->firstname . " ". $dtlog->lastname)."</small><br><i class='fa fa-calendar'></i> <small class='text-danger'>".date('m/d/Y h:i:s A',$dtlog->dt)."</small></p>";
																}
															} else {
																echo "<p>N/A</p>";
															}
														?>
													</td>
												<?php } ?>
												<td data-title="Created"><?php echo $pullout_date; ?></td>
												<td><?php echo $alltechnician; ?></td>
												<td>
													<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnDetails'>
														<i class='fa fa-list'></i> Details
													</button>
													<?php if($user->hasPermission('item_service_rem')){
														?>
														<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnAddRemarks'>
															<i class='fa fa-plus'></i> Add Remarks
														</button>
														<?php
													} ?>

													<?php if($user->hasPermission('measure') && $item->second_status == 2){
														?>
														<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnAddMeasurement'>
															<i class='fa fa-plus'></i> Measurement
														</button>
														<?php
													} ?>
													<?php
														$service_cls  = new Service_request_item();
														$get_stats = $service_cls->stillPending($item->id);

														if($user->hasPermission('item_service_s') &&  $item->item_req_status != 3 && $item->branch_id){
															?>
															<button data-branch_id='<?php echo escape($item->branch_id); ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnRequestItem'>
																<i class='fa fa-share'></i> Request Item
															</button>
															<?php
														} ?>
													<?php if($user->hasPermission('item_service_s') && ($item->item_req_status == 1 || $item->item_req_status == 2)){
														?>
														<button data-branch_name='<?php echo $item->branch_name; ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md  btnRequestedItem'>
															<i class='fa fa-share'></i> Requested
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('item_service_ap') && $get_stats->cnt > 0){
														?>
														<button data-branch_name='<?php echo $item->branch_name; ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnReleaseItem'>
															<i class='fa fa-ok'></i> Release Item
														</button>
														<?php
													} ?>
												</td>
											</tr>													<?php
										}
									?>

									</tbody>
								</table>
							</div>									<?php
						} else {
							?>
							<div class="alert alert-info">No request found.</div>									<?php
						} ?>
					</div>
					<div id="con_homeservice" style='display:none;padding-top:15px;'>
						<h4>On site</h4>
						<?php if($for_homeservice) {
							?>
							<div id="no-more-tables">
								<table class='table'  id='tblForApproval'>
									<thead>
									<tr>
										<th>Details</th>

										<th>Member</th>
										<?php if($is_aquabest){ ?>
											<th>Date Log</th>
										<?php } ?>
										<th>Schedule</th>
										<th>Technician</th>
										<th></th>
									</tr>
									</thead>
									<tbody>
									<?php
										foreach($for_homeservice as $item) {
											if($item->member_id) {
												$mem = escape($item->mln . ", " . $item->mfn . " " . $item->mmn);
											} else {
												$mem = 'Not available';
											}
											$techids = $item->technician_id;
											$btn_assign_tech = "";
											if($user->hasPermission('ass_tech')){
												$btn_assign_tech = "<button class='btn btn-default btn-sm btnAssignTech' data-id='$item->id'><i class='fa fa-pencil'></i> Assign </button>";
											}
											$alltechnician = "<p class='text-danger'>No technician assign. $btn_assign_tech </p>";
											if($techids) {
												$listech = $techcls->getTech($techids);
												if($listech) {
													$alltechnician = "";
													foreach($listech as $l) {
														$alltechnician .= "<p class='text-danger'><i class='fa fa-user'></i> $l->name</p>";
													}
												}
											}
											?>
											<tr>
												<td data-title="Id">
													<strong>ID:</strong> <?php echo escape($item->id); ?><br>
													<strong>User: </strong><?php echo escape($item->lastname . ", " . $item->firstname . " " . $item->middlename); ?><br>
													<strong>Branch: </strong>
													<?php
														if($item->branch_name){
															echo escape($item->branch_name);
														} else {
															if($user->hasPermission('ass_branch')){
																echo "<button data-id='$item->id' class='btn btn-default btn-sm btnAddBranch'><i class='fa fa-pencil'></i> Assign </button>";
															} else {
																echo "None";
															}

														}


													?>
													<?php if(Configuration::thisCompany('cebuhiq')){
														?>
														<br><strong>RF Id: </strong><?php echo ($item->rf_id) ? ($item->rf_id) : 'NA'; ?>
														<br><strong>Backload Ref Number: </strong><?php echo ($item->backload_ref_number) ? ($item->backload_ref_number) : 'NA'; ?>

													<?php } ?>
													<?php if(Configuration::thisCompany('avision')){
														?>
														<br><strong>Client PO: </strong><?php echo ($item->client_po) ? ($item->client_po) : 'NA'; ?>
														<?php
													}?>
												</td>
												<td data-title="Member">
													<?php echo $mem; ?>
													<small class='text-danger span-block'>
														<?php
															$allstatus = $item_service->getStatuses($item->id);
															$lblstats = "";

															if(count($allstatus)) {
																foreach($allstatus as $ind_stat) {
																	$lblstats .= $status_arr[$ind_stat->status] . ", ";
																}
																echo $lblstats = rtrim($lblstats, ", ");
															}

														?>

													</small>
													<small class='span-block'>
														<?php echo ($item->service_type_name) ? $item->service_type_name : ''; ?>
													</small>
													<?php
														if($is_aquabest){
															?>
															<strong class='text-success span-block'>
																<?php echo strtoupper($secondary[$item->second_status]); ?>
																<br>
																<?php
																	$item_service_step1 = $item->second_status == 0 && $user->hasPermission('service_step_1');
																	$item_service_step2 = $item->second_status == 1 && $user->hasPermission('service_step_2');
																	$item_service_step3 = $item->second_status == 2 && $user->hasPermission('service_step_3');
																	$item_service_step4 = $item->second_status == 3 && $user->hasPermission('service_step_4');

																	if($item_service_step1 || $item_service_step2 || $item_service_step3 || $item_service_step4 || $item->second_status == 5)
																	{
																		?>
																		<button title='Process' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-primary btn-sm secondary-status-change'><i  class='fa fa-pencil' style='cursor: pointer;'></i></button>
																		<?php
																		if($item->second_status != 6 && $item->second_status != 5 ){
																			?>
																			<button title='Cancel' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-danger btn-sm secondary-status-change-cancel'><i  class='fa fa-close' style='cursor: pointer;'></i></button>
																			<?php
																		}
																		?>

																		<?php
																		if($item->second_status != 5  && $item->second_status != 6){
																			?>
																			<button title='Hold' data-id="<?php echo $item->id; ?>"  data-status="<?php echo $item->second_status; ?>" class='btn btn-warning btn-sm secondary-status-change-hold'><i  class='fa fa-warning' style='cursor: pointer;'></i></button>
																			<?php
																		}
																		?>


																		<?php
																	}
																?>


															</strong>
															<?php
														}
													?>
												</td>
												<?php if($is_aquabest){ ?>
													<td>
														<?php
															$cls_service_date_log = new Service_date_log();
															$service_date_log = $cls_service_date_log->getList($item->id);
															if($service_date_log){
																foreach($service_date_log as $dtlog){
																	echo "<p>".$secondary[$dtlog->status]."<br><i class='fa fa-user'></i> <small class='text-danger'>".ucwords($dtlog->firstname . " ". $dtlog->lastname)."</small><br><i class='fa fa-calendar'></i> <small class='text-danger'>".date('m/d/Y h:i:s A',$dtlog->dt)."</small></p>";
																}
															} else {
																echo "<p>N/A</p>";
															}
														?>
													</td>
												<?php } ?>
												<td data-title="Created">
													<?php
														if($item->home_repair){
															echo date('m/d/Y', $item->home_repair);
														} else {
															echo "<button class='btn btn-default btn-sm btnAddOnsiteSchedule' data-id='$item->id' >Add Schedule</button>";
														}

													?>
												</td>
												<td><?php echo $alltechnician; ?></td>
												<td>
													<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnDetails'>
														<i class='fa fa-list'></i> Details
													</button>
													<?php if($user->hasPermission('item_service_rem')){
														?>
														<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnAddRemarks'>
															<i class='fa fa-plus'></i> Add Remarks
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('measure') && $item->second_status == 2){
														?>
														<button data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnAddMeasurement'>
															<i class='fa fa-plus'></i> Measurement
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('item_service_s') && (!$item->item_req_status) && $item->branch_id){
														?>
														<button data-branch_id='<?php echo escape($item->branch_id); ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnRequestItem'>
															<i class='fa fa-share'></i> Request Item
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('item_service_s') && ($item->item_req_status == 1 || $item->item_req_status == 2)){
														?>
														<button data-branch_name='<?php echo $item->branch_name; ?>' data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnRequestedItem'>
															<i class='fa fa-share'></i> Requested
														</button>
														<?php
													} ?>
													<?php if($user->hasPermission('item_service_ap') && $item->item_req_status == 1){
														?>
														<button data-branch_name='<?php echo $item->branch_name; ?>'  data-member_id='<?php echo escape($item->member_id); ?>' data-id='<?php echo escape(Encryption::encrypt_decrypt('encrypt', $item->id)); ?>' class='btn btn-sm btn-primary btn-fixed-width-md btnReleaseItem'>
															<i class='fa fa-ok'></i> Release Item
														</button>
														<?php
													} ?>
												</td>
											</tr>													<?php
										}
									?>

									</tbody>
								</table>
							</div>									<?php
						} else {
							?>
							<div class="alert alert-info">No request found.</div>									<?php
						} ?>
					</div>

					<?php
				} else {
					echo $nopending;
				}
			?>
		</div>
		<?php
	}
	function assignBranch(){
		$service = new Item_service_request();
		$id = Input::get('id');
		$branch_id = Input::get('branch_id');
		if(is_numeric($id) && is_numeric($branch_id) && $id && $branch_id){
			$service->update(['branch_id' => $branch_id],$id);
			echo "Branch updated successfully.";
		}
		else
			echo "Invalid request";
	}

	function payCommission($id=0,$dt1=0,$dt2=0){
		$com = new Commission_list();
		if(!$id){
			$id = Input::get('id');
			$dt1 = Input::get('dt1');
			$dt2 = Input::get('dt2');
		}

		$now = time();
		if(is_numeric($id)&& $id){
			$com->payCommission($id,$now,$dt1,$dt2);
		}

	}

	function getCommission(){
		$com = new Commission_list();
		$status = Input::get('status');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$status = Input::get('status');
		$list = $com->getCommission($status,$dt1,$dt2);
		 if($list){
			?>
			<table class="table table-bordered" id='tblForApproval'>
				<thead>
				<tr>
					<th class='text-center'><input type="checkbox" id='chkAll'></th>
					<th>Name</th>
					<th class='text-right'>Amount</th>
					<th class='text-center'></th>
					<th class='text-center'></th>
				</tr>
				</thead>
				<tbody>
				<?php
					$total_pending = 0;
					foreach($list as $l){
						$total_pending += $l->total_pending;
						?>
						<tr data-id='<?php echo $l->agent_id; ?>'>
							<td class='text-center'><input class='chbk' type="checkbox"></td>
							<td><?php echo ucwords($l->firstname . " " . $l->lastname); ?></td>
							<td class='text-right'><?php echo number_format($l->total_pending,2); ?></td>

							<td class='text-center'>
								<?php if($status == 0){
									?>
									<button data-id='<?php echo $l->agent_id; ?>' class='btn btn-default btn-sm btnPay'>Pay Commission</button>
									<?php
								} else {
									?>
									<span class='text-danger'><?php echo date('m/d/Y',$l->pay_date); ?></span>
									<?php
								}?>
							</td>
							<td class='text-center'>
								<button data-status='<?php echo $status; ?>' data-pay_date='<?php echo $l->pay_date; ?>' data-id='<?php echo $l->agent_id; ?>' class='btn btn-default btn-sm btnDetails'>Details</button>
							</td>
						</tr>
						<?php
					}
					?>
				<tr>
					<td></td>
					<td></td>
					<td class='text-right'><strong><?php echo number_format($total_pending,2); ?></strong></td>
					<td></td>
					<td></td>
				</tr>
					<?php
				?>
				</tbody>
			</table>
			<?php
		} else {
			?>
			<div class="alert alert-info">No record found.</div>
			<?php
		}
	}

	function batchProcessCommission(){
		$agent_ids = Input::get('agent_ids');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		if($agent_ids){
			$agent_ids = json_decode($agent_ids,true);
			foreach($agent_ids as $id){
				payCommission($id,$dt1,$dt2);
			}
			echo "Processed Successfully";
		}
	}

	function commissionDetails(){
		$id = Input::get('id');
		$pay_date = Input::get('pay_date');
		$status = Input::get('status');
		$dt1 = Input::get('dt1');
		$dt2 = Input::get('dt2');
		$status = Input::get('status');
		$com = new Commission_list();

		$list = $com->getDetails($id,$pay_date,$status,$dt1,$dt2);
		if($list){

			echo "<table class='table table-bordered table-condensed'>";
			echo "<tr><th>Date</th><th>Item</th><th>Commission</th></tr>";
			$total = 0;
			foreach($list as $l){
				$total += $l->amount;
					echo "<tr>";
					echo "<td style='border-top:1px solid #ccc;'>" . date('m/d/Y',$l->created) . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . $l->item_code . "</td>";
					echo "<td style='border-top:1px solid #ccc;'>" . number_format($l->amount,2) . "</td>";
					echo "</tr>";
			}
			echo "</table>";
			echo "<p>Total: ".number_format($total,2)."</p>";

		} else {
			echo "Problem loading the content.";
		}

	}

	function batchApproveMember(){
		$ids = Input::get('ids');
		$ids = json_decode($ids,true);
		if(count($ids)){

			foreach($ids as $id){
				if(is_numeric($id)){
					$member_terms = new Member_term($id);
					$member_terms->updateSameTypAndQty($member_terms->data()->member_id,$member_terms->data()->item_id,$member_terms->data()->qty,$member_terms->data()->type);
					$user = new User();
					$member_terms->update(array('status' => 2,'approval_id'=>$user->data()->id),$id);
					// update prev terms with same type and qty

					// check current order for approval
					$adjustment = $member_terms->data()->adjustment;
					$whdet = new Wh_order();
					$details = $whdet->getMemberOrderWh($member_terms->data()->member_id,1,$member_terms->data()->item_id);
					if($details) {
						$whorderdet = new Wh_order_details();
						foreach($details as $det) {

							$qty = $det->qty;
							$memberTerms = new Member_term();
							$alladj = 0;
							$memadj = $memberTerms->getAdjustment($member_terms->data()->member_id, $member_terms->data()->item_id);
							if(count($memadj)) {
								foreach($memadj as $m) {
									$madj = $m->adjustment;

									if($m->type == 1) { // for every
										if($qty < 1 && $qty != 0) {
											if($m->qty == 1) {
												$x = $qty / $m->qty;
											} else {
												$x = 0;
											}
										} else {
											$x = floor($qty / $m->qty);
										}

										$madj = $madj * $x;
										$alladj += $madj;

									} else if($m->type == 2) { // above qty
										if($qty >= $m->qty) {
											if($m->discount_type == 0) {
												$alladj += $madj;
											} else {
												$madj = $madj * $qty;
												$alladj += $madj;
											}

										}
									}
								}
							}
							$whorderdet->update(['member_adjustment' => $alladj], $det->id);

						}
					}
				}
			}
			Log::addLog($user->data()->id,$user->data()->company_id,"Batch Approve Member Terms IDs ".implode(',',$ids),"ajax_member_service.php");

			echo "Approved successfully.";
		} else {
			echo "Invalid request";
		}

	}

	function batchDeclineMember() {
		$ids = Input::get('ids');
		$ids = json_decode($ids, true);
		if(count($ids)) {
			foreach($ids as $id) {
				if(is_numeric($id)){
					$member_terms = new Member_term();
					$member_terms->update(array('status' => 3),$id);

				}
			}
			$user = new User();
			Log::addLog($user->data()->id,$user->data()->company_id,"Batch Decline Member Terms IDs ".implode(',',$ids),"ajax_member_service.php");
			echo "Terms declined successfully.";
		} else {
			echo "Invalid request";
		}
	}

	function batchSubmitTerms(){
		$data = Input::get('arr');
		$data = json_decode($data,true);
		if($data){
			foreach($data as $d){
				$member_id =$d['member_id'];
				$is_all = $d['is_all'];
				$item_id = $d['item_id'];
				$adjustment =$d['adjustment'];
				$terms =$d['terms'];
				$qty = $d['qty'];
				$type = $d['type'];
				$remarks = $d['remarks'];
				$discount_type = $d['discount_type'];
				$transaction_type = $d['transaction_type'];
				if((is_numeric($member_id) || $is_all)&& is_numeric($item_id) && is_numeric($adjustment) && is_numeric($terms) && is_numeric($qty) && is_numeric($type)) {

					$now = time();
					$member = new Member_term();
					$user = new User();
					if($is_all) $member_id = -1;
					$member->create(array('member_id' => $member_id, 'user_id' => $user->data()->id, 'company_id' => $user->data()->company_id, 'branch_id' => $user->data()->branch_id, 'qty' => $qty, 'item_id' => $item_id, 'adjustment' => $adjustment, 'type' => $type, 'discount_type' => $discount_type, 'transaction_type' => $transaction_type, 'terms' => $terms, 'is_active' => 1, 'status' => 1, 'remarks' => $remarks, 'created' => $now, 'modified' => $now));
					Log::addLog($user->data()->id,$user->data()->company_id,
						"Add Member Terms Item ID $item_id Client ID $member_id Adjustment $adjustment",
						"ajax_member_service.php");
				}
			}
			echo "Request submitted successfully";
		} else {
			echo "Invalid request";
		}
	}

	function updatePayment(){

		$id = Input::get('id');
		$pid = Input::get('pid');
		$method = Input::get('m');
		$cr_log_id= Input::get('cr_log_id');


		if($id && is_numeric($id)){
			echo "<input type='hidden' id='update_cr_log_id' value='$cr_log_id' >";
			if($method == 'cheque'){

				$cheque = new Cheque($id);
				echo "<div class='form-group'>";
				echo "<input id='ch_update_id' type='hidden' class='form-control' value='". $cheque->data()->id."' >";
				echo "<input id='ch_update_check_number' class='form-control' value='". $cheque->data()->check_number."' >";
				echo "<span class='help-block'>Check Number</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='ch_update_bank'  class='form-control' value='". $cheque->data()->bank."' >";
				echo "<span class='help-block'>Bank</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='ch_update_maturity_date' class='form-control' value='" . date('m/d/Y', $cheque->data()->payment_date). "' >";
				echo "<span class='help-block'>Maturity Date</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<button class='btn btn-default' id='btnCheckUpdate' >Save</button>";
				echo "</div>";


			}

			if($method == 'bt'){

				$bt = new Bank_transfer($id);
				echo "<div class='form-group'>";
				echo "<input id='bt_update_id' type='hidden' class='form-control' value='". $bt->data()->id."' >";
				echo "<input id='bt_update_bankfrom_name' class='form-control' value='". $bt->data()->bankfrom_name."' >";
				echo "<span class='help-block'>Bank Name</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='bt_update_bankfrom_account_number'  class='form-control' value='". $bt->data()->bankfrom_account_number."' >";
				echo "<span class='help-block'>Account Number</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='bt_update_date' class='form-control' value='" . date('m/d/Y', $bt->data()->date). "' >";
				echo "<span class='help-block'>Date</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<button class='btn btn-default' id='btnBTUpdate' >Save</button>";
				echo "</div>";

			}

			if($method == 'credit'){

				$credit = new Credit($id);
				echo "<div class='form-group'>";
				echo "<input id='credit_update_id' type='hidden' class='form-control' value='". $credit->data()->id."' >";
				echo "<input id='credit_update_card_number' class='form-control' value='". $credit->data()->card_number."' >";
				echo "<span class='help-block'>Card Number</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='credit_update_bank_name'  class='form-control' value='". $credit->data()->bank_name."' >";
				echo "<span class='help-block'>Bank</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='credit_update_card_type'  class='form-control' value='". $credit->data()->card_type."' >";
				echo "<span class='help-block'>Card Type</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='credit_update_approval_code'  class='form-control' value='". $credit->data()->approval_code."' >";
				echo "<span class='help-block'>Approval Code</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='credit_update_trace_number'  class='form-control' value='". $credit->data()->trace_number."' >";
				echo "<span class='help-block'>Trace Number</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<input  id='credit_update_date' class='form-control' value='" . date('m/d/Y', $credit->data()->date). "' >";
				echo "<span class='help-block'>Date</span>";
				echo "</div>";
				echo "<div class='form-group'>";
				echo "<button class='btn btn-default' id='btnCreditUpdate' >Save</button>";
				echo "</div>";
			}
		}
	}


	function updateOnsiteDate(){
		$service_id = Input::get('service_id');
		$onsite_date= Input::get('onsite_date');
		if($service_id && is_numeric($service_id) && $onsite_date){
			$item_service = new Item_service_request($service_id);
			if(isset($item_service->data()->id)){
				$item_service->update(['home_repair' => strtotime($onsite_date)],$service_id);
				echo "Date updated successfully.";
			}
		}
	}

	function saveCreditHolder(){

		$id = Input::get('id');
		$name = Input::get('name');
		$remarks = Input::get('remarks');

		if($id && $name && $remarks){

			$member_credit = new Member_credit($id);

			if(isset($member_credit->data()->id) && $member_credit->data()->id){

				$arr =[];

				if($member_credit->data()->holders){

					$arr = json_decode($member_credit->data()->holders, true);
					$arr[] = ['name' => $name,'remarks' => $remarks, 'date' => date('m/d/Y') ];

				} else {

					$arr[] = ['name' => $name,'remarks' => $remarks, 'date' => date('m/d/Y') ];

				}

				$member_credit->update(
					[ 'holders' => json_encode($arr) ], $id
				);
				echo "Updated successfully.";
			}
		}
	}

	function insertCheckForm(){


			$data = Input::get('data');

			$ref_table = 'bounce_check';
			$form = new Form();
			$ref_id = $form->lastID($ref_table);
			$ref_id++;
			$c = $form->checker($ref_table,$ref_id);

			$arr = json_decode(Input::get('arr'),true);

			foreach($arr as $a){

				$ch = new Cheque();

				if($a && is_numeric($a)){

					$ch->update(
						['print_ref_id' => $ref_id] , $a
					);

				}

			}


		if($c){

			} else {
				$data = str_replace('0000',$ref_id,$data);
				$form->create(
					[
						'json_data' => $data,
						'ref_id' => $ref_id,
						'ref_name' => $ref_table,
					]
				);
				echo  $data;
			}




	}

	function bounceCheck(){

		$cheque = new Cheque();
		$dt_from = Input::get('dt1');
		$dt_to = Input::get('dt2');
		$list = $cheque->getBounceList($dt_from , $dt_to);


		if($list){

			if($dt_from){
				$dt = date('Y-m',strtotime($dt_from));
			}
			echo "<input id='dt_cover' type='hidden' value='$dt' />";
			echo "<table id='tblForApproval' class='table table-bordered' style='font-size:12px;'>";
			echo "<thead>";
			echo "<tr><th colspan='5'>Bounce Check Information</th><th rowspan='2' class='text-center'>Reason</th><th colspan='7'>Replacement of Bounce Check</th></tr>";
			echo "<tr><th>Customer</th><th class='text-right'>Amount</th><th>Bank</th><th>Check #</th><th>Check Date</th><th class='text-right'>Cash</th><th class='text-right'>Check Amount</th><th>Bank</th><th>Check #</th><th>Check Date</th><th>Others</th><th class='text-right'>Balance</th></tr>";
			echo "</thead>";
			echo "<tbody>";
			$printed= 0;
			$total_balance = 0;
			$total_collection = 0;
			$total_cash = 0;
			$total_check = 0;

			foreach($list as $l){

				$mt2 = $l->payment_date2 ? date('m/d/Y',$l->payment_date2) : '';
				$cash_amount = $l->cash_amount ? $l->cash_amount : 0;
				$balance  = $l->amount  -  $cash_amount - $l->amount2;

				$print_ref_id = $l->print_ref_id;
				$cls = "";
				if($print_ref_id){
					$cls = "bg-warning";
					$printed = $print_ref_id;
				}
				$reason =  $l->bounce_reason;
				if($l->other_reason) {
					$reason = $l->other_reason;
				}

				echo "<tr class='$cls' data-id='$l->id'>";
				echo "<td>". $l->member_name . "</td>";
				echo "<td class='text-right'>".number_format($l->amount,2)."</td>";
				echo "<td>".$l->bank."</td>";
				echo "<td>".$l->check_number."</td>";
				echo "<td>". date('m/d/Y',$l->payment_date)."</td>";
				echo "<td >$reason</td>";
				echo "<td class='text-right'> ". number_format($cash_amount,2). "</td>";
				echo "<td class='text-right'>". number_format($l->amount2,2) ."</td>";
				echo "<td>".$l->bank2."</td>";
				echo "<td>".$l->check_number2."</td>";
				echo "<td>".$mt2."</td>";
				echo "<td></td>";
				echo "<td class='text-right'> " . number_format($balance,2) ."</td>";
				echo "</tr>";

				$total_balance += $balance;
				$total_collection += ($l->amount2 + $cash_amount);
				$total_check += $l->amount2;
				$total_cash += $cash_amount;

			}
			echo "<tr><th>Total Balance</th><th colspan='12' class='text-right'> " . number_format($total_balance,2) ."</th></tr>";
			echo "</tbody>";

			echo "</table>";

			echo "<table class='table table-bordered' >";
			echo "<tfoot></tr>";
			echo "<tr>";
			echo "<th>Total Collection</th><th colspan='6'> " . number_format($total_collection,2) ."</th>";
			echo "<th>Total Cash</th><th colspan='6'> " . number_format($total_cash,2) ."</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<th></th><th colspan='6'> </th>";
			echo "<th>Total Cheque</th><th colspan='6'> " . number_format($total_check,2) ."</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<th></th><th colspan='6'> </th>";
			echo "<th>Total Online</th><th colspan='6'> " . number_format(0,2) ."</th>";
			echo "</tr>";
			echo "</tfoot>";
			echo "</table>";
			echo "<br>";

			echo "<div class='text-right'>	<button data-print_id='$printed' class='btn btn-default hideOnPrint' id='btnPrint'>Print</button></div>";

		}

	}

	function approvedDeduction(){
		$id = Input::get('id');
		$deduction = new Deduction();
		if($id && is_numeric($id)){
			$deduction->update(['status' => 0],$id);
		}

		echo "Updated successfully.";


	}

