<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('pettycash')){
		// redirect to denied page
		Redirect::to(1);
	}

?>

	<style>
		#page{
			padding-top: 13px;
			width: 816px;
			height: 1056px;
			border: 1px dotted #ccc;
			position: relative;
		}

		.cols{

			border-bottom: 1px solid #ccc;
			padding:0;
			margin: 0;
			height: 19px;
			background: red;
			padding: 0px;
			position:relative;
			display:inline-block;
			font-size: 11px;
			line-height: 18px;

		}
		.input-col{
			position:absolute;
			right:-200px;
			height: 18px;
			line-height: 17px;
			font-size:16px;
		}
		.rows{
			line-height: 18px;
			height:19px;

			padding:0px !important;
			margin:0px !important;

		}
	</style>

	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div id='pdf' class="page-content inset">
		<div class="content-header">
			<input type="text" v-model="cur_row.contents[cur_index]"><button @click="saveValues">save</button>
			<div id="page">




				<div class="rows" v-for="row,row_index in computed_rows">
					<span
						class='cols'
						v-for="n,index in getCols(row)"
						v-bind:style="{ width: (81 * n)  + 'px'}">
						<span @click="updateValues(row,index,row_index)">{{row.contents[index]}}</span>
					</span>
					<input type="text" v-model="row.values" class='input-col'>
				</div>



			</div>
			<br>
		</div>
	</div> <!-- end page content wrapper-->
	</div>
	<script src="../js/vue3.js"></script>
	<script>
		var vm = new Vue({
			el: "#pdf",
			data: {
				rows:[
				],
				cur_index:0,
				cur_rowindex:0,
				cur_values:"",
				cur_row:{contents:[]}
			},
			mounted: function(){
				var rows = this.rows;
				for(var i = 1; i <= 54; i++){
					rows.push({values:"10",contents:["Value"]});
				}
			},
			computed: {
				computed_rows: function(){
					var self = this;
					var rows = self.rows;
					var arr = [];
					for(var i in rows){
						arr.push(rows[i]);
					}
					return arr;
				}
			},
			methods:{
				getCols: function(row){

					var s = row.values.split(',');
					var count = s.length;
					row.contents = [];

					for(var i=1;i<= count; i++){
						row.contents.push("value" + i);
					}

					return s;

				},
				updateValues:function(row,index,row_index){
					var self = this;
					self.cur_row = row;
					self.cur_index = index;
					self.cur_rowindex = row_index;
				},
				saveValues: function(){
					var self = this;
					self.rows[self.cur_rowindex]['contents'][self.cur_index] = self.cur_row.contents[self.cur_index];
				},
			}
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>