<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('station')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$cf = new Custom_field();
	$cfd = new Custom_field_details();
	$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);

	$description = $cfd->getIndData('description',$user->data()->company_id,$getstationdet->id);
	$region = $cfd->getIndData('region',$user->data()->company_id,$getstationdet->id);
	$brand = $cfd->getIndData('brand',$user->data()->company_id,$getstationdet->id);
	$package = $cfd->getIndData('package',$user->data()->company_id,$getstationdet->id);

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
							<input id="branchName" name="name" placeholder="Label Name" class="form-control input-md" type="text" value="<?php echo isset($getstationdet->label_name) ?$getstationdet->label_name:''; ?>">
							<span class="help-block"></span>
						</div>
					</div>

				<table id='tblForm' class='table'>
					<thead>
						<tr><th>Field</th><th>Label</th><th>Show/Hide</th><th></th></tr>
					</thead>
					<tbody>
						<tr>
							<td>Description</td>
							<td><input type='text' class='form-control'   name='f_description' id='f_description' value="<?php echo isset($description->field_label) ? $description->field_label :''; ?>"></td>
							<td><input type="checkbox" id='c_description' name='c_description' <?php echo (isset($description->is_visible) && !empty($description->is_visible)) ? 'checked' :''; ?> /></td>
							<td></td>
						</tr>
						<tr>
							<td>Region</td>
							<td><input type='text' class='form-control' name='f_region' id='f_region' value="<?php echo isset($region->field_label) ? $region->field_label :''; ?>"></td>
							<td><input type="checkbox" id='c_region'   name='c_region' <?php echo (isset($region->is_visible) && !empty($region->is_visible)) ? 'checked' :''; ?>/></td>
							<td></td>
						</tr>
						<tr>
							<td>Field 1</td>
							<td><input type='text' class='form-control' id='f_brand' name='f_brand' value="<?php echo isset($brand->field_label) ? $brand->field_label :''; ?>"></td>
							<td><input type="checkbox" id='c_brand' name='c_brand' <?php echo (isset($brand->is_visible) && !empty($brand->is_visible)) ? 'checked' :''; ?>/></td>
							<td></td>
						</tr>
						<tr>
							<td>Field 2</td>
							<td><input type='text' class='form-control' id='f_package' name='f_package' value="<?php echo isset($package->field_label) ? $package->field_label :''; ?>"></td>
							<td><input type="checkbox" id='c_package' name='c_package' <?php echo (isset($package->is_visible) && !empty($package->is_visible)) ? 'checked' :''; ?> /></td>
							<td></td>
						</tr>
						<?php
							if(isset($getstationdet->other_field)){
								$countjd = count(json_decode($getstationdet->other_field));
								$otherarr= json_decode($getstationdet->other_field,true);
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
				    data: {jsonfields :jf,functionName:'customStation' },
				    success: function(data){
					    alertify.alert(data);
				    },
				    error:function(){

				    }
				})
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>