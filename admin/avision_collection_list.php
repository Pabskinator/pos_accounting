<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head


	require_once '../libs/phpexcel/Classes/PHPExcel.php';
	require_once '../includes/admin/page_head2.php';


	function toUnixTimeStamp2($date) {
		return ($date - 32662) * 24 * 60 * 60 + 612806400;
	}

	function removeExcessSpace($s) {
		$s = trim($s);
		$s = str_replace('    ', ' ', $s);
		$s = str_replace('   ', ' ', $s);
		$s = str_replace('  ', ' ', $s);

		return $s;
	}

	function removeUnwatedChar($s) {
		$s = trim($s);
		$s = str_replace('mr.', '', strtolower($s));
		$s = str_replace('mr', '', strtolower($s));

		return strtolower($s);

	}

	function addPayment($cid){
		$payment = new Payment();

		$payment->create(array(
			'created' => time(),
			'company_id' => $cid,
			'is_active' => 1
		));
		$payment_lastid = $payment->getInsertedId();
		return $payment_lastid;
	}


	$sales_type = new Sales_type();
	$types = $sales_type->get_active('salestypes',[1,'=',1]);

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">

			<div class="content-header">
				<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Collection List</h1>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default btn_nav' data-con='1' title='Detailed List' href='#'>
							<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Detailed List</span>
						</a>
						<a class='btn btn-default btn_nav' data-con='2' title='Summary ' href='#'>
							<span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Summary List</span>
						</a>
					</div>
				</div>
				<div class="col-md-6 text-right">
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='wh-order.php'>
							Back To Orders
						</a>
						<a class='btn btn-default'  href='upload_avision_collection.php'>
							Back To Upload Collection
						</a>
					</div>
				</div>
			</div>
			<div class="panel panel-primary">
				<div class="panel-heading">
					List Of Uploaded Collection
				</div>
				<div class="panel-body">
					<div id='con1'>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='search' placeholder='Search Record...'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' id='type'>
										<option value="">Select Transaction Type</option>
										<option value="Orders-Item Charges">Orders-Item Charges</option>
										<option value="Orders-Lazada Fees">Orders-Lazada Fees</option>
										<option value="Refunds-Item Charges">Refunds-Item Charges</option>
										<option value="Refunds-Lazada Fees">Refunds-Lazada Fees</option>
										<option value="Orders-Other Credit">Orders-Other Credit</option>
										<option value="Other Services-Services">Other Services-Services</option>
										<option value="Other Services-Others">Other Services-Others</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' id='fee_name'>
										<option value="">Select Fee Name</option>
										<option value="Item Price Credit">Item Price Credit</option>
										<option value="Payment Fee">Payment Fee</option>
										<option value="Reversal Item Price">Reversal Item Price</option>
										<option value="Reversal shipping Fee (Paid by Customer)">Reversal shipping Fee (Paid by Customer)</option>
										<option value="Shipping Fee (Charged by Lazada)">Shipping Fee (Charged by Lazada)</option>
										<option value="Shipping Fee (Paid By Customer)">Shipping Fee (Paid By Customer)</option>
										<option value="Adjustments Others">Adjustments Others</option>
										<option value="Other Debits">Other Debits</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='dt_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='dt_to' placeholder='Date To'>
								</div>
							</div>

						</div>
						<input type="hidden" id="hiddenpage" value='0'/>
						<div id="holder"></div>
					</div>
					<!-- end container one -->
					<!-- start container two -->
					<div id="con2">
						<h3>Statement of Account</h3>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='soa_dt_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='soa_dt_to' placeholder='Date To'>
								</div>
							</div>
						</div>
						<div id='holder2'></div>
					</div>
					<!-- end container two -->
					<!-- start container three -->
					<div id="con3">
						<h3>SOA</h3>
					</div>
					<!-- end container three -->

				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->

	<script>

		$(document).ready(function() {
			showCon(1);


			$('body').on('click','.btn_nav',function(){
				var con = $(this);
				var c = con.attr('data-con');
				showCon(c);
			});

			function showCon(c){
				var con1 = $('#con1');
				var con2 = $('#con2');
				var con3 = $('#con3');
				con1.hide();
				con2.hide();
				con3.hide();
				if ( c == 1 ){
					con1.show();
				} else if ( c == 2 ){
					con2.show();
					getSOA();
				} else if ( c == 3 ){
					con3.show();
				}
			}

			$('#soa_dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#soa_dt_from').datepicker('hide');
				changeDateSoa();
			});

			$('#soa_dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#soa_dt_to').datepicker('hide');
				changeDateSoa();
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				changeDate();
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				changeDate();
			});

			getList();

			$('body').on('click','.paging',function(e){

				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getList();

			});

			var timer;

			$("#search").keyup(function(){
				var searchtxt = $("#search");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getList();
				}, 1000);

			});
			
			$('body').on('change','#type,#fee_name',function(){
				getList();
			});

			function changeDate(){

				if($('#dt_from').val() && $('#dt_to').val()){
					getList();
				}

			}
			function changeDateSoa(){

				if($('#soa_dt_from').val() && $('#soa_dt_to').val()){
					getSOA();
				}

			}

			function getSOA(){
				var dt_from = $('#soa_dt_from').val();
				var dt_to = $('#soa_dt_to').val();

				$.ajax({
				    url:'../ajax/ajax_avision.php',
				    type:'POST',
				    data: {functionName:'getSOA',dt_from:dt_from,dt_to:dt_to},
				    success: function(data){
					    $('#holder2').html(data);
				    },
				    error:function(){

				    }
				});
			}
			function getList(){

				var page = $('#hiddenpage').val();
				var search = $('#search').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var type = $('#type').val();
				var fee_name = $('#fee_name').val();

				$.ajax({
				    url:'../ajax/ajax_avision.php',
				    type:'POST',
				    data: {functionName:'paymentList',fee_name:fee_name,type:type,page:page,search:search,dt_from:dt_from,dt_to:dt_to},
				    success: function(data){
						$('#holder').html(data);
				    },
				    error:function(){

				    }
				});
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>