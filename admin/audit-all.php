<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(false) {
		// redirect to denied page
		Redirect::to(1);
	}
	$user_permbranch = $user->hasPermission('inventory_all');
	$witnesscls = new Witness();
	$witnesslist = $witnesscls->get_active('witnesses',array('company_id','=',$user->data()->company_id));
	$optwitness ='';
	if($witnesslist){
		foreach($witnesslist as $wit){
			$optwitness .= "<option value='{$wit->lastname}, {$wit->firstname} {$wit->middlename}'>{$wit->lastname}, {$wit->firstname} {$wit->middlename}</option>";
		}
	}
?>
	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="container-fluid">
			<h3>Audit Inventories</h3>
			<?php include 'includes/inventory_nav.php'; ?>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<input id='txtSearch' type="text" class='form-control' placeholder='Search Item'>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select  <?php echo (!$user_permbranch) ? 'disabled' : ''; ?> id="branch_id" name="branch_id" class="form-control">
							<option value=''></option>
							<?php
								$branch = new Branch();
								$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
								foreach($branches as $b){
									$a = $user->data()->branch_id;
									if($a==$b->id){
										$selected='selected';
									} else {
										$selected='';
									}
									?>
									<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
									<?php
								}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-3"></div>
				<div class="col-md-3 text-right">
					<?php if($user->hasPermission('dl_inv_audit')){ ?>
					<button class='btn btn-primary btn-sm' id='btnDownloadExcel'><i class='fa fa-download'></i> Download</button>
					<?php } ?>
				</div>
			</div>
			<input type="hidden" id="hiddenpage" />
			<div id="holder"></div>
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
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="modalAmmend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="atitle"></h4>
				</div>
				<div class="modal-body" id="abody">
					<form id="data" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-3">QUANTITY</div>
							<div class="col-md-9">
							<strong class='text-danger' id='ammend_final'></strong>
							</div>
						</div>
						<hr />
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
	<script>
		$('body').on('keyup','#damageqty,#missingqty,#incqty,#goodqty,#otherissueqty',function(){
			if($(this).val() && isNaN($(this).val())){
				tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
				$(this).val('');
			}
			var goodqty = $('#goodqty').val();
			var damageqty = $('#damageqty').val();
			var otherissueqty = $('#otherissueqty').val();

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
			if(goodqty == '' || !goodqty){
				goodqty=0;
			}

			otherissueqty = (otherissueqty) ? otherissueqty : 0;

			var final = parseFloat(goodqty) - (parseFloat(damageqty) + parseFloat(missingqty) + parseFloat(incqty)+ parseFloat(otherissueqty) )

			$('#ammend_final').html(number_format(final));


		});
		$('#branch_id').select2({
			allowClear: true,
			placeholder:'Branch'
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
							alertify.alert("Amend successfully.");
							$('#modalAmmend').modal('hide');
							showItemInRack(branch,bc,$('#hidden_row_id').val());
							btncontext.attr('disabled',false);
							btncontext.val('SAVE');
						} else {
							alertify.alert("Invalid request");
							btncontext.attr('disabled',false);
							btncontext.val('SAVE');
						}
					},
					error: function(){
						alertify.alert('Problem loading content. The page will be refresh',function(){
							location.href='audit-all.php';
						});
					}
				});
			}
		});
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
			$('#ammend_final').html(qty);
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
		}
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
							alertify.alert("Submitted successfully.");
							$('#modalConfirm').modal('hide');
							showItemInRack(branch,bc,$('#hidden_row_id').val());
							btncontext.attr('disabled',false);
							btncontext.val('SAVE');
						} else {
							alertify.alert("Invalid request");
							showItemInRack(branch,bc,$('#hidden_row_id').val());
							btncontext.attr('disabled',false);
							btncontext.val('SAVE');
						}

					},
					error: function(){
						alertify.alert('Problem loading content. The page will be refresh',function(){
							location.href='audit-all.php';
						});
					}
				});
			}
		});
		function showItemInRack(branch_id,item_id,row_id){
			$.ajax({
				url:'../ajax/ajax_inventory.php',
				type:'POST',
				data: {functionName:'showItemRackData',branch_id:branch_id,item_id:item_id},
				success: function(data){
					$('#mbody').html(data);
					$('#hidden_row_id').val(row_id);
					$('#'+row_id).html($('#hidden_row_new_qty').val());
				},
				error:function(){

				}
			});
		}

		$(document).ready(function() {
			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			var timer;
			$("#txtSearch").keyup(function(){

				var searchtxt = $("#txtSearch");

				var search = searchtxt.val();

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});

			$('body').on('change','#branch_id',function(){
				getPage(0);
			});

			$('body').on('click','.btnDetails',function(){
				var con = $(this);
				var row = con.parents('tr');
				var item_id = con.attr('data-item_id');
				var branch_id = con.attr('data-branch_id');
				var row_id = row.attr('data-id');


				$('#myModal').modal('show');
				$('#mbody').html('Loading...');

				showItemInRack(branch_id,item_id,row_id);
			});
			function getPage(p){
				var search = $('#txtSearch').val();
				var branch_id = $('#branch_id').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'inventoryAuditAll',cid: <?php echo $user->data()->company_id; ?>,search:search,b:branch_id},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#txtSearch').val();
				var branch_id = $('#branch_id').val();

				window.open(
					'excel_downloader.php?downloadName=inventoryAudit&search='+search+'&b='+branch_id,
					'_blank'
				);
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>