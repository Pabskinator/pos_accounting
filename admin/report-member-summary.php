<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$st  = new Sales_type();
	$sale_types = $st->getSalesType();

?>

	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Client </h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('salesflash')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
				}
			?>
			<?php require_once 'includes/report_nav.php'; ?>
			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-9"></div>
						<div class="col-md-3 text-right">
							<button class='btn btn-default btn-sm' @click="downloadSummary"><i class='fa fa-download'></i></button>
						</div>
					</div>
				</div>
				<div class="panel-body">

					<div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">

									<select name="sales_type_id" id="sales_type_id" v-model="sales_type_id" class='form-control'>

										<?php 
											if($sale_types){
												foreach($sale_types as $st){
												?>
													<option value="<?php echo $st->id; ?>"><?php echo $st->name; ?></option>
										<?php
												}
											}
										?>
									</select>
									<span class='help-block'>Sales Type</span>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<button class='btn btn-default' @click='getYearlySummary()'>Submit</button>
								</div>
							</div>
						</div>

						<div id="yearly-holder" style='font-size: 5px;'>
							<div v-show="ajax.pending">Loading...</div>
							<hr>
							<div class='table-responsive'>
								<div id='holder'>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:70%;' >
			<div class="modal-content"  >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script src='https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/TableExport/4.0.11/js/tableexport.min.js'></script>
	<script src='../js/vue3.js'></script>
	<script>

		var vm = new Vue({
			el:'#page-content-wrapper',
			data:{

				yearlyData:[],
				yearlyTotal:[],
				yearlyKeys:[],
				st:[],
				member_total: [],
				ajax: { pending:false },
				test:{},
				sales_type_id:"",
			},
			mounted: function(){
				this.getYearlySummary();

			},
			computed: {

			},
			methods: {

				downloadSummary: function(){

					var self = this;
					var tid = (self.sales_type_id) ? self.sales_type_id :'';

					window.open(
						'../ajax/ajax_reports.php?functionName=memberSummaryByAgent&sales_type_id='+tid+'&is_dl=1',
						'_blank'
					);


				},

				getYearlySummary:function(){
					var self = this;
					self.ajax.pending = true;
					$.ajax({
						url:'../ajax/ajax_reports.php',
						type:'POST',

						data: {functionName:'memberSummaryByAgent',sales_type_id:self.sales_type_id},
						success: function(data){

							$('#holder').html(data);

							self.ajax.pending = false;
						},
						error:function(){
							self.ajax.pending = false;
						}
					});
				}
			}

		});

	</script>

	<script type="text/javascript">

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>