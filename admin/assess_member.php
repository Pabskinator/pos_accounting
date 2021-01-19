<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('wo_mod')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$offerred = new Offered_service();
	$offerred_services = $offerred->get_active('offered_services',array('1' ,'=','1'));

	$coach = new Coach();
	$coaches = $coach->get_active('coaches',array('1' ,'=','1'));

	$member_id = Input::get('member_id');
	$member_name = "";

	if(is_numeric($member_id)){
		$member = new Member($member_id);
		$member_name = $member->data()->lastname;
	}

?>
	<div id="page-content-wrapper">


		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Assessment
				</h1>

			</div>
			<?php include_once "includes/assessment_nav.php" ?>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
					<select class='form-control' name="disc_id" id="disc_id">
						<option value="">Select Discipline</option>
						<?php
							if($offerred_services){
								foreach($offerred_services as $os){
									echo "<option value='$os->id'>$os->name</option>";
								}
							}
						?>
					</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class='form-control' id='member_id'>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select class='form-control' name="coach_id" id="coach_id">
							<option value="">Select Coach</option>
							<?php
								if($coaches){
									foreach($coaches as $os){
										echo "<option value='$os->id'>$os->name</option>";
									}
								}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class='form-control' id='dt' placeholder="Date">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class='form-control' id='session_number' placeholder="Session Number">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<input type="text" class='form-control' id='goal' placeholder="Goal">
					</div>
				</div>
			</div>

			<br>
			<div id="form_container"><h5 class='text-danger'>Choose discipline.</h5></div>
			<hr>

			<div class="form-group">
			<strong>Analysis</strong>
			<textarea name="analysis" id="analysis" class='form-control' placeholder='Analysis'cols="30" rows="10"></textarea>
			</div>
			<div class="form-group">
				<strong>Recommendations</strong>
				<textarea name="recommendation" class='form-control'  id="recommendation" placeholder='Recommendations'cols="30" rows="10"></textarea>
			</div>
			<button class='btn btn-primary' id='saveAssessment'>
				Save Assessment
			</button>
		</div>
	</div> <!-- end page content wrapper-->


	<script>
		$(function(){

			var mem_select2 = $('#member_id');
			var member_id = "<?php echo $member_id; ?>";
			var member_name = "<?php echo $member_name; ?>";


			mem_select2.select2({
				placeholder: 'Search client',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'members'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			if(member_id != '0'){
				mem_select2.select2('data',{ id: member_id, text: member_name });
			}
			$('body').on('change','#disc_id',function(){
				var id= $(this).val();
				getForm(id);

			});
			function getForm(id){
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
					beforeSend: function(){
						$('#form_container').html('Loading form...');
					},
				    data: {functionName:'getAssessmentForm', id:id},
				    success: function(data){
					    $('#form_container').html(data);
				    },
				    error:function(){

				    }
				});
			}
			$('body').on('click','#saveAssessment',function(){
				saveAssessment();
			});
			function saveAssessment(){
				var disc_id = $('#disc_id').val();
				var member_id = $('#member_id').val();
				var coach_id = $('#coach_id').val();
				var session_number = $('#session_number').val();
				var dt = $('#dt').val();
				var goal = $('#goal').val();
				var recommendation = $('#recommendation').val();
				var analysis = $('#analysis').val();

				var arr_assess = [];

				$('.tbl_assess tbody tr').each(function(){
					var row = $(this);
					var star = row.children().eq(1).find('input').val();
					var duration = row.children().eq(2).find('input').val();
					var remarks = row.children().eq(3).find('input').val();
					var status = row.children().eq(4).find('select').val();
					var id = row.attr('data-id');
					arr_assess.push({
						id:id,
						star:star,
						duration:duration,
						remarks:remarks,
						status:status
					});
				});


				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {
					    functionName:'saveAssessmentData',
					    data: JSON.stringify(arr_assess),
					    disc_id:disc_id,
					    member_id:member_id,
					    coach_id:coach_id,
					    dt:dt,
					    goal:goal,
					    session_number:session_number,
					    analysis:analysis,
					    recommendation:recommendation,
				    },
				    success: function(data){
					    alertify.alert(data,function(){
						    location.href='assessment_history.php';
					    });

				    },
				    error:function(){

				    }
				});
			}

		});
	</script>
<?php	require_once '../includes/admin/page_tail2.php';