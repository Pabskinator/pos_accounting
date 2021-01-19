<?php


	require_once '../includes/admin/page_head2.php';


?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Member Discount Base On Category</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>list</div>
							<div class='col-md-6 text-right'>
								<button id='addItem' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></button>
								<button id='btnDownload' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">


						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="search" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>

						</div>



						<input type="hidden" id="hiddenpage" value='0'/>
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
					<input type="hidden" id='request_id' value='0'>
					<div class="form-group">
						<strong>Member:</strong>
						<input type="text" class='form-control' id='member_id' >
					</div>
					<div class="form-group">
						<strong>Category:</strong>
						<?php

							$ccc = new Category();
							$cc = objectToArray($ccc->getCategory($user->data()->company_id));
							$array = array();

							$_SESSION['test'] =[];
							function get_nested($array,$child = FALSE,$iischild='',$selectedid=0){

								$str = '';
								$mycateg = new Category();
								$thisuser = new User();
								if (count($array)){
									$iischild .= $child == FALSE ? '' : ' --> ';

									foreach ($array as $item){
										$haschild = $mycateg->hasChild($thisuser->data()->company_id,$item['id']);
										$disabledme='';
										if($haschild){
											$disabledme = ''; // disabled
										}
										if($selectedid){
											if($selectedid == $item['id']){
												$selected = 'selected';
												$selectedid=0;
											}
										} else {
											$selected = '';
										}


										if(isset($item['children']) && count($item['children'])){
											//$_SESSION['test'][$iischild.$item['name']] = $item['id'];
											$str .= '<option value="'.$item['id'].'" '.$disabledme.' '.$selected.'>'.$iischild.$item['name'].'</option>';
											$str .= get_nested($item['children'], true, $iischild . $item['name'],$selectedid);

										} else {
											if($child == false) $iischild='';
											//	$_SESSION['test'][$iischild.$item['name']] = $item['id'];
											$str .= '<option value="'.$item['id'].'" '.$disabledme.' '.$selected.'>'.$iischild.($item['name']).'</option>';
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

						?>
						<select id="category_id" name="category_id" class="form-control hasChild" >
							<option value=""></option>
							<?php
								if(isset($id)){
									echo get_nested(makeRecursive($cc), FALSE,'',$editProd->data()->category_id);
								} else {
									echo get_nested(makeRecursive($cc));
								}
							?>


						</select>
					</div>
					<div class="form-group">
						<strong>Discount 1:</strong>
						<input type="text" class='form-control' id='discount_1' placeholder='Discount 1'>
					</div>
					<div class="form-group">
						<strong>Discount 2:</strong>
						<input type="text" class='form-control' id='discount_2' placeholder='Discount 2'>
					</div>
					<div class="form-group">
						<strong>Discount 3:</strong>
						<input type="text" class='form-control' id='discount_3' placeholder='Discount 3'>
					</div>
					<div class="form-group">
						<strong>Discount 4:</strong>
						<input type="text" class='form-control' id='discount_4' placeholder='Discount 4'>
					</div>

					<div class="form-group">
						<button class='btn btn-default' id='btnSubmit'>Submit</button>
					</div>

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

			var timer;

			$("#search").keyup(function(){
				var searchtxt = $("#search");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});

			function getPage(p){

				var search = $('#search').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'memberCategoryPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});

			}

			$('body').on('click','#addItem',function(){

				$('#member_id').select2('val',null);
				$('#category_id').val('');
				$('#discount_1').val('');
				$('#discount_2').val('');
				$('#discount_3').val('');
				$('#discount_4').val('');
				$('#request_id').val('0');

				$('#myModal').modal('show');
			});

			$('body').on('click','#btnSubmit',function(){

				var member_id  = $('#member_id').val();
				var request_id  = $('#request_id').val();
				var category_id  = $('#category_id').val();
				var discount_1  = $('#discount_1').val();
				var discount_2  = $('#discount_2').val();
				var discount_3  = $('#discount_3').val();
				var discount_4  = $('#discount_4').val();

				if(member_id && category_id && discount_1){
					$.ajax({
						url:'../ajax/ajax_service_item.php',
						type:'POST',
						data: {functionName:'addMemberDiscountCategory',request_id:request_id,member_id:member_id,category_id:category_id,discount_1:discount_1,discount_2:discount_2,discount_3:discount_3,discount_4:discount_4},
						success: function(data){
							if(data == '1'){
								tempToast('info','Added successfully','Info');
								$('#myModal').modal('hide');
								getPage($('#hiddenpage').val());
							} else {
								tempToast('error','Invalid request.','Error');
							}
						},
						error:function(){

						}
					});
				} else {
					tempToast('error','Invalid request.','Error');
				}

			});
			
			$('body').on('click','#btnDownload',function(){
				var search = $('#search').val();
				window.open(
					'excel_downloader_2.php?downloadName=byCategoryDiscount&search='+search,
					'_blank' // <- This is what makes it open in a new window.
				);
			});


			$('body').on('click','.btnUpdate',function(){

				var con = $(this);
				var info = con.attr('data-info');
				info = JSON.parse(info);
				$('#member_id').select2('data',{id:info.member_id, text:info.member_name});
				$('#category_id').val(info.category_id);
				$('#discount_1').val(info.discount_1);
				$('#discount_2').val(info.discount_2);
				$('#discount_3').val(info.discount_3);
				$('#discount_4').val(info.discount_4);
				$('#request_id').val(info.id);
				$('#myModal').modal('show');

			});

			$('#member_id').select2({
				placeholder: 'Search Client', allowClear: true, minimumInputLength: 2,

				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>