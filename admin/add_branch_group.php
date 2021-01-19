<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	$branch_group = new Branch_group();
	$groups = $branch_group->get_active('branch_groups',['1','=','1']);

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset"  id='bgroup'>
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Add Item In Group
			</h1>

		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">List</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<?php
										if($groups){
											?>
											<select class="form-control" v-model="request.branch_group_id" name="branch_group_id" id="branch_group_id">
											<?php
											foreach($groups as $gr){
												?>
												<option value="<?php echo $gr->id; ?>"><?php echo $gr->name; ?></option>
										<?php
												}
										?>
											</select>
									<?php
										}
									?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text"  v-model="request.item_id" id='item_id' class='form-control selectitem'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" v-model="request.barcode" class='form-control' placeholder='Barcode'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" v-model="request.price" class='form-control' placeholder='Price'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' @click="addItem" >Add Item</button>
								</div>
							</div>
						</div>
						<br>
						<div v-show="cart_items.length">
							<table class='table table-bordered'>
								<thead>
									<tr>
										<th>Group</th>
										<th>Item</th>
										<th>Partner Barcode</th>
										<th>Price</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="item in cart_items">
										<td style='border-top:1px solid #ccc;' >{{ item.branch_group_name }}</td>
										<td style='border-top:1px solid #ccc;' >{{ item.item_code }}</td>
										<td style='border-top:1px solid #ccc;' >{{ item.barcode }}</td>
										<td style='border-top:1px solid #ccc;' >{{ item.price }}</td>
										<td style='border-top:1px solid #ccc;' ><button class='btn btn-danger' @click="removeItem(item)" ><i class='fa fa-trash'></i></button></td>
									</tr>
								</tbody>
							</table>
							<div>
								<button class='btn btn-default' @click="submitItem">Save</button>
							</div>
						</div>
						<div v-show="errors.length">
							<h5>Errors: </h5>
							<ul>
								<li v-for="error in errors" class='text-danger'>{{error}}</li>
							</ul>
						</div>
						<div v-show="!cart_items.length && !errors.length">
							<div class="alert alert-info">No Item</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script src='../js/vue3.js'></script>
	<script>

	var vm  = new Vue({
		el:'#bgroup',
		data: {
			request: {
				branch_group_id: '',
				branch_group_name: '',
				item_id: '',
				barcode: '',
				price: '',
				item_code: '',
				description: ''
			},
			cart_items:[],
			errors:[],
		},
		mounted: function(){

			var self = this;

			$('body').on('change','#item_id',function(){

				var con = $(this);
				self.request.item_id = con.val();
				var data = con.select2('data').text;
				var splitted = data.split(':');
				self.request.item_code = splitted[1];
				self.request.description = splitted[2];

			});


			$('body').on('change','#branch_group_id',function(){

				var name = $('#branch_group_id option:selected').text();
				self.request.branch_group_name = name;

			});


		},
		methods:{
			removeItem: function(r){

				this.cart_items = this.cart_items.filter(function(i){
					return i.item_id != r.item_id;
				});

			},
			addItem: function(){

				var self = this;
				self.cart_items.push(self.request);
				self.clearRequest();
				self.errors= [];

			},
			clearRequest: function(){
				var b = this.request.branch_group_id;
				var n = this.request.branch_group_name;
				this.request = {
					branch_group_id: b,
					branch_group_name: n,
						item_id: '',
						barcode: '',
						price: '',
						item_code: '',
						description: ''
				};
				$('#item_id').select2('val',null);
			},
			submitItem: function(){
				var self = this;
				self.errors= [];
				$.ajax({
				    url:'../ajax/ajax_branch_group.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'getList', items:JSON.stringify(self.cart_items)},
				    success: function(data){
					    if(data.length){
						    tempToast('error','Some files are not inserted successfully.');
					    } else {
						    tempToast('info','Processed successfully');
					    }
						self.cart_items = [];
				        self.errors = data;
				    },
				    error:function(){
				        
				    }
				})
			}
		}
	});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>