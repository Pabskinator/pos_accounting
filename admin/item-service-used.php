<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_service_p') && !$user->hasPermission('item_service_s') && !$user->hasPermission('item_service_r') ) {
		// redirect to denied page
		Redirect::to(1);
	}


	//
	//                       _oo0oo_
	//                      o8888888o
	//                      88" . "88
	//                      (| -_- |)
	//                      0\  =  /0
	//                    ___/`---'\___
	//                  .' \\|     |// '.
	//                 / \\|||  :  |||// \
	//                / _||||| -:- |||||- \
	//               |   | \\\  -  /// |   |
	//               | \_|  ''\---/''  |_/ |
	//               \  .-\__  '-'  ___/-. /
	//             ___'. .'  /--.--\  `. .'___
	//          ."" '<  `.___\_<|>_/___.' >' "".
	//         | | :  `- \`.;`\ _ /`;.`/ - ` : | |
	//         \  \ `_.   \_ __\ /__ _/   .-` /  /
	//     =====`-.____`.___ \_____/___.-`___.-'=====
	//                       `=---='
	//
?>


	<!-- Page content -->
	<div id="page-content-wrapper">	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">


	<div class="content-header">

		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Item Used</h1>
	</div> <?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>
	<?php include 'includes/service_nav.php'; ?>
	<div id="test"></div>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-6">
							Item Used
						</div>
						<div class="col-md-6 text-right">
							<button id='btnDownload' class='btn btn-default btn-sm'><i  class='fa fa-download'></i></button>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-8">

						</div>
						<div class="col-md-4">
							<input type="text" class='form-control' id='txtSearch' placeholder='Search'>
						</div>
					</div>
					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'></div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->


	<script>

		$(function() {
			getPage(0);
			$('body').on('click','#btnDownload',function(){
				var s = $('#txtSearch').val();

				window.open(
					'excel_downloader_2.php?downloadName=serviceUsed&s='+s,
					'_blank'
				);
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var s = $('#supplier_id').val();
				getPage(page,search,b,r,s);
			});
			var timer;
			$("#txtSearch").keyup(function(){
				var searchtxt = $("#txtSearch");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});
			function getPage(p){
				var s = $('#txtSearch').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,s:s,functionName:'itemUsedPaginate',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>