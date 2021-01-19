<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('sales')){
		// redirect to denied page
		Redirect::to(1);
	}

?>




	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.3.0/gridstack.min.css" />

	<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'></script>
	<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js'></script>
	<script type="text/javascript" src='//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.3.0/gridstack.min.js'></script>
	<script type="text/javascript" src='//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.3.0/gridstack.jQueryUI.min.js'></script>
	<style>
		.grid-stack-item{

		}
		.grid-stack-item-content {

			border:1px solid #ccc;
		}
		.table{

			background:url('../css/img/table.svg') no-repeat center center;

			/* Ensure the html element always takes up the full height of the browser window */
			min-height:100%;

			/* The Magic */
			background-size:contain;
		}
		.bar{

			background:url('../css/img/bar.svg') no-repeat center center;

			/* Ensure the html element always takes up the full height of the browser window */
			min-height:100%;

			/* The Magic */
			background-size:contain;
		}
		.donut{

			background:url('../css/img/donut.svg') no-repeat center center;

			/* Ensure the html element always takes up the full height of the browser window */
			min-height:100%;

			/* The Magic */
			background-size:contain;
		}
	</style>
	<div id="page-content-wrapper">



	<div class="page-content inset">
		<div>
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<select name="type" id="type" v-model="form.type"  class='form-control'>
							<option value="donut">Donut</option>
							<option value="bar">Bar</option>
							<option value="table">Table</option>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<input type="text" class='form-control' placeholder='Name' v-model='form.name'>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<input type="text" class='form-control' placeholder='Width' v-model='form.width'>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<input type="text" class='form-control'placeholder='Height' v-model='form.height'>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<input type="text" class='form-control' placeholder='X' v-model='form.x'>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<input type="text" class='form-control' placeholder='Y' v-model='form.y'>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<button @click="save">Save</button>
					</div>
				</div>
			</div>

			<div class="grid-stack">


			</div>
		</div>
	</div>
	</div> <!-- end page content wrapper-->
	<script src='../js/vue3.js'></script>
	<script>
		var vm = new Vue({
			el:'#page-content-wrapper',
			data:{
				grid_items:[],
				form: {x:0,y:0,width:'',height:'',type:'donut',name:''},
				grid: null,
				options:{
					cellHeight: 100,
					verticalMargin: 10
				}
			},
			mounted: function(){
				var self = this;

				self.grid = $('.grid-stack');
				self.grid.gridstack(self.options);
				let grid = self.grid.data('gridstack');
				grid.addWidget('<div class="grid-stack-item donut" data-gs-width="2" data-gs-height="1" name=Test"><div class="grid-stack-item-content"><p>HTML (added)</p></div> </div>', 0, 0, 1, 1, true);
				grid.addWidget('<div class="grid-stack-item table" data-gs-width="2" data-gs-height="1" name=Test"><div class="grid-stack-item-content"><p>HTML (added)</p></div> </div>', 0, 0, 1, 1, true);
				grid.addWidget('<div class="grid-stack-item bar" data-gs-width="2" data-gs-height="1" name=Test"><div class="grid-stack-item-content"><p>HTML (added)</p></div> </div>', 0, 0, 1, 1, true);




			},
			methods:{
				toJSON: function(s){
					var arr =[];
					$(s +  " .grid-stack-item").each(function(){
						var g = $(this);
						arr.push({
							x: g.attr('data-gs-x'),
							y: g.attr('data-gs-y'),
							width: g.attr('data-gs-width'),
							height: g.attr('data-gs-height'),
							name: g.attr('data-name')
						})
					});
					return JSON.stringify(arr);
				},
				save: function(){
					this.grid_items.push(this.form);
					var self = this;
					let grid = self.grid.data('gridstack');
					grid.addWidget('<div class="grid-stack-item donut" data-gs-width="'+this.form.width+'" data-gs-height="' +this.form.height+'" name=Test"><div class="grid-stack-item-content"><p>HTML (added)</p></div> </div>', 0, 0, 1, 1, true);

					this.form = {x:0,y:0,width:'',height:'',type:'donut',name:''};
					console.log(this.toJSON('.grid-stack'));
				}
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>