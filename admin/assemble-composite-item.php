<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$getOrderId = $_GET['order_id'];
	if($getOrderId){
		$getOrderId = (int) $getOrderId;
	} else {
		$getOrderId = 0;
	}


	//  for approval

	// for preparation

	// convert spare to set


?>
	<style>
		@media only screen and (max-width: 800px) {
			#prep_container{
				position:fixed;
				top:0;
				left:0;
				background: #eee;
				z-index: 999;
				height: 100%;
				width: 100%;
				padding: 10px;
				display: none;
				overflow-y: auto;
			}
			#prep_close, #prep_show{
				display: block;
			}
		}
	</style>

	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo Configuration::getValue('assemble'); ?></h1>

		</div> <?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<?php include 'includes/spare_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6"><?php echo Configuration::getValue('assemble'); ?></div>
							<div class="col-md-6 pull-right">
								<button class='btn btn-default visible-xs' id='prep_show'><i class='fa fa-list-alt'></i> Show Prepared Item</button>
							</div>
						</div>




					</div>
					<div class="panel-body">
						<div class="row ">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='SEARCH...' id='txtSearch'>
								</div>
							</div>
							<div class="col-md-4">

							</div>
							<div class="col-md-4 text-right">
								<input type="hidden" id='hid_list'>
							</div>
						</div>
						<div class="row">
							<div class="col-md-7">
								<input type="hidden" id="hiddenpage" />
								<div class="panel panel-default">
									<div class="panel-body">
										<div id="holder"></div>
										<div id="test"></div>
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="panel panel-default">
									<div class="panel-body">
										<div id='prep_container'>
										<button id='prep_close' class='btn btn-default pull-right visible-xs'><i class='fa fa-times'></i> CLOSE</button>
										<div id="conToPrepare" style='display:none;'>
											<div id="invalid-qty-holder">

											</div>
											<div class='form-group'>
												Order ID
												<input type="text" placeholder='Enter Order ID Here' class='form-control' id='txtOrderToAssemble'>
											</div>
											<table id="tblToPrepare" class='table'>
												<thead>
												<tr>
													<th>Item</th>
													<th>Convert Qty</th>
													<th>Spare</th>
													<th></th>

												</tr>
												</thead>
												<tbody></tbody>
											</table>
											<hr>
											<div class="text-right">
												<button id="btnFinalize" class='btn btn-default'>Finalize</button>
											</div>
										</div>
										<div  id="noItemSpList" style="display:none;">
											<br>
											<div  class="alert alert-info">
												No item yet.
											</div>
										</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id='order_items' value=''>
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
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			function clearInvalidQty(){
				$('#invalid-qty-holder').html("");
			}
			localStorage['prepare_sp'] ="";
			spListing();
			$('body').on('click','#prep_close',function(){
				$('#prep_container').hide();
			});
			$('body').on('click','#prep_show',function(){
				$('#prep_container').show();
			});

			function spListing(){
				var splistall = localStorage['prepare_sp'];
				if(splistall){
					try{
						splistall = JSON.parse(splistall);
						var ret = "";
						if(splistall.length > 0){
							for(var i in splistall){
								var arrSpList = JSON.parse(splistall[i].splist);
								var ret2 = "<table class='table'>";
								for(var j in arrSpList){
									var totalneed = arrSpList[j].need_total;
									if(isNumDecimal(arrSpList[j].need_total)){
										totalneed = (arrSpList[j].need_total).toFixed(3);
									}
									ret2 += "<tr><td><strong>"+arrSpList[j].item_code+"</strong><small class='span-block'>"+arrSpList[j].desc+"</small><small class='text-danger span-block'>"+arrSpList[j].typename+"</small></td><td>"+totalneed+"</td></tr>";
								}
								ret2 += "</table>";
								ret += "<tr data-index='"+i+"'><td style='border-top:1px solid #ccc;'>" + splistall[i].item_code + "</td><td style='border-top:1px solid #ccc;'>" + splistall[i].convertQty + "</td><td style='border-top:1px solid #ccc;'>"+ret2+"</td><td  style='border-top:1px solid #ccc;'><button class='btn btn-default removeSp'><span class='glyphicon glyphicon-remove'></span></button></td></tr>";
							}
							$('#tblToPrepare tbody').html(ret);
							$('#noItemSpList').hide();
							$('#conToPrepare').show();
						} else {
							$('#noItemSpList').show();
							$('#conToPrepare').hide();
						}
					} catch(e){
						$('#noItemSpList').show();
						$('#conToPrepare').hide();
					}
				} else {
					$('#noItemSpList').show();
					$('#conToPrepare').hide();
				}
			}
			$('body').on('click','.removeSp',function(e){
				e.preventDefault();
				var row = $(this).parents('tr');
				var i = row.attr('data-index');
				alertify.confirm("Are you sure you want to delete this item?" , function(e){
					if(e){
						var all = JSON.parse(localStorage['prepare_sp']);
						all.splice(i, 1);
						localStorage['prepare_sp'] = JSON.stringify(all);
						row.remove();
						getPage(0,'');
						spListing();
					}
				});
			});

			$('body').on('click','#btnFinalize',function(e){
				e.preventDefault();
				var all = localStorage['prepare_sp'];
				var order_id = $('#txtOrderToAssemble').val();
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.attr('disabled',true);
				btncon.html("Loading...");


				alertify.confirm("Are you sure you want to process this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:"assembleItem", item_list:all,order_id:order_id},
							success: function(data){
								alertify.alert(data);
								localStorage.removeItem('prepare_sp');
								btncon.attr('disabled',false);
								btncon.html(btnoldval);
								spListing();
								getPage(0,'');
							},
							error:function(){
								alert('Something went wrong. The page will be refresh.');
								location.href='assemble-composite.item.php';
								btncon.attr('disabled',false);
								btncon.html(btnoldval);
								spListing();
							}
						});
					} else {
						btncon.attr('disabled',false);
						btncon.html(btnoldval);
						spListing();
					}
				});

			});
			$('body').on('click','.btnAssemble',function(e){
				e.preventDefault();
				var btn = $(this);
				var row = btn.parents('tr');
				var rowID = row.attr('id');
				var item_set = row.attr('data-item_set');
				var item_set_item_code = row.children().eq(0).html();
				var sparelist = row.children().eq(1).find('.tblSpareNeeded');
				var spare_list_id = sparelist.attr('id');

				var td = btn.parents('td');
				var minqty = td.attr('data-min');
				var convertQty = btn.prev().val();
				if(!convertQty || isNaN(convertQty) || parseInt(convertQty) < 1 || parseInt(minqty) < parseInt(convertQty)){
					btn.prev().val('');
					var alerterror = "<div class='alert alert-warning'>No stock(s): "+row.children().eq(0).html()+"</div>";
					$('#invalid-qty-holder').append(alerterror);
					tempToast('error',"<p>Invalid qty'</p>","<h4>Information!</h4>");

					return;
				}
				var thisList;
				var splist = [];
				var isValid = true;
				$('#'+spare_list_id + " tbody tr").each(function(){
					var sprow = $(this);
					var raw_id = sprow.attr('data-id');
					var raw_desc = sprow.attr('data-desc');
					var raw_item_code = sprow.attr('data-item_code');
					var raw_need= sprow.attr('data-need');
					var cur_inv = sprow.attr('data-stock');
					var typename = sprow.attr('data-typename');
					var consume = sprow.children().eq(3).find('input').val();
					consume = (consume) ? consume : 0;
					var orig_needed = raw_need;
					var raw_need_total = 0;
					if(consume){
						raw_need = consume;
						raw_need_total = parseFloat(raw_need);
					} else {
						raw_need_total = parseFloat(raw_need) * parseFloat(convertQty);
					}

				    splist.push({id:raw_id,item_code:raw_item_code,desc:raw_desc,need:raw_need,orig_needed:orig_needed,need_total:raw_need_total,consume:consume,typename:typename});
					sprow.children().eq(3).find('input').val('');
				});
				thisList = {item_set : item_set,item_code:item_set_item_code,convertQty:convertQty, splist:JSON.stringify(splist)};
				if(!isValid){
					tempToast('error',"<p>Not enough spare parts to assemble the item</p>","<h4>Information!</h4>");
					return;
				}

				var allitem = localStorage['prepare_sp'];
				if(!allitem){

					tempToast('info',"<p>Added on list</p>","<h4>Information!</h4>");
					btn.prev().val('');
					localStorage['prepare_sp'] = JSON.stringify([thisList]);
					console.log(1);
					updateCurrentRowSP(spare_list_id,convertQty,rowID);
					spListing();
				} else {
					try{
						allitem = JSON.parse(allitem);
						var exists = false;
						for(var i in allitem){
							if(allitem[i].item_set ==item_set ){
								exists = true;
							}
						}
						if(!exists){
							allitem.push(thisList);
							localStorage['prepare_sp'] = JSON.stringify(allitem);
							tempToast('info',"<p>Added on list</p>","<h4>Information!</h4>");
							btn.prev().val('');
							console.log(2);
							updateCurrentRowSP(spare_list_id,convertQty,rowID);

							spListing();
						} else {
							tempToast('error',"<p>Item already on the list</p>","<h4>Information!</h4>");
							btn.prev().val('');
						}

					} catch(e){

					}
				}
			});
			function updateCurrentRowSP(spare_list_id,convertQty,rowID){
				var row = $('#'+rowID);
				var curqty = row.children().eq(2).text();
				row.children().eq(2).text(parseFloat(curqty) - parseFloat(convertQty));
				$('#'+spare_list_id + " tbody tr").each(function(){
					var sprow = $(this);
					var raw_id = sprow.attr('data-id');
					var raw_desc = sprow.attr('data-desc');
					var raw_need= sprow.attr('data-need');
					var cur_inv = sprow.attr('data-stock');
					var consume = sprow.children().eq(3).find('input').val();
					var unit_name = sprow.attr('data-unit');
					consume = (consume) ? consume : 0;
					var orig_needed = raw_need;
					if(consume){
						raw_need = consume;
					}
					var raw_need_total = parseFloat(raw_need) * parseFloat(convertQty);
					cur_inv = parseFloat(cur_inv) - parseFloat(raw_need_total);
					sprow.attr('data-stock',cur_inv);
					if(isNumDecimal(cur_inv)){
						sprow.children().eq(2).html("<span class='text-danger' style='font-weight:bold;'>" + cur_inv.toFixed(3)+ "</span><small class='span-block'>"+unit_name+"</small>");
					} else {
						sprow.children().eq(2).html("<span class='text-danger' style='font-weight:bold;'>" + cur_inv + "</span><small class='span-block'>"+unit_name+"</small>");
					}

					sprow.children().eq(3).find('input').val('');
				});
			}
			function isNumDecimal(num){
				if(parseFloat(num) % 1 != 0){
					return true;
				} else {
					return false;
				}
			}
			getPage(0,'');
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page,'');
			});
			var timersearch;
			$('body').on('keyup','#txtSearch',function(){
				clearTimeout(timersearch);
				timersearch = setTimeout(function() {
					getPage(0,'');
				}, 1000);
			});

			$('body').on('click','.btnAssembleOrder',function(e){
				e.preventDefault();
				var btn = $(this);
				var id = btn.attr('data-id');
				var items = btn.attr('data-items');
				$('#txtOrderToAssemble').val(id);
				getPage(0,items);
			});
			function getPage(p,toAssemble){
				$('.loading').show();
				var splistall = localStorage['prepare_sp'];
				var search = $('#txtSearch').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend: function(){
						$('#holder').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
					},
					data:{page:p,curOnList:splistall,toAssemble:toAssemble,search:search,functionName:'itemWithSpareList',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){

						$('#holder').html(data);

						if(toAssemble){
							clearInvalidQty();
							localStorage['prepare_sp'] ="";
							$('.btnAssemble').click();

						}

						if(localStorage['get_order_id_assemble']){
							var local_id = localStorage['get_order_id_assemble'];
							localStorage.removeItem('get_order_id_assemble');
							var btn = $('#forAssemble' + local_id);
							$('#txtOrderToAssemble').val(local_id);
							var id = btn.attr('data-id');
							var items = btn.attr('data-items');
							getPage(0,items);
						}

						if(localStorage['get_order_id_assemble_from_releasing']){
							var items = localStorage['get_order_id_assemble_from_releasing'];
							localStorage.removeItem('get_order_id_assemble_from_releasing');
							getPage(0,items);
						}

						$('.loading').hide();

					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='assemble-composite.item.php';
						$('.loading').hide();
					}
				});
			}
		});
		function getPendingQty(item_raw){
			var all = localStorage['prepare_sp'];
			try{
				var allocated = 0;
				all = JSON.parse(all);
				for(var i in all){
					var splist = all[i].splist;
					splist = JSON.parse(splist);
					for(var j in splist){
						if(splist[j].id == item_raw){
							allocated = parseFloat(allocated) + parseFloat(splist[j].need_total);
						}
					}
				}
				console.log(allocated);
				return allocated;
			} catch(e){
				return 0;
			}
		}

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>