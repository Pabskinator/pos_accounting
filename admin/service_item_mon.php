<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(false) {
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
		<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span>For releasing </h1>
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
				<div class="panel-heading">Service Request</div>
				<div class="panel-body">

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
		$(function(){

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>