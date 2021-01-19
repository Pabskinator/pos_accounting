<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	$company_name =  $thiscompany->name;
	$company_address =  $thiscompany->address;
	$company_description =  $thiscompany->description;
	$company_number =  $thiscompany->contact_number;
	$company_website =  $thiscompany->web_address;
	$company_email =  $thiscompany->email;

	$different_unit = 0;
	if(Configuration::getValue('d_unit') == 1){
		$different_unit = 1;
	}

?>
	<style>
		@media print {
			.withBG {
				background-color: #222 !important;
				color: #fff !important;
				-webkit-print-color-adjust: exact;
			}
			body{
				-webkit-print-color-adjust: exact !important;
			}
		}


	</style>


	<!-- Page content -->
	<div id="page-content-wrapper">
	<div id="page-content-wrapper-app">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Quotation
			</h1>

		</div>


				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><i class='fa fa-info '></i> Quotation information</div>
					<div class="panel-body">
						<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
							<a class='btn btn-default' @click="showContainer(1)" title='Request' href='#'> <span class='glyphicon glyphicon-pencil'></span> <span class='hidden-xs'>Request</span></a>
							<a class='btn btn-default' @click="showContainer(2)" title='List' href='#'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>List</span></a>

						</div>
						<div v-show="container.request">

							<div id="form" v-show="reprint != 1 || updating != 1">

								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<input type="text"   class='form-control' v-model='form.remarks' placeholder='Remarks/Quotation for'>
											<span class="help-block">Quotation</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">

											<div v-show="!already_member">
												<input type="text" class='form-control' v-model='form.client_name' placeholder='Company Name' >

											</div>
											<div v-show="already_member" >
												<input type="text" class='form-control' v-model='form.member_id' id='member_id'  placeholder='Member Name'>
											</div>
											<span class="help-block"><input type="checkbox" @change="toggleMember" v-model='already_member'> Already a Member</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.address'  placeholder='Address' >
											<span class="help-block">Address</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.contact_person'  placeholder='Contact Person'>
											<span class="help-block">Contact Person</span>
										</div>
									</div>
									</div>
									<div class='row'>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.contact_number' placeholder='Contact Number'>
											<span class="help-block">Contact Number</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.date' placeholder='Date'>
											<span class="help-block">Date</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.validity' placeholder='Validity'>
											<span class="help-block">Validity</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.note' placeholder='Note'>
											<span class="help-block">Note</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.payment_terms' placeholder='Payment Terms'>
											<span class="help-block">Payment Terms</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<input type="text" class='form-control' v-model='form.availability' placeholder='Availability'>
											<span class="help-block">Availability</span>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<select class='form-control' name="price_group_id" id="price_group_id" v-model="form.price_group_id">
												<?php
													$price_group_cls = new Price_group();
													$price_groups = $price_group_cls->get_active('price_groups',[1,'=',1]);

													echo "<option value='0'>Choose Price Group</option>";
													foreach($price_groups as $price_group){
														echo "<option value='{$price_group->id}'>$price_group->name</option>";
													}
												?>
											</select>
											<span class='help-block'>Member group</span>
										</div>
									</div>
								</div>
							</div>

							<div id="output" style='padding:10px;margin:0 auto;width: 816px;height: 1056px; border:1px dotted #ccc;'>
								<div >
									<div style='float:left;width:60%;margin-left: 30px;'>
										<h3> <span style='background: #222 !important;color:#fff !important;padding: 10px;'><img src="../css/img/logo.jpg" alt=""> <?php echo $company_name; ?></span></h3>
										<p><?php echo $company_address; ?>
											<br>Website: <?php echo $company_website; ?> Email: <?php echo $company_email; ?> </p>
									</div>

									<div style='float:left;width:30%;' class="text-right">
										<h3><span style='background: #222 !important;color:#fff !important;padding: 10px;'>Quotation</span></h3>
									</div>

									<hr>
									<table class="table table-bordered table-condensed" style='font-size: 9px !important;padding: 1px!important;'>
										<tr>
											<td  style='width:150px;background:#222 !important; color:white !important;'>Quotation For:</td>
											<td >{{form.remarks}}</td>

										</tr>
									</table>
									<table class="table table-bordered table-condensed" style='font-size: 9px !important;padding: 1px!important;'>
										<tr>
											<td class='withBG' style='width:150px;background:#222 !important;color:#fff !important;'>Company Name:</td>
											<td style='width:400px;'>{{form.client_name}}</td>
											<td class='withBG' style='width:100px;background:#222 !important;color:#fff !important;'>Quote #</td>
											<td>{{form.id_number}}</td>
										</tr>
										<tr>
											<td class='withBG' style='width:150px;border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Address:</td>
											<td style='width:400px;border-top:1px solid #ccc;'>{{form.address}}</td>
											<td class='withBG' style='width:100px;border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Date:</td>
											<td style='border-top:1px solid #ccc;'>{{form.date}}</td>
										</tr>
										<tr>
											<td class='withBG' style='width:150px;border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Contact Person:</td>
											<td style='width:400px;border-top:1px solid #ccc;'>{{form.contact_person}}</td>
											<td class='withBG' style='width:100px;border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Validity:</td>
											<td style='border-top:1px solid #ccc;'>{{form.validity}}</td>
										</tr>
										<tr>
											<td class='withBG' style='width:150px;border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Contact Number:</td>
											<td colspan='3' style='border-top:1px solid #ccc;'>{{form.contact_number}}</td>

										</tr>
									</table>

									<table class="table table-bordered table-condensed" style='font-size: 9px !important;padding: 1px!important;'>
										<tr>
											<td style='width:100px;border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Quantity</td>
											<td style='width:80px;border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Unit</td>
											<td style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Description</td>
											<td style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Price</td>
											<td style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;'>Total</td>
										</tr>
										<tr v-for="(item,index) in items">
											<td>{{item.qty}}</td>
											<td>{{item.unit}}</td>
											<td>{{item.description}}</td>
											<td>{{item.price_label}}</td>
											<td>{{item.total_label}}
												<span class='span-block'><i class='fa fa-remove fa-2x cpointer' @click="removeItem(index,item)"></i></span>
											</td>
										</tr>

										<tr class='text-danger text-center' v-show="!items.length"><td colspan="5">Add Item First</td></tr>
										<tr v-show="!preview || updating == 1">
											<td style='position: relative;' class='text-center' colspan="5"><button id='btnAdd'  style='position: absolute;right: -120px;top:-30px;' class='btn btn-default btn-sm' @click="addItem"> <i class='fa fa-plus'></i> Add Item </button></td>
										</tr>
										<tr>
											<td  class='withBG' style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' colspan="2">Payment Terms</td>
											<td style='border-top:1px solid #ccc;'>{{form.payment_terms}}</td>
											<td class='withBG' style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' >Sub Total</td>
											<td style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' >{{ sub }}</td>
										</tr>
										<tr>
											<td  class='withBG' style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' colspan="2">Availability</td>
											<td style='border-top:1px solid #ccc;'>{{form.availability}}</td>
											<td class='withBG' style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' >Vat</td>
											<td style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' >{{v}}</td>
										</tr>
										<tr>
											<td  class='withBG' style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' colspan="2">Note</td>
											<td style='border-top:1px solid #ccc;'>{{form.note}}</td>
											<td class='withBG' style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' ></td>
											<td style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' ></td>
										</tr>
										<tr>
											<td  style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' colspan="2"></td>
											<td style='border-top:1px solid #ccc;'>Price subject to change without prior notice upon validity date</td>
											<td class='withBG' style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' >Grand Total</td>
											<td style='border-top:1px solid #ccc;background:#222 !important;color:#fff !important;' >{{grand}}</td>
										</tr>
									</table>

									<blockquote style='font-size:9px;'>We trust you find this quote satisfactory and we look forward in
										<br> doing business with you for your present and future requirements
										<br> Kindly contact us at <?php echo $company_number; ?> for additional queries.
									</blockquote>


									<div style='float:left;width:21%;margin-left: 30px;'>
										Prepared By: <br><br>
										<span v-show="preview" style='display:block;border-bottom: 1px solid #ccc;'>{{form.prepared_by}}</span>
										<input v-show="!preview" type="text" class='form-control' v-model='form.prepared_by'>
									</div>
									<div style='float:left;width:21%;margin-left: 12px;'>
										Checked By: <br><br>
										<span v-show="preview" style='display:block;border-bottom: 1px solid #ccc;'>{{form.checked_by}}</span>
										<input v-show="!preview" type="text" class='form-control' v-model='form.checked_by'>
									</div>
									<div style='float:left;width:21%;margin-left: 12px;'>
										Approved By: <br><br>
										<span v-show="preview" style='display:block;border-bottom: 1px solid #ccc;'>{{form.approved_by}}</span>
										<input v-show="!preview" type="text" class='form-control' v-model='form.approved_by'>
									</div>
									<div style='float:left;width:21%;margin-left: 12px;'>
										Received By: <br><br>
										<span v-show="preview" style='display:block;border-bottom: 1px solid #ccc;'>{{form.received_by}}</span>
										<input v-show="!preview" type="text" class='form-control' v-model='form.received_by'>
									</div>

								</div>
							</div>
							<div class='text-center'>
								<button class='btn btn-default' v-show="preview && reprint != 1 && updating == 0" @click="preview = !preview">Back</button>
								<button class='btn btn-default' @click="preview = !preview" v-show="!preview & updating == 0">Preview</button>
								<button v-show="preview & updating == 0" class='btn btn-default' id='btnPrint' @click="printDiv">Print</button>
								<button v-show="reprint == 1" class='btn btn-default' @click="refreshPage">Refresh</button>
								<button v-show="updating == 1" class='btn btn-default' @click="updateRecord">Update</button>
							</div>

						</div> <!-- end container request -->
						<div v-show="container.list">
							<div class="row">
								<div class="col-md-9"></div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="filter_status" id="filter_status" class='form-control'>
											<option value="1">Pending</option>
											<option value="2">Approved</option>
											<option value="4">Ordered</option>
											<option value="3">Decline</option>
										</select>
									</div>
								</div>
							</div>
							<input type="hidden" id="hiddenpage" />
							<div id="holder"></div>
						</div> <!-- end container list -->


					</div>
				</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='mtitle'></h4>
						</div>
						<div class="modal-body" id='mbody'>
							<div>

								<div v-show="!new_item">

									<strong>Item:</strong>
									<input type="text" class='form-control selectitem'  v-model='item.item_id' id='item_id'>

								</div>

								<div v-show="new_item">

									<strong>Item: </strong>
									<input type="text" autocomplete="off" class='form-control'  v-model='item.description' id='description'>

									<strong>Price: </strong>
									<input type="text" autocomplete="off" class='form-control'  v-model='item.price' id='price'>

									<strong>Unit: </strong>
									<input type="text" autocomplete="off" class='form-control'  v-model='item.unit' id='unit'>

								</div>

								<input type="checkbox" v-model='new_item'> Not in the system yet

							</div>

							<div v-show="show_adj" >
								<strong>Price:</strong> <input type="text" class='form-control'  v-model='item.addtl_adjustment' id='adjustment'>
							</div>


							<?php
								if($different_unit == 1){
									?>
									<div v-show="multiplier_qty.length">
											<strong>Unit:</strong>
											<select name="dif_qty" id="dif_qty" v-model="dif_qty" class='form-control'>
												<option v-for="qtys in multiplier_qty" v-bind:value="qtys.qty">{{qtys.unit_name}}</option>
											</select>
									</div>
									<?php
								}
							?>
							<strong>Qty:</strong> <input type="text" class='form-control' id='qty' v-model='item.qty'> <br>
							<button class='btn btn-default' @click="saveItem()">Add Item</button>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<div class="modal fade" id="myModalOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Order Request</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<strong>Client: </strong>
								<div class="form-group">
									<input type="text" class='form-control' id='order_member_id'>
									<input type="hidden" class='form-control' id='order_id'>
								</div>
							</div>
							<div class="col-md-6">
								<strong>Branch: </strong>
								<div class="form-group">
									<input type='text' class='form-control' id='order_branch_id' >
								</div>
							</div>
							<div class="col-md-12">
								<strong>Remarks: </strong>
								<div class="form-group">
									<input type='text' placeholder='Enter Remarks' class='form-control' id='order_remarks' >
								</div>
							</div>
						</div>


						<br>
						<table class='table table-bordered'>
							<tr v-for="oi in ordered_items">
								<td>{{ oi.description }}</td>
								<td><input type="text" v-model="oi.computed_qty" @change="changeOrderQty(oi)"></td>
								<td>{{ oi.price_label }}</td>
								<td>{{ oi.total_label }}</td>
							</tr>
						</table>
						<br>
						<button v-show="ordered_items.length" class='btn btn-primary' @click='finalizeOrder'>Finalize Order</button>
						<div v-show="!ordered_items.length" >No valid items to order.</div>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<div class="modal fade" id="myModalUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='utitle'></h4>
					</div>
					<div class="modal-body" id='ubody'>
						<div class="row">

							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class='form-control selectitem' id='update_item_id'>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class='form-control' id='update_qty' placeholder='Qty'>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<button>Add</button>
								</div>
							</div>
						</div>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

	</div> <!-- end page content wrapper-->
	</div>
	<script src='../js/vue3.js'></script>
	<script>
		var vm = new Vue({
			el:'#page-content-wrapper-app',
			data: {
				new_item: false,
				already_member: false,
				container:{request: true,list:false},
				form: {id_number:'Auto',prepared_by:'',received_by:'',approved_by:'',checked_by:'',client_name:'',address:'',contact_person:'',contact_number:'',date:'',validity:'30',remarks:'',note:'',payment_terms:'80% down payment, 20% upon delivery',availability:'1-2 weeks upon receipt of down payment',member_id:'',	price_group_id:'0'},
				items:[],
				item:{ item_id:'', unit:'',addtl_adjustment:'',  item_code:'',description:'',qty:'',price:'',computed_qty:'',total:'', price_label:'', total_label:'',adj:''},
				multiplier_qty: [],
				ordered_items: [],
				dif_qty:1,
				preview:false,
				vat:0,
				subtotal:0,
				grandtotal:0,
				reprint:0,
				updating:0,
				service_ids:[1520,1521],
				show_adj: false,

			},
			computed:{
				sub: function (){
					return number_format(this.subtotal,2);
				},
				v: function (){
					return number_format(this.vat,2);
				},
				grand: function (){
					return number_format(this.grandtotal,2);
				}
			},
			mounted: function(){
				var self = this;
				self.form.date = self.currentDate();

				$('body').on('change','#item_id',function(){

					var i = $(this);
					var data = i.select2('data').text;
					var split = data.split(':');
					self.item.item_id= i.val();
					self.item.price = split[3];
					self.item.price_label = number_format(split[3],2);
					self.item.item_code = split[1];
					self.item.description = split[2];
					self.item.unit = i.select2('data').unit_name;
					self.inFlexiList();

					$.ajax({
						url:'../ajax/ajax_wh_order.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getItemInfo',item_id: i.val()},
						success: function(dd){
							self.dif_qty = 1;
							if(dd.units.length){
								self.multiplier_qty = dd.units;
							} else {
								self.multiplier_qty = [];
							}

						},
						error:function(){

						}
					});

				});


				$('body').on('click','.paging',function(e){
					e.preventDefault();
					var page = $(this).attr('page');
					$('#hiddenpage').val(page);
					self.getPage(page);
				});

				$('#member_id').select2({
					placeholder: 'Search Client' , allowClear: true, minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
							return {
								q: term, functionName: 'members',
							};
						}, results: function(data) {
							return {
								results: $.map(data, function(item) {
									return {
										text: item.lastname + ", " + item.sales_type_name,
										slug: item.lastname + ", " + item.firstname + " " + item.middlename,
										id: item.id
									}
								})
							};
						}
					}
				});

				$('#order_member_id').select2({
					placeholder: 'Search Client' , allowClear: true, minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
							return {
								q: term, functionName: 'members',
							};
						}, results: function(data) {
							return {
								results: $.map(data, function(item) {
									return {
										text: item.lastname + ", " + item.sales_type_name,
										slug: item.lastname + ", " + item.firstname + " " + item.middlename,
										id: item.id
									}
								})
							};
						}
					}
				});



				$('#member_id').change(function(){

					self.form.member_id = $(this).val();
					self.form.client_name = $('#member_id').select2('data').text;

					$.ajax({
						url:'../ajax/ajax_wh_order.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getOwnedBranch',member_id:self.form.member_id},
						success: function(data){

							if(!data.price_group_id){
								data.price_group_id = '0';
							}

							self.form.price_group_id = data.price_group_id;

							self.form.address = data.personal_address;

							self.form.contact_number = data.contact_number;

							self.form.contact_person = data.contact_person;

						},
						error:function(){

						}
					});
				});
				
				$('body').on('click','.btnReprint',function(){
					
					var con = $(this);
					var id = con.attr('data-id');
					
					$.ajax({
					    url:'../ajax/ajax_item.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'reprintItems',id:id},
					    success: function(data){
						    self.showContainer(1);
					        self.form = data.form;
						    self.items = data.items;
						    self.reprint = 1;
						    self.preview = true;
						    self.generateTotals();
						    $('#btnAdd').tooltip('hide');
					    },
					    error:function(){
					        
					    }
					});
				});

				$('body').on('click','.btnUpdate',function(){

					var con = $(this);
					var id = con.attr('data-id');

					$.ajax({
					    url:'../ajax/ajax_item.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'reprintItems',id:id},
					    success: function(data){
						    self.showContainer(1);
					        self.form = data.form;
						    self.items = data.items;
						    self.updating = 1;
						    self.preview = true;
						    self.generateTotals();
					    },
					    error:function(){

					    }
					});
				});

				$('body').on('click','.btnApprove',function(){
					var con = $(this);
					var id = con.attr('data-id');
					alertify.confirm("Are you sure you want to approve this record?", function(e){
						if(e){
							self.approveRequest(id);
						}
					});
				});

				$('body').on('click','.btnDetails',function(){
					var con = $(this);
					var id = con.attr('data-id');
					var member_id = con.attr('data-member_id');
					var member_name = con.attr('data-member_name');
					if(member_id != 0){
						$('#order_member_id').select2('data',{text:member_name,id:member_id});
					} else {
						$('#order_member_id').select2('val',null);
					}

					$('#order_id').val(id);

					$.ajax({
					    url:'../ajax/ajax_item.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getOrderedItems',id:id},
					    success: function(data){
						    self.ordered_items = data;
					    },
					    error:function(){
					        
					    }
					});

					$('#myModalOrder').modal('show');

				});


				$('body').on('click','.btnDecline',function(){
					var con = $(this);
					var id = con.attr('data-id');

					alertify.confirm("Are you sure you want to approve this record?", function(e){
						if(e){
							self.declineRequest(id);
						}
					});
				});

				$('body').on('change','#filter_status',function(){
					self.getPage(0);
				});

				$('#order_branch_id').select2({
					placeholder: 'Branch',
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
								functionName:'branches'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.name ,
										slug: item.name ,
										id: item.id
									}
								})
							};
						}
					}
				});

				/*$('body').on('click','.btnUpdate',function(){

					var con = $(this);

					var id = con.attr('data-id');

					$('#myModalUpdate').modal('show');

				}); */

			},
			methods: {
				removeItem: function(i,item){
					var self = this;

					self.subtotal = parseFloat(self.subtotal) - parseFloat(item.total);
					self.vat = 0;
					self.grandtotal = self.subtotal - self.vat;
					self.items.splice(i,1);


				},

				updateRecord: function(){
					var self = this;

					$.ajax({
						url:'../ajax/ajax_item.php',
						type:'POST',
						data: {functionName:'updateQuotation',form:JSON.stringify(self.form),items: JSON.stringify(self.items)},
						success: function(data){

							self.updating = 0;
							self.showContainer(2);

						},
						error:function(){

						}
					});
				},
				inFlexiList: function(){
					var self = this;
					self.show_adj = false;
					for(var i in self.service_ids){
						if(self.service_ids[i] == self.item.item_id){
							self.show_adj =  true;
						}
					}


				},
				finalizeOrder: function(){

					var member_id = $('#order_member_id').val();
					var order_id = $('#order_id').val();
					var branch_id = $('#order_branch_id').val();
					var remarks = $('#order_remarks').val();
					var self = this;

					if(!(member_id && order_id)){
						alert("Complete the form first.");
					} else {

						$.ajax({
							url: '../ajax/ajax_item.php',
							type: 'POST',
							data: {
								functionName: 'submitOrder',
								remarks: remarks,
								branch_id: branch_id,
								id: order_id,
								member_id: member_id,
								items: JSON.stringify(self.ordered_items)
							},
							success: function(data) {
								alert(data);
							},
							error: function() {

							}
						});

					}



				},
				changeOrderQty: function(oi){
					oi.total_label = number_format((oi.computed_qty * oi.price),2);
				},
				resetPage: function(){
					var self = this;
					self.already_member = false,
					self.container = {request: true,list:false};
					self.form = {id_number:'Auto',prepared_by:'',received_by:'',approved_by:'',checked_by:'',client_name:'',address:'',contact_person:'',contact_number:'',date:'',validity:'30',remarks:'',note:'',payment_terms:'80% down payment, 20% upon delivery',availability:'1-2 weeks upon receipt of down payment',member_id:'',	price_group_id:'0'};
					self.items = [];
					self.item = { item_id:'', unit:'', item_code:'',description:'',qty:'',price:'',computed_qty:'',total:'', price_label:'', total_label:'',adj:''};
					self.multiplier_qty =[];
					self.dif_qty =1;
					self.preview=false;
					self.vat=0;
					self.subtotal=0;
					self.grandtotal=0;
					self.reprint=0;
					self.updating=0;
				},
				refreshPage: function(){
					location.href = 'quotation.php';
				},
				declineRequest: function(id){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_item.php',
					    type:'POST',
					    data: {functionName:'updateQuotationStatus',id:id, status:3},
					    success: function(data){
						    self.getPage(0);
					        alert(data);
					    },
					    error:function(){

					    }
					});
				},
				approveRequest: function(id){
					var self = this;
					$.ajax({
						url:'../ajax/ajax_item.php',
						type:'POST',
						data: {functionName:'updateQuotationStatus',id:id, status:2},
						success: function(data){
							self.getPage(0);
							alert(data);
						},
						error:function(){

						}
					});
				},
				toggleMember: function(){
					if(!this.already_member){
						this.form.member_id = '';
						this.form.client_name = '';
					}
				},
				getPage: function(p){
					var filter_status = $('#filter_status').val();

					$.ajax({
						url: '../ajax/ajax_paging_2.php',
						type:'post',
						beforeSend:function(){
							$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
						},
						data:{page:p,functionName:'quotationPaginate', status:filter_status},
						success: function(data){
							$('#holder').html(data);
						}
					});
				},
				showContainer: function(c){
					var self = this;
					self.container  = { request: false, list:false};
					if(c == 1){
						self.container.request = true;
						self.resetPage();
					} else if (c == 2){
						self.container.list = true;
						self.getPage(0);
					} else {
						self.container.request = true;
					}
				},
				currentDate: function(){
					var today = new Date();
					var dd = today.getDate();
					var mm = today.getMonth()+1; //January is 0!

					var yyyy = today.getFullYear();
					if(dd<10){
						dd='0'+dd;
					}
					if(mm<10){
						mm='0'+mm;
					}
					var today = mm+'/'+dd+'/'+yyyy;
					return today;
				},
				addItem: function(){
					var self = this;
					$('#item_id').select2('val',null);

					self.item = {item_id:'',unit:'',item_code:'',description:'',qty:'',price:'',computed_qty:'',total:'', price_label:'', total_label:'',adj:''};
					$('#myModal').modal('show');
				},
				itemExists: function(item_id){
					var self = this;
					var e = false;
					for(var i in self.items){
						if(self.items[i].item_id == item_id){
							e = true;
						}
					}
					return e;
				},
				saveItem: function(){
					var self= this;
					if(!self.item.item_id && self.item.description){
						self.item.item_id = -1;
					}
					if(self.item.item_id && self.item.qty){

						if(self.item.item_id != -1 && self.itemExists(self.item.item_id)){
							alert("Item already added.");
							return;
						}

						if(!self.dif_qty){
							self.dif_qty = 1;
						}

						self.item.computed_qty = self.item.qty * self.dif_qty;

						// query
						$.ajax({
							url:'../ajax/ajax_item.php',
							type:'POST',
							data: {functionName:'getPriceAdjustment', qty:self.item.computed_qty,member_id:self.form.member_id,item_id:self.item.item_id,price_group_id:self.form.price_group_id},
							success: function(data){
								var adj = 0;
								if(data){
									adj =  parseFloat(data);
								}
								if(adj){
									self.item.price = parseFloat(self.item.price) + parseFloat(adj);
									self.item.price_adjustment = parseFloat(adj);
								} else {
									self.item.price_adjustment = 0;
								}
								self.item.price_total =  (self.item.price * (self.item.computed_qty / self.item.qty));
								self.item.total =  self.item.price * self.item.computed_qty;
								self.item.total_label = number_format(self.item.total,2);
								self.item.price_label = number_format(self.item.price_total,2);
								self.item.total= (self.item.total ) ? self.item.total : 0;


								if(self.dif_qty){

									self.item.unit = $('#dif_qty :selected').text();

								}


								if(self.item.addtl_adjustment){

									self.item.price = self.item.addtl_adjustment;
									self.item.price_total =  (self.item.addtl_adjustment * (self.item.computed_qty / self.item.qty));
									self.item.total =  self.item.addtl_adjustment * self.item.computed_qty;
									self.item.total_label = number_format(self.item.total,2);
									self.item.price_label = number_format(self.item.price_total,2);
									self.item.total= (self.item.total ) ? self.item.total : 0;

								}

								self.subtotal = parseFloat(self.subtotal) + parseFloat(self.item.total);
								self.vat = 0;
								self.grandtotal = self.subtotal - self.vat;

								self.items.push(self.item);
								$('#item_id').select2('val',null);
								self.multiplier_qty= [];
								self.dif_qty= 1;


								self.item = {item_id:'',unit:'',item_code:'',description:'',qty:'',computed_qty:'',price:'',price_total:'',total:'', price_label:'', total_label:'',adj:''};
								$('#myModal').modal('hide');

							},
							error:function(){

							}
						});


					} else {
						alert("Choose item and qty");
					}

				},
				generateTotals: function(){
					var self= this;
					if(self.items.length){
						var items = self.items;
						var total = 0;
						for(var i in items){
							total = parseFloat(items[i].total) + parseFloat(total);
						}
						var vat = 0;
						var grandtotal = total - vat;

						self.subtotal = total;
						self.vat = vat;
						self.grandtotal = grandtotal
					}
				},
				popUpPrintWithStyle: function(data) {
					var self = this;
					var mywindow = window.open('', 'new div', '');
					mywindow.document.write('<html><head><title></title><style></style>');
					mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
					mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
					mywindow.document.write(data);
					mywindow.document.write('</body></html>');
					setTimeout(function() {
						mywindow.print();
						mywindow.close();
						if(self.reprint == 1){
							self.reprint = 0;
							self.showContainer(2);
						}

						self.form = {id_number:'Auto',prepared_by:'',received_by:'',approved_by:'',checked_by:'',client_name:'',address:'',contact_person:'',contact_number:'',date:'',validity:'30',remarks:'',note:'',payment_terms:'80% down payment, 20% upon delivery',availability:'1-2 weeks upon receipt of down payment',member_id:'',	price_group_id:'0'};
						self.items = [];


					}, 300);
					return true;
				},
				printDiv: function(){
					var con = $('#btnPrint');
					button_action.start_loading(con);
					var self = this;

					if(self.reprint == 1){

						var html = $('#output').html();
						self.popUpPrintWithStyle(html);

					} else {

						$.ajax({
							url:'../ajax/ajax_item.php',
							type:'POST',
							data: {functionName:'saveQuotation',form:JSON.stringify(self.form),items: JSON.stringify(self.items)},
							success: function(data){
								self.form.id_number = data;
								button_action.end_loading(con);
								setTimeout(function(){
									var html = $('#output').html();
									self.popUpPrintWithStyle(html);
								},500);

							},
							error:function(){

							}
						});

					}





				}
			}
		})

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>