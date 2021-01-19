<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('inventory')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$inv = new Inventory();
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
	$ccc = new Category();
	$noparent = $ccc->getNoparent($user->data()->company_id);
	$categselect = '';
	if($noparent){
		foreach($noparent as $cat){
			$categselect .= "<option value='$cat->id'>$cat->name</option>";
		}
	}
	$user_permbranch = $user->hasPermission('inventory_all');

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Feedbacks </h1>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Logs</div>
							<div class='col-md-6 text-right'>
								</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3"></div>
							<div class="col-md-3"></div>
							<div class="col-md-3"></div>
							<div class="col-md-3">
								<div class="form-group">
									<strong>Status: </strong>
									<select name="status" id="status" class='form-control'>
										<option value="0">Queued</option>
										<option value="1">Processing</option>
										<option value="2">Processed</option>
									</select>
								</div>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
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
	<script>

		$(document).ready(function() {
			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$('body').on('change','#status',function(){
				getPage(0);
			});
			function getPage(p){
				var status = $('#status').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'feedbackPaginate',cid: <?php echo $user->data()->company_id; ?>,status:status},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','.btnProcess',function(){
				var con = $(this);
				var id = con.attr('data-id');
				$.ajax({
				    url:'../ajax/ajax_service.php',
				    type:'POST',
				    data: {functionName:'processesFeedback',id:id},
				    success: function(data){
					    alert(data);
					    getPage(0);
				    },
				    error:function(){
				        
				    }
				});
			});

			$('body').on('change','#branch_id,#category_id,#rack_tag_id',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var s =  $('#supplier_id').val();
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'getBranchRack',branch_id:b},
					success: function(data){
						$('#rack_id').html(data);
					},
					error:function(){

					}
				});
				getPage(0,search,b,r,s);
			});
			$('body').on('change','#rack_id',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var s =  $('#supplier_id').val();
				getPage(0,search,b,r,s);
			});
			$('body').on('change','#supplier_id',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var s =  $('#supplier_id').val();

				getPage(0,search,b,r,s);
			});
			$('body').on('click','#btnDownloadExcel',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var r = $('#rack_id').val();
				var s =  $('#supplier_id').val();
				var txtRack  = $('#txtRack').val();
				var category_id = $("#category_id").val();


				window.open(
					'excel_downloader.php?downloadName=inventories&search='+search+'&b='+b+'&r='+r+'&s='+s+'&txtRack='+txtRack+'&category_id='+category_id,
					'_blank' //
				);
			});

			$('body').on('click','.btnReorderDetails',function(e){
				e.preventDefault();
				var con = $(this);
				var item_id = con.attr('data-item_id');
				var branch_id = con.attr('data-branch_id');
				$('#myModal').modal('show');
				$('#mbody').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_query.php',
					type:'POST',
					data: {functionName:'getReOrderDetails',item_id:item_id,branch_id:branch_id},
					success: function(data){
						$('#mbody').html(data);
					},
					error:function(){

					}
				})
			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>