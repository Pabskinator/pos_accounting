<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('supplier')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$cf = new Custom_field();
	$cfd = new Custom_field_details();
	$getmember = $cf->getcustomform('members',$user->data()->company_id);
	$custom_arr = ['address','telephone','cellphone','fax','contact1','contact2','terms','payment_type','credit_limit','tin','remarks','email','member_since','agent','invoice','sales_man','tax_type','member_num','k_type'];

	$alldata = $cfd->getAllData($user->data()->company_id,$getmember->id);

	foreach($alldata as $data){
		$f_label = $data->field_label;
		$c_visible = $data->is_visible;
		$name = $data->field_name;
		$f = "f_".$name;
		$c= "c_".$name;
		$$f = $f_label;
		$$c = $c_visible;
		//echo $name .  " " . $$f . " - " . $$c . "<br>";
	}
?>


	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Settings </h1>

			</div>
			<?php
				// get flash message if add or edited successfully

				if(Session::exists('flash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}

			?>
			<div class="row">
				<form class="form-horizontal" id='allfield' action="" method="POST">
					<fieldset>
						<legend></legend>
						<div class="form-group">
							<label class="col-md-4 control-label" for="name">Label Name</label>
							<div class="col-md-4">
								<input id="name" name="name" placeholder="Name" class="form-control input-md" type="text" value="<?php echo isset($getmember->label_name) ?$getmember->label_name:''; ?>">
								<span class="help-block"></span>
							</div>
						</div>

						<table id='tblForm' class='table'>
							<thead>
							<tr><th>Field</th><th>Label</th><th>Show/Hide</th><th></th></tr>
							</thead>
							<tbody>
							<?php foreach($custom_arr as $arr){
								$f = "f_".$arr;
								$c= "c_".$arr;
								$$f = isset($$f) ? $$f : '';
								$$c =  (isset($$c) && !empty($$c)) ? 'Checked'  : '';

								?>
								<tr>
									<td><?php echo ucwords($arr); ?></td>
									<td><input type='text' class='form-control'   name='<?php echo $f; ?>' id='<?php echo $f; ?>' value="<?php echo $$f; ?>"></td>
									<td><input type="checkbox" id='<?php echo $c; ?>' name='<?php echo $c; ?>' <?php echo $$c; ?> /></td>
									<td></td>
								</tr>
								<?php
							}?>



							<?php
								if(isset($getmember->other_field)){
									$countjd = count(json_decode($getmember->other_field));
									$otherarr= json_decode($getmember->other_field,true);
									$cusno = 1;
									foreach($otherarr as $ar){
										echo "<tr><td>Custom field $cusno</td>";
										$fid = $ar['field-id'];
										$oldname= $ar['field-label'];
										$fisvisible = $ar['field-visibility'];
										$ftime = $ar['timestamp'];
										if($fisvisible == 1){
											$fisvisible = 'checked';
										} else {
											$fisvisible='';
										}
										echo "<td><input type='text' name='field".$cusno."' class='form-control' value='$oldname'><input type='hidden' value='$fid' name='fid".$cusno."'/><input type='hidden' value='$ftime' name='ftime".$cusno."'/></td><td><input $fisvisible name='checkbox".$cusno."' type='checkbox' /></td></tr>";
										$cusno= $cusno + 1;
									}
								}
							?>

							</tbody>
						</table>


						<div class="form-group">
							<div class="col-md-6">
								<input type='button' class='btn btn-success' id='buttonSave' value='Save'/>
							</div>
							<div class="col-md-6 text-right">
								<input type='button' class='btn btn-success' id='buttonAddNew' value='Add more'/>
							</div>
						</div>
					</fieldset>
				</form>

			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<script src='../js/tojson.js'></script>
	<script>
		$(function(){

			var num='<?php echo isset($cusno) ? $cusno : 1?>';
			$('body').on('click','#buttonAddNew', function(){
				$('#tblForm > tbody').append("<tr class='custom-field'><td>Custom Field "+num+"</td><td><input type='text' name='field"+num+"' class='form-control'></td><td><input name='checkbox"+num+"' type='checkbox' /></td><td><span class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>")
				num = parseInt(num) + 1;
			});
			$('body').on('click','.removeItem',function(){
				$(this).parents('tr').remove();
			});
			$('body').on('click','#buttonSave',function(){
				var jf = ($('#allfield').serializeJSON());
				$.ajax({
					url:'../ajax/ajax_custom_fields.php',
					type:'post',
					data: {jsonfields :jf,functionName:'customMember' },
					success: function(data){
						alertify.alert(data);
					},
					error:function(){

					}
				});
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>