<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('print_scs')){
		// redirect to denied page
		Redirect::to(1);
	}


	$date_width = 300;
	$name_width = 380;
	$address_width = 425;
	$tel_width = 180;
	$model_width = 180;
	$sn_width = 230;
	$outlet_width = 195;
	$purchase_width = 130;
	$dr_width = 210;
	$dec_width = 410;

	$dents_width=150;
	$dents_width_next = 230;
	$accesories_width=150;
	$accesories_width_next = 210;

	$receive_width=150;
	$conforme_width=150;

?>
	<!-- 	<link rel="stylesheet" href="../css/gridster.css" /> -->

	<style>

		#main-body{
			width: 600px;
			height: 500px;
			border: 1px solid #000;
			margin: 0 auto;
			padding: 10px;
		}
		#main-header{

			width:600px;
			margin-left: 20px;
			margin-right: 20px;
			margin-top: 10px;
			line-height: 12px;

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
				<a class='btn btn-default' @click ="showCon(1)" href='#'>Issue Service Call Slip</a>
				<a class='btn btn-default' @click ="showCon(2)"  href='#'>History</a>
			</div>
			<div v-show="!list_view">
				<div v-show="id == 0">
					<div class="row">
					<div class='col-md-3'>
						<div class="form-group" style='display:none;'>
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
							<input type="text" class='form-control' v-model='form.outlet'>
							<span class='help-block'> Outlet </span>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" class='form-control' v-model='form.accessories'>
							<span class='help-block'> Accessories </span>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" class='form-control' v-model='form.dents'>
							<span class='help-block'> Dents/Scratches </span>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" class='form-control' v-model='form.received_by'>
							<span class='help-block'> Receive By </span>
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
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input type="checkbox"  v-model='form.under_warranty'>
							<span> Under Warranty </span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.no_cold"> No cold</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.poor_cooling"> Poor cooling</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.water"> Water won't flow</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.no_hot">No Hot</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.motor">Motor won't run</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.water_con">Water continuous flow</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.no_power">No power</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.crack">Crack</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.poor_flowing">Poor Flowing</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.leaking">Leaking</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.malfunction">Malfunctions</div>
					<div class="col-md-4"><input type="checkbox" v-model="form.complains.other">Other Complains</div>
				</div>
					<br>
					<div class="row">

						<div class="col-md-6">
							<input type="text" class='form-control' v-show="form.complains.other" placeholder='Enter Other Complain' v-model='form.complain_other'>
						</div>
					</div>
				</div>
			<hr>

			<div class="conpage" id='print_div' style='width: 1256px;height: 766px; border: 1px solid #000;'>
				<div style='float:left;width:48%;margin-top:20px;margin-left:10px;overflow-x: hidden; font-size: 10px;' v-for="i in [1,2]">
				<div id='main-header'>
					<div >
						<div style='float:left; width:47%'>
							<h5>CEBUHIQ</h5>
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
				<div id='main-body' style='border:1px solid #000;height: 650px;padding:5px;'>
					<h3 class='text-center'>Service Call Slip</h3>
					<div style='float:left; width:70%'>
					<p>Date: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $date_width; ?>px;'>{{form.date}}</span></p>
					</div>
					<div style='float:left; width:27%'>
						<p style='color:red;font-size:15px;'>{{service_id}}</p>
					</div>
					<div style='clear: both;'></div>
					<p>Customer's Name: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $name_width; ?>px;'>{{ form.customer_name }}</span></p>
					<p>Address: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $address_width; ?>px;'>{{ form.address }}</span></p>
					<p>Tel Number: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $tel_width; ?>px;'>{{ form.tel_number }}</span> Outlet: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $outlet_width; ?>px;'>{{form.outlet}}</span></p>
					<p>Model: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $model_width; ?>px;'>{{ form.model }}</span> S/N: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $sn_width; ?>px;'>{{form.serial_number}}</span>  </p>
					<p>Date of Purchase: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $purchase_width; ?>px;'>{{form.date_sold}}</span> SI/DR #: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $dr_width; ?>px;'>{{form.si_dr}}</span>  </p>

					<p>
						Complains:
						<span style='display: inline-block;width:<?php echo $dec_width; ?>px;border-bottom: 1px solid #000;'>
						<span  v-show="form.complains.no_cold">No Cold. </span>
						<span  v-show="form.complains.no_hot">No Hot. </span>
						<span  v-show="form.complains.no_power">No Power. </span>
						<span  v-show="form.complains.leaking">Leaking. </span>
						<span  v-show="form.complains.poor_cooling">Poor Cooling. </span>
						<span  v-show="form.complains.motor">Motor won't run. </span>
						<span  v-show="form.complains.crack">Crack. </span>
						<span  v-show="form.complains.malfunction">Malfunctions. </span>
						<span  v-show="form.complains.water">Water Won't Flow. </span>
						<span  v-show="form.complains.water_con">Water continuous flow. </span>
						<span  v-show="form.complains.poor_flowing">Poor Flowing. </span>
						<span  v-show="form.complains.other">{{form.complain_other}} </span>
						</span>
					</p>

					<div style='clear: both;'></div>
					<div>
						<div>
							<div style='float:left; width:47%'>
								<p>Accessories: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $accesories_width; ?>px;'>{{form.accessories}}</span></p>
								<p><span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $accesories_width_next; ?>px;height:12px;'></span> </p>
								<p><span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $accesories_width_next; ?>px;height:12px;'></span> </p>
								<p><span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $accesories_width_next; ?>px;height:12px;'></span> </p>
							</div>
							<div style='float:left; width:47%;margin-left: 5px;'>
								<p>Dents/Scratches: <span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $dents_width; ?>px;'>{{form.dents}}</span> </p>
								<p><span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $dents_width_next; ?>px;height:12px;'></span> </p>
								<p style='height:12px;'>Under Warranty:  <span v-show="!form.under_warranty">(_)</span><span v-show="form.under_warranty">( / )</span></p>
								<p style='height:12px;'>Out of Warranty: <span v-show="form.under_warranty">(_)</span><span v-show="!form.under_warranty">( / )</span></p>
							</div>
						</div>
					</div>
					<div style='clear: both;'></div>
					<div>
						<h4 class='text-center' style='text-decoration: underline;' >Terms and Conditions</h4>
						<div style='margin: 0 auto;width:450px'>
							<p>I, the bearer of this unit certify that I authorize Cebu HiQ Service Dept.
							   to perform the necessary repair/job on my unit and that I agree with the
							   terms and conditions stated hereunder:
							</p>
							<p>
								1. Cebu HiQ shall not held liable for the loss and damage of my unit in the event of
								fire, typhoon, flood, and other acts of God.
							</p>
							<p>
								2. For unit under warranty, A copy of Sales Invoice must be presented. Failure to do so
								will mean that the customer shall pay the corresponding charge as in the SAR.
							</p>
							<p>
								3. This slip must be presented upon claiming the unit. The loss of this claim slip must be reported
								to Cebu HiQ immediately.
							</p>
						</div>
					</div>
					<br>
					<div style='clear: both;'></div>
					<div style='padding-left:50px;'>
						<div style='float:left; width:40%'>
							Received By: <br><br>
							<span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $receive_width; ?>px;'>{{form.received_by}}</span>
							<p class='text-center'>Signature over printed name</p>
						</div>
						<div style='float:left; width:40%; margin-left:30px;'>
							Conforme: <br><br>
							<span style='display: inline-block; border-bottom: 1px solid #000; width: <?php echo $conforme_width; ?>px;'>{{form.conforme}}</span>

							<p class='text-center'>Signature over printed name</p>
						</div>
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
				service_id:'',
				form:{
					date:'',
					customer_name:'',
					address:'',
					tel_number:'',
					model:'',
					serial_number:'',
					date_sold:'',
					si_dr:'',
					outlet:'',
					complains:{
						no_cold:false,poor_cooling:false,water: false,no_hot:false,motor:false,water_con:false,no_power:false,
						crack: false, poor_flowing:false, leaking:false,malfunction:false,other:false
					},
					complain_other:'',
					accessories:'',
					dents:'',
					under_warranty: false,
					received_by:'',
					conforme:'',
					ref_number:'',
				},
				service_list: [],
			},
			mounted: function(){

				var scs_form = localStorage['scs_form'];
				var self = this;

				if(scs_form){

					try {

						scs_form = JSON.parse(scs_form);

						self.service_id = scs_form.service_id;

						self.form = scs_form.form;

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
				clearForm: function(){
					var self = this;
					 self.service_id ='';
					localStorage.removeItem('scs_form');
					 self.form = {
						date:'',
							customer_name:'',
							address:'',
							tel_number:'',
							model:'',
							serial_number:'',
							date_sold:'',
							si_dr:'',
							outlet:'',
						 complain_other:'',
							complains:{
							no_cold:false,poor_cooling:false,water: false,no_hot:false,motor:false,water_con:false,no_power:false,
								crack: false, poor_flowing:false, leaking:false,malfunction:false,other:false
							},
							accessories:'',
							dents:'',
							under_warranty: false,
							received_by:'',
							conforme:'',
						 ref_number:'',
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
					    data: {functionName:'getList',ref_name:'service_call_slip'},
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
							data: {functionName:'insertForm',ref_table:'service_call_slip',service_id:self.service_id,data:JSON.stringify(self.form)},
							success: function(data){
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