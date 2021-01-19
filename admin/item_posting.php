<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	$item_spec = new Item_specification();

	$item_specs =$item_spec->get_active('item_specifications',[1,'=',1]);

	$arr_main_spec = [];
	$arr_ind_spec = [];

	foreach($item_specs as $i_spec){

		$spec_type = capitalize($i_spec->spec_type);
		if(!in_array($spec_type,$arr_main_spec)) $arr_main_spec[] = $spec_type;
		$arr_ind_spec[$spec_type][] = ['name' => $i_spec->name ,'id' => $i_spec->id];

	}


?>
	<link rel="stylesheet" href="../css/dropzone2.css">
	<input type="hidden" value='<?php echo json_encode($arr_main_spec)?>'  id='main_specs'>
	<input type="hidden" value='<?php echo json_encode($arr_ind_spec)?>'  id='all_specs'>

	<!-- Page content -->
	<div id="page-content-wrapper">


	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset" id ='app'>
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Product Post
			</h1>

		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default btn_nav' @click="showContainer(1)" data-con='1' title='Post Item' href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Post Item </span></a>
					<a class='btn btn-default btn_nav'  @click="showContainer(2)" data-con='2' title='Item List'  href='#'> <span class='glyphicon glyphicon-ok'></span> <span class='hidden-xs'>Item List</span></a>
					<a class='btn btn-default btn_nav'  @click="showContainer(3)" data-con='3' title='Log'  href='#'> <span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>Log</span></a>
					<a class='btn btn-default btn_nav'  @click="showContainer(4)" data-con='4' title='Add item specification'  href='#'> <span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add Item Specification</span></a>
					<a class='btn btn-default btn_nav'  @click="showContainer(5)" data-con='4' title='Item specification list'  href='#'> <span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Item Specification List</span></a>
				</div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Item Posting</div>
					<div class="panel-body">
						<div v-show="con.post_view">
							<h4 class='text-center'>Post Item</h4>
							<div class="row">
								<div class="col-md-3"></div>
								<div class="col-md-6">
									<div class="panel panel-default">
										<div class="panel-body">
											<h4>Item Information</h4>
											<div class="form-group">
												<label for="">Item Name</label>
												<input v-model="post.item_id" id='item_id' type="text" class='form-control selectitem'>
											</div>
											<div class="form-group">
												<label for="post_price">Price: </label>
												<p><strong>{{ adjustedPrice }}</strong></p>
											</div>
											<div class="form-group">
												<label for="">Quantity</label>
												<input v-model="post.qty" placeholder='Quantity' type="text" class='form-control'>
											</div>
											<div class="form-group">
												<label for="">Remarks</label>
												<input v-model="post.remarks" placeholder='Additional Remarks' type="text" class='form-control'>
											</div>
											<div class="form-group">
												<label for="">Price Adjustment</label>
												<input v-model="post.price_adjustment" placeholder='Enter Price adjustment' type="text" class='form-control'>
												<span class='help-block'>Negative value for discount</span>
											</div>
											<div class='text-right'>
												<button class='btn btn-default' @click="submitRecord">Submit</button>
											</div>

										</div>
									</div>

								</div>
								<div class="col-md-3"></div>
							</div>
						</div> <!-- End Post View -->

						<div v-show="con.item_list_view">
							<h4>Item List</h4>
							<div v-show="item_posts.length">
								<table class='table table-bordered table-bordered' id='tblForApproval'>
									<thead>
									<tr>
										<th>ID</th>
										<th>Item</th>
										<th>Qty</th>

										<th>Price adjustment</th>
										<th>Adjusted Price</th>
										<th>Remarks</th>
										<th>Created At</th>
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
										<tr v-for="item in item_posts">
											<td>{{item.id}}</td>
											<td style='width:240px;'>
												<span v-show="!item.updating">
												{{item.item_code}}
												<small class='text-danger span-block'>
													{{ item.description }}
												</small>
													<span class='span-block'>
													<span class='label label-danger'>{{item.ribbon_label}}</span>
													</span>
												</span>
												<div  v-show="item.updating">
													<input type="text" class='form-control updateselectitem' v-model='item.item_id'>
													<span class='span-block'>Current: {{item.item_code}}
												<small class='text-danger span-block'>
													{{ item.description }}
												</small></span>
												</div>
											</td>
											<td>
												<span v-show="!item.updating">{{ item.qty }}</span> <input class='form-control' v-show="item.updating" type="text" v-model='item.qty'>
											</td>
											<td>
												<span v-show="!item.updating">{{ item.price_adjustment }}</span> <input class='form-control' v-show="item.updating" type="text" v-model='item.price_adjustment'>
											</td>
											<td>
												{{ (parseFloat(item.price_adjustment) + parseFloat(item.price)).toFixed(2) }}
											</td>
											<td>
												<span v-show="!item.updating">{{ item.remarks }}</span> <input  class='form-control' v-show="item.updating" type="text" v-model='item.remarks'>
											</td>
											<td>
												{{ item.dateStr }}
											</td>
											<td>
												<button  v-show="!item.updating" @click="updateItem(item)" class='btn btn-default btn-sm'>
													<i class='fa fa-pencil'></i>
												</button>
												<button  v-show="item.updating" @click="saveItem(item)" class='btn btn-success btn-sm'>
													<i class='fa fa-save'></i>
												</button>
												<button @click="deleteItem(item)" class='btn btn-default btn-sm'>
													<i class='fa fa-trash'></i>
												</button>
												<button @click="attachFiles(item)" class='btn btn-default btn-sm'>
													<i class='fa fa-upload'></i>
												</button>
												<button @click="getAttachments(item)" class='btn btn-default btn-sm'>
													<i class='fa fa-file'></i>
												</button>
												<button v-show="item.ribbon_label == '' " @click="addLabel(item)" class='btn btn-default btn-sm'>
													<i class='fa fa-tag'></i> Add Label
												</button>
												<button v-show="item.ribbon_label != '' " @click="deleteLabel(item)" class='btn btn-default btn-sm'>
													<i class='fa fa-tag'></i> Delete Label
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div v-show="!item_posts.length">
								<div class="alert alert-info">
									No Record Found
								</div>
							</div>
						</div> <!-- End Item List View -->

						<div v-show="con.log_view">
							<h4>Log</h4>
							<div v-show="item_posts.length">
								<table class='table table-bordered table-bordered' id='tbl'>
									<thead>
									<tr>
										<th>ID</th>
										<th>Item</th>
										<th>Qty</th>
										<th>Price adjustment</th>
										<th>Adjusted Price</th>
										<th>Remarks</th>
										<th>Created At</th>

									</tr>
									</thead>
									<tbody>
									<tr v-for="item in item_posts">
										<td>{{item.id}}</td>
										<td style='width:240px;'>
												<span v-show="!item.updating">
												{{item.item_code}}
												<small class='text-danger span-block'>
													{{ item.description }}
												</small>
													<span class='span-block'>
													<span class='label label-danger'>{{item.ribbon_label}}</span>
													</span>
												</span>
											<div  v-show="item.updating">
												<input type="text" class='form-control updateselectitem' v-model='item.item_id'>
													<span class='span-block'>Current: {{item.item_code}}
												<small class='text-danger span-block'>
													{{ item.description }}
												</small></span>
											</div>
										</td>
										<td>
											<span v-show="!item.updating">{{ item.qty }}</span> <input class='form-control' v-show="item.updating" type="text" v-model='item.qty'>
										</td>
										<td>
											<span v-show="!item.updating">{{ item.price_adjustment }}</span> <input class='form-control' v-show="item.updating" type="text" v-model='item.price_adjustment'>
										</td>
										<td>
											{{ (parseFloat(item.price_adjustment) + parseFloat(item.price)).toFixed(2) }}
										</td>
										<td>
											<span v-show="!item.updating">{{ item.remarks }}</span> <input  class='form-control' v-show="item.updating" type="text" v-model='item.remarks'>
										</td>
										<td>
											{{ item.dateStr }}
										</td>

									</tr>
									</tbody>
								</table>
							</div>
							<div v-show="!item_posts.length">
								<div class="alert alert-info">
									No Record Found
								</div>
							</div>

						</div> <!-- End Log View -->

						<div v-show="con.spec_view">
							<h4 class='text-center'>Add Item Specification</h4>
							<div class="row">
								<div class="col-md-3"></div>
								<div class="col-md-6">
									<div class="panel panel-default">
										<div class="panel-body">

											<div class="form-group">
												<label for="">Item Name</label>
												<input v-model="spec_item_id" id='spec_item_id' type="text" class='form-control selectitem'>
											</div>
											 Specification
											<select class='form-control' @change="mainOptionChange" v-model='opt_main_spec'>
												<option v-for="option in main_specs" v-bind:value="option">
													{{ option }}
												</option>
											</select>

											<table class='table'>
												<thead><tr><th>Specification</th><th>Value</th></tr></thead>
												<tbody>
												<tr v-for="spec in current_specs">
													<td>{{spec.name}}</td>
													<td><input v-model='spec.value' class='form-control' type="text"></td>
												</tr>
												</tbody>
											</table>
											<hr>
											<div class='text-right'>
												<button class='btn btn-default' @click="addSpec">Submit</button>
											</div>

										</div>
									</div>
								</div>
								<div class="col-md-3"></div>
							</div>
						</div> <!-- End Spec View -->

						<div v-show="con.spec_list_view">
							<h4>List</h4>

							<table class='table'>
								<thead></thead>
								<tbody>
									<tr v-for="item in item_spec_list">
										<td v-bind:class="[ item.item_code != '' ? 'withTopBorder' : '']"><strong class='text-danger'>{{ item.item_code}}</strong></td>
										<td v-bind:class="[ item.item_code != '' ? 'withTopBorder' : '']">{{ item.spec_name}}</td>
										<td v-bind:class="[ item.item_code != '' ? 'withTopBorder' : '']">
											<span v-show="!item.is_editing">{{ item.spec_value}}</span>
											<input type="text" v-model="item.spec_value" v-show="item.is_editing">

										</td>
										<td v-bind:class="[ item.item_code != '' ? 'withTopBorder' : '']">
											<button v-show="!item.is_editing" class='btn btn-default' @click="editSpecs(item)"><i class='fa fa-pencil'></i></button>
											<button v-show="item.is_editing" class='btn btn-default' @click="saveSpecs(item)"><i class='fa fa-save'></i></button>
										</td>
										<td  v-bind:class="[ item.item_code != '' ? 'withTopBorder' : '']">
											<div v-show="item.item_code != ''">
												<button @click="deleteSpecsItem(item)" class='btn btn-default btn-sm'><i class='fa fa-trash'></i></button>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div> <!-- End Spec list View -->
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModalAttachment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content"  >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id=''>Uploading files to Order # {{ cur_order.id }}</h4>
					</div>
					<div class="modal-body" id=''>
						<p class='text-danger'>*Enter title and description before uploading.</p>
						<p class='text-danger'>*You can upload multiple files</p>
						<div class="row">
							<div class="col-md-6"><input type="text" class='form-control' v-model="att_title" placeholder="Title"></div>
							<div class="col-md-6"><input type="text" class='form-control' v-model="att_description" placeholder="Description"></div>
						</div>
						<br>
						<form class='dropzone' id="dropzone-form">
							<input type="hidden" id='dropzone_request_id'>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModalAttachmentList" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style='width:95%;' >

				<div class="modal-content"  >
					<div class="modal-header">
						Attachments
					</div>
					<div class="modal-body">

						<div class="row" v-if="attachments.length">
							<div class="col-md-4" v-for="att in attachments">
								<div class="thumbnail">
									<a v-bind:href="att.url" target="_blank">
										<div style='height: 210px;text-align: center;'>
											<img  style='max-height: 100%;max-width: 100%;' v-bind:src="att.thumbnail" alt="att.title">
										</div>
									</a>
									<div class="caption">
										<h5>{{ att.title }}</h5>
										<p>{{ att.description }}</p>
										<div>
											<button v-show="att.is_main == 0" @click="markAsMain(att)" class='btn btn-default btn-sm'>Mark as Thumbnail</button>
											<div v-show="att.is_main == 1" class='btn btn-success btn-sm' >Selected Thumbnail</div>
											<button @click="deletePic(att)" class='btn btn-danger btn-sm'>Delete</button>
										</div>
									</div>
								</div>

							</div>
						</div>
						<div v-else><div class="alert alert-info">No attachment</div></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script src='../js/vue.js'></script>
	<script src='../js/dropzone2.js'></script>
	<script>
		Dropzone.autoDiscover = false;
		var vm = new Vue({
			el :"#app",
			data: {
				title:'New App',
				con: {post_view:true,item_list_view:false,log_view:false,spec_view: false,spec_list_view:false},
				post: {item_id: '',qty:'',remarks:'', price: '0.00',price_adjustment:''},
				all_specs: [],
				main_specs: [],
				current_specs: [],
				opt_main_spec:'',
				item_posts: [],
				item_log: [],
				ajax_running:false,
				cur_request:{},
				att_title:'',
				att_description:'',
				attachments:[],
				spec_item_id:0,
				item_spec_list:[]

			},
			computed: {
				adjustedPrice: function(){
					var vm = this;
					var adj = vm.post.price_adjustment;
					if(isNaN(adj) || !adj) adj = 0;
					var price = replaceAll(vm.post.price,',','');
					var adjusted = parseFloat(adj) + parseFloat(price);

					return adjusted;
				},
				adjPrice: function(a,p){
					var adjusted = parseFloat(a) + parseFloat(p);
					return number_format(adjusted,2);

				}
			},
			ready: function(){
				var vm  = this;
				$('body').on('change','#item_id',function(){
					var con = $(this);
					var splitted =con.select2('data').text;
					splitted =	splitted.split(':');
					var price = splitted[3];
					price = (price) ? price : 0;
					vm.post.price = number_format(price,2);
				});
				try{
					this.main_specs = JSON.parse($('#main_specs').val());
					this.all_specs = JSON.parse($('#all_specs').val());
					this.opt_main_spec = this.main_specs[0];
					this.mainOptionChange();
				} catch(e){
					tempToast('error','Please Add Item specification type first.','');
				}
				var myDropzone = new Dropzone("#dropzone-form", {
						url: "../ajax/ajax_item_post.php?functionName=upload",
						acceptedFiles: "image/*"
					}
				);
				myDropzone.on('sending', function(file, xhr, formData){
					formData.append('request_id', vm.cur_request.id);
					formData.append('title', vm.att_title);
					formData.append('description', vm.att_description);
					vm.att_title = '';
					vm.att_description = '';
				});

			},
			methods: {
				deletePic: function(a){
					var vm = this;
					$.ajax({
						url:'../ajax/ajax_item_post.php',
						type:'POST',

						data: {functionName:'deletePicture', att: JSON.stringify(a)},
						success: function(data){
							tempToast('info',data,'');
							vm.getAttachments(a)

						},
						error:function(){

						}
					});
				},
				formatItemsel: function(o){

						if(!o.id)
							return o.text; // optgroup
						else {
							var r = o.text.split(':');
							return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
						}

				},
				updateItem: function(item){
					item.updating = true;
					var vm = this;
					$(".updateselectitem").select2({
						placeholder: 'Item code',
						allowClear: true,
						minimumInputLength: 2,
						formatResult: vm.formatItemsel,
						formatSelection: vm.formatItemsel,
						escapeMarkup: function(m) {
							return m;
						},
						ajax: {
							url: '../ajax/ajax_query.php',
							dataType: 'json',
							type: "POST",
							quietMillis: 50,
							data: function(term) {
								return {
									search: term, functionName: 'searchItemJSON'
								};
							},
							results: function(data) {
								return {
									results: $.map(data, function(item) {
										return {
											text: item.barcode + ":" + replaceAll(item.item_code,':','') + ":" + replaceAll(item.description,':','') + ":" + item.price,
											slug: item.description,
											is_bundle: item.is_bundle,
											unit_name: item.unit_name,
											id: item.id
										}
									})
								};
							}

						}
					}).on("select2-close", function(e) {
						// fired to the original element when the dropdown closes


					}).on("select2-highlight", function(e) {

					});
				},
				saveItem: function(item){
					$.ajax({
					    url:'../ajax/ajax_item_post.php',
					    type:'POST',
					    data: {functionName:'updateItem', item: JSON.stringify(item)},
					    success: function(data){
					        tempToast('info',data,'Info');
						    vm.getRecord(1);
						    item.updating = false;
					    },
					    error:function(){

					    }
					})
				},
				deleteItem: function(item){
					var vm = this;
					alertify.confirm("Are you sure you want to delete this record?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_item_post.php',
								type:'POST',
								data: {functionName:'deleteItem', item: JSON.stringify(item)},
								success: function(data){
									tempToast('info',data,'Info');
									vm.getRecord(1);
								},
								error:function(){

								}
							});
						}
					});
				},
				addLabel: function(item){
					var vm = this;
					alertify.prompt('Enter tag here: ', function (e, str) {
						if (e) {
							vm.updateLabel(item.id,str);
						} else {

						}
					}, '');
				},
				deleteLabel: function(item){
					var vm = this;
					alertify.confirm('Are you sure you want to continue this action?',function(e){
						if(e){
							vm.updateLabel(item.id,'');
						}

					});

				},
				updateLabel: function(id,str){
					$.ajax({
						url:'../ajax/ajax_item_post.php',
						type:'POST',
						data: {functionName:'updateLabel',id:id,label: str},
						success: function(data){
							tempToast('info',data,'Info');
							vm.getRecord(1);
						},
						error:function(){

						}
					});
				},
				addSpec: function(){

					if(this.ajax_running == false){
						if(!this.spec_item_id){
							tempToast('error',"Invalid data.",'');
							return;
						}
						var con = this;
						con.ajax_running =true;
						$.ajax({
							url:'../ajax/ajax_item_post.php',
							type:'POST',
							data: {functionName:'addSpec',item_id:con.spec_item_id, current_specs: JSON.stringify(con.current_specs)},
							success: function(data){
								con.ajax_running =false;

								tempToast('info',data,'');
								con.spec_item_id ='';
								$('#spec_item_id').select2('val',null);
								con.mainOptionChange();

							},
							error:function(){
								con.ajax_running =false;
								tempToast('error','Unavailable to make a server request. Please try again.','');
							}
						});
					}
				},
				markAsMain: function (a){
					var vm = this;
					$.ajax({
						url:'../ajax/ajax_item_post.php',
						type:'POST',

						data: {functionName:'markAsMainPicture', att: JSON.stringify(a)},
						success: function(data){
							tempToast('info',data,'');
							$('#myModalAttachmentList').modal('hide');
						},
						error:function(){

						}
					});
				},
				attachFiles: function(request){
					this.cur_request = request;
					Dropzone.forElement("#dropzone-form").removeAllFiles(true);
					$('#myModalAttachment').modal('show');
				},
				getAttachments : function (order){
					$('#myModalAttachmentList').modal('show');
					var vm = this;
					$.ajax({
						url:'../ajax/ajax_item_post.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getAtt',id:order.id},
						success: function(data){
							vm.attachments = data;
						},
						error:function(){

						}
					});
				},
				hideContainer: function(){
					this.con = {post_view:false,item_list_view:false,log_view:false,spec_view:false,spec_list_view:false}
				},
				showContainer: function(c){
					this.hideContainer();
					if(c == 1){
						this.con.post_view = true;
					} else if (c == 2){
						this.con.item_list_view = true;
						this.getRecord(1);

					}else if (c == 3){
						this.con.log_view = true;
						this.getRecord(2);
					}else if (c == 4){
						this.con.spec_view = true;

					}else if (c == 5){
						this.con.spec_list_view = true;
						this.getSpecList();
					}
				},
				editSpecs: function (item){
					item.is_editing = true;
				},
				saveSpecs: function(item){
					item.is_editing = false;
					$.ajax({
					    url:'../ajax/ajax_item_post.php',
					    type:'POST',
					    data: {functionName:'saveSpecs', item: JSON.stringify(item)},
					    success: function(data){
					        tempToast('info',data,'Info');
					    },
					    error:function(){
					        
					    }
					});
				},
				deleteSpecsItem: function(item){
					var vm = this;
					alertify.alert("Are you sure you want to delete this item specification?", function(e){
						$.ajax({
						    url:'../ajax/ajax_item_post.php',
						    type:'POST',
						    data: {functionName:'deleteSpecs', item: JSON.stringify(item)},
						    success: function(data){
							    tempToast('info',data,'Info');

							    vm.getSpecList();
						    },
						    error:function(){

						    }
						});
					});
				},
				getSpecList: function(){
					if(this.ajax_running == false){

						var con = this;
						con.ajax_running =true;
						$.ajax({
							url:'../ajax/ajax_item_post.php',
							type:'POST',
							dataType:'json',
							data: {functionName:'getSpecList'},
							success: function(data){
								con.ajax_running =false;

								con.item_spec_list = data;


							},
							error:function(){
								con.ajax_running =false;
								tempToast('error','Unavailable to make a server request. Please try again.','');
							}
						});
					}
				},
				mainOptionChange: function(){
					var child = this.all_specs[this.opt_main_spec];

					if(child){
						var arr = [];
						for(var i in child){
							arr.push({id:child[i].id,name:child[i].name, value: ''});
						}
						this.current_specs  = arr;

					}
				},
				submitRecord : function(){
					if(this.ajax_running == false){

						var con = this;
						con.ajax_running =true;
						$.ajax({
						    url:'../ajax/ajax_item_post.php',
						    type:'POST',
						    dataType:'json',
						    data: {functionName:'addPostRecord', post_request: JSON.stringify(this.post)},
						    success: function(data){
							    con.ajax_running =false;

							    if(data.success){
								    tempToast('info',data.message,'');
								    con.post =  {item_id: '',qty:'',remarks:''};
								    $('#item_id').select2('val',null);

							    } else {
								    tempToast('error',data.message,'');
								    con.post =  {item_id: '',qty:'',remarks:''};
								    $('#item_id').select2('val',null);

							    }


						    },
						    error:function(){
							    con.ajax_running =false;
							    tempToast('error','Unavailable to make a server request. Please try again.','');
						    }
						});
					}
				},
				getRecord: function(s){
					if(this.ajax_running == false){
						var con = this;
						con.ajax_running =true;
						$.ajax({
							url:'../ajax/ajax_item_post.php',
							type:'POST',
							dataType:'json',
							data: {functionName:'getRecord', status:s},
							success: function(data){
								con.ajax_running =false;

								con.item_posts = data;
							},
							error:function(){

							}
						});
					}
				}

			}
		});
		$(document).ready(function(){

		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>