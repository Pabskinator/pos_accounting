<?php
	include_once '../core/admininit.php';

	$service = new Service_attendance();
	$attendance= $service->getAllSignIn(1);

	$offered = new Offered_service();
	$offered = $offered->get_active('offered_services',array('1' ,'=','1'));

	$coach = new Coach();
	$coaches = $coach->get_active('coaches',array('1' ,'=','1'));

?>

<?php include_once 'includes/service/page_head.php'; ?>

<?php include_once 'includes/service/sidebar.php'; ?>

<main>

	<div id="main">

		<h3>List</h3>
		<?php if ($attendance) {
			?>
		<table class='bordered striped highlight'>
			<thead>
			<tr>
				<th data-field="name">Name</th>
				<th data-field="time_in">Time In</th>
				<th data-field="action"></th>
			</tr>
			</thead>

			<tbody>
			<?php foreach($attendance as $att){
				?>
				<tr>
					<td><?php echo $att->member_name; ?></td>
					<td><?php echo date('m/d/Y H:i:s A',$att->time_in); ?></td>
					<td>
						<button data-id='<?php echo $att->id; ?>' data-member_id='<?php echo $att->member_id; ?>' class='waves-effect waves-light btn grey logout'><i class='material-icons'>not_interested</i></button>
					</td>
				</tr>
				<?php
			}?>
			</tbody>
		</table>
			<?php
		} else {
			?>
			<div style='padding:10px;' class="grey lighten-5 z-depth-2">
				 <h3 class='black-text'>No record yet.</h3>
			</div>
			<?php
		}
		?>


	</div>
</main>
<footer>

</footer>
<!-- Modal Structure -->
<div id="modal1" class="modal">
	<div class="modal-content">
		<h5>You are signing out.</h5>


		<form id='formLogin'>
			<div id="checklist">
				<p>Service(s) consumed:</p>
				<div class="row">
				<?php
					if($offered){
						foreach($offered as $of) {
							?>
							<div class="col m4">
							<input id='chk_<?php echo $of->id ?>' value='<?php echo $of->id ?>' type='checkbox' class='chkServices'><label for='chk_<?php echo $of->id ?>'><?php echo $of->name ?></label>
							</div>
								<?php
						}
					}
				?>

				</div>
			</div>
			<div id='res'></div>
			<input type="hidden" id='member_id' value='0'>
			<input type="hidden" id='att_id' value='0'>
			<br>
			<div class="input-field">
				<div class="input-field col s12">
					<select id='coach_id'><option value="">Select coach</option>
						<?php if($coaches){
							foreach($coaches as $c){
								?>
								<option value="<?php echo $c->id; ?>"> <?php echo $c->name; ?> </option>
								<?php
							}
						}?>
					</select>
					<label>Select Coach</label>
				</div>
			</div>
			<br>

			<div class="input-field">

				<textarea id="remarks" class="materialize-textarea"></textarea>
				<label for="remarks">Remarks (optional)</label>
			</div>

			<br>

		</form>
	</div>
	<div class="modal-footer">

		<a href="#" id='signOut' class="waves-effect waves-green btn">Sign Out</a> &nbsp;&nbsp;&nbsp;
		<a href="#" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>

	</div>
</div>

<script src="../js/jquery.js"></script>
<script src="../js/materialize.min.js"></script>
<script>
	$(function(){
		$('.button-collapse').sideNav();
		$('#modal1').modal();
		$('select').material_select();
		$('body').on('click','.logout',function(){
			var con = $(this);
			var member_id = con.attr("data-member_id");
			var id = con.attr("data-id");
			$('#modal1').modal("open");
			$('#member_id').val(member_id);
			$('#att_id').val(id);
			$('#password').val('');
			$('#remarks').val('');
		});
		$('body').on('click','#signOut',function(e){
			e.preventDefault();
			var arr_con = [];
			var member_id = $('#member_id').val();
			var id = $('#att_id').val();
			var remarks = $('#remarks').val();
			var con  = $(this);
			var oldval = con.html();
			var coach_id = $('#coach_id').val();

			con.attr('disabled',true);
			con.html('Loading...');
			$(".chkServices").each(function(e){
				var chk = $(this);
				if(chk.is(":checked")){
					arr_con.push(chk.val());
				}
			});
			if(coach_id){
				$.ajax({
					url:'../ajax/ajax_member_service.php',
					type:'POST',
					data: {functionName:'signOut',arr_con:JSON.stringify(arr_con),id:id,member_id:member_id,remarks:remarks,coach_id:coach_id},
					success: function(data){
						if(data == 1){
							$('#password').val('');
							Materialize.toast("Invalid credentials",2000,"red lighten-2");
							con.attr('disabled',false);
							con.html(oldval)
						} else {
							Materialize.toast("Signed out successfully.",1200,"green lighten-2",function(){

								location.href="list.php";
							});
						}

					},
					error:function(){

					}
				});
			} else {
				Materialize.toast("Enter coach first.",2000,"red lighten-2");
			}

		});
	});
</script>


<?php include_once 'includes/service/page_tail.php';  ?>
