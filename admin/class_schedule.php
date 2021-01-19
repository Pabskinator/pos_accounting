<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$class_schedule = new Class_schedule();
	$class_schedules = $class_schedule->get_active('class_schedules',array('1' ,'=','1'));


	$coach = new Class_schedule();
	$coaches = $coach->get_active('coaches',array('1' ,'=','1'));
	$arr_coach = [];
	foreach($coaches as $c){
		$arr_coach[$c->id] = $c->name;
	}

	function date_compare($a, $b)
	{
		$a_ex = explode("-",$a['time_of_the_day']);
		$b_ex = explode("-",$b['time_of_the_day']);

		$t1 = strtotime($a_ex[0]);
		$t2 = strtotime($b_ex[0]);
		return $t1 - $t2;
	}
?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Class Schedule
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div><br>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php 	if($user->hasPermission('member_m')) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='add_class_schedule.php' title='Add Service'>
							<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add Class</span> </a>
					</div>
				<?php } ?>
				<?php
					if ($class_schedules){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Schedule</div>
					<div class="panel-body">
					<table id='tblWithBorder' class="table table-bordered ">
								<?php
									$arr_os = [];
									foreach($class_schedules as $b){
										$os = new Offered_service($b->class_id);
										$coach_name = isset($arr_coach[$b->coach_id]) ? $arr_coach[$b->coach_id] : 'N/A';
										$arr_os[$os->data()->name][$b->day_of_the_week][] =['time_of_the_day' => $b->time_of_the_day ,'id' => $b->id,'class_type' => $b->class_type,'is_pt' => $b->is_pt,'coach_name' => $coach_name];
									}

									foreach($class_schedules as $b){
										usort($arr_os[$os->data()->name][$b->day_of_the_week], 'date_compare');
									}


									$arr_days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
									$classtype = ['','Turf Area','Matted Area'];
									$is_pt = ['','Class','PT'];
									echo "<tr>";
									echo "<th>Class</th>";
									foreach($arr_days as $day){
										echo "<th>$day</th>";
									}
									echo "</tr>";
									foreach($arr_os as $class_name => $aos){
										echo "<tr>";
										echo "<td style='border-top:1px solid #ccc;'>$class_name</td>";
										foreach($arr_days as $d){
											$cur = isset($aos[$d]) ? $aos[$d] : [];
											echo "<td style='border-top:1px solid #ccc;'>";
											if(count($cur)){
												foreach($cur as $a){
													$ex = explode('-',$a['time_of_the_day']);
													$hr_from = date('h:i A',strtotime($ex[0]));
													$hr_to = date('h:i A',strtotime($ex[1]));
													echo "<small class='span-block'>
															$hr_from - $hr_to
															<a class='' href='add_class_schedule.php?edit=".Encryption::encrypt_decrypt('encrypt',$a['id'])."' title='Edit'><span class='glyphicon glyphicon-pencil'></span></a>
															<a href='#' class='deleteSched' id='".Encryption::encrypt_decrypt('encrypt',$a['id']) . "' title='Delete'><span class='glyphicon glyphicon-remove'></span></a>
															<smal class='text-danger span-block'>".$classtype[$a['class_type']]." - ".$is_pt[$a['is_pt']]." </smal>
															<smal class='text-danger span-block'> Coach: ".$a['coach_name']." </smal>
															</small>";
												}
											}
											echo "</td>";
										}
										echo "</tr>";
									}
							?>
					</table>
					</div>
				</div>
						<?php
						} else {
						?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
						<?php
					}
					?>

		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteSched").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'class_schedules'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
		});


		$('#tblbrands').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>