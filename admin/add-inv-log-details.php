<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}

	/*function get_nested($array,$child = FALSE,$iischild=''){

		$str = '';

		if (count($array)){
			$iischild .= $child == FALSE ? '' : '-';

			foreach ($array as $item){


				if(isset($item['children']) && count($item['children'])){

					$str .= '<option value="'.$item['id'].'">'.$iischild.$item['name'].'</option>';
					$str .= get_nested($item['children'], true, $iischild);
				} else {
					if($child == false) $iischild='';
					$str .= '<option value="'.$item['id'].'">'.$iischild.($item['name']).'</option>';
				}

			}
		}

		return $str;
	}

	function objectToArray ($object) {
		if(!is_object($object) && !is_array($object))
			return $object;

		return array_map('objectToArray', (array) $object);
	}
	function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'children') {
		$m = array();
		foreach ($d as $e) {
			isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
			isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
			$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
		}
		return $m[$r]; // remove [0] if there could be more than one root nodes
	}
	$ccc = new Category();
	$cc = objectToArray($ccc->getCategory($user->data()->company_id));
	*/

	$branch_cls = new Branch();
	$branch_list = $branch_cls->get_active('branches',[1,'=',1]);

	$ccc = new Category();
	$noparent = $ccc->getNoparent($user->data()->company_id);
	$categselect = '';
	if($noparent){
		foreach($noparent as $cat){
			$categselect .= "<option value='$cat->id'>$cat->name</option>";
		}
	}



?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Log  </h1>

		</div>
		<?php
			// get flash message if add or edited successfully

			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}


		?>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/inventory_nav.php'; ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Record</div>
							<div class='col-md-6 text-right'>
								<button class='btn btn-default btn-sm' id='btnDownload'><i class='fa fa-download'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12 text-right">
								<a href="add-inv-log.php" class='btn btn-default btn-sm'>Per Transaction</a>
								<a href="add-inv-log-details.php"  class='btn btn-default btn-sm'>Per Item</a>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
								<br> <input type="text" class='form-control selectitem'  id='item_id' >
									</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<strong>Filter Branch</strong>
								<select class='form-control' name="branch_id" id="branch_id">
									<option value="">All</option>
									<?php if($branch_list){
										foreach($branch_list as $bl){
											echo "<option value='$bl->id'>$bl->name</option>";
										}
									}
									?>
								</select>
									</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<strong>Filter Date</strong>
								<input type="text" class='form-control' id='dt_from' placeholder="Date From">
									</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<br>
								<input type="text" class='form-control' id='dt_to' placeholder="Date To">
							</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="category_id" name="category_id" class="form-control ">
										<option value=""></option>
										<?php echo $categselect; ?>
									</select>
								</div>
							</div>
						</div>
						<br>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
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
	<script src="../js/vue.js"></script>
	<script>

		$(document).ready(function() {
			$("#category_id").select2({
				placeholder: 'Choose Category',
				allowClear: true
			});

			$("#category_id").change(function(){
				getPage(0);
			});

			$('#item_id').change(function(){
				getPage(0);
			})

			getPage(0);

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#branch_id',function(){
				getPage(0);
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				getPage(0);
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				getPage(0);
			});

			$('body').on('click','#btnDownload',function(){

				var branch_id = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var item_id = $('#item_id').val();
				var category_id = $('#category_id').val();

				if(dt_from && !dt_to){
					return;
				}
				if(!dt_from && dt_to){
					return;
				}



				window.open(
					'excel_downloader_2.php?downloadName=invlog&branch_id='+branch_id+'&dt_from='+dt_from+'&dt_to='+dt_to+'&item_id='+item_id+'&category_id='+category_id,
					'_blank' //
				);

			});

			function getPage(p){

				var branch_id = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var item_id = $('#item_id').val();
				var category_id = $('#category_id').val();

				if(dt_from && !dt_to){
					return;
				}
				if(!dt_from && dt_to){
					return;
				}

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{branch_id:branch_id,category_id:category_id,dt_from:dt_from,item_id:item_id,dt_to:dt_to,page:p,functionName:'inventoryLogDetailsPaginate',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>