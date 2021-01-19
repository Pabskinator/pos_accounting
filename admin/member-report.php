<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<!-- Page content -->
	<div id="page-content-wrapper" >

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Assessment
				</h1>

			</div>
			<div class="row">
				<div class="col-md-12">


					<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' @click="getContainer(1)"  title='Referrals'>
							<span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>Referrals</span></span></a>
						<a class='btn btn-default' @click="getContainer(2)"  title='Booking Summary'>
							<span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>Booking Summary</span></span></a>
					</div>
				</div>
				<div class="col-md-12">

					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">
							<div class='row'>
								<div class='col-md-6'>List</div>
								<div class='col-md-6 text-right'>

								</div>
							</div>
						</div>
						<div class="panel-body">
							<div v-show="con.con1">
								<h4>Referrals</h4>
								<div v-show="referrals.length">
									<table class='table table-bordered'>
										<thead>
											<tr>
												<th>Client</th>
												<th>Referral</th>
												<th>Date</th>
												<th>Original Expiration</th>
												<th>Extended Expiration</th>
											</tr>
										</thead>
										<tbody>
										<tr v-for="ref in referrals">
											<td style='border-top:1px solid #ccc;'>{{ref.referred_by}}</td>
											<td style='border-top:1px solid #ccc;' >{{ref.member_name}}</td>
											<td style='border-top:1px solid #ccc;'>{{ref.created}}</td>
											<td style='border-top:1px solid #ccc;'>{{ref.old_expiration}}</td>
											<td style='border-top:1px solid #ccc;'>{{ref.new_expiration}}</td>
										</tr>
										</tbody>
									</table>
								</div>
								<div v-show="!referrals.length">
									<div class="alert alert-info">
										No record.
									</div>
								</div>
							</div>
							<div v-show="con.con2">
								<h4>Booking Summary</h4>
								<div class="row" style='margin-bottom: 10px;'>
									<div class="col-md-4">
										<a href="#"  class='btn btn-default' @click.prevent="prevBook">Prev</a>
									</div>
									<div class="col-md-4 text-center">
										<strong class='text-danger'>{{dt_range}}</strong>
									</div>
									<div class="col-md-4 text-right">
										<a href="#" class='btn btn-default' @click.prevent="nextBook">Next</a>
									</div>
								</div>


								<table class='table table-bordered'>
									<thead>
										<tr>
											<th>Monday</th>
											<th>Tuesday</th>
											<th>Wednesday</th>
											<th>Thursday</th>
											<th>Friday</th>
											<th>Sat</th>
											<th>Sun</th>
										</tr>
									</thead>
									<tbody>
									<tr>
										<td v-html='booking.monday'></td>
										<td v-html='booking.tuesday'></td>
										<td v-html='booking.wednesday'></td>
										<td v-html='booking.thursday'></td>
										<td v-html='booking.friday'></td>
										<td v-html='booking.saturday'></td>
										<td v-html='booking.sunday'></td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div> <!-- end page content wrapper-->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div>
	<script src='../js/vue3.js'></script>
	<script>

		var vm = new Vue({
			el:'#page-content-wrapper',
			data: {
				con : {con1:true,con2:false},
				referrals: [],
				dt_range:'',
				cur_week:0,
				booking:{monday:'',tuesday:'',wednesday:'',thursday:'',friday:'',saturday:'',sunday:''}
			},
			computed:{

			},
			mounted: function(){
				var vm = this;
				vm.getReferrals();
				vm.bookings();
			},
			methods: {
				prevBook: function(){
					this.cur_week--;
					this.bookings();
				},
				nextBook: function(){
					this.cur_week++;
					this.bookings();
				},

				getContainer: function(c){
					this.con = {con1:false,con2: false};

					if(c == 1){
						this.con = {con1:true,con2: false};
						this.getReferrals();
					} else if (c == 2){
						this.con = {con1:false,con2: true};
					}
				},
				getReferrals: function(){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_member_service.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getReferrals'},
					    success: function(data){
					        self.referrals = data;

					    },
					    error:function(){

					    }
					});
				},
				bookings: function(){
					var self = this;
					$.ajax({
					    url:'../ajax/ajax_member_service.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getBooking',cur_week:self.cur_week},
					    success: function(data){
							self.booking = data.bookings;
							self.dt_range = data.range
					    },
					    error:function(){
					        
					    }
					});

				}
			}
		})
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>