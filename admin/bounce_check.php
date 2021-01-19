<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('cheque_monitoring')){
		// redirect to denied page
		Redirect::to(1);
	}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
						Bounce Check Monitoring
				</h1>
			</div>


		<div class="panel panel-primary">
			<div class="panel-heading">
				Bounce Cheque
			</div>
			<div class="panel-body">
				<div class="row">
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
				<div class="col-md-3">
					<button class='btn btn-default' id='btnSubmit'>Submit</button>
				</div>
				<div class="col-md-3 text-right">

				</div>
			</div>

			<div id="con"></div>

			</div>
		</div>


	</div>

	</div> <!-- end page content wrapper-->

	<script>
		$(function(){

			getBounceCheck();

			$('body').on('click','#btnPrint',function(){

				// insert first

				// ref id

				//
				var id = $(this).attr('data-print_id');
				if(id == 0){
					id = '0000';
				}
				var html = $('#con').html();
				var date_obj = new Date();
				var curDate = (parseInt(date_obj.getMonth()) + parseInt(1)) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
				var dt_cover = $('#dt_cover').val();
				var ref_id = "BCR "+ date_obj.getFullYear() +"-"+(parseInt(date_obj.getMonth()) + parseInt(1));

				if(dt_cover){
					ref_id = "BCR "+ dt_cover;
				}
				var company_name = localStorage['company_name'];
				var ref_con = "<div style='font-size:15px;position:fixed;top:10px;right:10px;'>Reference Number: "+ref_id+"</div>";
				var tbl = "<table class='table table-bordered'><tr><th>Reported Date: "+curDate+"</th></tr></table>";
				var head = 	"<div style='font-size:25px;' class='text-center'><img src='../css/img/logo.jpg' alt=''> &nbsp;&nbsp; </strong>" + company_name + "</strong></div>";
				head += "<div  style='font-size:20px;'  class='text-center'><strong>Bounce Check Report</strong></div>";
				head += ref_con;
				var ret = head + tbl + html;
				var arr = [];

				$('#tblForApproval tbody tr').each(function(){

					var row = $(this);
					var cheque_id = row.attr('data-id');

					arr.push(cheque_id);

				});

				if(id == 0){


					$.ajax({
					    url:'../ajax/ajax_member_service.php',
					    type:'POST',
					    data: {functionName:'insertCheckForm', data: ret, arr:JSON.stringify(arr)},
					    success: function(data){
						    var extra = "<div style='clear:both;'></div><div style='width:45%;float:left;font-size:15px;'>Prepared By: <span style='display: inline-block;border-bottom: 1px solid #ccc;width:35%;'></span> </div>";
						    extra += "<div style='width:45%;float:left;font-size:15px;'>Checked By: <span style='display: inline-block;border-bottom: 1px solid #ccc;width:35%;'></span></div>";
						    popUpPrint(data + extra,true);

					    },
					    error:function(){

					    }
					});

				} else {
					var extra = "<div style='clear:both;'></div><div style='width:45%;float:left;font-size:15px;'>Prepared By: <span style='display: inline-block;border-bottom: 1px solid #ccc;width:35%;'></span> </div>";
					extra += "<div style='width:45%;float:left;font-size:15px;'>Checked By: <span style='display: inline-block;border-bottom: 1px solid #ccc;width:35%;'></span></div>";
					popUpPrint(ret + extra,true);

				}

			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
			});

			$('body').on('click','#btnSubmit',function(){

				getBounceCheck();
			});

			  function popUpPrint(data, withStyle) {

				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				if(withStyle) {
					/*optional stylesheet*/
					mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
				}
				mywindow.document.write('<style>.hideOnPrint { display:none; } table.table-bordered > tbody  > tr > td { border:1px solid #000 !important; } table.table-bordered > tbody  > tr > th { border:1px solid #000 !important; } table.table-bordered > thead  > tr > th { border:1px solid #000 !important; } table.table-bordered > tfoot  > tr > th { border:1px solid #000 !important; }</style></head><body style="padding:0;margin:0;font-size:10px;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				  setTimeout(function(){
					  mywindow.print();
					  mywindow.close();
				  },1000);

				return true;

			}

			function getBounceCheck(){

				var dt1 = $('#dt_from').val();
				var dt2 = $('#dt_to').val();

				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'bounceCheck',dt1:dt1, dt2:dt2},
				    success: function(data){
						$('#con').html(data);
				    },
				    error:function(){

				    }
				});


			}

		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>