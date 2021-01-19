<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('supplier_ol')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$supplier = new Supplier();
	$suppliers = $supplier->get_active('suppliers',array('company_id' ,'=',$user->data()->company_id));

	$digital_sign = Configuration::getValue('digital_sign');
	if($digital_sign != 1){
		$digital_sign = 0;
	}
	$signatory = $thiscompany->signatory;


	$barcodeClass = new Barcode();
	$barcode_format = $barcodeClass->getFormat($user->data()->company_id,"SUPPLIER");

	$by_branch = $barcodeClass->get_format_by_branch($user->data()->branch_id,"SUPPLIER");
	$has_own_layout = 0;
	if($by_branch){
		$has_own_layout = 1;
		$barcode_format = $by_branch;
	}
	$styles =  $barcode_format->styling;
?>
	<link rel="stylesheet" href="../css/dropzone2.css?v=1">
	<style>

	</style>
	<div id='app'>
	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<?php include 'includes/supplier_nav.php'; ?>
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Supplier Order Monitoring </h1>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('flash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
				}

				include  'includes/supplier_order_nav.php';

			?> <input type="hidden" value='<?php echo $styles; ?>' id='form_layout'>
			<input type="hidden" value='<?php echo date('m/d/Y'); ?>' id='server_date'>
			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading"></div>
				<div class="panel-body">
					<input type="hidden" id='digital_sign' value='<?php echo $digital_sign; ?>'>
					<input type="hidden" id='signatory' value='<?php echo $signatory; ?>'>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" placeholder='Search...' id='search' class='form-control' v-model='search'>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select name="supplier_id" id="supplier_id" class='form-control' v-model='supplier_id'>
									<option value=""></option>
									<?php
										foreach($suppliers as $sup):
											?>
											<option value="<?php echo $sup->id; ?>"><?php echo $sup->name; ?></option>
										<?php endforeach;?>
								</select>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<input type="hidden" id='branch_id' class='form-control' v-model='branch_id'>
							</div>
						</div>

					</div>
			<div v-show="views.pending">

				<div v-if="orders.length && views.pending">
					<div id="no-more-tables">

						<table class='table' id='tblBranches'>
							<thead>
							<tr>
								<TH>Details</TH>
								<th class='text-center'>Actions</th>
							</tr>
							</thead>
							<tbody>
							<paginate
								name="orders"
								:list="cmp_orders"
								:per="10"

								>

								<tr v-for="order in paginated('orders')" v-bind:class="[order.is_rush == 1 ? 'bg-danger': '']">

									<td class="withTopBorder"  data-title='Id'>
										<div class="panel panel-primary">
											<div class="panel-heading">Primary Info</div>
											<div class="panel-body">
												<table class='table'>
													<tr><td><strong>ID</strong></td><td>{{order.id}}</td></tr >
													<tr><td><strong>Supplier</strong></td><td>{{ order.sname }}<br> <small>{{order.sdesc}}</small></td></tr >
													<tr><td><strong>Branch</strong></td><td>{{order.bname}}</td></tr >
													<tr><td><strong>Created</strong></td><td>{{order.created}}</td></tr >
													<tr><td><strong>Order by</strong></td><td>{{order.lastname}}</td></tr >
												</table>
											</div>
										</div>

									</td>


									<td class="withTopBorder text-center">
										<div style='margin:0 auto;width:130px;'>
										<button class='btn btn-default btn-fixed-width-md' @click="showDetails(order.bname,order.baddress,order.id,order.status)">Details</button>
										<button class='btn btn-default btn-fixed-width-md' @click="showTimelog(order.timelog)">Timelog</button>
										<button class='btn btn-default btn-fixed-width-md' @click="attachFiles(order)">Upload File</button>
										<button class='btn btn-default btn-fixed-width-md' @click="getAttachments(order)">Attachment</button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<div class='text-center'>
						<paginate-links for="orders"
						                :classes="{
	                                        'ul': 'pagination',}
	                                        "
						                :limit="10"
						                :show-step-links="true"
							></paginate-links>
						</div>

					</div>
				</div>
				<div v-else><div class="alert alert-info">No Record found.</div></div>
			</div>
			<!-- end pending -->
			<div v-show="views.approved">

			<div class="row">
				<div class="col-md-12">
					<div v-if="orders.length && views.approved">

								<div id="no-more-tables">
									<table class='table' id='tblBranches'>
										<thead>
										<tr>
											<TH>Details</TH>
											<th></th>
											<th></th>
											<th class='text-center'>Actions</th>
										</tr>
										</thead>
										<tbody>
										<paginate
											name="orders"
											:list="cmp_orders"
											:per="10"

											>

											<tr v-for="order in paginated('orders')" v-bind:class="[order.is_rush == 1 ? 'bg-danger': '']">

												<td class="withTopBorder"  data-title='Id'>
													<div class="panel panel-primary">
														<div class="panel-heading">Primary Info</div>
														<div class="panel-body">
															<table class='table'>
																<tr><td><strong>ID</strong></td><td>{{order.id}}</td></tr >
																<tr><td><strong>Supplier</strong></td><td>{{ order.sname }}<br> <small>{{order.sdesc}}</small></td></tr >
																<tr><td><strong>Branch</strong></td><td>{{order.bname}}</td></tr >
																<tr><td><strong>Created</strong></td><td>{{order.created}}</td></tr >
																<tr><td><strong>Order by</strong></td><td>{{order.lastname}}</td></tr >
															</table>
														</div>
													</div>

												</td>
												<td class="withTopBorder" data-title='Remarks'>
													<div class="panel panel-primary">
														<div class="panel-heading">Other Info</div>
														<div class="panel-body">
															<table class='table'>
																<tr>
																	<td><strong>PO #</strong></td>
																	<td>{{order.po_number}}</td>
																</tr>
																<tr>
																	<td><strong>Invoice</strong></td>
																	<td>{{order.invoice}}</td>
																</tr>
																<tr>
																	<td><strong>DR</strong></td>
																	<td>{{order.dr}}</td>
																</tr>
																<tr>
																	<td><strong>CR</strong></td>
																	<td>{{order.cr}}</td>
																</tr>
																<tr>
																	<td><strong>Item Cost</strong></td>
																	<td>{{order.cost}}</td>
																</tr>
																<tr>
																	<td><strong>Terms</strong></td>
																	<td>{{order.terms}}</td>
																</tr>
																<tr>
																	<td><strong>Due Date</strong></td>
																	<td>{{order.due_date}}</td>
																</tr>
																<tr>
																	<td><strong>Delivery Date</strong></td>
																	<td>{{order.expected_delivery_date}}</td>
																</tr>
															</table>
														</div>
													</div>
												</td>
												<td class="withTopBorder">
													<div class="panel panel-primary">
														<div class="panel-heading">Delivery Info</div>
														<div class="panel-body">
															<table class='table'>
																<tr>
																	<td>

																		<div v-html="order.date_delivered"></div>
																	</td>

																</tr>

															</table>
															<button class='btn btn-primary'  @click="receiveHistory(order)">Receive History</button>
															</div>
														</div>

												</td>

												<td class="withTopBorder" >
													<div style='width:130px; margin: 0 auto;'>
														<button class='btn btn-default btn-fixed-width-md' @click="showDetails(order.bname,order.baddress,order.id,order.status)">Details</button>
														<button class='btn btn-default btn-fixed-width-md' @click="showTimelog(order.timelog)">Timelog</button>
														<button class='btn btn-default btn-fixed-width-md' @click="getAttachments(order)">Attachment</button>
														<button class='btn btn-default btn-fixed-width-md' @click="showExpense(order)">Expense</button>
														<button class='btn btn-default btn-fixed-width-md' @click="showPayment(order)">Payment</button>

													</div>

												</td>

											</tr>
										</tbody>

									</table>
									<div class='text-center'>
									<paginate-links for="orders"
									                :classes="{
	                                        'ul': 'pagination',}
	                                        "
									                :limit="10"
									                :show-step-links="true"
										></paginate-links>
									</div>

						</div>
					</div>
					<div v-else><div class="alert alert-info">No Record found.</div></div>
				</div>
			</div>
		</div>
					<div v-show="views.received">

						<div class="row">
							<div class="col-md-12">
								<div v-if="orders.length && views.received">

									<div id="no-more-tables">

										<table class='table' id='tblBranches'>
											<thead>
											<tr>
												<TH>Details</TH>
												<th></th>
												<th></th>
												<th class='text-center'>Actions</th>
											</tr>
											</thead>
											<tbody>
											<paginate
												name="orders"
												:list="cmp_orders"
												:per="10"

												>

												<tr v-for="order in paginated('orders')" v-bind:class="[order.is_rush == 1 ? 'bg-danger': '']">

													<td class="withTopBorder"  data-title='Id'>
														<div class="panel panel-primary">
															<div class="panel-heading">Primary Info</div>
															<div class="panel-body">
																<table class='table'>
																	<tr><td><strong>ID</strong></td><td>{{order.id}}</td></tr >
																	<tr><td><strong>Supplier</strong></td><td>{{ order.sname }}<br> <small>{{order.sdesc}}</small></td></tr >
																	<tr><td><strong>Branch</strong></td><td>{{order.bname}}</td></tr >
																	<tr><td><strong>Created</strong></td><td>{{order.created}}</td></tr >
																	<tr><td><strong>Order by</strong></td><td>{{order.lastname}}</td></tr >
																</table>
															</div>
														</div>

													</td>
													<td class="withTopBorder" data-title='Remarks'>
														<div class="panel panel-primary">
															<div class="panel-heading">Other Info</div>
															<div class="panel-body">
																<table class='table'>
																	<tr>
																		<td><strong>PO #</strong></td>
																		<td>{{order.po_number}}</td>
																	</tr>
																	<tr>
																		<td><strong>Invoice</strong></td>
																		<td>{{order.invoice}}</td>
																	</tr>
																	<tr>
																		<td><strong>DR</strong></td>
																		<td>{{order.dr}}</td>
																	</tr>
																	<tr>
																		<td><strong>CR</strong></td>
																		<td>{{order.cr}}</td>
																	</tr>
																	<tr>
																		<td><strong>Item Cost</strong></td>
																		<td>{{order.cost}}</td>
																	</tr>
																	<tr>
																		<td><strong>Terms</strong></td>
																		<td>{{order.terms}}</td>
																	</tr>
																	<tr>
																		<td><strong>Due Date</strong></td>
																		<td>{{order.due_date}}</td>
																	</tr>
																	<tr>
																		<td><strong>Expected Delivery</strong></td>
																		<td>{{order.expected_delivery_date}}</td>
																	</tr>
																</table>
															</div>
														</div>
													</td>
													<td class="withTopBorder">
														<div class="panel panel-primary">
															<div class="panel-heading">Delivery Info</div>
															<div class="panel-body">
																<table class='table'>
																	<tr>
																		<td>

																			<div v-html="order.date_delivered"></div>
																		</td>

																	</tr>
																</table>

																<button class='btn btn-primary'  @click="receiveHistory(order)">Receive History</button>

															</div>
														</div>
													</td>

													<td class="withTopBorder" >
														<div style='width:130px; margin: 0 auto;'>
															<button class='btn btn-default btn-fixed-width-md' @click="showDetails(order.bname,order.baddress,order.id,order.status)">Details</button>
															<button class='btn btn-default btn-fixed-width-md' @click="showTimelog(order.timelog)">Timelog</button>
															<button class='btn btn-default btn-fixed-width-md' @click="attachFiles(order)">Upload File</button>
															<button class='btn btn-default btn-fixed-width-md' @click="getAttachments(order)">Attachment</button>
															<button class='btn btn-default btn-fixed-width-md' @click="showExpense(order)">Expense</button>
															<button class='btn btn-default btn-fixed-width-md' @click="showPayment(order)">Payment</button>

														</div>
													</td>

												</tr>
											</tbody>

										</table>

										<div class='text-center'>
											<paginate-links for="orders"
											                :classes="{
	                                        'ul': 'pagination',}
	                                        "
											                :limit="10"
											                :show-step-links="true"
												></paginate-links>
										</div>

									</div>
								</div>
								<div v-else><div class="alert alert-info">No Record found.</div></div>
							</div>
						</div>
					</div>
			<!-- end view approved -->
			<div v-show="views.processed">

				<div v-if="orders.length && views.processed">

					<div id="no-more-tables">

						<table class='table' id='tblBranches'>
							<thead>
							<tr>
								<TH>Details</TH>
								<th></th>
								<th class='text-center'>Actions</th>
							</tr>
							</thead>
							<tbody>
							<paginate
								name="orders"
								:list="cmp_orders"
								:per="10"

								>

							<tr v-for="order in paginated('orders')" v-bind:class="[order.is_rush == 1 ? 'bg-danger': '']">

								<td class="withTopBorder"  data-title='Id'>
									<div class="panel panel-primary">
										<div class="panel-heading">Primary Info</div>
										<div class="panel-body">
											<table class='table'>
												<tr><td><strong>ID</strong></td><td>{{order.id}}</td></tr >
												<tr><td><strong>Supplier</strong></td><td>{{ order.sname }}<br> <small>{{order.sdesc}}</small></td></tr >
												<tr><td><strong>Branch</strong></td><td>{{order.bname}}</td></tr >
												<tr><td><strong>Created</strong></td><td>{{order.created}}</td></tr >
												<tr><td><strong>Order by</strong></td><td>{{order.lastname}}</td></tr >
											</table>
										</div>
									</div>
								</td>

								<td class="withTopBorder" data-title='Remarks'>
									<div class="panel panel-primary">
										<div class="panel-heading">Other Info</div>
										<div class="panel-body">
										<table class='table'>
										<tr>
											<td><strong>PO #</strong></td>
											<td>{{order.po_number}}</td>
										</tr>
										<tr>
											<td><strong>Invoice</strong></td>
											<td>{{order.invoice}}</td>
										</tr>
										<tr>
											<td><strong>DR</strong></td>
											<td>{{order.dr}}</td>
										</tr>
										<tr>
											<td><strong>CR</strong></td>
											<td>{{order.cr}}</td>
										</tr>

										<tr>
											<td><strong>Item Cost</strong></td>
											<td>{{order.cost}}</td>
										</tr>

										<tr>
											<td><strong>Terms</strong></td>
											<td>{{order.terms}}</td>
										</tr>
										<tr>
											<td><strong>Due Date</strong></td>
											<td>{{order.due_date}}</td>
										</tr>
										<tr>
											<td><strong>Expected Delivery</strong></td>
											<td>{{order.expected_delivery_date}}</td>
										</tr>
									</table>
										</div>
									</div>
								</td>

								<td class="withTopBorder" >
									<div style='width:130px; margin: 0 auto;'>
										<button class='btn btn-default btn-fixed-width-md' @click="showDetails(order.bname,order.baddress,order.id,order.status)">Details</button>
										<button class='btn btn-default btn-fixed-width-md' @click="showTimelog(order.timelog)">Timelog</button>
										<button class='btn btn-default btn-fixed-width-md' @click="attachFiles(order)">Upload File</button>
										<button class='btn btn-default btn-fixed-width-md' @click="getAttachments(order)">Attachment</button>
										<button class='btn btn-default btn-fixed-width-md' @click="showExpense(order)">Expense</button>

									</div>
								</td>
							</tr>

							</tbody>
						</table>
						<div class='text-center'>
							<paginate-links for="orders"
							                :classes="{
	                                        'ul': 'pagination',}
	                                        "
							                :limit="10"
							                :show-step-links="true"

								></paginate-links>
						</div>
					</div>
				</div>
				<div v-else><div class="alert alert-info">No Record found.</div></div>
			</div>
			<!-- end processed -->
					<div v-show="views.returned">
						<h3>Returned</h3>
						<div v-if="orders.length && views.returned">

							<div id="no-more-tables">
								<table class='table' id='tblBranches'>
									<thead>
									<tr>
										<TH>Id</TH>
										<TH>Supplier</TH>
										<TH>Branch</TH>
										<th>Created</th>
										<th>Ordered by</th>
										<th>Details</th>

									</tr>
									</thead>
									<tbody>

									<tr v-for="order in cmp_orders" v-bind:class="[order.is_rush == 1 ? 'bg-danger': '']">

										<td class="withTopBorder" data-title='Id'>{{order.id}}

										</td>
										<td class="withTopBorder" data-title='Supplier'>{{ order.sname }} <br> <small>{{order.sdesc}}</small></td>
										<td  class="withTopBorder" data-title='Branch' >{{ order.bname }}</td>
										<td  class="withTopBorder" data-title='Created'>{{ order.created }}</td>
										<td class="withTopBorder" data-title='Ordered By'>{{order.lastname}}</td>
										<td class="withTopBorder" >
											<button class='btn btn-default' @click='showReturned(order)'>Details</button>
											<button class='btn btn-default' @click="showTimelog(order.timelog)">Timelog</button>
											<button class='btn btn-default' @click='resendData(order)'>Resend</button>
										</td>

									</tr>

									</tbody>
								</table>

							</div>
						</div>
						<div v-else><div class="alert alert-info">No Record found.</div></div>
					</div>
			<!-- end returned -->
					<div v-show="views.declined">
						<h3>Declined</h3>
						<div v-if="orders.length && views.declined">

							<div id="no-more-tables">
								<table class='table' id='tblBranches'>
									<thead>
									<tr>
										<TH>Id</TH>
										<TH>Supplier</TH>
										<TH>Branch</TH>
										<th>Created</th>
										<th>Ordered by</th>
										<th>Details</th>
									</tr>
									</thead>
									<tbody>

									<tr v-for="order in cmp_orders" v-bind:class="[order.is_rush == 1 ? 'bg-danger': '']">

										<td data-title='Id'>{{order.id}}

										</td>
										<td data-title='Supplier'>{{ order.sname }} <br> <small>{{order.sdesc}}</small></td>
										<td  data-title='Branch' >{{ order.bname }}</td>
										<td  data-title='Created'>{{ order.created }}</td>
										<td data-title='Ordered By'>{{order.lastname}}</td>
										<td>

											<button class='btn btn-default' @click="showDetails(order.bname,order.baddress,order.id,order.status)">Details</button>
											<button class='btn btn-default' @click="showTimelog(order.timelog)">Timelog</button>
									</td>

									</tr>

									</tbody>
								</table>

							</div>
						</div>
						<div v-else><div class="alert alert-info">No Record found.</div></div>
					</div>
			<!-- end declined -->
				</div>
			</div>
		</div>

	</div> <!-- end page content wrapper-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style='width:95%;' >
				<div class="modal-content"  >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
					</div>
				</div>
			</div>
	</div>
		<div class="modal fade" id="myModalEmail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='etitle'></h4>
						</div>
						<div class="modal-body" id='ebody'>
							<strong>Email:</strong> <input type="text" id='e_email' class='form-control'>
							<strong>Subject:</strong> <input type="text" id='e_subject' class='form-control'>
							<strong>Preview: </strong> <br>
							<div style='border: 1px dashed #ccc;padding: 5px;'>
							<div id="email_body"></div>
							</div>
							<br>
							<button class='btn btn-primary' id='btnSubmitEmail'>Email</button>
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
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
							<input type="hidden" id='dropzone_order_id'>
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
									<div style='height: 210px;text-align: center;overflow-x: hidden;'>
									<img  style='max-height: 200px;' v-bind:src="att.url" alt="att.title">
									</div>
								</a>
								<div class="caption">
									<h5>{{ att.title }}</h5>
									<p>{{ att.description }}</p>
								</div>
							</div>

						</div>
					</div>
					<div v-else><div class="alert alert-info">No attachment</div></div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModalTimelog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content"  >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Timelog</h4>
					</div>
					<div class="modal-body" >
						<table class='table'>
							<thead>
								<th>Date</th>
								<th>Remarks</th>
							</thead>
							<tbody>
							<tr v-for="tl in timelog">
								<td>{{tl.time}}</td>
								<td class='text-danger'>{{tl.message}}</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModalExpense" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content"  >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Expense</h4>
					</div>
					<div class="modal-body" >
						<div class="row">
							<div class="col-md-12"><h5>Supplier Order ID: {{ cur_order.id }}</h5></div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<strong>Payable To:</strong>
									<select class='form-control' name="payable_to" v-model='expense.payable_to'>
										<option value="Supplier">Supplier</option>
										<option value="Freight">Freight</option>
										<option value="Tax">Tax</option>
										<option value="Others">Others</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<strong>Currency:</strong>
									<select class='form-control' name="currency" v-model='expense.currency'>
										<option value="PHP">PHP</option>
										<option value="USD">USD</option>
										<option value="RMB">RMB</option>
										<option value="EURO">EURO</option>
										<option value="JPY">JPY</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<strong>Amount</strong>
									<input type="text" class='form-control' v-model='expense.amount' placeholder='Amount'>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<strong>Due Date</strong>
									<input type="text" class='form-control' v-model='expense.due_date' placeholder='Due Date'>
								</div>
							</div>
							<div class="col-md-6">
								<button class='btn btn-primary' @click="submitExpense()">Submit Expense</button>
							</div>
						</div>

						<hr>
						<table class='table table-bordered'>
							<thead>
								<tr><th>Payable To</th><th>Amount</th><th>Currency</th><th>Due Date</th></tr>
							</thead>
							<tbody>
								<tr v-for="ex in expenses">
									<td class="withTopBorder">{{ex.payable_to}}</td>
									<td class="withTopBorder">{{ex.amount}}</td>
									<td class="withTopBorder">{{ex.currency}}</td>
									<td class="withTopBorder">{{ex.due_date}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModalPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" >
				<div class="modal-content "  >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Payment</h4>
					</div>
					<div class="modal-body" >
						<div class="row">
							<div class="col-md-6">
									<h5>Supplier Order ID: {{ cur_order.id }}</h5>
									<div class="row">
										<div class="col-md-4" v-for="exp in pexpense">
											<div class="panel panel-default">
												<div class="panel-body cpointer" @click="changeSelectedPayment(exp)" v-bind:class="[exp.selected == 1 ? 'bg-warning' :'']">
													Payable To: {{ exp.payable_to }} <br>
													Total Amount: {{ exp.amount }} <br>
													Currency: {{ exp.currency }}  <br>
													Due: {{ exp.dt }} <br>
													Paid Amount: {{ exp.paid_amount }} <br>
												</div>
											</div>
										</div>
									</div>
									<div v-show="!pexpense.length" class="alert alert-info">No pending expense.</div>
							</div>
							<div class="col-md-6">
								<h5>Payment</h5>
								<div class="row">
									<div class="col-md-6">
										<strong>Amount to Pay: </strong>
										<input type="text" class='form-control' v-model="payment_info.amount_to_pay">
									</div>
									<div class="col-md-6">
										<strong>Currency</strong>
										<input type="text" class='form-control' v-model="payment_info.currency" disabled>
									</div>
									<div class="col-md-6">
										<strong>Exchange Rate: </strong>
										<input type="text" class='form-control' @keyup="computeExchangeRate" v-model="payment_info.exchange_rate">
									</div>
									<div class="col-md-6">
										<strong>In Peso: </strong>
										<input type="text" class='form-control' v-model="payment_info.in_peso">
									</div>
									</div>
									<br>
								<strong>Optional Field: </strong>
									<div class="row">
										<div class="col-md-4">
											<strong>Invoice: </strong>
											<input type="text" class='form-control' v-model="payment_info.invoice_number">
										</div>
										<div class="col-md-4">
											<strong>DR: </strong>
											<input type="text" class='form-control' v-model="payment_info.dr_number">
										</div>
										<div class="col-md-4">
											<strong>PO number: </strong>
											<input type="text" class='form-control' v-model="payment_info.po_number">
										</div>
									</div>
									<div class="row">
									<div class="col-md-6">
										<br>
										<button class='btn btn-primary' @click="submitPayment()">Submit</button>
									</div>
								</div>
							</div>
						</div>
						<hr>
						<div id='payment_con'></div>




					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModalReceiveHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content"  >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Receive History</h4>
					</div>
					<div class="modal-body" >
							<div id="receive_history_body"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModalReturned" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content"  >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id=''>Returned</h4>
						<p class='text-danger'>{{cur_order.return_message}}</p>
					</div>
					<div class="modal-body" id=''>
						<div class="row">
							<div class="col-md-12"><input type="text" class='form-control' placeholder="Search Item" id='supplier_item_id' v-model="supplier_item_id"></div>
							<div class="col-md-12"><br><input class='form-control' placeholder="Qty" type="text" v-model="supplier_item_qty"></div>
							<div class="col-md-12"><br>
							<button class='btn btn-default' @click="addItem">Add Item</button></div>
						</div>
						<div v-if="return_items.length">
						<table class="table">
							<thead>
							<tr><th>Item</th><th>Qty</th><th></th></tr>
							</thead>
							<tbody>
							<tr v-for="item in return_items">
								<td>{{item.item_code}}</td>
								<td><input type="text" v-model="item.qty" v-bind:value="item.qty"></td>
								<td><button class='btn btn-danger btn-sm' @click="deleteItem(item)">Delete</button></td>
							</tr>
							</tbody>
						</table>
						<div class='text-right'>
							<button class='btn btn-default' @click="updateItem">Update Item Quantity</button>
						</div>
						</div>
						<div v-else>
							<br>
							<div class="alert alert-info">
								No record found
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src='../js/vue3.js?v=2'></script>
	<script src='../js/dropzone2.js?v=1'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/vue-paginate/3.6.0/vue-paginate.min.js'></script>

	<script>

		Dropzone.autoDiscover = false;

		var vm = new Vue({
			el: '#app',
			data: {
				payment_info: {invoice_number:'',dr_number:'',po_number:'',expense_id:'',amount_to_pay:'',currency:'',in_peso:'',exchange_rate:''},
				expense:{amount:'',payable_to:'Supplier',currency:'PHP',due_date:''},
				orders:[],
				timelog:[],
				current_expense: {},
				supplier_id:'',
				branch_id:'',
				supplier_item_id:'',
				supplier_item_qty:'',
				search:'',
				return_items:[],
				attachments:[],
				order_counts:[],
				expenses:[],
				pexpense:[],
				cur_order: {},
				nav_active: 'active',
				nav_inactive: '',
				att_title: '',
				att_description: '',
				paginate: ['orders'],
				nav :{pending:true, approve:false, process: false,return: false,declin:false},
				views :{pending:true, approved:false, processed: false,returned: false,declined:false}
			},
			computed: {
				jsonStringify: function(d){
					return JSON.stringify(d);
				},
				cmp_orders: function(){
					var self = this;
					return  self.orders.filter(function(o){

						var ret = true;

						if(self.supplier_id){
							ret = ret && (o.supplier_id == self.supplier_id);
						}

						if(self.branch_id){
							ret = ret && (o.branch_to == self.branch_id);
						}

						if(self.search){
							ret = ret && (o.po_number.toLowerCase().indexOf(self.search.toLowerCase()) > -1 || o.expected_delivery_date.indexOf(self.search) > -1);
						}

						return ret;


					});
				}
			},
			mounted: function(){
				this.getOrderCount();
				this.getOrders(1);
				this.showView(1);
				var vm = this;
				$('#supplier_id').select2({
					allowClear: true,
					placeholder:'Search Supplier'
				});
				$('#branch_id').select2({
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

				$('body').on('change','#supplier_id',function(){
					vm.supplier_id = $(this).val();
				});

				$('body').on('change','#branch_id',function(){
					vm.branch_id = $(this).val();
				});

				var myDropzone = new Dropzone("#dropzone-form", {
						url: "../ajax/ajax_supplier.php?functionName=upload",
						acceptedFiles: "image/jpegimage/jpg,image/bmp,image/png,image/gif"
					}
				);

				myDropzone.on('sending', function(file, xhr, formData){
					formData.append('order_id', vm.cur_order.id);
					formData.append('title', vm.att_title);
					formData.append('description', vm.att_description);
					vm.att_title = '';
					vm.att_description = '';
				});

				$("#supplier_item_id").select2({
					placeholder: 'Item code',
					allowClear: true,
					minimumInputLength: 2,
					escapeMarkup: function(m) {
						return m;
					},
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function(term) {
							return {
								search: term, functionName: 'supplierItems', supplier_id: vm.cur_order.supplier_id
							};
						},
						results: function(data) {
							return {
								results: $.map(data, function(item) {
									return {
										text:  item.item_code,
										id: item.id
									}
								})
							};
						}
					}

				}).on("select2-close", function(e) {


				}).on("select2-highlight", function(e) {

				});

				$('body').on('change','#supplier_item_id',function(){
						vm.supplier_item_id = $(this).val();
						console.log(vm.supplier_item_id);
				});

				$('body').on('click','.pagination li a',function(){
					$('body, html, #search').animate({scrollTop: 0}, 100);
				});


			},

			methods: {
				submitPayment : function(){

					var self = this;
					if(!self.payment_info.expense_id){
						tempToast('error','Please select account to pay.');
						return;
					}
					if(!self.payment_info.amount_to_pay || !self.payment_info.exchange_rate){
						tempToast('error','Please complete the form');
						return;
					}
					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    data: {functionName:'addPayment', order_id: self.cur_order.id, payment_info: JSON.stringify(self.payment_info)},
					    success: function(data){

						    tempToast('info',data,'Info');
						    self.getExpenseToPay(self.cur_order.id);
						    self.getPayments(self.cur_order.id);
						    self.payment_info =  {invoice_number:'',dr_number:'',po_number:'',expense_id:'',amount_to_pay:'',currency:'',in_peso:'',exchange_rate:''};

					    },
					    error:function(){

					    }
					});

				},
				computeExchangeRate: function(){
					this.payment_info.amount_to_pay = (this.payment_info.amount_to_pay) ? this.payment_info.amount_to_pay : 0;
					if(this.pament_info !== ''){
						this.payment_info.in_peso = this.payment_info.amount_to_pay * this.payment_info.exchange_rate;
					} else {
						this.payment_info.in_peso='';
					}

				},
				changeSelectedPayment: function(e){
					this.clearSelectedPayment();
					this.current_expense = e;
					this.payment_info.expense_id = e.id;
					this.payment_info.currency = e.currency;
					e.selected=1
				},
				clearSelectedPayment: function(e){
					var self = this;
					for(var i in self.pexpense){
						self.pexpense[i].selected = 0;
					}
				},
				getPayments: function(order_id){

					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    data: {functionName:'getPayment',order_id:order_id},
					    success: function(data){
					        $('#payment_con').html(data);
					    },
					    error:function(){

					    }
					});


				},
				showPayment: function(o){
					var self = this;
					self.cur_order = o;
					self.getPayments(self.cur_order.id);
					self.getExpenseToPay(self.cur_order.id);
					$('#myModalPayment').modal('show');
				},
				getExpenseToPay: function(id){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getExpenseToPay',order_id:id},
					    success: function(data){
							self.pexpense = data;
					    },
					    error:function(){

					    }
					});
				},
				addPayment:function(id){
					var self = this;

				},
				getExpenses:function(id){

						var self = this;

						$.ajax({
							url:'../ajax/ajax_supplier.php',
							type:'POST',
							dataType:'json',
							data: {functionName:'getExpenses',order_id:id},
							success: function(data){

								self.expenses = data;

							},
							error:function(){

							}
						});

				},
				submitExpense: function(){
					var self = this;
					if(self.expense.amount && self.expense.currency && self.expense.payable_to){
						$.ajax({
							url:'../ajax/ajax_supplier.php',
							type:'POST',

							data: {functionName:'addExpense',order_id:self.cur_order.id,expense: JSON.stringify(self.expense)},
							success: function(data){
								tempToast('info',data,'Info');
								self.getExpenses(self.cur_order.id);
							},
							error:function(){

							}
						});
					}
				},
				showExpense: function(o){
					var self = this;

					// payables to =  supplier, freight, tax
					// amount
					// currency
					// due
					//
					//

					self.cur_order = o;
					self.getExpenses(self.cur_order.id);
					$('#myModalExpense').modal('show');


				},
				getOrderCount: function(){
					var vm = this;
					$.ajax({
						url:'../ajax/ajax_supplier.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'orderCount'},
						success: function(data){
							vm.order_counts = data;
						},
						error:function(){

						}
					});
				},
				filterRecord: function(){

				},
				attachFiles: function(order){
					this.cur_order = order;
					Dropzone.forElement("#dropzone-form").removeAllFiles(true);
					$('#myModalAttachment').modal('show');
				},
				getAttachments : function (req){
					$('#myModalAttachmentList').modal('show');
					var vm = this;
					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'getAtt',id:req.id},
					    success: function(data){
					        vm.attachments = data;
					    },
					    error:function(){

					    }
					});
				},
				showTimelog: function(t){
					try{
						var vm = this;
						vm.timelog = JSON.parse(t);
						$('#myModalTimelog').modal('show');
					} catch(e){

					}
				},receiveHistory: function(o){
					var self = this;
					$('#myModalReceiveHistory').modal('show');
					$('#receive_history_body').html('Loading...');
					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    data: {functionName:'receiveHistory', id: o.id},
					    success: function(data){
						    $('#receive_history_body').html(data);
					    },
					    error:function(){
					        
					    }
					});
				},
				resendData: function(order){
					var vm = this;
					alertify.confirm("Are you sure you want to resend this request?",function(e){
						if(e){
							$.ajax({
								url:'../ajax/ajax_supplier.php',
								type:'POST',
								data: {functionName:'changeStatusSupOrder',id:order.id,status:0,msg:''},
								success: function(data){
									tempToast('info',data,'Info');
									vm.showView(-1);
									vm.getOrderCount();
								},
								error:function(){

								}
							});
						}

					});

				},
				addItem: function(){
					var vm = this;
					if(vm.supplier_item_id && vm.supplier_item_qty){
						$.ajax({
						    url:'../ajax/ajax_supplier.php',
						    type:'POST',
						    data: {functionName:'addSupplierToOrder',id:vm.cur_order.id,qty:vm.supplier_item_qty,item_id:vm.supplier_item_id},
						    success: function(data){
						        tempToast('info',data,'Info');
							    vm.getReturnData(vm.cur_order.id);
							    vm.supplier_item_id = '';
							    vm.supplier_item_qty = '';
							    $('#supplier_item_id').select2('val',null);
						    },
						    error:function()    {

						    }
						});
					} else {
						tempToast('error','Enter data first','Error');
					}
				},
				deleteItem: function(o){
					var vm = this;
					alertify.confirm("Are you sure you want to delete this item? ", function(e){
						if(e){
							$.ajax({
							    url:'../ajax/ajax_supplier.php',
							    type:'POST',
							    data: {functionName:'' +
							    '',id: o.id},
							    success: function(data){
								    tempToast('info',data,'Info');
								    vm.getReturnData(vm.cur_order.id);
							    },
							    error:function(){

							    }
							})
						}
					})
				},
				updateItem: function(){
					var vm = this;
					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    data: {functionName:'updateSupplierItem',items: JSON.stringify(vm.return_items)},
					    success: function(data){
						    tempToast('info',data,'Info');
						    vm.getReturnData(vm.cur_order.id);
					    },
					    error:function(){

					    }
					})

				},
				showReturned : function (order){
					var vm = this;
					vm.cur_order = order;
					$('#myModalReturned').modal('show');
					vm.getReturnData(order.id);
				},
				getReturnData: function(id){
					var vm = this;
					$.ajax({
						url:'../ajax/ajax_supplier.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getReturned',id:id},
						success: function(data){
							vm.return_items = data;
						},
						error:function(){

						}
					});
				},
				showView: function(n){
					var vm = this;
					vm.views = {pending:false, approved:false, processed: false,returned: false,declined:false,received:false};
					vm.nav = {pending:false, approve:false, process: false,return: false,decline:false,received:false};
					if(n == 1){
						vm.views.pending = true;
						vm.nav.pending = true;
						this.getOrders(0);
					} else if (n == 2){
						vm.views.approved = true;
						vm.nav.approve = true;
						this.getOrders(1);
					} else if (n == 3){
						vm.views.processed = true;
						vm.nav.process = true;
						this.getOrders(2);
					}else if (n == 4){
						vm.views.received = true;
						vm.nav.received = true;
						this.getOrders(4);
					} else if (n == -1){
						vm.views.returned = true;
						vm.nav.return = true;
						this.getOrders(-1);
					}else if (n == 99){
						vm.views.declined = true;
						vm.nav.decline = true;
						this.getOrders(99);
					}
				},
				showDetails: function(name,address,id,status){
					$('.loading').show();
					var supplier_order_id = id;
					var branch_name =  name;
					var branch_address =  address;
					$.ajax({
						url:'../ajax/ajax_query.php',
						type:'post',
						data: {functionName:'getSupplierOrdersDetails',status:status,supplier_order_id:supplier_order_id,branch_name:branch_name,branch_address:branch_address},
						success: function(data){
							$('#mbody').html(data);

							$('#u_expected_delivery').datepicker({
								autoclose:true
							}).on('changeDate', function(ev){
								$('#u_expected_delivery').datepicker('hide');
							});
							$('#r_date_delivered').datepicker({
								autoclose:true
							}).on('changeDate', function(ev){
								$('#r_date_delivered').datepicker('hide');
							});

							$('#myModal').modal('show');
							$('.loading').hide();
						},
						error:function(){
							alert('Error Occurs. The page will be refresh. If you continue to see this message. Please contact the IT department.');
							location.href='supplier_receive_order.php';
							$('.loading').hide();
						}
					})
				},
				getOrders: function(status){
					var vm = this;
					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'getSupplierList',status:status},
					    success: function(data){
						    vm.orders = data;

					    },
					    error:function(){

					    }
					});
				}
			}
		});


			(function(vm){

				$('body').on('keyup','.recqty',function(){
					var row = $(this).parents('tr');
					var qty = $(this).val();
					var availableqty = row.children().eq(9).text().trim();
					if(!qty || isNaN(qty) || parseInt(qty) > parseInt(availableqty)){
						tempToast('error','<p>Invalid Quantity</p>','<h3>WARNING!</h3>');
						$(this).val('');
					}
				});

				$('body').on('click','#btnResend',function(){

					var con = $(this);
					var id = con.attr('data-id');

					$.ajax({
						url:'../ajax/ajax_supplier.php',
						type:'POST',
						data: {functionName:'changeStatusSupOrder',id:id,status:0},
						success: function(data){
							tempToast('info',data,'Info');
							vm.showView(-1);
							vm.getOrderCount();
							$('#myModal').modal('hide');
						},
						error:function(){

						}
					});

				});

				$('body').on('click','#btnUpdateInfo',function(){

					var po_number = $('#u_po_number').val();
					var terms = $('#u_terms').val();
					var expected_del  = $('#u_expected_delivery').val();
					var invoice  = $('#u_invoice').val();
					var dr  = $('#u_dr').val();
					var cr  = $('#u_cr').val();
					var due_date  = $('#u_due_date').val();
					var cost  = $('#u_cost').val();
					var id  = $(this).attr('data-id');

					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    data: {functionName:'updateSupInfo',
						    invoice:invoice,dr:dr,cr:cr,due_date:due_date,cost:cost,id:id,po_number:po_number, terms:terms,expected_del:expected_del},
					    success: function(data){
						    tempToast('info',data,'Info');
						    $('#myModal').modal('hide');
						    vm.showView(3);
						    vm.getOrderCount();
					    },
					    error:function(){

					    }
					});
				});

				$('body').on('click','#btnReceiveUpdate',function(){

				/*
					var date_delivered = $('#r_date_delivered').val();
					var remarks = $('#r_remarks').val();

					var id  = $(this).attr('data-id');

					$.ajax({
					    url:'../ajax/ajax_supplier.php',
					    type:'POST',
					    data: {functionName:'updateSupInfoRec',id:id, date_delivered:date_delivered,remarks:remarks},
					    success: function(data){
						    tempToast('info',data,'Info');
						    $('#myModal').modal('hide');
						    vm.showView(2);
						    vm.getOrderCount();
					    },
					    error:function(){

					    }
					});
					*/

				});

				function timeConverter(UNIX_timestamp){
					var a = new Date(UNIX_timestamp * 1000);
					var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
					var year = a.getFullYear();
					var month = months[a.getMonth()];
					var date = a.getDate();
					var hour = a.getHours();
					var min = a.getMinutes();
					var sec = a.getSeconds();
					var time = month + '/' + date + '/' + year;
					return time;
				}

				$('body').on('click','.btnPrintReceive',function(){

					var toprint ='';
					var  con = $(this);
					var tblid = con.attr('data-tblid');
					var id = con.attr('data-id');
					var branch = con.attr('data-branch');

					var branch_address = con.attr('data-branch_address');
					var company = "<?php echo $thiscompany->name; ?>";
					var company_address = "<?php echo $thiscompany->address; ?>";
					var description = "<?php echo $thiscompany->description; ?>";
					var count =	$('#tblReceive > tbody > tr').length;
					var supplier_name = con.attr('data-supplier_name');
					var supplier_description = con.attr('data-supplier_description');

					var po_number = "";
					var invoice_number = "";
					var recipient = con.attr('data-receive_by');
					var remarks = con.attr('data-remarks');

					var date_received =  con.attr('data-dt');
					var expected_delivery = "";

					try {

						var json = JSON.parse($(this).attr('data_list'));
						po_number = json.po_number;
						invoice_number = json.invoice;
						date_received = json.date_delivered;
						recipient = json.received_by;
						if(date_received){
							date_received = timeConverter(date_received);
						}

					} catch(e){

					}
					var pagehead ='';

					pagehead+= "<div class='perpage' style='page-break-after:always;' >";
					pagehead += "<div>";
					pagehead += "<div style='width:300px;float:left;'><h3>"+company+"</h3></div>";
					pagehead += "<div style='width:300px;float:right;'><h3>Materials Receiving Inspection Report</h3></div>";
					pagehead += "</div>";
					pagehead += "<div style='clear:both;'></div><br>";

					pagehead += "<div>";
					pagehead += "<div style='width:300px;float:left;'>";
					pagehead += "<p><strong>Supplier</strong></p>";
					pagehead += "<p>"+supplier_name+"<br>"+supplier_description+"</p>";
					pagehead += "<p></p>";
					pagehead += "</div>";

					pagehead += "<div>";
					pagehead += "<div style='width:300px;float:left;'>";
					pagehead += "<p><strong>Shipping Address</strong></p>";
					pagehead += "<p>"+branch+"<br>"+branch_address+"</p>";

					pagehead += "</div>";
					pagehead += "</div>";
					pagehead += "<div style='clear:both;'></div><br>";

					pagehead += "<table class='table table-bordered' >";
					pagehead += "<tr><th>Invoice Number</th><th>Order Origin</th><th>Recipient</th><th>Date Received</th></tr>";
					pagehead += "<tr><td>"+invoice_number+"</td><td>"+remarks+"</td><td>"+recipient+"</td><td>"+date_received+"</td></tr>";
					pagehead += "</table>";

					pagehead += "<table class='table table-bordered' >";
					pagehead +="<tr style='padding:5px;'>" +
						"<th style='text-align: left;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Item</th>" +
						"<th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Status</th>" +
						"<th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Qty</th>" +
						"</tr>";

					var grandtotal = 0;
					var totalqty = 0;
					var pageitem = [];
					var i = 1;
					var strholder ='';
					var per_page = 12;

					$('#'+tblid+' > tbody > tr').each(function(index){

						var row = $(this);
						var itemcode = row.attr('data-item_code')+ " <br>" + row.attr('data-description');
						var qty = row.attr('data-qty');
						var status = row.attr('data-status');



						strholder += "<tr style='height:40px;'>" +
							"<td style='border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+itemcode+"</td>" +
							"<td style='border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+status+"</td>" +
							"<td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(qty)+"</td>" +
							"</tr>";
						totalqty = parseFloat(totalqty) + parseFloat(qty);


						if(parseInt(i) % per_page == 0) {
							pageitem.push(strholder);
							strholder = '';
						}

						i = parseInt(i) + 1;

					});

					var num = (Math.ceil(parseInt(i) / per_page) * per_page);

					if(parseInt(i) < per_page){
						while(parseInt(i) != parseInt(num)+1){
							strholder+= "<tr style='height:40px;'><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					} else {
						while(parseInt(i) != parseInt(num)+1){
							strholder+= "<tr style='height:40px;'><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					}

					var endtable = "</table>";
					var pagend = "";
					pagend += "<div style='clear:both;'></div>";
					pagend+= "<p><span style='float:left'><strong>Total Quantity:"+totalqty+"</strong></span></p>";
					pagend += "<br><br>";
					pagend += "<p>Prepared by:<span style='display:inline-block;width:300px;border-bottom: 1px solid #ccc'> &nbsp;</span></p>";
					pagend += "<p>Received by:<span style='display:inline-block;width:300px;margin-left:8px;border-bottom: 1px solid #ccc'> &nbsp;</span></p>";
					pagend += "</div>";

					var allprint ='';
					var countpages = pageitem.length;
					var pageof = 1;

					for(var j in pageitem){
						allprint += pagehead;
						allprint += pageitem[j];
						allprint += endtable;
						allprint +=	 "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page "+pageof+" of "+countpages+"</p>";
						pageof = parseInt(pageof) + 1;
						allprint += pagend;
					}

					Popup(allprint);

				});

				$('body').on('click','#btnPrintMRIR',function(){

					var toprint ='';
					var tbl = $('#tblReceive');
					var id = tbl.attr('data-order_id');
					var branch = tbl.attr('data-branch');
					var branch_address = tbl.attr('data-branch_address');
					var company = "<?php echo $thiscompany->name; ?>";
					var company_address = "<?php echo $thiscompany->address; ?>";
					var description = "<?php echo $thiscompany->description; ?>";
					var count =	$('#tblReceive > tbody > tr').length;
					var supplier_name = tbl.attr('data-supplier_name');
					var supplier_description = tbl.attr('data-supplier_description');
					var po_number = "";
					var invoice_number = "";
					var recipient = "";
					var date_received = "";
					var expected_delivery = "";

					try {

						var json = JSON.parse($(this).attr('data_list'));
						po_number = json.po_number;
						invoice_number = json.invoice;
						date_received = json.date_delivered;
						recipient = json.received_by;
						if(date_received){
							date_received = timeConverter(date_received);
						}

					} catch(e){

					}

					var pagehead ='';

					pagehead+= "<div class='perpage' style='page-break-after:always;' >";
					pagehead += "<div>";
					pagehead += "<div style='width:300px;float:left;'><h3>"+company+"</h3></div>";
					pagehead += "<div style='width:300px;float:right;'><h3>Materials Receiving Inspection Report</h3></div>";
					pagehead += "</div>";
					pagehead += "<div style='clear:both;'></div><br>";

					pagehead += "<div>";
					pagehead += "<div style='width:300px;float:left;'>";
					pagehead += "<p><strong>Supplier</strong></p>";
					pagehead += "<p>"+supplier_name+"<br>"+supplier_description+"</p>";
					pagehead += "<p></p>";
					pagehead += "</div>";

					pagehead += "<div>";
					pagehead += "<div style='width:300px;float:left;'>";
					pagehead += "<p><strong>Shipping Address</strong></p>";
					pagehead += "<p>"+branch+"<br>"+branch_address+"</p>";

					pagehead += "</div>";
					pagehead += "</div>";
					pagehead += "<div style='clear:both;'></div><br>";

					pagehead += "<table class='table table-bordered' >";
					pagehead += "<tr><th>Invoice Number</th><th>Order Origin</th><th>Recipient</th><th>Date Received</th></tr>";
					pagehead += "<tr><td>"+invoice_number+"</td><td>"+id+"</td><td>"+recipient+"</td><td>"+date_received+"</td></tr>";
					pagehead += "</table>";

					pagehead += "<table class='table table-bordered' >";
					pagehead +="<tr style='padding:5px;'>" +
						           "<th style='text-align: left;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Item</th>" +
						           "<th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Status</th>" +
						           "<th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Qty</th>" +
						        "</tr>";

					var grandtotal = 0;
					var totalqty = 0;
					var pageitem = [];
					var i = 1;
					var strholder ='';
					var per_page = 12;

					$('#tblReceive > tbody > tr').each(function(index){

						var row = $(this);
						var itemcode = row.attr('data-s_item_code')+ " <br>" + row.attr('data-s_description');
						var price =  row.attr('data-s_purchase_price');
						var qty = row.children().eq(3).text();
						var total = parseFloat(price) * parseFloat(qty) ;
						var cbm = row.attr('data-cbm');
						var total_cbm = row.attr('data-total_cbm');

						strholder += "<tr style='height:40px;'>" +
							"<td style='border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+itemcode+"</td>" +
							"<td style='border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>done</td>" +
							"<td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(qty)+"</td>" +
							"</tr>";
						totalqty = parseFloat(totalqty) + parseFloat(qty);
						grandtotal = parseFloat(grandtotal) + parseFloat(total);

						if(parseInt(i) % per_page == 0) {
							pageitem.push(strholder);
							strholder = '';
						}

						i = parseInt(i) + 1;

					});

					var num = (Math.ceil(parseInt(i) / per_page) * per_page);

					if(parseInt(i) < per_page){
						while(parseInt(i) != parseInt(num)+1){
							strholder+= "<tr style='height:40px;'><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					} else {
						while(parseInt(i) != parseInt(num)+1){
							strholder+= "<tr style='height:40px;'><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					}

					var endtable = "</table>";
					var pagend = "";
					pagend += "<div style='clear:both;'></div>";
					pagend+= "<p><span style='float:left'><strong>Total Quantity:"+totalqty+"</strong></span></p>";
					pagend += "<br><br>";
					pagend += "<p>Prepared by:<span style='display:inline-block;width:300px;border-bottom: 1px solid #ccc'> &nbsp;</span></p>";
					pagend += "<p>Received by:<span style='display:inline-block;width:300px;margin-left:8px;border-bottom: 1px solid #ccc'> &nbsp;</span></p>";
					pagend += "</div>";

					var allprint ='';
					var countpages = pageitem.length;
					var pageof = 1;

					for(var j in pageitem){
						allprint += pagehead;
						allprint += pageitem[j];
						allprint += endtable;
						allprint +=	 "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page "+pageof+" of "+countpages+"</p>";
						pageof = parseInt(pageof) + 1;
						allprint += pagend;
					}

					Popup(allprint);

				});

				$('body').on('click','#btnPrintPOForm',function(){

					var toprint ='';
					var tbl = $('#tblReceive');
					var id = tbl.attr('data-order_id');
					var branch = "";
					var branch_address = "";
					var company = "<?php echo $thiscompany->name; ?>";
					var company_address = "<?php echo $thiscompany->address; ?>";
					var description = "<?php echo $thiscompany->description; ?>";
					var count =	$('#tblReceive > tbody > tr').length;
					var supplier_name = tbl.attr('data-supplier_name');
					var supplier_description = tbl.attr('data-supplier_description');
					var po_number = "";
					var invoice_number = "";
					var recipient = "";
					var date_received = "";
					var expected_delivery = "";
					var remarks = "";
					var order_by = "";
					var termstxt = "";
					var ship_to = "";
					var ctr_num = "";
					try {

						var json = JSON.parse($(this).attr('data_list'));
						po_number = json.po_number;
						invoice_number = json.invoice;
						date_received = json.date_delivered;
						recipient = json.received_by;
						ship_to = json.ship_to;
						termstxt = json.terms;
						branch = json.bname;
						branch_address = json.baddress;
						remarks = json.remarks;
						ctr_num = json.id;
						expected_delivery = timeConverter(json.expected_delivery_date);
						if(date_received){
							date_received = timeConverter(date_received);
						}
						order_by = (json.firstname +  " " + json.lastname).toUpperCase();

					} catch(e){

					}
					var styling = JSON.parse($('#form_layout').val());

					var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
					var logovisible = (styling['logo']['visible']) ? 'display:block;' : 'display:none;';
					var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
					var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
					var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
					var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
					var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
					var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
					var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
					var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
					var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
					var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
					var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
					var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
					var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
					var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
					var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
					var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

					var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var logoBold = (styling['logo']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
					var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
					var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
					var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
					var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

					var printhtml = "";
					var server_date = $('#server_date').val();
					printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
					printhtml = printhtml + "<div style='" + drnumvisible + drnumbold + "position:absolute;top:" + styling['drnum']['top'] + "px; left:" + styling['drnum']['left'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'> <br/><br/>ID: " + ctr_num + " </div><div style='clear:both;'></div>";
					printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'> <br/><br/>" + server_date + " </div><div style='clear:both;'></div>";
					printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + supplier_name + "</div>";
					printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + supplier_description + "</div>";
					printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + company + "</div>";
					printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + branch_address + "</div>";

					printhtml = printhtml + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" +ship_to + "</div>";
					printhtml = printhtml + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "</div>";
					printhtml = printhtml + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + order_by + "</div>";

					printhtml = printhtml + "<div style='" + paymentsvisible + paymentsBold + "position:absolute;left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'></div>";
					printhtml = printhtml + "<div style='" + payments2visible + payments2Bold + "position:absolute;left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'></div>";
					printhtml = printhtml + "<div style='" + payments3visible + payments3Bold + "position:absolute;left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'></div>";

					printhtml = printhtml + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
					printhtml = printhtml + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>"+expected_delivery+"</div>";
					printhtml = printhtml + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'></div>";



					printhtml = printhtml + "<table id='itemscon' style='position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";


					var overall_total = 0;


					$('#tblReceive > tbody > tr').each(function(index){

						var row = $(this);
						var itemcode = row.attr('data-s_item_code')+ " <br>" + row.attr('data-s_description');
						var price =  row.attr('data-s_purchase_price');
						var qty = row.children().eq(3).text();
						var total = parseFloat(price) * parseFloat(qty) ;

						overall_total = parseFloat(overall_total) +  parseFloat(total);

						var cbm = row.attr('data-cbm');
						var total_cbm = row.attr('data-total_cbm');
						printhtml += "<tr>";
							printhtml += "<td style='width:120px;'>"+row.attr('data-s_item_code')+"</td>";
							printhtml += "<td style='width:320px;'>"+row.attr('data-s_description')+"</td>";
							printhtml += "<td style='width:70px;'>"+qty+"</td>";
							printhtml += "<td style='width:120px;'>"+number_format(price,2)+"</td>";
							printhtml += "<td style='width:120px;'>"+number_format(total,2)+"</td>";
						printhtml += "</tr>";


					});

					printhtml +="</table>";
					printhtml +="</div>";

					if(styling['lbl']){

						var lblvisible = (styling['lbl']['visible']) ? 'display:inline-block;' : 'display:none;';
						var lblbold = (styling['lbl']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

						printhtml = printhtml + "<div style='" + lblvisible + lblbold + "position:absolute;left:" + styling['lbl']['left'] + "px;top:" + styling['lbl']['top'] + "px;font-size:" + styling['lbl']['fontSize'] + "px;'>";
						printhtml = printhtml + "<span style='display:inline-block;width:150px;'> Elsa Villahermosa</span> <span style='width:150px;display:inline-block;'>Lyn Cordon</span> <span style='width:150px;display:inline-block;'>John Christopher Tan</span><span style='width:150px;display:inline-block;text-align:center;'>Total: "+number_format(overall_total,2)+"</span>";
						printhtml = printhtml + "</div>";


					}



					Popup(printhtml);

				});


				$('body').on('click','#btnDecline',function(){
					var con = $(this);
					var id = con.attr('data-id');
					$.ajax({
						url:'../ajax/ajax_supplier.php',
						type:'POST',
						data: {functionName:'changeStatusSupOrder',id:id,status:99},
						success: function(data){
							tempToast('info',data,'Info');
							vm.showView(1);
							vm.getOrderCount();
							$('#myModal').modal('hide');
						},
						error:function(){

						}
					});
				});

				$('body').on('click','#btnReturn',function(){
					var con = $(this);
					var id = con.attr('data-id');
					$('#myModal').modal('hide');
					alertify.prompt('Insert your message here:', function (e, str) {
						if (e) {
							$.ajax({
								url:'../ajax/ajax_supplier.php',
								type:'POST',
								data: {functionName:'changeStatusSupOrder',id:id,status:-1,ret_msg: str},
								success: function(data){
									tempToast('info',data,'Info');
									vm.showView(1);

								},
								error:function(){

								}
							});
						} else {

						}
					}, '');
				});
				$('body').on('click','#btnApproved',function(){
					var con = $(this);
					var id = con.attr('data-id');
					$.ajax({
						url:'../ajax/ajax_supplier.php',
						type:'POST',
						data: {functionName:'changeStatusSupOrder',id:id,status:2},
						success: function(data){
							tempToast('info',data,'Info');
							vm.showView(1);
							vm.getOrderCount();
							$('#myModal').modal('hide');
						},
						error:function(){

						}
					})
				});
				$('body').on('click','#btnProcessed',function(){
					var con = $(this);
					var id = con.attr('data-id');
					$.ajax({
						url:'../ajax/ajax_supplier.php',
						type:'POST',
						data: {functionName:'changeStatusSupOrder',id:id,status:1},
						success: function(data){
							tempToast('info',data,'Info');
							vm.showView(3);
							vm.getOrderCount();
							$('#myModal').modal('hide');
						},
						error:function(){

						}
					})
				});
				$('#r_date_delivered').datepicker({
					autoclose:true
				}).on('changeDate', function(ev){
					$('#r_date_delivered').datepicker('hide');
				});
				$('body').on('click','#btnReceive',function(){
					var torec = [];
					var supplier_order_id = $('#tblReceive').attr('data-order_id');
					var receive_status = $('#receive_status').val();
					var dt_delivered = $('#r_date_delivered').val();
					var rec_remarks = $('#r_remarks').val();
					var received_by = $('#r_received_by').val();

					var btncon = $(this);
					var btnoldval = btncon.val();
					btncon.val('Loading...');
					btncon.attr('disabled',true);

					$('#tblReceive > tbody > tr').each(function(index){
						var row = $(this);
						var witherror = false;
						var sup_item_id = row.attr('data-sup_id');
						var noitem = row.attr('data-no_item');
						var details_id = row.attr('data-details_id');
						var item_id = 0;
						var product_details='';
						var recqty = row.children().eq(9).find('input').val();
						var pending = row.children().eq(8).text();
						var is_done = row.children().eq(10).find('input').is(":checked");
						is_done = (is_done) ? 1 : 0;
						pending = replaceAll(pending,',','');
						pending =number_format(pending,3,'.','');

						if(noitem == 1){
							item_id = row.children().eq(1).find('select').val();
							var is_new_item = row.attr('data-is_new_item');
							if(!item_id){

								witherror = true;

							} else if(item_id == -1){
								product_details  = row.attr('data-product_details');
								if(!product_details){
									witherror = true;
								}
							}
						} else {
							item_id = row.attr('data-item_id');
						}

						if(!parseFloat(recqty) || isNaN(recqty)  || parseFloat(recqty) > parseFloat(pending) ){
							if(recqty !== ''){
								tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
								witherror = true;
								btncon.val(btnoldval);
								btncon.attr('disabled',false);
								return;
							}

						}
						recqty = number_format(recqty,3,'.','');

						if(!recqty && recqty !== '')  return true;
						if(witherror) return true;


						torec[index] = {
							sup_item_id:sup_item_id,
							noitem:noitem,
							item_id:item_id,
							product_details:product_details,
							recqty:recqty,
							details_id:details_id,
							is_done:is_done
						}
					});

					if(torec.length > 0){
						var jsonorder= JSON.stringify(torec);
						$.ajax({
							url:'../ajax/ajax_query.php',
							type:'POST',
							data: {
								received_by:received_by,
								rec_remarks:rec_remarks,
								dt_delivered:dt_delivered,
								receive_status:receive_status,
								order_id:supplier_order_id,
								torec:jsonorder,functionName:'receiveOrderFromSupplier'},
							success: function(data){
								alertify.alert(data,function(){
									location.href='supplier_receive_order.php';
								});
							},
							error:function(){

							}
						});
					} else {
						tempToast('error','<p>Invalid request</p>','<h3>WARNING!</h3>');
						btncon.val(btnoldval);
						btncon.attr('disabled',false);
					}


				});

				function Popup(data)
				{
					var mywindow = window.open('', 'new div', '');

					mywindow.document.write('<html><head><title></title>');
					mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
					mywindow.document.write('</head><body style="padding:0;margin:10px;">');
					mywindow.document.write(data);
					mywindow.document.write('</body></html>');

					setTimeout(function(){
						mywindow.print();
						mywindow.close();
						return true;
					},1000);

				}

				$('body').on('click','#btnEmail',function(){
					var toprint ='';
					var tbl = $('#tblReceive');
					var id = tbl.attr('data-order_id');
					var branch = tbl.attr('data-branch');
					var branch_address = tbl.attr('data-branch_address');
					var company = "<?php echo $thiscompany->name; ?>";
					var company_address = "<?php echo $thiscompany->address; ?>";
					var description = "<?php echo $thiscompany->description; ?>";
					var count =	$('#tblReceive > tbody > tr').length;
					var supplier_name = tbl.attr('data-supplier_name');
					var pagehead ='';
					pagehead+= "<div class='perpage' style='page-break-after:always;' >";
					pagehead += "<h1 class='text-center'>"+company+"</h1>";
					pagehead += "<p class='text-center' style='color:#ccc;'>"+company_address+"</p>";
					pagehead += "<p style='text-align: center;font-weight:bold;'>Supplier Order</p>";
					pagehead += "<p style='text-align:right'>Order ID #"+id+"</p>";
					pagehead += "<p >Branch: <span style='display:inline-block;width:400px;border-bottom: 1px solid #ccc'>"+branch+"</span></p>";
					pagehead += "<p >Address:<span style='display:inline-block;width:400px;border-bottom: 1px solid #ccc'>"+branch_address+"</span></p>";
					pagehead += "<p >Supplier:<span style='display:inline-block;width:400px;border-bottom: 1px solid #ccc'>"+supplier_name+"</span></p>";

					pagehead += "<table class='table table-bordered' >";
					pagehead +="<tr style='padding:5px;'><th style='text-align: left;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Item</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Quantity</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>CBM</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Total CBM</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Price</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Total</th></tr>";
					var grandtotal = 0;
					var totalqty = 0;
					var pageitem = [];
					var i = 1;
					var strholder ='';
					var per_page = 12;
					$('#tblReceive > tbody > tr').each(function(index){
						var row = $(this);
						var itemcode = row.attr('data-s_item_code')+ " <br>" + row.attr('data-s_description') ;
						var price =  row.attr('data-s_purchase_price');
						var qty = row.children().eq(3).text();
						var total = parseFloat(price) * parseFloat(qty) ;
						var cbm = row.attr('data-cbm');
						var total_cbm = row.attr('data-total_cbm');

						strholder += "<tr style='height:40px;'><td style='border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+itemcode+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(qty)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(cbm,3)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(total_cbm,3)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(price,2)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(total,2)+"</td></tr>";
						totalqty = parseFloat(totalqty) + parseFloat(qty);
						grandtotal = parseFloat(grandtotal) + parseFloat(total);
						if(parseInt(i) % per_page == 0) {
							pageitem.push(strholder);
							strholder='';
						}
						i = parseInt(i) + 1;
					});
					var num = (Math.ceil(parseInt(i) / per_page) * per_page);
					if(parseInt(i) < per_page){
						while(parseInt(i) != parseInt(num)+1){
							//strholder+= "<tr style='height:40px;'><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					} else {
						while(parseInt(i) != parseInt(num)+1){
							//strholder+= "<tr style='height:40px;'><td></td><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					}
					var endtable = "</table>";
					var pagend = "";
					var digital_sign = $('#digital_sign').val();
					var signatory = $('#signatory').val();
					pagend += "<div style='clear:both;'></div>";
					pagend+= "<p><span style='float:left'><strong>Total Quantity:"+totalqty+"</strong></span><span style='float:right'><strong>Total Amount: "+number_format(grandtotal,2)+"</strong></span></p>";
					pagend += "<br><br>";
					//pagend += "<p>Prepared by:<span style='display:inline-block;width:300px;border-bottom: 1px solid #ccc'> &nbsp;</span></p>";
					if(digital_sign == 1){
						pagend += "<p><img style='display:inline-block;width:150px;margin-left:50px;' src='../css/img/signatory.png' /></p>";
					}

					pagend += "<p>Approved by: "+signatory+"</p>";
					pagend += "<p><span style='display:inline-block;width:150px;margin-left:70px;border-top: 1px solid #ccc'> &nbsp;</span></p>";
					pagend += "</div>";
					var allprint ='';
					var countpages = pageitem.length;
					var pageof = 1;
					for(var j in pageitem){

						allprint += pagehead;
						allprint += pageitem[j];
						allprint += endtable;
						allprint +=	 "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page "+pageof+" of "+countpages+"</p>";
						pageof = parseInt(pageof) + 1;
						allprint += pagend;
					}
					$('#myModalEmail').modal('show');
					$('#email_body').html(allprint);

				});

				$('body').on('click','#btnPrint',function(){
					var toprint ='';
					var tbl = $('#tblReceive');
					var id = tbl.attr('data-order_id');
					var branch = tbl.attr('data-branch');
					var branch_address = tbl.attr('data-branch_address');
					var company = "<?php echo $thiscompany->name; ?>";
					var company_address = "<?php echo $thiscompany->address; ?>";
					var description = "<?php echo $thiscompany->description; ?>";
					var count =	$('#tblReceive > tbody > tr').length;
					var supplier_name = tbl.attr('data-supplier_name');
					var po_number = "";
					var expected_delivery = "";

					try {

						var json = JSON.parse($(this).attr('data_list'));
						po_number = json.po_number;

					} catch(e){

					}

					var pagehead ='';
					pagehead+= "<div class='perpage' style='page-break-after:always;' >";
					pagehead += "<h1 class='text-center'>"+company+"</h1>";
					pagehead += "<p class='text-center' style='color:#ccc;'>"+company_address+"</p>";
					pagehead += "<p style='text-align: center;font-weight:bold;'>Supplier Order</p>";
					pagehead += "<p style='text-align:right'>PO Number: "+po_number+"</p>";
					pagehead += "<p style='text-align:right'>Order ID: "+id+"</p>";
					pagehead += "<div>";
					pagehead += "<div style='420px;float:left;'><span style='display:inline-block;width:90px;'>Supplier:</span> <span style='display:inline-block;width:240px;border-bottom: 1px solid #ccc'>"+supplier_name+"</span></div>";
					pagehead += "<div style='420px;float:left;'><span style='display:inline-block;width:90px;'>Branch:</span> <span style='display:inline-block;width:240px;border-bottom: 1px solid #ccc'>"+branch+"</div>";
					pagehead += "</div>";
					pagehead += "<div style='clear:both;'></div><br>";
					pagehead += "<p ><span style='display:inline-block;width:90px;'>Address:</span> <span style='display:inline-block;width:570px;border-bottom: 1px solid #ccc'>"+branch_address+"</span></p>";
					pagehead += "<div>";
					pagehead += "<div style='420px;float:left;'><span style='display:inline-block;width:90px;'>Terms:</span> <span style='display:inline-block;width:240px;border-bottom: 1px solid #ccc'>a</span></div>";
					pagehead += "<div style='420px;float:left;'><span style='display:inline-block;width:90px;'>Del. Date:</span> <span style='display:inline-block;width:240px;border-bottom: 1px solid #ccc'>b</div>";
					pagehead += "</div>";
					pagehead += "<div style='clear:both;'></div><br>";
					pagehead += "<table class='table table-bordered' >";
					pagehead +="<tr style='padding:5px;'><th style='text-align: left;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Item</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Quantity</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>CBM</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Total CBM</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Price</th><th style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>Total</th></tr>";

					var grandtotal = 0;
					var totalqty = 0;
					var pageitem = [];
					var i = 1;
					var strholder ='';
					var per_page = 12;

					$('#tblReceive > tbody > tr').each(function(index){

						var row = $(this);
						var itemcode = row.attr('data-s_item_code')+ " <br>" + row.attr('data-s_description');
						var price =  row.attr('data-s_purchase_price');
						var qty = row.children().eq(3).text();
						var total = parseFloat(price) * parseFloat(qty) ;
						var cbm = row.attr('data-cbm');
						var total_cbm = row.attr('data-total_cbm');

						strholder += "<tr style='height:40px;'><td style='border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+itemcode+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(qty)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(cbm,3)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(total_cbm,3)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(price,2)+"</td><td style='text-align: right;border-top : 1px solid #ccc;border-bottom : 1px solid #ccc; border-collapse:collapse;'>"+number_format(total,2)+"</td></tr>";
						totalqty = parseFloat(totalqty) + parseFloat(qty);
						grandtotal = parseFloat(grandtotal) + parseFloat(total);

						if(parseInt(i) % per_page == 0) {
							pageitem.push(strholder);
							strholder = '';
						}

						i = parseInt(i) + 1;

					});

					var num = (Math.ceil(parseInt(i) / per_page) * per_page);

					if(parseInt(i) < per_page){
						while(parseInt(i) != parseInt(num)+1){
							strholder+= "<tr style='height:40px;'><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					} else {
						while(parseInt(i) != parseInt(num)+1){
							strholder+= "<tr style='height:40px;'><td></td><td></td><td></td><td></td></tr>";
							i = parseInt(i) + 1;
						}
						pageitem.push(strholder);
						strholder='';
					}

					var endtable = "</table>";
					var pagend = "";
					pagend += "<div style='clear:both;'></div>";
					pagend+= "<p><span style='float:left'><strong>Total Quantity:"+totalqty+"</strong></span><span style='float:right'><strong>Total Amount: "+number_format(grandtotal,2)+"</strong></span></p>";
					pagend += "<br><br>";
					pagend += "<p>Prepared by:<span style='display:inline-block;width:300px;border-bottom: 1px solid #ccc'> &nbsp;</span></p>";
					pagend += "<p>Approved by:<span style='display:inline-block;width:300px;margin-left:8px;border-bottom: 1px solid #ccc'> &nbsp;</span></p>";
					pagend += "</div>";

					var allprint ='';
					var countpages = pageitem.length;
					var pageof = 1;

					for(var j in pageitem){
						allprint += pagehead;
						allprint += pageitem[j];
						allprint += endtable;
						allprint +=	 "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page "+pageof+" of "+countpages+"</p>";
						pageof = parseInt(pageof) + 1;
						allprint += pagend;
					}

					Popup(allprint);

				});


				$('body').on('click','#btnSubmitEmail',function(){

					var html = $('#email_body').html();
					var email = $('#e_email').val();
					var subject = $('#e_subject').val();

					if(email && subject){

						$.ajax({
						    url:'../ajax/ajax_supplier.php',
						    type:'POST',
						    data: {functionName:'emailSupplier',email:email,subject:subject,html:html},
						    success: function(data){

						    },
						    error:function(){

						    }
						});

					} else {
						tempToast('error','Enter email and subject first','Error');
					}

				});

			})(vm);

	</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>