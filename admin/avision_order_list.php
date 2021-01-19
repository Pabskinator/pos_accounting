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
				<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Upload Order List</h1>
			</div>
			<div class="row">
				<div class="col-md-6"></div>
				<div class="col-md-6 text-right">
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='wh-order.php'>
							Back To Orders
						</a>
						<a class='btn btn-default'  href='upload_avision.php'>
							Back To Upload Order
						</a>
					</div>
				</div>
			</div>
			<div class="panel panel-primary">
				<div class="panel-heading">
					List Of Uploaded Orders
				</div>
				<div class="panel-body">

						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" autocomplete="off" class='form-control' id='search' placeholder='Search Record...'>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<input autocomplete="off" placeholder='Search Item'  class='form-control' id='item_name' class='Search Item'>

								</div>
							</div>


						</div>
						<input type="hidden" id="hiddenpage" value='0'/>
						<div id="holder"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->

	<script>

		$(document).ready(function() {

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

			$("#item_name").keyup(function(){
				var searchtxt = $("#item_name");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getList();
				}, 1000);

			});



			function getList(){

				var page = $('#hiddenpage').val();
				var search = $('#search').val();

				var item_name = $('#item_name').val();

				$.ajax({
					url:'../ajax/ajax_avision.php',
					type:'POST',
					data: {functionName:'orderList',item_name:item_name,page:page,search:search},
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