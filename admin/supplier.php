<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('supplier')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$supplier = new Supplier();
	$suppliers = $supplier->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));
	$cf = new Custom_field();
	$cfd = new Custom_field_details();
	$getsupplierdet = $cf->getcustomform('suppliers',$user->data()->company_id);
	$label_name = isset($getsupplierdet->label_name)? strtoupper($getsupplierdet->label_name):'Supplier';
	$description = $cfd->getIndData('description',$user->data()->company_id,$getsupplierdet->id);
	$otherfield = isset($getsupplierdet->other_field)?$getsupplierdet->other_field:'';
	if($otherfield){
		$otherfield = json_decode($otherfield,true);
	}

?>



	<!-- Page content -->
<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">

			<?php include 'includes/supplier_nav.php'; ?>


		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Supplier
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<?php

					if ($suppliers){
				?>

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Suppliers</div>
					<div class="panel-body">
						<div id="no-more-tables">
						<table class='table' id='tblSuppliers'>
							<thead>
							<tr>
								<TH>Supplier</TH>
								<TH>Description</TH>
								<TH>Data Created</TH>
								<th>Other Information</th>
								<?php if($user->hasPermission('supplier_m')) {  ?>
									<TH>Actions</TH>
								<?php } ?>
							</tr>
							</thead>
							<tbody>
							<?php
								foreach($suppliers as $s){
									?>
									<tr>
										<td data-title='Supplier'>
											<?php echo escape($s->name); ?>
											<small class='text-danger span-block'>
												<?php echo ($s->sup_type) ? 'International' : 'Local'; ?>
											</small>
										</td>
										<td data-title='Description'><?php echo escape($s->description); ?></td>
										<td  data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$s->created)); ?></td>
										<td data-title='Other info'>
											<div><strong>Contact Person: </strong><span><?php echo ($s->contact_person) ? $s->contact_person : 'NA'; ?></span></div>
											<div><strong>Contact Number: </strong><span><?php echo ($s->contact_number) ? $s->contact_number : 'NA'; ?></span></div>
								<?php
									if($otherfield){
										foreach($otherfield as $cfield){
											if($cfield['field-visibility'] == 1){
													$jsonind = json_decode($s->jsonfield,true);
												?>
												<span style='display:block;'><?php echo  "<span style='color:#888;' class=''>" . $cfield['field-label'] . ":</span> <span class='text-danger'>" . $jsonind[$cfield['field-id']] . "</span>"; ?></span>
												<?php
											}
										}
									}
								?>
										</td>
										<?php if($user->hasPermission('supplier_m')) {  ?>
											<td  data-title='Action'>
												<a class='btn btn-primary' href='addsupplier.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>' title='Edit Supplier'><span class='glyphicon glyphicon-pencil' ></span></a>
												<a href='#' class='btn btn-primary deleteSupplier' id="<?php echo Encryption::encrypt_decrypt('encrypt',$s->id);?>" title='Delete Supplier'><span class='glyphicon glyphicon-remove'></span></a>
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
					<?php
						} else {
						?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){

			$(".deleteSupplier").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'suppliers'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});

		});


		$('#tblSuppliers').dataTable({
			iDisplayLength: 50
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>