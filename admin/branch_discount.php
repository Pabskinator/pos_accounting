<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

?>
	<div id='app'>
	<!-- Page content -->
		<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					{{ title }}
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
								<button class='btn btn-default' title='List' v-on:click="showList"> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'> List</span></button>
								<button class='btn btn-default' title='Add' v-on:click="showAdd"> <span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'> Add</span></button>
							</div>
							<branch-discount-list v-bind:list="branch_discounts" v-show="page.list"></branch-discount-list>
							<branch-discount-add v-show="page.add"></branch-discount-add>
						</div>
					</div>

				</div>
				</div>
			</div> <!-- end page content wrapper-->
		</div>
	</div>
	<template id='branch-discount-add'>
		<form class="form-horizontal" action="" method="POST">
			<fieldset>
				<legend class='text-center'>Discount Information</legend>
				{{ request | json }}
				<div class="form-group">
					<label class="col-md-4 control-label" for="name">Branch</label>
					<div class="col-md-4">
						<input id='branch_id_req' placeholder="Brand Name" class="form-control input-md" type="text" v-model="request.branch_id_req">
						<span class="help-block">Enter branch name</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Order To</label>
					<div class="col-md-4">
						<input id='branch_id_src' placeholder="Order to" class="form-control input-md" type="text" v-model="request.branch_id_src">
						<span class="help-block">Enter branch name</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label" >Discount</label>
					<div class="col-md-4">
						<input placeholder="Discount" class="form-control input-md" type="text" v-model="request.discount">
						<span class="help-block">Enter discount in percentage</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label" for="button1id"></label>
					<div class="col-md-8">
						<button class='btn btn-default' @click.prevent="$parent.addNewDiscount(request)">Submit</button>
					</div>
				</div>
			</fieldset>
		</form>
	</template>
	<template id="branch-discount-list">
		<div>
		<table class='table' v-show="list.length">
			<thead>
				<tr>
					<th>Branch</th>
					<th>Order To</th>
					<th>Discount</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="item in list">
					<td>{{ item.branch_req }}</td>
					<td>{{ item.branch_src }}</td>
					<td class='text-danger'>{{ item.discount }} %</td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<div v-show="!list.length">
			<div class="alert alert-info">No record found.</div>
		</div>
		</div>
	</template>

	<script src='../js/vue-2.js'></script>
	<script>

		Vue.component('branch-discount-add', {
			data:function () {
				return {
					request : {branch_id_req: '', branch_id_src:'', discount:0 }
				}
			},
			beforeCreate: function (){
				var co = this;
				co.$parent.$on('branch_id_req-selected',function(id){
					co.request.branch_id_req = id;
				});
				co.$parent.$on('branch_id_src-selected',function(id){
					co.request.branch_id_src = id;
				});
				co.$parent.$on('branch_discount-added',function(){
					co.request = {branch_id_req: '', branch_id_src:'', discount:0 };
					$('#branch_id_req').select2('val',null);
					$('#branch_id_src').select2('val',null);
				});
			},
			template: '#branch-discount-add',
			methods:{

			}
		});
		Vue.component('branch-discount-list', {
			props: ['list'],
			data:function () {
				return {child_title: 'test sdf'}
			},
			template: '#branch-discount-list',
			methods:{
				alertMe: function(){
					alert(1);
					this.$parent.title = this.child_title;
				}
			}
		});
		var vm = new Vue({
			el:'#app',
			data: {
				title:'Branch Discounts',
				branch_discounts: [],
				page: {list:false,add:false}
			},
			created: function(){
				this.fetchBranchDiscount();
				this.showList();

			},
			methods:{
				alertFromParent : function(s){
					alert(s);
				},
				fetchBranchDiscount : function () {
					var vuecon = this;
					$.ajax({
					    url:'../ajax/ajax_branch_discount.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'getList'},
					    success: function(data){
						    vuecon.branch_discounts = data;
					    },
					    error:function(){

					    }
					})
				},
				hideComponents: function(){
					this.page.list = false;
					this.page.add = false;
				},
				showList: function(){
					this.hideComponents();
					this.page.list = true;
				},
				showAdd: function(){
					this.hideComponents();
					this.page.add = true;
				},
				addNewDiscount : function (request){
					var vuecon = this;
					$.ajax({
					    url:'../ajax/ajax_branch_discount.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'insertDiscount',request:JSON.stringify(request)},
					    success: function(data){
						    vm.$emit('branch_discount-added', 1);
					        if(data.success == true){
						        tempToast('info',"<p>Action completed successfully.</p>","<h3>Info</h3>");
					        } else {
						        tempToast('error',"<p>Invalid request</p>","<h3>Error</h3>");
					        }
					    },
					    error:function(){

					    }
					})
				}
			}
		});
		(function(vm){
			$('#branch_id_req').select2({
				placeholder: 'Search branch', allowClear: true, minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'branches'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.name,
									slug: item.name,
									id: item.id
								}
							})
						};
					}
				}
			});
			$('#branch_id_src').select2({
				placeholder: 'Search branch', allowClear: true, minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'branches'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.name,
									slug: item.name,
									id: item.id
								}
							})
						};
					}
				}
			});

			$('#branch_id_req').change(function(){
				vm.$emit('branch_id_req-selected', $(this).val());
			});
			$('#branch_id_src').change(function(){
				vm.$emit('branch_id_src-selected', $(this).val());
			});
		})(vm);
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>