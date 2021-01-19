<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory_adj')){
		// redirect to denied page
		Redirect::to(1);
	}
	$gt = new Rack();
	$allracks = $gt->getAllRacks($user->data()->company_id);
	$rackselect = "<select class='form-control rackp'>";
	$rackselect .= "<option value=''>--select rack-</option>";
	foreach ($allracks as $irack) {
		$rackselect.= "<option value='".$irack->id."'>".$irack->rack."</option>";
	}
	$rackselect .='</select>';
	$witnesscls = new Witness();
	$witnesslist = $witnesscls->get_active('witnesses',array('company_id','=',$user->data()->company_id));
	$optwitness ='';
	if($witnesslist){
		foreach($witnesslist as $wit){
			$optwitness .= "<option value='{$wit->lastname}, {$wit->firstname} {$wit->middlename}'>{$wit->lastname}, {$wit->firstname} {$wit->middlename}</option>";
		}
	}
?>
<?php
	$item = new Product();
	$items = $item->get_active('items',array('company_id' ,'=',$user->data()->company_id));
?>
	<link rel="stylesheet" href="../css/swipebox.css">
<?php
	$selecitem ='';
	foreach($items as $i):
		if($i->item_type != -1) continue;
		?>

		<?php $selecitem .= '<option value="'.escape($i->id).'">'.$i->barcode .":".$i->item_code .":". $i->description.'</option>'; ?>
	<?php endforeach; ?>


	<!-- Page content -->
	<div id="page-content-wrapper">




		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Item Audit
				</h1>
			</div>
			<?php include 'includes/inventory_nav.php'; ?>
			
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<div class="form-group">
						<input type="text" class='form-control' id='txtSearchRack' placeholder="Search Rack">
					</div>
				</div>
				<div class="col-md-4">
					<?php if($user->hasPermission('inventory_all')){
						?>
						<select name="branch_audit" id="branch_audit" class='form-control'>
							<?php
								$branch = new Branch();
								$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
							?>
							<?php 
								foreach($branches as $b):
									$a = $user->data()->branch_id;
									if($a==$b->id){
										$selected='selected';
									} else {
										$selected='';
									}
								?>
								<option value="<?php echo escape($b->id); ?>" <?php echo $selected; ?>><?php echo escape($b->name); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="help-block">Enter the Branch name</span>
					<?php
					}
					?>

				</div>
				<div class="col-md-12">
					<p id='stillauditng'></p>
				</div>
			</div>

			<div id="holdersp"></div>

		</div>

	</div> <!-- end page content wrapper-->


	<!-- MODALS -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="mtitle"></h4>
				</div>
				<div class="modal-body" id="mbody">

				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div class="modal fade" id="modalFoundItem" tabindex="-1" role="dialog" aria-labelledby="modalFoundItemLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="fititle"></h4>
				</div>
				<div class="modal-body" id="fibody">

					<input type="hidden" id='fi_rackid'> <input type="hidden" id='fi_auditid'>
					<input type="hidden" id='fi_branchid'>
					<div class="form-group">
						<strong>Item</strong>
						
						<input type='text' name="fi_item" id="fi_item" class='selectitem'>

					</div>
					<div class="form-group">
						<strong>Quantity</strong>
						<input type="text" class='form-control' id='fi_qty'>
					</div>
					<div class="form-group">
						<strong>Witness 1</strong>
						<select name="fi_witness1" id="fi_witness1" class='form-control'>
							<option value="">Choose Witness</option>
							<?php  echo $optwitness; ?>
						</select>
					</div>
					<div class="form-group">
						<strong>Witness 2</strong>
						<select name="fi_witness2" id="fi_witness2" class='form-control'>
							<option value="">Choose Witness</option>
							<?php  echo $optwitness; ?>
						</select>
					</div>
					<div class="form-group">
						<strong>Remarks</strong>
						<input type="text" class='form-control' id='fi_remarks'>
					</div>
					<div class="form-group">
						<div>
							<button class='btn btn-primary' id='saveFI'>Save</button>
						</div>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div id="imagecon">
		<span style='cursor:pointer; position:absolute;right:2px;top:2px;font-size:1.1em;' class='glyphicon glyphicon-remove-sign removeImage'></span>
		<img src="" alt="Image" />
	</div>

	<div class="modal fade" id="modalAmmend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="atitle"></h4>
				</div>
				<div class="modal-body" id="abody">
					<p class='text-danger' id='amend_warning'></p>
					<form id="data" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-3">
							GOOD QUANTITY
						</div>
						<div class="col-md-9">
							<input type="text" id='goodqty' name='goodqty' class='form-control'/>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							<?php echo DAMAGE_LABEL; ?>
						</div>
						<div class="col-md-9">
							<input type="text" id='damageqty' name='damageqty' class='form-control'/>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							<?php echo MISSING_LABEL; ?>
						</div>
						<div class="col-md-9">
							<input type="text" id='missingqty' name='missingqty' class='form-control'/>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							<?php echo INCOMPLETE_LABEL; ?>
						</div>
						<div class="col-md-9">
							<input type="text" id='incqty' name='incqty' class='form-control'/>
						</div>
					</div>
					<?php if(trim(OTHER_ISSUE_LABEL)){
						?>
						<hr />
						<div class="row">
							<div class="col-md-3">
								<?php echo OTHER_ISSUE_LABEL; ?>
							</div>
							<div class="col-md-9">
								<input type="text" id='otherissueqty' name='otherissueqty' class='form-control'/>
							</div>
						</div>
					<?php
						}?>
					<hr />
					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
								ATTACHMENT
							</div>
							<div class="col-md-9">
								<input type="file"  name='file[]' class='form-control'/>
							</div>
						</div>
					</div>
					<div id="moreAttCon"></div>
						<div style='margin-top:10px;'>
							<div class="row">
							<div class="col-md-3"></div>
							<div class="col-md-9">
							<button id='moreAtt' class='btn btn-default btn-sm'>Add more</button>
							</div>
							</div>
						</div>

					<hr />
					<div class="row">
						<div class="col-md-3">
							WITNESS 1
						</div>
						<div class="col-md-9">
							<select name="awitness1" id="awitness1" class='form-control'>
								<option value="">Choose Witness</option>
								<?php  echo $optwitness; ?>
							</select>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							WITNESS 2
						</div>
						<div class="col-md-9">
							<select name="awitness2" id="awitness2" class='form-control'>
								<option value="">Choose Witness</option>
								<?php  echo $optwitness; ?>
							</select>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							Remarks
						</div>
						<div class="col-md-9">
							<input type="text" id='aremarks' name='aremarks'  class='form-control' placeholder="Optional"/>
						</div>
					</div>
					<hr />
					<input type="hidden" id='abranch'  name='abranch' />
					<input type="hidden" id='abc' name='abc' />

					<input type="hidden"  id='arack' name='arack' />
					<input type="hidden" id='aid' name='aid'  />
					<input type="submit" class='btn btn-default' value='SAVE' id='ammendSave' />
					</form>
				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="ctitle"></h4>
				</div>
				<div class="modal-body" id="cbody">
					<div class="row">
						<div class="col-md-3">
							GOOD QUANTITY
						</div>
						<div class="col-md-9">
							<input type="text" id='cgoodqty' class='form-control' disabled/>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							WITNESS 1
						</div>
						<div class="col-md-9">

							<select name="cwitness1" id="cwitness1" class='form-control'>
								<option value="">Choose Witness</option>
								<?php  echo $optwitness; ?>
							</select>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							WITNESS 2
						</div>
						<div class="col-md-9">

							<select name="cwitness2" id="cwitness2" class='form-control'>
								<option value="">Choose Witness</option>
								<?php  echo $optwitness; ?>
							</select>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							Remarks
						</div>
						<div class="col-md-9">
							<input type="text" id='cremarks' placeholder="Optional" class='form-control'/>
						</div>
					</div>
					<hr />
					<input type="hidden" id='cbranch' />
					<input type="hidden" id='cbc' />

					<input type="hidden" id='crack' />
					<input type="hidden" id='cid' />
					<input type="button" class='btn btn-default' value='SAVE' id='confirmSave' />
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="modalPartition" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:100%;height:100%;'>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="ptitle"></h4>
				</div>
				<div class="modal-body" id="pbody">
					<div class="row">
						<div class="col-md-3">
							GOOD QUANTITY
						</div>
						<div class="col-md-9">
							<input type="text" id='pgoodqty' class='form-control' disabled/>
						</div>
					</div>
					<hr />
					<table class="partitiontable table">
						<thead>
						<tr>
							<td>Rack</td>
							<td>Quantity</td>
							<td></td>
						</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					<input type="text" class='btn btn-default' id="addmorerack" value='Add More'/>
					<hr />
					<div class="row">
						<div class="col-md-3">
							WITNESS 1
						</div>
						<div class="col-md-9">

							<select name="pwitness1" id="pwitness1" class='form-control'>
								<option value="">Choose Witness</option>
								<?php  echo $optwitness; ?>
							</select>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							WITNESS 2
						</div>
						<div class="col-md-9">
							<select name="pwitness2" id="pwitness2" class='form-control'>
								<option value="">Choose Witness</option>
								<?php  echo $optwitness; ?>
							</select>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-3">
							Remarks
						</div>
						<div class="col-md-9">
							<input type="text" id='premarks' placeholder="Optional" class='form-control'/>
						</div>
					</div>
					<hr />
					<input type="hidden" id='pbranch' />
					<input type="hidden" id='pbc' />

					<input type="hidden" id='prack' />
					<input type="hidden" id='pid' />
					<div class='text-right'>
						<input type="button" class='btn btn-default' value='SAVE' id='partitionSave' />
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="modalHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:80%;' >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="htitle"></h4>
				</div>
				<div class="modal-body" id="hbody">

				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="loading" style='display:none;'>Loading&#8230;</div>
	<!-- SCRIPTS -->
	<script src='../js/swipebox.js'></script>
	<script>
		getStillAuditing();
		function getStillAuditing(){
			var branch = $('#branch_audit');
			var branch_id = (branch.val()) ? branch.val() : localStorage['branch_id'];
			$.ajax({
				url:'../ajax/ajax_inventoryadjustment.php',
				type:'post',
				beforeSend:function(){
					$('#stillauditng').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
				},
				data: {branch:branch_id,q:10},
				success: function(data){
					$("#stillauditng").html(data);
				},
				error: function(){
					alertify.alert('Problem loading content. The page will be refresh',function(){
						location.href='inventory_adjustments.php';
					});
				}
			});
		}
		getRackThumb('');
		var timer;
		$('body').on('keyup','#txtSearchRack',function(){
			clearTimeout(timer);
			var r = $(this).val();
			timer = setTimeout(function() {
				getRackThumb(r);
			}, 1000);
		});
		$('body').on('change','#branch_audit',function(){
			getRackThumb('');
			getStillAuditing();
		});
		function getRackThumb(r){
			var branch = $('#branch_audit');
			var branch_id = (branch.val()) ? branch.val() : localStorage['branch_id'];
			$.ajax({
				url:'../ajax/ajax_inventoryadjustment.php',
				type:'post',
				beforeSend:function(){
					$('#holdersp').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
				},
				data: {rack:r,branch:branch_id,q:1},
				success: function(data){
					$("#holdersp").html(data);
				},
				error: function(){
					alertify.alert('Problem loading content. The page will be refresh',function(){
						location.href='inventory_adjustments.php';
					});
				}
			});
		}
		function getThisGroup(g){
			$('#txtSearchRack').val(g);
			getRackThumb(g);
		}
		function getThisRack(r,b,c){
			var rack = r;
			var branch = b;
			$('.loading').show();
			$.ajax({
				url:'../ajax/ajax_inventoryadjustment.php',
				type:'post',
				beforeSend:function(){
					$('#mbody').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
				},
				data: {rack:r,branch:b,company:c,q:2},
				success: function(data){
					$("#mbody").html(data);
					$("#myModal").modal('show');
					$('.loading').hide();
				},
				error: function(){
					alertify.alert('Problem loading content. The page will be refresh',function(){
						location.href='inventory_adjustments.php';
					});
					$('.loading').hide();
				}
			});
		}
		$('#awitness1,#awitness2').change(function(){
			var w1 = $('#awitness1').val();
			var w2 = $('#awitness2').val();
			if(w1 && w2){
				if(w1 == w2){
					alertify.alert('Invalid witness.');
					$(this).val('');
				}
			}

		});
		$('#cwitness1,#cwitness2').change(function(){
			var w1 = $('#cwitness1').val();
			var w2 = $('#cwitness2').val();
			if(w1 && w2){
				if(w1 == w2){
					alertify.alert('Invalid witness.');
					$(this).val('');
				}
			}
		});
		$('#pwitness1,#pwitness2').change(function(){
			var w1 = $('#pwitness1').val();
			var w2 = $('#pwitness2').val();
			if(w1 && w2){
				if(w1 == w2){
					alertify.alert('Invalid witness.');
					$(this).val('');
				}
			}
		});
		function auditThis(r,b,c){

			$.ajax({
				url:'../ajax/ajax_inventoryadjustment.php',
				type:'post',
				data: {rack:r,branch:b,company:c,q:3},
				success: function(data){
					if(data == "1"){
						$.ajax({
							url:'../ajax/ajax_inventoryadjustment.php',
							type:'post',
							data: {rack:r,branch:b,company:c,q:2},
							success: function(data){
								$("#mbody").empty();
								$("#mbody").append(data);
								$("#myModal").modal('show');
							},
							error: function(){
								alertify.alert('Problem loading content. The page will be refresh',function(){
									location.href='inventory_adjustments.php';
								});
							}
						});
					} else {
						alertify.alert(data);
					}
				},
				error: function(){
					alertify.alert('Problem loading content. The page will be refresh',function(){
						location.href='inventory_adjustments.php';
					});
				}
			});
		}
		function ammendThis(rack,branch,item_id,qty,item_code,aid,isAudited){

			$("#atitle").empty();
			$("#atitle").append("ITEM: " + item_code );
			$('#goodqty').val('');
			$('#missingqty').val('');
			$('#incqty').val('');
			$('#otherissueqty').val('');
			$('#damageqty').val('');
			$('#awitness1').val('');
			$('#awitness2').val('');
			$('#aremarks').val('');
			$('#goodqty').val(qty);
			$('#arack').val(rack);
			$('#abranch').val(branch);
			$('#abc').val(item_id);
			$('#aid').val(aid);
			if(isAudited == '1'){
				$('#goodqty').attr('readonly',false);
				$('#aremarks').attr('readonly',false);
			} else {
				$('#goodqty').attr('readonly',false);
				$('#aremarks').val('Set beginning inventory');
				$('#aremarks').attr('readonly',false);
			}
			$('#moreAttCon').html('');
			$("#modalAmmend").modal('show');

			getWarningAmend(item_id,qty,branch);
		}

		function getWarningAmend(item_id,qty,branch){

			$.ajax({
			    url:'../ajax/ajax_inventory.php',
			    type:'POST',
			    data: {functionName:'amendWarning',item_id:item_id,qty:qty,branch_id:branch},
			    success: function(data){
					$('#amend_warning').html(data);
			    },
			    error:function(){

			    }
			});

		}
		function confirmThis(rack,branch,item_id,qty,item_code,aid){
			$("#ctitle").empty();
			$("#ctitle").append("ITEM : " + item_code );
			$('#cgoodqty').val('');
			$('#cwitness1').val('');
			$('#cwitness2').val('');
			$('#cremarks').val('');
			$('#cgoodqty').val(qty);
			$('#crack').val(rack);
			$('#cbranch').val(branch);
			$('#cbc').val(item_id);
			$('#cid').val(aid);
			$("#modalConfirm").modal('show');
		}
		function partitionThis(rack,branch,item_id,qty,item_code,aid){
			$("#ptitle").empty();
			$("#ptitle").append("ITEM : " + item_code);
			$('#pgoodqty').val('');
			$('#pgoodqty').val(qty);
			$('#prack').val(rack);
			$('#pbranch').val(branch);
			$('#pbc').val(item_id);
			$('#pid').val(aid);
			$("#pwitness1").val('');
			$("#pwitness2").val('');
			$("#premarks").val('');
			$(".partitiontable > tbody").empty();
			$(".partitiontable > tbody").append("<tr><td><?php echo $rackselect; ?></td><td><input type='text' class='form-control qtyp'/></td><td></td></tr>")
			$("#modalPartition").modal('show');

		}
		$('#myModal').on('hidden.bs.modal', function () {
			getRackThumb($('#txtSearchRack').val());
		});

		$('body').on('keyup','#damageqty,#missingqty,#otherissueqty',function(){

			if($(this).val() && isNaN($(this).val())){
				tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
				$(this).val('');
			}

			var good = $('#goodqty').val();
			var damageqty = $('#damageqty').val();
			var missingqty = $('#missingqty').val();
			var incqty = $('#incqty').val();
			var otherissueqty = $('#otherissueqty').val();

			good = (good) ? good : 0;
			missingqty = (missingqty) ? missingqty : 0;
			damageqty = (damageqty) ? damageqty : 0;
			incqty = (incqty) ? incqty : 0;
			otherissueqty = (otherissueqty) ? otherissueqty : 0;

			var totaldis = parseFloat(missingqty) + parseFloat(damageqty) + parseFloat(incqty) + parseFloat(otherissueqty);
			if(totaldis > parseFloat(good)){
				tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
				$(this).val('');
			}

		});

		$("#moreAtt").click(function(e){
			e.preventDefault();
			$('#moreAttCon').append("<div class='form-group'  style='margin-top:10px;'><div class='row'><div class='col-md-3'></div><div class='col-md-9'><input type='file'  name='file[]' class='form-control'/></div></div></div>")
		});
		$('body').on('click','.btnAttPath',function(e){
			var paths = $(this).attr('data-paths');
			e.preventDefault();
			if(paths.indexOf("||") > 0){
				var splitted = paths.split("||");
				var ob = [];
				for(var i in splitted){
					ob.push({href:splitted[i],title:''});
				}
				$.swipebox(ob);
			} else {

				$.swipebox( [
					{ href:paths, title:'' }
				] );

			}
		});
		$("#ammendSave").click(function(e){
			e.preventDefault();
			var btncontext = $(this);
			btncontext.attr('disabled',true);
			btncontext.val('Loading...');
			var goodqty = $('#goodqty').val();
			var damageqty = $('#damageqty').val();
			if(damageqty == '' || !damageqty){
				damageqty=0;
			}
			var missingqty = $('#missingqty').val();
			if(missingqty == '' || !missingqty){
				missingqty=0;
			}
			var incqty = $('#incqty').val();
			if(incqty == '' || !incqty){
				incqty=0;
			}
			var otherissueqty = $('#otherissueqty').val();
			if(otherissueqty == '' || !otherissueqty){
				otherissueqty=0;
			}

			var witness1 = $('#awitness1').val();
			var witness2 = $('#awitness2').val();
			var remarks = $('#aremarks').val();
			var bc = $('#abc').val();
			var branch = $('#abranch').val();
			var aid = $('#aid').val();
			var rack = $('#arack').val();
			if(witness1 == '' || !witness1 || witness2 == '' || !witness2  ){
				alertify.alert('Please Complete the form (Witness 1, Witness 2)');
				btncontext.attr('disabled',false);
				btncontext.val('SAVE');
			} else {

				var fd = new FormData($('#data')[0]);
				//enc
				fd.append('item_id',bc);
				fd.append('rack',rack);
				fd.append('branch',branch);
				fd.append('q',4);
				fd.append('witness1',witness1);
				fd.append('witness2',witness2);
				fd.append('remarks',remarks);

				$.ajax({
					url:'../ajax/ajax_inventoryadjustment.php',
					type:'post',
					async: false,
					cache: false,
					contentType: false,
					processData: false,
					data: fd,
					success: function(data){

						if (data == 1){
							$.ajax({
								url:'../ajax/ajax_inventoryadjustment.php',
								type:'post',
								data: {rack:rack,branch:branch,company:0,q:2},
								success: function(data){
									$("#mbody").empty();
									$("#mbody").append(data);
									$("#modalAmmend").modal('hide');
									btncontext.attr('disabled',false);
									btncontext.val('SAVE');
								},
								error: function(){
									alertify.alert('Problem loading content. The page will be refresh',function(){
										location.href='inventory_adjustments.php';
									});
									btncontext.attr('disabled',false);
									btncontext.val('SAVE');
								}
							});

						}

					},
					error: function(){
						alertify.alert('Problem loading content. The page will be refresh',function(){
							location.href='inventory_adjustments.php';
						});
					}
				});
			}
		});
		$("#confirmSave").click(function(){
			var goodqty = $('#cgoodqty').val();
			var witness1 = $('#cwitness1').val();
			var witness2 = $('#cwitness2').val();
			var remarks = $('#cremarks').val();
			var bc = $('#cbc').val();
			var branch = $('#cbranch').val();
			var aid = $('#cid').val();
			var rack = $('#crack').val();
			var btncontext = $(this);
			btncontext.attr('disabled',true);
			btncontext.val('Loading...');
			if(witness1 == '' || !witness1 || witness2 == '' || !witness2 ){

				alertify.alert('Please Complete the form (Witness 1, Witness 2)');
				btncontext.attr('disabled',false);
				btncontext.val('SAVE');
			} else {
				$.ajax({
					url:'../ajax/ajax_inventoryadjustment.php',
					type:'post',
					data: {item_id:bc,aid:aid,rack:rack,branch:branch,q:6,goodqty:goodqty,witness1:witness1,witness2:witness2,remarks:remarks},
					success: function(data){
						if (data == 1){
							$.ajax({
								url:'../ajax/ajax_inventoryadjustment.php',
								type:'post',
								data: {rack:rack,branch:branch,company:0,q:2},
								success: function(data){
									$("#mbody").empty();
									$("#mbody").append(data);
									$("#modalConfirm").modal('hide');
									btncontext.attr('disabled',false);
									btncontext.val('SAVE');
								},
								error: function(){
									alertify.alert('Problem loading content. The page will be refresh',function(){
										location.href='inventory_adjustments.php';
									});
									btncontext.attr('disabled',false);
									btncontext.val('SAVE');
								}
							});

						}

					},
					error: function(){
						alertify.alert('Problem loading content. The page will be refresh',function(){
							location.href='inventory_adjustments.php';
						});
					}
				});
			}
		});
		$('body').on('click','.btnStopAudit',function(){
			var rack = $(this).attr('data-rack_id');
			var audit_id =  $(this).attr('data-audit_id');
			var btn = $(this);
			var btnoldval = btn.val();
			btn.val('Loading...');
			btn.attr('disabled',true);
			$.ajax({
				url:'../ajax/ajax_warehouse.php',
				type:'post',
				data: {functionName:'stopAuditRack',rack_id:rack,audit_id:audit_id},
				success: function(data){
					alertify.alert(data,function(){
						location.href='inventory_adjustments.php';
					});

				},
				error:function(){

					btn.val(btnoldval);
					btn.attr('disabled',false);
				}
			});
		});
		$('body').on('click','.btnContinueAudit',function(){
			var rack = $(this).attr('data-rack_id');
			var audit_id =  $(this).attr('data-audit_id');
			var countnoamend = $('#countnoammend').val();


			$.ajax({
				url:'../ajax/ajax_warehouse.php',
				type:'post',
				data: {functionName:'continueAuditRack',rack_id:rack,audit_id:audit_id,countnoamend:countnoamend},
				success: function(data){
					alertify.alert(data,function(){
						location.href='inventory_adjustments.php';
					});

				},
				error:function(){

				}
			});
		});
		$('body').on('click',"#partitionSave",function(){
			var goodqty = $('#pgoodqty').val();
			var rqty =  0;
			var racking = new Array();
			var valid = false;
			$(".partitiontable > tbody > tr").each(function(index){
				var row = $(this);
				var rackqty = row.children().eq(1).find('input').val();
				var selectrack = row.children().eq(0).find('select').val();
				if(selectrack && rackqty){
					valid = true;
					racking[index] = {
						rack: selectrack,
						qty: rackqty
					}
				}
			});
			if(valid){
				var witness1 = $('#pwitness1').val();
				var witness2 = $('#pwitness2').val();
				var remarks = $('#premarks').val();
				var bc = $('#pbc').val();
				var branch = $('#pbranch').val();
				var aid = $('#pid').val();

				var rack = $('#prack').val();
				if(witness1 == '' || !witness1 || witness2 == '' || !witness2  ){
					alertify.alert('Please Complete the form (Witness 1, Witness 2)');
				} else {
					racking = JSON.stringify(racking);
					$.ajax({
						url:'../ajax/ajax_inventoryadjustment.php',
						type:'post',
						data: {item_id:bc,aid:aid,rack:rack,branch:branch,q:8,goodqty:goodqty,witness1:witness1,witness2:witness2,remarks:remarks,racking:racking},
						success: function(data){
							if (data == 1){
								$.ajax({
									url:'../ajax/ajax_inventoryadjustment.php',
									type:'post',
									data: {rack:rack,branch:branch,company:0,q:2},
									success: function(data){
										$("#mbody").empty();
										$("#mbody").append(data);
										$("#modalPartition").modal('hide');
									},
									error: function(){
										alertify.alert('Problem loading content. The page will be refresh',function(){
											location.href='inventory_adjustments.php';
										});
									}
								});

							} else {

								alertify.alert(data);
							}

						},
						error: function(){
							alertify.alert('Problem loading content. The page will be refresh',function(){
								location.href='inventory_adjustments.php';
							});
						}
					});
				}
			} else {
				alertify.alert('Please allocate the item properly');
			}
		});
		$('body').on('change','#auditHistory',function(){
			var audit_id = $(this).val();
			if(audit_id){
				$.ajax({
					url:'../ajax/ajax_inventoryadjustment.php',
					type:'post',
					data: {aid:audit_id,q:5},
					success: function(data){
						$("#htitle").empty();
						$("#hbody").append("Audit History");
						$("#hbody").empty();
						$("#hbody").append(data);
						$("#modalHistory").modal('show');
					},
					error: function(){
						alertify.alert('Problem loading content. The page will be refresh',function(){
							location.href='inventory_adjustments.php';
						});
					}
				});
			}
		});

		$("body").on('change',".rackp",function(){
			if ($(this).val() == $('#prack').val()){
				$(this).val('');

				alertify.alert('Cannot allocate to same rack');
				return;
			}
			var curcon = $(this);
			var rack = $(this).val();
			var branch = $('#pbranch').val();
			$.ajax({
				url:'../ajax/ajax_inventoryadjustment.php',
				type: 'POST',
				data: {rack:rack,branch:branch,q:7},
				success: function(data){
					if(data=="1"){
						curcon.val('');
						alertify.alert('Cannot allocate to the destination rack because it is in auditing status.');
					}
				}
			});
		});
		$("body").on('click',"#addmorerack",function(){
			$(".partitiontable > tbody").append("<tr><td><?php echo $rackselect; ?></td><td><input type='text' class='form-control qtyp'/></td><td></td></tr>")

		});

		$('body').on('click','#addfounditem',function(){
			var auditid = $(this).attr('data-auditid');
			if(auditid == 0){
				alertify.alert('Start Audit First');
				return;
			}
			$('#fi_item').select2('val',null);
			$('#fi_qty').val('');
			$('#fi_remarks').val('');
			$('#fi_witness1').val('');
			$('#fi_witness2').val('');
			$('#fi_rackid').val($(this).attr('data-rackid'));
			$('#fi_branchid').val($(this).attr('data-branchid'));
			$('#fi_auditid').val(auditid);
			$('#modalFoundItem').modal('show');
		});


		$('#saveFI').click(function(){
			var item = $('#fi_item').val();
			var qty = $('#fi_qty').val();
			var remarks = $('#fi_remarks').val();
			var witness1 = $('#fi_witness1').val();
			var witness2 = $('#fi_witness2').val();
			var rackid = $('#fi_rackid').val();
			var branchid = $('#fi_branchid').val();
			var auditid = $('#fi_auditid').val();
			var btncontext = $(this);
			btncontext.attr('disabled',true);
			btncontext.html('Loading...');

			$.ajax({
				url:'../ajax/ajax_inventoryadjustment.php',
				type: 'POST',
				data: {witness1:witness1,remarks:remarks,witness2:witness2,item:item,qty:qty,rackid:rackid,branchid:branchid,auditid:auditid,q:9},
				success: function(data){
						alertify.alert(data,function(){
							$('#modalFoundItem').modal('hide');
							var branch = $('#branch_audit');
							var branch_id = (branch.val()) ? branch.val() : localStorage['branch_id'];
							$.ajax({
								url:'../ajax/ajax_inventoryadjustment.php',
								type:'post',
								data: {rack:rackid,branch:branch_id,company:0,q:2},
								success: function(data){
									$("#mbody").empty();
									$("#mbody").append(data);
									btncontext.attr('disabled',false);
									btncontext.html('Save');
								},
								error: function(){
									alertify.alert('Problem loading content. The page will be refresh',function(){
										location.href='inventory_adjustments.php';
									});
									btncontext.attr('disabled',false);
									btncontext.html('Save');
								}
							});

						});

				}
			});

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>