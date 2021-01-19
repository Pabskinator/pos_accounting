<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('print_sar')){
		// redirect to denied page
		Redirect::to(1);
	}



?>
	<!-- 	<link rel="stylesheet" href="../css/gridster.css" /> -->

	<style>

		#main-body{
			width: 800px;
			height: 930px;
			border: 1px solid #000;
			margin: 0 auto;
			padding: 10px;
		}
		#main-header{

			width:780px;
			margin-left: 20px;
			margin-right: 20px;
			margin-top: 10px;

		}
		.bg-black{
			/*	background: red; */
		}
	</style>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset" id='scs'>
			<div class="container-fluid">
				<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default' @click ="showCon(1)" href='#'>Issue Service Accomplishment Receipt</a>
					<a class='btn btn-default' @click ="showCon(2)"  href='#'>History</a>
				</div>
				<div v-show="!list_view">
					<div v-show="id == 0">
						<div class="row">
							<div class='col-md-3'>
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Service ID' v-model="service_id">
									<span class='help-block'>Service ID</span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.date'>
									<span class='help-block'>Date</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.customer_name'>
									<span class='help-block'>Customer Name</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.address'>
									<span class='help-block'>Address</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.tel_number'>
									<span class='help-block'>Tel Number</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.model'>
									<span class='help-block'>Model</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.serial_number'>
									<span class='help-block'>Serial Number</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.date_sold'>
									<span class='help-block'>Date Sold</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.si_dr'>
									<span class='help-block'> SI/DR </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.complains'>
									<span class='help-block'> Complains  </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.work_done'>
									<span class='help-block'> Actual Work Done </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.findings'>
									<span class='help-block'>Findings </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.parts_needed'>
									<span class='help-block'>Part Needed </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.labor_charge'>
									<span class='help-block'>Labor Charge </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.parts_charge'>
									<span class='help-block'>Parts Charge</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.other_charge'>
									<span class='help-block'>Other Charge</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' disabled v-model='overall_charge'>
									<span class='help-block'> Over All Charge </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.technician'>
									<span class='help-block'> Technician </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.conforme'>
									<span class='help-block'> Conforme </span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' v-model='form.ref_number'>
									<span class='help-block'> Ref Number </span>
								</div>
							</div>

						</div>


					</div>
					<hr>

					<div class="conpage" id='print_div' style='width: 816px;height: 1056px; border: 1px solid #000;'>
						<div id='main-header'>
							<div >
								<div style='float:left; width:47%'>
									<h3>CEBUHIQ</h3>
									<p>Manufacturing Corp</p>
								</div>
								<div style='float:left; width:47%' class="text-right">
									<p>Landing, Catarmina, Liloan Center</p>
									<p>Telephone# (032)  424-4000/424-3148</p>
									<p>Fax#(032) 424-4863</p>
								</div>
							</div>
						</div>
						<div style='clear: both;'></div>
						<div id='main-body' style='border:1px solid #000;height: 930px;padding:5px;'>
							<h3 class='text-center'>Service Accomplishment Report</h3>
							<div style='float:left; width:70%'>
								<p>Date: <span style='display: inline-block; border-bottom: 1px solid #000; width: 350px;'>{{form.date}}</span></p>
							</div>
							<div style='float:left; width:27%'>
								<p style='color:red;font-size:15px;'>{{service_id}}</p>
							</div>
							<div style='clear: both;'></div>
							<p>Customer's Name: <span style='display: inline-block; border-bottom: 1px solid #000; width: 350px;'>{{ form.customer_name }}</span></p>
							<p>Address: <span style='display: inline-block; border-bottom: 1px solid #000; width: 655px;'>{{ form.address }}</span></p>
							<p>Tel Number: <span style='display: inline-block; border-bottom: 1px solid #000; width: 240px;'>{{ form.tel_number }}</span> <span style='margin-left: 20px;'>Date Prepared <span style='display: inline-block; border-bottom: 1px solid #000; width: 240px;'>{{form.date_prepared}}</span></span>  </p>
							<p>Model: <span style='display: inline-block; border-bottom: 1px solid #000; width: 270px;'>{{ form.model }}</span> <span style='margin-left:90px;'>S/N: <span style='display: inline-block; border-bottom: 1px solid #000; width: 240px;'>{{form.serial_number}}</span> </span> </p>
							<p>Date of Purchase: <span style='display: inline-block; border-bottom: 1px solid #000; width: 200px;'>{{form.date_sold}}</span> <span style='margin-left:60px;'>SI/DR #: <span style='display: inline-block; border-bottom: 1px solid #000; width: 240px;'>{{form.si_dr}}</span></span>  </p>
							<hr>
							<table>
								<tr>
									<td style='width:360px;word-wrap: break-word;'>
										Complains:<br>
										<p style='border-bottom:1px solid #222;width:320px;'>&nbsp;{{form.complains}}</p>
										<p style='border-bottom:1px solid #222;width:320px;'>&nbsp;</p>
										<p style='border-bottom:1px solid #222;width:320px;'>&nbsp;</p>
									</td>
									<td style='width:360px'>
										Work Done:
										<p style='border-bottom:1px solid #222;'>&nbsp;{{form.work_done}}</p>
										<p style='border-bottom:1px solid #222;'>&nbsp;</p>
										<p style='border-bottom:1px solid #222;'>&nbsp;</p>
									</td>

								</tr>
							</table>
							<table>
								<tr>
									<td style='width:360px;word-wrap: break-word;'>
										Findings:<br>
										<p style='border-bottom:1px solid #222;width:320px;'>&nbsp;{{form.findings}}</p>
										<p style='border-bottom:1px solid #222;width:320px;'>&nbsp;</p>
										<p style='border-bottom:1px solid #222;width:320px;'>&nbsp;</p>
									</td>
									<td style='width:360px'>
										Parts needed:
										<p style='border-bottom:1px solid #222;'>&nbsp;{{form.parts_needed}}</p>
										<p style='border-bottom:1px solid #222;'>&nbsp;</p>
										<p style='border-bottom:1px solid #222;'>&nbsp;</p>
									</td>

								</tr>
							</table>
							<br>
							<table>
								<tr>
									<td style='width:360px;word-wrap: break-word;'>
										<h5>Service Charge</h5>
									</td>
									<td style='width:290px'>
										<h5>Important Remainders</h5>
									</td>

								</tr>
							</table>
							<table>
								<tr>
									<td style='width:370px;word-wrap: break-word;'>
										<p>
											Labor:
											<span style='border-bottom:1px solid #222;display: inline-block;width:280px;'>{{ formatNumber(form.labor_charge)}}</span>
											<br><br>
											Parts:
											<span style='border-bottom:1px solid #222;display: inline-block;width:285px;'>{{formatNumber(form.parts_charge)}}</span>
											<br>
											<br>
											Others:
											<span style='border-bottom:1px solid #222;display: inline-block;width:274px;'>{{formatNumber(form.other_charge)}}</span>
											<br>
											<br>
											Over All Charge:
											<span style='border-bottom:1px solid #222;display: inline-block;width:216px;'>{{formatNumber(form.overall_charge)}}</span>
										</p>

									</td>
									<td style='width:300px'>
										<p>(  ) I hereby agree to above charges incurred or to be incurred in the repair to my unit</p>
										<p>(  ) This is to acknowledge the above service rendered to my unit</p>
										<p>(  ) Received in good condition</p>
									</td>

								</tr>
							</table>

							<br>
							<div style='clear: both;'></div>
							<div>
								<div style='float:left; width:47%'>
									Technician: <br><br>
									<span style='display: inline-block; border-bottom: 1px solid #000; width: 360px;'>{{form.technician}}</span>
									<p class='text-center'>Signature over printed name</p>
								</div>
								<div style='float:left; width:47%; margin-left:30px;'>
									Conforme: <br><br>
									<span style='display: inline-block; border-bottom: 1px solid #000; width: 360px;'>{{form.conforme}}</span>

									<p class='text-center'>Signature over printed name</p>
								</div>
							</div>
						</div>
					</div>
					<br>
					<button class='btn btn-primary' @click="print">print</button>
				</div>
				<!-- end print view -->
				<div v-show="list_view">

					<table class='table' id='tblWithBorder'>
						<thead>
						<tr>
							<th>ID</th>
							<th>Service ID</th>
							<th>Ref Number</th>
							<th>Client Name</th>
							<th>DR/SI</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						<tr v-for="rem in service_list">
							<td>{{ rem.id }}</td>
							<td>{{ rem.ref_id }}</td>
							<td>{{ rem.ref_number }}</td>
							<td>{{rem.decoded.customer_name}}</td>
							<td>{{rem.decoded.si_dr}}</td>
							<td><button class='btn btn-default' @click="getDetails(rem)" >Reprint</button></td>
						</tr>
						</tbody>

					</table>
				</div>  <!-- end list view -->

			</div> <!-- end page content wrapper-->
		</div>
		<script src='../js/vue3.js'></script>
		<script>
			var vm = new Vue({
				el: '#scs',
				data:{
					list_view: false,
					id:0,
					service_id:'22',
					form:{
						date:'',
						customer_name:'',
						address:'',
						tel_number:'',
						date_prepared:'',
						model:'',
						serial_number:'',
						date_sold:'',
						si_dr:'',
						complains:'',
						work_done:'',
						findings:'',
						parts_needed:'',
						labor_charge:'',
						parts_charge:'',
						other_charge:'',
						overall_charge:'',
						technician:'',
						conforme:'',
						ref_number:''
					},
					service_list: [],
				},
				computed: {
					overall_charge: function(){
						var labor = (this.form.labor_charge) ? parseFloat(this.form.labor_charge) : 0;
						var parts = (this.form.parts_charge) ? parseFloat(this.form.parts_charge) : 0;
						var other = (this.form.other_charge) ? parseFloat(this.form.other_charge) : 0;
						this.form.overall_charge = (labor + parts + other);
						return number_format(this.form.overall_charge,2);

					}
				},
				mounted: function(){

					var sar_form = localStorage['sar_form'];
					var self = this;

					if(sar_form){

						try {

							sar_form = JSON.parse(sar_form);

							self.service_id = sar_form.service_id;

							self.form = sar_form.form;

						} catch(e){

							console.log("Invalid Data");

						}

					}
					/*
					var i = 1;

					var tmer = setInterval(function(){

						var n = '';
						if(i < 10){
							n = "0"+i;
						} else {
							n = i;
						}
						i++;
						window.open(
							'https://beta.cebupacificair.com/Flight/InternalSelect?s=true&o1=MNL&d1=KIX&dd1=2019-07-'+n+'&dd2=2019-07-30',
							'_blank' // <- This is what makes it open in a new window.
						);

						if(i == 31){
							clearTimeout(tmer);
						}

					},2000);
	 */


				},
				methods: {
					formatNumber: function(v){
						return number_format(v,2);
					},
					clearForm: function(){
						var self = this;
						self.service_id ='';
						localStorage.removeItem('scs_form');
						self.form = {
							date:'',
							customer_name:'',
							address:'',
							tel_number:'',
							date_prepared:'',
							model:'',
							serial_number:'',
							date_sold:'',
							si_dr:'',
							complains:'',
							work_done:'',
							findings:'',
							parts_needed:'',
							labor_charge:'',
							parts_charge:'',
							other_charge:'',
							overall_charge:'',
							technician:'',
							conforme:'',
							ref_number:''
						};
					},

					getDetails: function (rem){
						var self = this;
						console.log(rem);
						self.form = JSON.parse(rem.json_data);
						self.id = rem.id;
						self.service_id = rem.ref_id;
						this.list_view = false;
					},
					showCon: function(c){
						this.id = 0;
						if(c == 1){
							this.list_view = false;
							this.clearForm();
						} else if (c == 2){
							this.list_view = true;
							this.getList();
						}
					},

					getList: function(){
						var self = this;
						$.ajax({
							url:'../ajax/ajax_form.php',
							type:'POST',
							dataType:'json',
							data: {functionName:'getList',ref_name:'service_acknowledgement'},
							success: function(data){
								self.service_list = data;
							},
							error:function(){

							}
						});
					},
					print: function(){
						var self = this;
						var html = $('#print_div').html();
						if(self.id != 0){
							self.popUpPrintWithStyle(html);
						} else {

							$.ajax({
								url:'../ajax/ajax_form.php',
								type:'POST',
								data: {functionName:'insertForm',ref_table:'service_acknowledgement',service_id:self.service_id,data:JSON.stringify(self.form)},
								success: function(data){
									localStorage.removeItem('sar_form');
									self.popUpPrintWithStyle(html);
								},
								error:function(){

								}
							});
						}

					}, popUpPrintWithStyle: function(data) {
						var mywindow = window.open('', 'new div', '');
						mywindow.document.write('<html><head><title></title><style>.bg-black{background:black;}</style>');
						mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');

						mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
						mywindow.document.write(data);
						mywindow.document.write('</body></html>');
						setTimeout(function() {
							mywindow.print();
							mywindow.close();

						}, 300);
						return true;
					}
				}

			})
		</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>