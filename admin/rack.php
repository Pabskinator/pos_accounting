<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('rack')){
		// redirect to denied page
		Redirect::to(1);
	}

	if(Input::exists()){
		$branch_id = Input::get('branch_id');
	} else {
		$branch_id = $user->data()->branch_id;
	}

	$rack = new Rack();
	$racks = $rack->getDetailedRacks($user->data()->company_id,$branch_id);



	$branch = new Branch();
	$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));



?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Racks
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('rackflash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('rackflash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/inventory_nav.php'; ?>
				<?php if($user->hasPermission('rack_m')){ ?>
					<br>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='addrack.php' title='Add Rack'>
							<span class='glyphicon glyphicon-plus'></span>
						<span class='hidden-xs'>
						Add Rack
						</span>
						</a>
						<a class='btn btn-default' id='btnDefaultRack' href='#' title='Default Rack'>
							<span class='glyphicon glyphicon-list'></span>
						<span class='hidden-xs'>
						Manage default
						</span>
						</a>
						<a class='btn btn-default' href='rack_tagging.php' title='Tagging'>
							<span class='glyphicon glyphicon-tag'></span>
						<span class='hidden-xs'>
						Tagging
						</span>
						</a>
						<a class='btn btn-default' href='rack-stock-custodian.php' title='Stock Man'>
							<span class='glyphicon glyphicon-user'></span>
						<span class='hidden-xs'>
						Change Stock man
						</span>
						</a>
					</div>
				<?php } ?>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Racks</div>
					<div class="panel-body">
						<?php if($branches){
							?>
							<form method="POST">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<select name="branch_id" id="branch_id" class='form-control'>
											<option value="0">All</option>
											<?php
												foreach($branches as $b){
													$selectedbranch = '';
													if($b->id == $branch_id){
														$selectedbranch = 'selected';
													}

													echo "<option value='$b->id' $selectedbranch>$b->name</option>";
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="submit" value='Submit' class='btn btn-default'>
									</div>
								</div>
							</div>
							</form>
							<?php
							}
						?>
						<?php if($racks) { ?>
						<div id="no-more-tables">
							<table id='tblRacks' class='table'>
								<thead>
								<tr>

									<TH>Rack Name</TH>
									<TH>Description</TH>
									<TH>Created</TH>
									<?php if($user->hasPermission('rack_m')){ ?>
										<TH>Actions</TH>
									<?php } ?>
								</tr>
								</thead>
								<tbody>
								<?php
									$options = [];
									$branchRacks=[];

									foreach($racks as $t) {
										$options[$t->id] = $t->rack;
										$branchRacks[$t->branch_id][$t->id] = $t->rack;
										$bname = "";
										if($t->branch_id){

											$bname = $t->branch_name;
										}

										?>
										<tr>
											<td style='border-top:1px solid #ccc;' data-title='Rack'><?php echo escape($t->rack) ?> <small class='span-block text-danger'><?php echo $bname; ?></small><small class='span-block text-success'>Tag: <?php echo ($t->tag_name) ? $t->tag_name : 'None'; ?></small></td>
											<td style='border-top:1px solid #ccc;'  data-title='Description'><?php echo ($t->description) ? escape($t->description) : "<i class='fa fa-ban'></i>"; ?></td>
											<td style='border-top:1px solid #ccc;'  data-title='Created'><?php echo escape(date('m/d/Y H:i:s A', $t->created)) ?></td>
											<?php if($user->hasPermission('rack_m')){ ?>
												<td style='border-top:1px solid #ccc;' >
													<?php if($t->branch_id == $user->data()->branch_id || $user->hasPermission('inventory_all')){
														?>
														<a class='btn btn-primary' href='addrack.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $t->id); ?>' title='Edit Rack'><span class='glyphicon glyphicon-pencil' ></span></a>
														<?php if($t->rack != 'Display') { ?>
															<a href='#' class='btn btn-primary deleteRack' id="<?php echo Encryption::encrypt_decrypt('encrypt', $t->id); ?>" title='Delete Rack'><span class='glyphicon glyphicon-remove'></span></a>
														<?php } ?>
														<?php
													} ?>

												</td>
											<?php } ?>
										</tr>
										<?php

									}
								?>
								</tbody>
							</table>
						</div>
					</div>
					<?php   }  else { ?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
					<div id="no-more-tables">
					<table id='tblDefaultRack' class='table'>
						<thead>
						<tr><th>Branch</th><th>Good</th><th><?php echo DAMAGE_LABEL; ?></th><th><?php echo INCOMPLETE_LABEL; ?></th><th><?php echo "BO Section"; ?></th></tr>
						</thead>
						<tbody>

						<?php
							if($branches){
								//$options = "<select class='form-control'>" . $options . "</select>";
								$def_racks = new Rack();
								$arr_default = [];
								foreach($branches as $b){
									$mydefaults = $def_racks->getRackDefaults($b->id);
									$prevGood ="0";
									$prevIssue = "0";
									$prevSurplus = "0";
									$prevBO= "0";
									if($mydefaults){
										$prevGood = ($mydefaults->good_rack) ? $mydefaults->good_rack: 0;
										$prevIssue = ($mydefaults->issues_rack) ? $mydefaults->issues_rack : 0;
										$prevSurplus = ($mydefaults->surplus_rack) ? $mydefaults->surplus_rack : 0;
										$prevBO = ($mydefaults->bo_section) ? $mydefaults->bo_section : 0;

										$arr_default[] = [
											'id' => $b->id,
											'good' => $prevGood,
											'bo_section' => $prevBO,
											'rack_bo' => $mydefaults->rack_bo,
											'rack_good' => $mydefaults->rack_good,
											'rack_issue' => $mydefaults->rack_issues,
											'rack_surplus' => $mydefaults->rack_surplus,
											'damage' => $prevIssue,
											'surplus' => $prevSurplus
										];
									}

									$optgood = "<input id='rackbranchgood".$b->id."' class='form-control rack_select2' data-branch_id='$b->id'>";
									$optissue = "<input id='rackbranchdamage".$b->id."' class='form-control rack_select2' data-branch_id='$b->id'>";
									$optsurplus = "<input id='rackbranchsurplus".$b->id."' class='form-control rack_select2' data-branch_id='$b->id'>"; // incomplete
									$optbo = "<input id='rackbranchbo".$b->id."' class='form-control rack_select2' data-branch_id='$b->id'>"; // bo
									/*foreach($options as $rack_id => $rack_value){
										if(!isset($branchRacks[$b->id][$rack_id])) continue;
										$selectedgood = "";
										if($prevGood == $rack_value){
											$selectedgood = "selected";
										}
										$selectedissue = "";
										if($prevIssue == $rack_value){
											$selectedissue = "selected";
										}
										$selectedsurplus = "";
										if($prevSurplus == $rack_value){
											$selectedsurplus = "selected";
										}
										$optgood .= "<option value='$rack_id' $selectedgood>$rack_value</option>";
										$optissue .= "<option value='$rack_id' $selectedissue>$rack_value</option>";
										$optsurplus .= "<option value='$rack_id' $selectedsurplus>$rack_value</option>";

									}
									$optgood .= "</select>";
									$optissue .= "</select>";
									$optsurplus .= "</select>"; */


									echo "<tr  data-branch_id='$b->id' >";
									echo "<td data-title='Branch'>$b->name</td>";
									echo "<td data-title='Good'>$optgood</td>";
									echo "<td data-title='".DAMAGE_LABEL."'>$optissue</td>";
									echo "<td data-title='".INCOMPLETE_LABEL."'>$optsurplus</td>";
									echo "<td data-title='BO'>$optbo</td>";
									echo "</tr>";
								}
							}
						?>

						</tbody>
					</table>
						<?php
							echo "<input type='hidden' value='".json_encode($arr_default)."' id='rack_defaults' >";
						?>
						</div>
					<hr>
					<div class='text-right'>
						<button class='btn btn-default' id='btnSaveDefaults'>SAVE</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function(){
			$(".deleteRack").click(function(){
				if(confirm("Are you sure you want to delete this record?")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'racks'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
			var rack_defaults = [];



			$('.rack_select2').select2({
				placeholder: 'Search rack',
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
							functionName:'racks',
							branch_id: $(this).attr('data-branch_id')
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.rack,
									slug: item.rack,
									id: item.id
								}
							})
						};
					}
				}
			});
			try{

				rack_defaults = JSON.parse($('#rack_defaults').val());
				for(var i in rack_defaults){
					var det_rack = rack_defaults[i];
					if(det_rack.id){
						console.log(det_rack);
						if(det_rack.good != '0'){
							$('#rackbranchgood'+det_rack.id).select2('data', {id: det_rack.good, text: det_rack.rack_good});
						}
						if(det_rack.damage != '0'){
							$('#rackbranchdamage'+det_rack.id).select2('data', {id: det_rack.damage, text: det_rack.rack_issue});
						}
						if(det_rack.surplus != '0'){
							$('#rackbranchsurplus'+det_rack.id).select2('data', {id: det_rack.surplus, text: det_rack.rack_surplus});
						}
						if(det_rack.bo_section != '0'){
							$('#rackbranchbo'+det_rack.id).select2('data', {id: det_rack.bo_section, text: det_rack.rack_bo});
						}
					}
				}
			} catch(e){
				console.log("error in parsing rack defaults");
			}
			$('#tblRacks').dataTable({
				iDisplayLength: 100
			});
			$('body').on('click','#btnDefaultRack',function(e){
				e.preventDefault();

				$('#myModal').modal('show');

			});
			$('body').on('click','#btnSaveDefaults',function(){
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.html('Loading...');
				btncon.attr('disabled',true);

				if($('#tblDefaultRack tbody tr').length > 0){
					var arr = [];
					$('#tblDefaultRack tbody tr').each(function(){
						var row = $(this);
						var branch_id = row.attr('data-branch_id');
						var good_rack = $('#rackbranchgood'+ branch_id).val();
						var issues_rack = $('#rackbranchdamage'+ branch_id).val();
						var surplus_rack = $('#rackbranchsurplus'+ branch_id).val();
						var bo_rack = $('#rackbranchbo'+ branch_id).val();;
						arr.push({branch_id:branch_id,good_rack:good_rack, issues_rack:issues_rack,surplus_rack:surplus_rack,bo_rack:bo_rack});
					});
					if(arr.length>0){
						$.ajax({
							url:'../ajax/ajax_query.php',
							type:'POST',
							data: {functionName:'saveRackDefaults',arr:JSON.stringify(arr)},
							success: function(data){
								alertify.alert(data);
								$('#myModal').modal('hide');
								btncon.html(btnoldval);
								btncon.attr('disabled',false);
							},
							error:function(){

								btncon.html(btnoldval);
								btncon.attr('disabled',false);
							}
						})
					} else {
						btncon.html(btnoldval);
						btncon.attr('disabled',false);
					}
				} else {
					btncon.html(btnoldval);
					btncon.attr('disabled',false);

				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>