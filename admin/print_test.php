<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('invoice_layout')){
		// redirect to denied page
		Redirect::to(1);
	}

	$layout = new Barcode();
	$cur = $layout->get_print_layout($user->data()->id,'invoice');

	$format = json_decode($cur->layout);
	$final = [];

	foreach($format as $f){
		$final[$f->order] = $f;
	}
	ksort($final);



?>
	<!-- 	<link rel="stylesheet" href="../css/gridster.css" /> -->
	<style>
		.conpage{
			width: <?php echo $cur->width . "px"; ?>;
			height: 700px;
			border: 1px solid #000;
		}

	</style>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<input type="hidden" id='txtLayout' value='<?php echo json_encode($final); ?>'>
			<div class="conpage" id="content">

			</div>
			<button id='btnPrint'>Print</button>
			<div class='clearfix'></div>
			<hr>
			<div id='PrintController'>
				<h3>{{title}}</h3>
				<table class="table table-bordered">
					<tr v-for="el in getAll">
						<td style='border-top:1px solid #ccc;'>
							<input type="text" v-model="el.order" value="{{el.order}}">
						</td>
						<td style='border-top:1px solid #ccc;'>
							<input type="text" v-model="el.key" value="{{el.key}}">
						</td>
						<td style='border-top:1px solid #ccc;'>
							<input type="text" v-model="el.label" value="{{el.label}}">
						</td>
						<td style='border-top:1px solid #ccc;'>
							<input type="text" v-model="el.value" value="{{el.value}}">
						</td>
						<td style='border-top:1px solid #ccc;'>
							<input type="text" v-model="el.div" value="{{el.div}}">
						</td>
						<td style='border-top:1px solid #ccc;'>
							<table class='table'>
								<tr v-for="(index,s) in el.style">
									<td>{{index}}</td>
									<td><input type="text" v-model="s" value="{{s}}"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<hr>
				<button v-on:click="saveLayout">Save</button>

				<table class='table'>
					<tr>
						<td>
							<input type="text" v-model='a_order'>
						</td>
						<td>
							<input type="text" v-model='a_key'>
						</td>
						<td>
							<input type="text" v-model='a_label'>
						</td>
						<td>
							<input type="text" v-model='a_value'>
						</td>
					</tr>
				</table>
				<button v-on:click="addMore">Add more</button>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script src='../js/vue.js'></script>
	<script>
		$(function(){
			var form = $('#txtLayout').val();
			var vm = new Vue({
				el:'#PrintController',
				data:{
					elements:JSON.parse(form),
					title: 'Print',
					a_key: '',
					a_order: '',
					a_label: '',
					a_value: '',
					styles: {
						"width": "0 auto",
						"height": "0 auto",
						"font-size": "12px",
						"text-align": "center",
						"float": "none",
						"position": "static"
					}
				},
				computed: {
					getAll : function(){
						return this.elements;
					}
				},
				methods:{
					saveLayout: function(){
						$.ajax({
						    url:'../ajax/ajax_accounting.php',
						    type:'POST',
						    data: {functionName:'saveLayout',type:'invoice',layout: JSON.stringify(this.elements)},
						    success: function(data){
						      alertify.alert(data);
						    },
						    error:function(){
						        
						    }
						})
					},
					addMore: function(){
						var det = {
							key: this.a_key,
							order: this.a_order,
							label: this.a_label,
							value: this.a_value,
							style: this.styles
						};
						var cur = this.elements;
						cur[this.a_order] = det;
						console.log(cur);
					}
				}
			});

			var data = {
				company: localStorage['company_name'],
				date: '01/01/2016',
				tin: '123213123213123213123123',
				contact: '012391230',
				ctr_no: '0001',
				items: [
					{qty:1,item_code:'VX12312312',price:20,total:20},
					{qty:1,item_code:'VX12312312',price:20,total:20},
					{qty:1,item_code:'VX12312312',price:20,total:20},
					{qty:1,item_code:'VX12312312',price:20,total:20},
					{qty:2,item_code:'VX45612321312',price:20,total:40}
				],
				sub_total: 123213123123,
				vat: 123,
				total: 123213,
				remarks: 'Test',
				test: 'TEST avl',
				address: '123 baker st.',
				member_address: '123 baker st.',
				member: 'Jay Temp',
				cashier: 'Jay Temp'
			};

			print_con_data(data);

			function print_con_data(data){
				try{
					form = JSON.parse(form);
					var ret_html = "";
					var prev = false;
					for(var i in form){
						var label = "";
						if(data[form[i].key]){
							if(form[i].type == 'table'){


								if(form[i].style){
									var ob;
									try{
										ob = form[i].style;
										for(var o in ob){
											styles += o +":"+ ob[o] +";";
										}
										console.log(styles);
									}catch(e){

									}
								}
								ret_html += "<table class='table'>";
								var items = data.items;
								for(var arr in items){
									var divs = (form[i].div).split("|");
									for(var j in divs){
										var props = (divs[j]).split(',');
										ret_html += "<tr>";
										for(var p in props){
											ret_html += "<td style='"+styles+"'>"+items[arr][props[p]]+"</td>";
										}
										ret_html += "</tr>";
									}
								}

								ret_html += "</table>";
							} else {
								var styles = "";
								var has_float = false;

								if(form[i].style){
									var ob;
									try{
										ob = form[i].style;
										for(var o in ob){
											var extra = "";
											if(o == "float"){
												has_float = true;
												prev = true;
											}
											styles += o +":"+ ob[o] +";";
										}
										console.log(styles);
									}catch(e){

									}
								}
								if(!has_float && prev){
									ret_html += "<div style='clear:both;'>&nbsp;</div>";
									prev = false;
								}
								if(form[i].label){
									label = form[i].label + " ";
								}
								ret_html += "<div style='"+styles+"'>"+label+data[form[i].key]+"</div>";
							}
						}
					}
					$('#content').html(ret_html);
				}catch(e){

				}
			}
			function popUpPrintWithStyle(data){
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:0;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function(){
					mywindow.print();
					mywindow.close();
				},300);
				return true;
			}
			$('body').on('click','#btnPrint',function(){
				popUpPrintWithStyle($('#content').html());
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>