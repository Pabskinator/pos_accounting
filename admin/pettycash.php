<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('pettycash')){
		// redirect to denied page
		Redirect::to(1);
	}
	$pettycash_holder = new Pettycash_holder();
	$cur_branch = 0;
	if($user->hasPermission('is_franchisee')){
		$cur_branch = $user->data()->branch_id ;
		$list = $pettycash_holder->getHolder($user->data()->company_id,$cur_branch);
		if($list){
			$petty_list[] = $list;
		} else {
			$petty_list = [];
		}

	} else {
		$petty_list = $pettycash_holder->getHolder($user->data()->company_id,0);
	}


	$hasRecordPetty = $pettycash_holder->get_active('pettycash_holder',array('branch_id','=',$user->data()->branch_id));
	if(count($hasRecordPetty)){
		$requestStarting = false;
	} else {
		$requestStarting = true;
	}
?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<?php include 'includes/petty_nav.php'; ?>
			<div class="row">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Petty Cash
					</h1>
				</div>
				<div class="col-md-6 text-right">
					<?php if($requestStarting && $user->hasPermission('pettycash_r')){
					?>
					<h1 style='margin:10px;'>
						<a  class='btn btn-default' title='Request' href='pettycash_request.php'> <span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Starting petty cash</span></a>
					</h1>
					<?php
					} else {
						?>
						<h1>
						&nbsp;
						</h1>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
						<?php
							if ($petty_list){
								?>

								<div id="no-more-tables">
									<table class='table' id='tblBranches'>
										<thead>
										<tr>
											<TH>Branch</TH>
											<TH>User</TH>
											<TH>Petty cash</TH>
											<th>Expense</th>
											<th></th>
										</tr>
										</thead>
										<tbody>
										<?php

											foreach($petty_list as $b){
												$pettycash_breakdown = new Pettycash_breakdown();
												$expense = $pettycash_breakdown->getTotalExpense($b->branch_id,0);
												if(isset($expense->totalExpense)){
													$totalEx = $expense->totalExpense;
												} else {
													$totalEx = 0;
												}
												?>
												<tr>
													<td data-title='Branch'><?php echo escape($b->branch_name); ?></td>
													<td data-title='User'><?php echo escape(ucwords($b->lastname . ", " . $b->firstname . " " . $b->middlename)); ?></td>
													<td data-title='Petty Cash'><?php echo escape(number_format($b->amount,2)); ?></td>
													<td data-title='Expense'><?php echo escape(number_format($totalEx,2)); ?></td>
													<td>
														<?php if($user->data()->id == $b->user_id && $user->data()->branch_id == $b->branch_id){

															?>
															<button data-branch_id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->branch_id); ?>" class='btn btn-sm btn-default btnLiquidate'>Liquidate</button>
															<button data-branch_id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->branch_id); ?>" data-total_expense="<?php echo $totalEx; ?>" data-branch_id="<?php echo Encryption::encrypt_decrypt('encrypt',$b->branch_id); ?>" class='btn btn-sm btn-default btnReplenish'>Replenish</button>
															<?php
														}?>

													</td>
												</tr>
												<?php
											}
										?>
										</tbody>
									</table>
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
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){

			$('body').on('click','.btnLiquidate',function(){
				var b = $(this).attr('data-branch_id');
				location.href='pettycash_liquidation.php?id='+b;
			});


			$('body').on('click','.btnReplenish',function(){
				var amount = $(this).attr('data-total_expense');
				var b = $(this).attr('data-branch_id');

				alertify.confirm('Are you sure you want to replenish '+amount+' pesos?',function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_query2.php',
						    type:'POST',
						    data: {functionName:'replenishPettyCash',amount:amount,branch_id:b},
						    success: function(data){
								alertify.alert(data,function(){
									location.href='pettycash.php'
								});

						    },
						    error:function(){

						    }
						})
					}
				});
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>