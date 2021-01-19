<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('terminal')){
		// redirect to denied page
		Redirect::to(1);
	}


	$branch = new Branch();
	if($user->hasPermission('is_franchisee')){
		$branches = $branch->get_active('branches',array('id' ,'=',$user->data()->branch_id));
	} else {
		$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
	}

	if (Input::exists()){

			$terminalid = Input::get('terminalcur');
			$tt = new Terminal();
			$tt->update(array('is_assigned' => 0),$terminalid);
			Session::flash('terminalflash','Terminal released successfully');
			Redirect::to('terminal.php');
			exit();

	}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
						Manage Terminals
					</h1>
				</div>

				</div>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('terminalflash')){
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('terminalflash')."</div><br>";
				}
			?>
			<div class="row">
				<div class="col-md-12">

					<div class="col-md-7">
					<?php include 'includes/terminal_nav.php'; ?>
					</div>
					<div class="col-md-5">
						<div class="row">
							<div class="col-md-6">
								<?php if($user->hasPermission('terminal_m')) { ?>
									<div id="releaseholder" style='display:none;'>
										<form id='relform' action="" method='POST' class='text-right'>
											<input type="hidden" name='terminalcur'  id='terminalcur' />
											<button title='Release' class='btn btn-default' id='btnRelease' name='btnRelease'><span class='glyphicon glyphicon-share'></span> <span class='hidden-xs'>Release</span></button>
										</form>
									</div>
								<?php } ?>
							</div>
							<div class="col-md-6 text-right">
								<?php if($user->hasPermission('deposit_add_m')) { ?>
									<button id='btnDep' <?php echo $disable_deposit; ?> class='btn btn-default' title='Deposit/Add' >
										<span class='glyphicon glyphicon-share-alt'></span>

									<span class='hidden-xs'>
									Deposit/Add
									</span>
									</button>
								<?php } ?>
							</div>
						</div>


					</div>
					<br><br>
					<?php if($branches){ ?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Terminals</div>
						<div class="panel-body">
							<div id="no-more-tables">
					<table class='table' id='tblTerminals'>
						<thead>
						<tr>
							<TH>Terminal</TH>
							<TH>Next <?php echo INVOICE_LABEL; ?></TH>
							<TH>Items Per <?php echo INVOICE_LABEL; ?></TH>
							<TH>Next <?php echo DR_LABEL; ?></TH>
							<TH>Items Per <?php echo DR_LABEL; ?></TH>
							<TH>Next <?php echo PR_LABEL; ?></TH>
							<TH>Items Per <?php echo PR_LABEL; ?></TH>
							<TH>Amount</TH>
							<TH>Created</TH>
							<TH>Branch</TH>
							<?php if($user->hasPermission('terminal_m')) { ?>
							<TH>Actions</TH>
							<?php } ?>
						</tr>
						</thead>
						<tbody>
						<?php
							// get all branch base on company

							// get all terminals of each branch
					$terminaloption = '';
							foreach($branches as $b){
								$terminal = new Terminal();
								$terminals = $terminal->get_active('terminals',array('branch_id' ,'=',$b->id));

								if(!$terminals){
									continue;
								}

								foreach($terminals as $t){
									$terminaloption .= "<option data-t_amount='$t->t_amount' data-t_amount_cc='$t->t_amount_cc' data-t_amount_ch='$t->t_amount_ch' data-t_amount_bt='$t->t_amount_bt' value='$t->id'>$b->name $t->name</option>";
							?>
							<tr>
								<td style='border-top:1px solid #ccc;' data-title='Terminal'><?php echo escape(ucwords($t->name)) ?></td>
								<td   style='border-top:1px solid #ccc;' data-title='Next Invoice'><?php echo $t->pref_inv . " " . escape($t->invoice+1) . " " .$t->suf_inv ?></td>
								<td style='border-top:1px solid #ccc;'  data-title='Items per invoice'><?php echo escape($t->invoice_limit) ?></td>
								<td style='border-top:1px solid #ccc;'  data-title='Next DR'><?php echo  $t->pref_dr. " " . escape($t->dr+1) . " " .$t->suf_dr ?></td>
								<td style='border-top:1px solid #ccc;'  data-title='Items per DR'><?php echo escape($t->dr_limit) ?></td>
								<td style='border-top:1px solid #ccc;'  data-title='Next DR'><?php echo $t->pref_ir. " " . escape($t->ir+1) . " " .$t->suf_ir  ?></td>
								<td style='border-top:1px solid #ccc;'  data-title='Items per DR'><?php echo   escape($t->ir_limit) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Sales amount'><?php
										if($t->t_amount){
											echo "Cash: <span class='text-danger'>". escape(number_format($t->t_amount,2)) . "</span>";
											echo "<br>";
										}
										if($t->t_amount_cc){
											echo "Credit Card: <span class='text-danger'>". escape(number_format($t->t_amount_cc,2)). "</span>";
											echo "<br>";
										}
										if($t->t_amount_ch){
											echo "Cheque: <span class='text-danger'>". escape(number_format($t->t_amount_ch,2)). "</span>";
											echo "<br>";
										}
										if($t->t_amount_bt){
											echo "Bank Transfer: <span class='text-danger'>". escape(number_format($t->t_amount_bt,2)). "</span>";
											echo "<br>";
										}
									?></td>
								<td style='border-top:1px solid #ccc;' data-title='Created'><?php echo escape(date('m/d/Y H:i:s A',$t->created)) ?></td>
								<td style='border-top:1px solid #ccc;' data-title='Branch' ><?php echo $b->name ?></td>
									<?php if($user->hasPermission('terminal_m')) { ?>
								<td style='border-top:1px solid #ccc;' data-title='Action' >
									<a class='btn btn-primary' href='addterminal.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt',$t->id);?>' title='Edit Terminal'><span class='glyphicon glyphicon-pencil' ></span></a>
									<a href='#' class='btn btn-primary deleteTerminal' id="<?php echo Encryption::encrypt_decrypt('encrypt',$t->id);?>" title='Delete Terminal'><span class='glyphicon glyphicon-remove'></span></a>
									<?php
										if(!$t->is_assigned)
										{
											if($user->data()->branch_id == $t->branch_id ) {
												?>
												<a href='#' class='btn btn-primary assignthis' data-name="<?php echo escape($t->name); ?>" data-tid="<?php echo escape($t->id); ?>" title='Assign This Terminal To This Computer'><span class='glyphicon glyphicon-check'></span></a>
											<?php
											}
										}
									?>

									</td>
									<?php } ?>
							</tr>
							<?php
								}
						}
						?>
						</tbody>
					</table>
							</div>
							</div>
				<?php } else { ?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
				<?php } ?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%'>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'>&nbsp;</h4>

				</div>
				<div class="modal-body" id='mbody'>
					<p id="amountlabel"></p>
					<div class="row">

						<div class="col-md-4">
							<div class="form-group">
							<select name="t_payment_type" id="t_payment_type" class='form-control'>
								<option value=""></option>
								<option value="1">Cash</option>
								<option value="2">Credit Card</option>
								<option value="3">Cheque</option>
								<option value="4">Bank Transfer</option>
							</select>
						</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
							<select name="t_terminal" id="t_terminal" class='form-control'>
								<option value=""></option>
								<?php echo $terminaloption; ?>
							</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
							<select name="d_type" id="d_type" class='form-control'>
								<option value=""></option>
								<option value="1">Add/Replenish</option>
								<option value="2">Deposit</option>

							</select>
							</div>
						</div>

					</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								<input type="text" placeholder='Amount' id='t_amount'  class='form-control' />
									</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<input type="text" placeholder='Remarks (Optional)' id='t_remarks'  class='form-control' />
									</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<button id='t_submit'  class='btn btn-default'> <span class='glyphicon glyphicon-save'></span> SUBMIT
									</button>
								</div>
							</div>

						</div>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function(){
			$('.loading').hide();
			$('#allcontent').fadeIn();
			$(".deleteTerminal").click(function(){
				var id = $(this).attr('id');
				alertify.confirm("Are you sure you want to delete this record?",function(e){
					if(e){
						$.post('../ajax/ajax_delete.php',{id:id,table:'terminals'},function(data){
							if(data == "true"){
								location.reload();
							}
						});
					}
				});
			});
			if(localStorage['terminal_id'] != 0){
				$('.assignthis').hide();
				$('#releaseholder').show();
				$('#terminalcur').val(localStorage['terminal_id']);
			}
			$('.assignthis').click(function(){
				var tid = $(this).attr('data-tid');
				var tname = $(this).attr('data-name');
				alertify.confirm("Are you sure you want to assign this computer as terminal?",function(e){
					if(e){
						localStorage['terminal_id'] = tid;
						localStorage['terminal_name'] = tname;
						assignedTerminal(tid);
					}
				});
			});
			function assignedTerminal(t){
				$.ajax({
					url: "../ajax/ajax_assigned_terminal.php",
					type:"POST",
					data:{t:t},
					success: function(data){
						window.parent.location.reload();
					},
					error:function(){
						alert('Problem occurs');
					}
				});
			}
			$('#btnRelease').click(function(){

				localStorage['terminal_id'] = 0;
				$('#relform').submit();
			});
			$("#t_terminal").select2({
				placeholder: 'Choose Terminal',
				allowClear: true
			});

			$("#d_type").select2({
				placeholder: 'Choose Action',
				allowClear: true
			});

			$("#t_payment_type").select2({
				placeholder: 'Choose type',
				allowClear: true
			});

			$('#btnDep').click(function(){
				$('#myModal').modal('show');
			});

			$('#t_submit').click(function(){
				var terminal_id = $('#t_terminal').val();
				var type = $('#d_type').val();
				var amount = replaceAll($('#t_amount').val(),',','');
				var remarks =$('#t_remarks').val();
				var payment_type =$('#t_payment_type').val();
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.html('Loading...');
				if(!terminal_id || !type || !amount ){
					alertify.alert('Please Complete the Form');
					btncon.html(btnoldval);
					return;
				}
				if(isNaN(amount) || parseInt(amount) < 1){
					alertify.alert('Invalid amount.');
					btncon.html(btnoldval);
					return;
				}

				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'post',
				    data: {payment_type:payment_type,terminal_id:terminal_id,type:type,amount:amount,remarks:remarks,functionName:'updateTerminalAmountOnHand'},
				    success: function(data){
					    alertify.alert(data,function(){
						    location.href='terminal.php';
					    });
				    },
				    error:function(){


				    }
				})
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>