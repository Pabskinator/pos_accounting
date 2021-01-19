<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$can_add_inv = [];
	$config_add = Configuration::getValue('can_add_inventory');
	if($config_add){
		if(strpos($config_add,',') > 0){
			$can_add_inv = explode(',',$config_add);
		} else {
			$can_add_inv[] = $config_add;
		}
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Add Inventories
				</h1>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('inventoryflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('inventoryflash') . "</div>";
				}
			?>
			<div class="row">
				<div class="col-sm-12">
					<?php


						// if submitted

					?>




					<div id='add_inv_app'>

						<fieldset>


							<legend></legend>

							<div class="col-md-3">
								<?php
									$branch_disable = "readonly";
									if($user->hasPermission('inventory_all')){
										$branch_disable = "";
									}
								?>
								<select v-model='request.branch_id' name="bname" id="bid" class='form-control' required <?php echo $branch_disable; ?>>
									<?php
										$branch = new Branch();
										$branches = $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));

									?>
									<?php
										$selected = "";
										foreach($branches as $b):
											if($can_add_inv && !in_array($b->id,$can_add_inv)){
												continue;
											}

											if($b->id == $user->data()->branch_id){
												$selected = $b->id;
											}
											?>
											<option value="<?php echo escape($b->id); ?>"><?php echo escape($b->name); ?></option>
										<?php endforeach; ?>
								</select> <input type="hidden" id='selected_branch_id' value='<?php echo $selected; ?>'>
								<span class="help-block">Enter the Branch name</span>
							</div>

							<div class="col-md-3">
								<select name="supplier_id" v-model='request.supplier_id'  id="supplier_id" class='form-control'>

									<?php
										$supcls = new Supplier();
										$suppliers = $supcls->getSuppliers($user->data()->company_id);
									?>
									<?php
										foreach($suppliers as $sup):
											$aartype = ['Local', 'International'];
											?>
											<option value="<?php echo escape($sup->id); ?>"><?php echo escape($sup->name) . " (" . $aartype[$sup->sup_type] . ")" ; ?></option>
										<?php endforeach; ?>
								</select>
								<span class="help-block">Choose Supplier (Optional)</span>
							</div>




							<div class="col-md-3">
								<input type="text" class='form-control' v-model='request.date_receive' placeholder='Enter date' id='date_receive' name='date_receive'>
								<span class="help-block">Date Receive (Optional)</span>
							</div>

							<div class="col-md-3">
								<input type="text" class='form-control' placeholder='Packing list'  v-model='request.packing_list' id='packing_list' name='packing_list'>
								<span class="help-block">Packing List (Optional)</span>
							</div>

							<div class="col-md-3">
								<input type="text" class='form-control' placeholder='Ref #'  v-model='request.ref_num'  id='ref_num' name='ref_num'>
								<span class="help-block">Ref Num (Optional)</span>
							</div>
							<div class="col-md-6">
								<input type="text" class='form-control' placeholder='Remarks'  v-model='request.remarks'  id='remarks' name='remarks'>
								<span class="help-block">Enter remarks (Optional)</span>
							</div>
							<div class="col-md-3">

							</div>
						</fieldset>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control selectitem' id='item_id' v-model='form.item_id'>
									<span class='help-block'>Choose Item</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='rack_id' v-model='form.rack_id'>
									<span class='help-block'>Choose Rack</span>
								</div>
							</div>
							<div class="col-md-3" v-show="multiplier_qty.length">
								<div class="form-group">
									<select name="dif_qty" id="dif_qty" v-model="dif_qty" class='form-control'>
										<option value="1">Choose unit</option>
										<option v-for="qtys in multiplier_qty" v-bind:value="qtys.qty">{{qtys.unit_name}}</option>
									</select>
									<span class='help-block'>Choose Unit</span>
								</div>

							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='qty' class='form-control' v-model='form.qty'>
									<span class='help-block'>Choose Qty</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='item_remarks' class='form-control' v-model='form.remarks'>
									<span class='help-block'>Item Remarks (optional)</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' @click="addItem">Add</button>
								</div>
							</div>
						</div>
						<br>
						<table class='table' v-show="items.length">
							<thead>
							<tr>
								<th>Item</th>
								<th>Rack</th>
								<th>Qty</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<tr v-for="item in items">
								<td style='border-top:1px solid #ccc;'>{{item.item_code}} <br> {{item.description}}</td>
								<td style='border-top:1px solid #ccc;'>{{item.rack}}</td>
								<td style='border-top:1px solid #ccc;'>{{item.qty}}</td>
								<td style='border-top:1px solid #ccc;'>
									<button class='btn btn-danger' @click="removeItem(item)"><i class='fa fa-remove'></i></button>
								</td>
							</tr>
							</tbody>
						</table>
						<hr>
						<div v-show="items.length">
							<button class='btn btn-success' id='btnSubmit' @click="submitInventory">Submit</button>
						</div>
						<div class="alert alert-info" v-show="!items.length">Add item first</div>

					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script src='../js/vue3.js'></script>
	<script>
		var vm = new Vue({
			el:'#add_inv_app',
			data: {
				multiplier_qty:[],
				dif_qty:'1',
				items:[],
				form: {item_id:'',qty:'',rack_id:'',rack:'',item_code:'',description:'',remarks:''},
				request: {branch_id:'',supplier_id:'',date_receive:'',packing_list:'',ref_num:'',remarks: ''}
			},
			mounted: function(){
				var vm = this;
				var selected_branch_id = $('#selected_branch_id').val();
				vm.request.branch_id = selected_branch_id;
				$('#rack_id').select2({
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
								branch_id: $('#bid').val()
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									var des = "";
									if(item.description){
										des = " - " + item.description;
									}
									return {
										text: item.rack + des,
										slug: item.rack,
										id: item.id
									}
								})
							};
						}
					}
				});
				$('#rack_id').change(function(){
					vm.form.rack_id = $(this).val();
					vm.form.rack = $('#rack_id').select2('data').text;
				});
				$('#item_id').change(function(){


				});

				$('body').on ('change','#item_id',function(){

					var id = $('#item_id').val();
					vm.form.item_id = $(this).val();
					var item = $('#item_id').select2('data').text;
					var split = item.split(':');
					vm.form.item_code = split[1];
					vm.form.description = split[2];

					$.ajax({
						url:'../ajax/ajax_wh_order.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getUnits',item_id:id},
						success: function(data){
							vm.multiplier_qty = data;
							vm.dif_qty = '1';
						},
						error:function(){

						}
					})
				});

				$('body').on('keyup','#qty',function(){
					var num = $(this).val();
					num = (num) ? num : 0;
					if(isNaN(num)){
						tempToast('error',"Invalid qty",'Error');
						$(this).val(1);
						vm.form.qty = 1;
					}
				});

				$('#date_receive').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#date_receive').datepicker('hide');
					vm.request.date_receive = $('#date_receive').val();
				});

			},
			methods: {
				submitInventory: function(){
					var vm = this;
					if(vm.request.branch_id && vm.items.length){
						var btn = $('#btnSubmit');
						button_action.start_loading(btn);
						$.ajax({
							url:'../ajax/ajax_inventory.php',
							type:'POST',
							data: {functionName:'addInvBatch', items: JSON.stringify(vm.items),request:JSON.stringify(vm.request)},
							success: function(data){
								tempToast('info',data,'Info');
								vm.multiplier_qty=[];
								vm.dif_qty='1';
								vm.items=[];
								vm.form= {item_id:'',qty:'',rack_id:'',rack:'',item_code:''};
								vm.request= {branch_id:'',supplier_id:'',date_receive:'',packing_list:'',ref_num:''};
								button_action.end_loading(btn);
							},
							error:function(){
								button_action.end_loading(btn);
							}
						});
					} else {
						tempToast('error',"Invalid request",'Error');
					}

				},
				addItem: function(){
					this.form.qty = this.dif_qty * this.form.qty;
					console.log(this.dif_qty);
					console.log(this.form.qty);
					this.multiplier_qty = [];
					this.dif_qty = '1';
					this.items.push(this.form);
					this.form = {item_id:'',qty:'',rack_id:'',rack:'',item_code:'',remarks:''};
					$('#rack_id').select2('val',null);
					$('#item_id').select2('val',null);
				},
				removeItem: function(item){
					var newitems = this.items.filter(function(e){
						return e.item_id !== item.item_id;
					});
					this.items = newitems;
				},
			}
		});

		$(document).ready(function() {
			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
				}
			}


		});
	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>