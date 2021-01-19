<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<div id='member_util' >
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

					<?php include_once "includes/assessment_nav.php" ?> <br>
					<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' @click="getContainer(1)" title='Client Session'>
							<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Client Sessions</span></span></a>
						<a class='btn btn-default' @click="getContainer(2)"  title='To assess'>
							<span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>To assess</span></span></a>
						<a class='btn btn-default' @click="getContainer(3)"  title='Referrals'>
							<span class='glyphicon glyphicon-book'></span> <span class='hidden-xs'>Referrals</span></span></a>
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
							<table class="table">
								<thead>
								<tr>
									<th>Member</th>
									<th>Item Consumable</th>
									<th>Total Session</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="m in member_sessions">
									<td style='border-top: 1px solid #ccc;'>{{ m.member_name}} </td>
									<td style='border-top: 1px solid #ccc;'>{{ m.item_code}} </td>
									<td style='border-top: 1px solid #ccc;'>{{ m.total}} </td>
									<td style='border-top: 1px solid #ccc;' class='text-danger'> {{ m.lbl}} </td>
								</tr>
								</tbody>
							</table>
						</div>
						<div v-show="con.con2">
							<table class="table">
								<thead>
								<tr>
									<th>Member</th>
									<th>Item Consumable</th>
									<th>Total Session</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<tr v-for="m in forAssessment">
									<td style='border-top: 1px solid #ccc;'>{{ m.member_name}} </td>
									<td style='border-top: 1px solid #ccc;'>{{ m.item_code}} </td>
									<td style='border-top: 1px solid #ccc;'>{{ m.total}} </td>
									<td style='border-top: 1px solid #ccc;' class='text-danger'>
										<a class='btn btn-primary' v-bind:href="m.url"><i class='fa fa-pencil'></i></a>
									</td>
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
				el:'#member_util',
				data: {
					con : {con1:true,con2:false},
					member_sessions:[],
					filters: {
						toAssessFil: function(d) {
							return d.toAssess == 1;
						}
					}
				},
				computed:{
					forAssessment: function(){
						return  this.member_sessions.filter(this.filters.toAssessFil);
					}
				},
				mounted: function(){
					var vm = this;
					vm.getSessions();
				},
				methods: {
					getContainer: function(c){
						this.con = {con1:false,con2: false};

						if(c == 1){
							this.con = {con1:true,con2: false};
						} else if (c == 2){
							this.con = {con1:false,con2: true};
						}
					},
					getSessions: function(){
						var vm = this;
						$.ajax({
						    url:'../ajax/ajax_member_service.php',
						    type:'POST',
							dataType: 'json',
						    data: {functionName:'getConsumablesV2'},
						    success: function(data){
						        vm.member_sessions = data;
						    },
						    error:function(){

						    }
						});
					}
				}
			})
		</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>