<?php
	include_once '../core/admininit.php';

?>

<?php include_once 'includes/service/page_head.php'; ?>

<?php include_once 'includes/service/sidebar.php'; ?>
<?php
	$member = new Member();
	$members = $member->get_member_record(1,0,200,'',0,0,1);

?>
<main>

	<div id="main">
		<h3>Assessment</h3>
		<form class="col s12">
			<div class="row">

				<div class="input-field col s12 m8">
					<?php
						if($members){
							?>
							<select name="member_id" id="member_id">
							<?php
							foreach($members as $m){
								echo "<option value='$m->id'>$m->lastname</option>";
							}
						?>
							</select>
						<?php
						}
					?>
				</div>
				<div class="input-field col s12 m4">
					<a id="btnSearch" class="waves-effect waves-light btn grey darken-2">Search</a>
				</div>
			</div>
		</form>

		<div id="con"></div>
	</div>
	<div class="fixed-action-btn">
	<a class="btn-floating waves-effect waves-light red" id='addNew'><i class="material-icons">add</i></a>
	</div>
	<div id="modal1" class="modal">
		<div class="modal-content">
			<h3>Add Information</h3>
			<div class="row">
				<div class="input-field col s12">
					<i class="material-icons prefix">account_circle</i>
					<?php
						if($members){
							?>
							<select name="member_id_new" id="member_id_new">
								<?php
									foreach($members as $m){
										echo "<option value='$m->id'>$m->lastname</option>";
									}
								?>
							</select>
							<?php
						}
					?>

					<label for="member_id_new">Name</label>
				</div>

			</div>

			<div class="input-field col s12 m12">
				<div class="row">
					<div class="input-field col s12">
						<i class="material-icons prefix">mode_edit</i>
						<textarea id="member_remarks" class="materialize-textarea"></textarea>
						<label for="member_remarks">Remarks</label>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
			<button id='btnSave' class='waves-effect waves-green btn'>SAVE</button>
		</div>
	</div>

</main>
<footer>

</footer>


<script src="../js/jquery.js"></script>
<script src="../js/materialize.min.js"></script>
<script>
	$(function(){
		$('.modal').modal();

		$('select').material_select();
		$('body').on('click','#addNew',function(){

			$('#modal1').modal('open');
		});
		$('body').on('click','#btnSearch',function(){
			var member_id = $('#member_id').val();
			getAssessment(member_id);
		});
		function getAssessment(member_id){
			if(member_id){
				$.ajax({
					url:'../ajax/ajax_member_service.php',
					type:'POST',
					beforeSend:function(){
						$('#con').html('<div class="progress"><div class="indeterminate"></div></div>');
					},
					data: {functionName:'getAssesstment',member_id:member_id},
					success: function(data){
						$('#con').html(data);
					},
					error:function(){

					}
				});
			}
		}
		$('body').on('click','#btnSave',function(){
			var member_id = $('#member_id_new').val();
			var member_remarks = $('#member_remarks').val();

			if(member_id && member_remarks){
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'saveAssessment', member_id: member_id,member_remarks:member_remarks},
				    success: function(data){
					    Materialize.toast(data,2000,"green lighten-2");
					    getAssessment(member_id);
					    $('#modal1').modal('close');
					    $('#member_id').val(member_id);
					   $('#member_remarks').html('');
					    $('#member_id').material_select();
				    },
				    error:function(){
				        
				    }
				})
			} else {
				Materialize.toast("Invalid information",2000,"red lighten-2");
			}
		});

	});
</script>


<?php include_once 'includes/service/page_tail.php';  ?>
