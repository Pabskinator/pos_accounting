<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}

	/*function get_nested($array,$child = FALSE,$iischild=''){

		$str = '';

		if (count($array)){
			$iischild .= $child == FALSE ? '' : '-';

			foreach ($array as $item){


				if(isset($item['children']) && count($item['children'])){

					$str .= '<option value="'.$item['id'].'">'.$iischild.$item['name'].'</option>';
					$str .= get_nested($item['children'], true, $iischild);
				} else {
					if($child == false) $iischild='';
					$str .= '<option value="'.$item['id'].'">'.$iischild.($item['name']).'</option>';
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
	function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'children') {
		$m = array();
		foreach ($d as $e) {
			isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
			isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
			$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
		}
		return $m[$r]; // remove [0] if there could be more than one root nodes
	}
	$ccc = new Category();
	$cc = objectToArray($ccc->getCategory($user->data()->company_id));
	*/

	$branch_cls = new Branch();
	$branch_list = $branch_cls->get_active('branches',[1,'=',1]);

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Log  </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/inventory_nav.php'; ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Record</div>
							<div class='col-md-6 text-right'>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12 text-right">
								<a href="add-inv-log.php"  class='btn btn-default btn-sm'>Per Transaction</a>
								<a href="add-inv-log-details.php"  class='btn btn-default btn-sm'>Per Item</a>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-3">
								<strong>Filter Branch</strong>
								<select class='form-control' name="branch_id" id="branch_id">
									<option value="">All</option>
									<?php if($branch_list){
											foreach($branch_list as $bl){
												echo "<option value='$bl->id'>$bl->name</option>";
											}
										}
									?>
								</select>
							</div>
							<div class="col-md-3">
								<strong>Filter Date</strong>
								<input type="text"  autocomplete="off" class='form-control' id='dt_from' placeholder="Date From">
							</div>
							<div class="col-md-3">
								<br>
								<input type="text" autocomplete="off" class='form-control' id='dt_to' placeholder="Date To">
							</div>
						</div>
						<br>
						<div id='pendingController'>
							<div id="pending" v-show='pending.length'>
								<h3>{{title}}</h3>
								<table class='table'>
									<thead>
										<tr>
											<th>Branch</th>
											<th>Items</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="p in pending" v-show="p.item_list.length">
											<td style='border-top:1px solid #ccc;'>{{p.request_data.branch_name}} <small class='span-block'>{{p.request_data.packing_list}}</small><small class='span-block'>{{p.request_data.ref_num}}</small></td>
											<td style='border-top:1px solid #ccc;'>
												<table class='table'>
													<tr>
														<th>Item</th><th>Rack</th><th>Qty</th><th></th>
													</tr>
													<tr v-for='item in p.item_list'>
														<td style='width:50%;'>{{item.item_code}} <span class='text-danger span-block'>{{item.item_description}}</span></td>
														<td>{{item.rack_name}}</td>
														<td><input type="text" class='form-control' v-model="item.qty" value="{{item.qty}}"></td>
														<td><button class='btn btn-danger btn-sm' v-on:click="deleteItem(p.item_list,$index)"><i class='fa fa-remove'></i></button></td>
													</tr>
												</table>
											</td>
											<td style='border-top:1px solid #ccc;'>
												<button  class='btn btn-default btn-sm' v-on:click="submitInventory(p,$index)">Submit</button>
												<button  class='btn btn-danger btn-sm' v-on:click="removeInventory(p,$index)">Remove</button>
											</td>
										</tr>
									</tbody>
								</table>
								<hr>
							</div>
						</div>

						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
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
	<script src="../js/vue.js"></script>
	<script>

		$(document).ready(function() {
			var vm = new Vue({
				el: '#pendingController',
				data:{
					pending:[],
					title:'Pending Inventory',
					ajaxPending : false
				},
				ready: function(){
					if(localStorage['add_inventory_local']){
						this.pending = JSON.parse(localStorage['add_inventory_local']);
					} else {
						this.pending = [];
					}

				},
				methods:{
					removeInventory : function(p,index){
						var vuecon = this;
						alertify.confirm("Are you sure you want to delete this inventory?",function(e){
							if(e){
								vuecon.pending.splice(index,1);
								localStorage['add_inventory_local'] = JSON.stringify(vuecon.pending);
							}
						});
					},
					submitInventory : function(p,index){
						var vuecon = this;
						alertify.confirm("Are you sure you want to add this inventory?",function(e){
							if(e){
								var to_add = p;
								vuecon.pending.splice(index,1);
								if(vuecon.ajaxPending == false){
									vuecon.ajaxPending = true;
									$.ajax({
										url:'../ajax/ajax_inventory.php',
										type:'POST',
										data: {p:JSON.stringify(to_add),functionName:'addInventory'},
										success: function(data){
											alertify.alert(data);
											localStorage['add_inventory_local'] = JSON.stringify(vuecon.pending);
											vuecon.ajaxPending = false;
										},
										error:function(){
											vuecon.ajaxPending = false;
										}
									})
								}

							}
						});
					},
					deleteItem : function(arr,i){
						arr.splice(i,1);
					}
				}
			});
			$('body').on('click','.btnDetails',function(){
				var id = $(this).attr('data-id');
				$('#myModal').modal('show');
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
					beforeSend:function(){
						$('#mbody').html('Fetching data...');
					},
				    data: {functionName:'getAddInvLog',id:id},
				    success: function(data){
				        $('#mbody').html(data);
				    },
				    error:function(){

				    }
				});
			});
			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#branch_id',function(){
				getPage(0);
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				getPage(0);
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				getPage(0);
			});

			function getPage(p){
				var branch_id = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();

				if(dt_from && !dt_to){
					return;
				}
				if(!dt_from && dt_to){
					return;
				}

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{branch_id:branch_id,dt_from:dt_from,dt_to:dt_to,page:p,functionName:'inventoryLogPaginate',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','.btnDecline',function(){

				var con = $(this);
				var id = con.attr('data-id');

				if(id){
					alertify.confirm("Are you sure you want to continue this action?",function(e){
						if(e){

							$.ajax({
								url:'../ajax/ajax_query.php',
								type:'POST',
								data: {functionName:'batchInvDecline',id:id},
								success: function(data){
									tempToast('info',data,'Info');
									var p = $('#hiddenpage').val();
									getPage(p);
									$('#myModal').modal('hide');
								},
								error:function(){

								}
							});

						}
					});


				}

			});

			$('body').on('click','.btnApprove',function(){

				var con = $(this);
				var id = con.attr('data-id');
				button_action.start_loading(con);
				if(id){
					alertify.confirm("Are you sure you want to continue this action?",function(e){
						if(e){

							$.ajax({
								url:'../ajax/ajax_query.php',
								type:'POST',
								data: {functionName:'batchInvAdd',id:id},
								success: function(data){
									tempToast('info',data,'Info');
									var p = $('#hiddenpage').val();
									getPage(p);
									$('#myModal').modal('hide');
									button_action.end_loading(con);
								},
								error:function(){
									button_action.end_loading(con);
								}
							});

						} else {
							button_action.end_loading(con);
						}
					});


				}

			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>