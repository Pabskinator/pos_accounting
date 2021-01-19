<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('pettycash')){
		// redirect to denied page
		Redirect::to(1);
	}

	$branch_id = Input::get('id');
	$branch_id = Encryption::encrypt_decrypt('decrypt',$branch_id);
	if(!is_numeric($branch_id)){
		Redirect::to(1);

	}
	$pettycash_holder = new Pettycash_holder();
	$petty_list = $pettycash_holder->getHolder($user->data()->company_id,$branch_id);

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Petty Cash Liquidation
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<?php include 'includes/petty_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
								<strong>Branch:</strong> <?php echo escape($petty_list->branch_name); ?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<strong>Petty Cash:</strong> <span id='cur_petty'><?php echo escape(number_format($petty_list->amount,2)); ?></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<strong>Account title:</strong>
									<?php

										$ccc = new Account_title();
										$cc = objectToArray($ccc->getAcc($user->data()->company_id));
										$array = array();


										function get_nested($array,$child = FALSE,$iischild='',$selectedid=0){

											$str = '';
											$mycateg = new Account_title();
											$thisuser = new User();
											if (count($array)){
												$iischild .= $child == FALSE ? '' : '--';

												foreach ($array as $item){
													$haschild = $mycateg->hasChild($thisuser->data()->company_id,$item['id']);
													$disabledme='';
													if($haschild){
														$disabledme = 'disabled';
													}
													if($selectedid){
														if($selectedid == $item['id']){
															$selected = 'selected';
															$selectedid=0;
														}
													} else {
														$selected = '';
													}

													if(isset($item['children']) && count($item['children'])){
														$str .= '<option value="'.$item['id'].'" '.$disabledme.' '.$selected.'>'.$iischild.$item['name'].'</option>';
														$str .= get_nested($item['children'], true, $iischild,$selectedid);
													} else {
														if($child == false) $iischild='';
														$str .= '<option value="'.$item['id'].'" '.$disabledme.' '.$selected.'>'.$iischild.($item['name']).'</option>';
													}

												}
											}

											return $str;
										}

										function objectToArray ($object) {
											if(!is_object($object) && !is_array($object))
												return $object;

											return array_map('objectToArray', (array) $object);
										}
										function makeRecursive($d, $r = 0, $pk = 'parent_id', $k = 'id', $c = 'children') {
											$m = array();
											foreach ($d as $e) {
												isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
												isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
												$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
											}

											return $m[$r]; // remove [0] if there could be more than one root nodes
										}

									?>
									<select id="account_title" name="account_title" class="form-control hasChild" >
										<option value=""></option>
										<?php
											if(isset($id)){
												echo get_nested(makeRecursive($cc), FALSE,'',$editProd->data()->category_id);
											} else {
												echo get_nested(makeRecursive($cc));
											}
										?>


									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<strong>Date:</strong> <input type="text" class='form-control' id='txtDate'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<strong>Amount:</strong> <input type="text" class='form-control' id='txtAmount'>
								</div>
							</div>

						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<strong>Description:</strong> <input type="text" class='form-control' id='txtDescription'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<br>
									<button class='btn btn-default' id='btnAdd'>Add Expense</button>
								</div>
							</div>

						</div>
						<div id="expense_holder">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			getExpense();
			$('#txtDate').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#txtDate').datepicker('hide');
			});
			$('#account_title').select2({
				allowClear: true,
				placeholder:'Account title'
			});
			$('body').on('click','#btnAdd',function(){
				var desc = $('#txtDescription').val();
				var dt = $('#txtDate').val();
				var amount = $('#txtAmount').val();
				var account_title = $('#account_title').val();
				if(!desc || !dt){
					alertify.alert('Please complete the form');
					return;
				}
				if(!amount || isNaN(amount) || parseFloat(amount) < 1){
					alertify.alert('Invalid amount');
					return;
				}
				addExpense(desc,dt,amount,account_title,$(this));
				clearInputs();
			});


			function addExpense(desc,dt,amount,account_title,btn){
				var oldval = btn.html();
				btn.html('Loading...');
				btn.attr('disabled',true);
				var cur_expense = $('#cur_petty').html();
				cur_expense = replaceAll(cur_expense,',','');
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'addPettyExpense', desc:desc,dt:dt,amount:amount,account_title_id:account_title},
				    success: function(data){
						alertify.alert(data);
					    var remaining = parseFloat(cur_expense) - parseFloat(amount);
					    $('#cur_petty').html(number_format(remaining,2));
					    btn.attr('disabled',false);
					    btn.html(oldval);
					    getExpense();
				    },
				    error:function(){

					    btn.attr('disabled',false);
					    btn.html(oldval);
				    }
				})
			}
			function getExpense(){
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
					beforeSend: function(){
						$('#expense_holder').html('Loading...');
					},
				    data: {functionName:'getPettyExpense'},
				    success: function(data){
					    $('#expense_holder').html(data);
				    },
				    error:function(){

				    }
				})
			}
			function deleteExpense(id,btn){
				var oldval = btn.html();
				btn.html('Loading...');
				btn.attr('disabled',true);
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'deletePettycashExpense',id:id},
				    success: function(data){
				        alertify.alert(data);
					    getExpense();
					    btn.attr('disabled',false);
					    btn.html(oldval);
				    },
				    error:function(){

					    getExpense();
					    btn.attr('disabled',false);
					    btn.html(oldval);
				    }
				})
			}
			function clearInputs(){
				$('#txtDescription').val('');
				$('#txtDate').val('');
				$('#txtAmount').val('');
			}
			$('body').on('click','.btnDelete',function(){
				var btn = $(this);
				var id = btn.attr('data-id');

				alertify.confirm('Are you sure you want to delete this record?', function(e){
					if(e){
						deleteExpense(id,btn);
					}
				});
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>