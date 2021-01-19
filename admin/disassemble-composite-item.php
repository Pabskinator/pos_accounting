<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')) {
		// redirect to denied page
		Redirect::to(1);
	}

	// select distinct item

	// get spare of item

	// get inventory of spare

	// calculate kung ilan mabubuo

	// choose kung ilan aayusin send request

	//  for approval

	// for preparation

	// convert spare to set


?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo Configuration::getValue('disassemble') ?> </h1>

		</div>
		<?php
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
					<div class="panel-heading">DISASSEMBLE</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<input type="text" class='form-control' placeholder='SEARCH...' id='txtSearch'>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class='form-control' id='branch_id'>
								</div>
							</div>
							<div class="col-md-4 text-right">

							</div>
						</div>
						<br>

						<div class="row">
							<div class="col-md-7">
								<div class="panel panel-default">
									<div class="panel-body">
										<input type="hidden" id="hiddenpage" />
										<div id="holder"></div>
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="panel panel-default">
									<div class="panel-body">
										<div id="conToPrepare" style='display:none;'>
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
	<!-- end page content wrapper-->

	<script>
		$(function(){
				spListing();
				function spListing(){
					var splistall = localStorage['prepare_sp_dis'];
					if(splistall){
						try{
							splistall = JSON.parse(splistall);
							var ret = "";
							if(splistall.length > 0){
								for(var i in splistall){
									var arrSpList = JSON.parse(splistall[i].splist);
									var ret2 = "<table class='table'>";
									for(var j in arrSpList){
										ret2 += "<tr><td>"+arrSpList[j].desc+"</td><td>"+arrSpList[j].need_total+"</td></tr>";
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
				$('body').on('click','.btnDis',function(){
					var btn = $(this);
					var row = btn.parents('tr');
					var item_set_item_code = row.children().eq(0).html();
					var item_set = row.attr('data-item_set');
					var qty = parseFloat(row.attr('data-qty'));
					var convertQty = parseFloat(btn.prev().val());
					if(convertQty > qty){
						alertify.alert('Invalid qty');
						btn.prev().val('');
						return;
					}
					var thisList;
					var splist = [];

					var sparelist = row.children().eq(1).find('.tblSpareNeeded');
					var spare_list_id = sparelist.attr('id');
					$('#'+spare_list_id + " tbody tr").each(function(){
						var sprow = $(this);
						var raw_id = sprow.attr('data-id');
						var raw_desc = sprow.attr('data-desc');
						var raw_need= sprow.attr('data-need');
						var raw_need_total = parseFloat(raw_need) * parseFloat(convertQty);

						splist.push({id:raw_id,desc:raw_desc,need:raw_need,need_total:raw_need_total});
					});
					thisList = {item_set : item_set,item_code:item_set_item_code,convertQty:convertQty, splist:JSON.stringify(splist)};
					console.log(thisList);

					var allitem = localStorage['prepare_sp_dis'];
					if(!allitem){
						tempToast('info',"<p>Added on list</p>","<h4>Information!</h4>");
						btn.prev().val('');
						localStorage['prepare_sp_dis'] = JSON.stringify([thisList]);
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
								localStorage['prepare_sp_dis'] = JSON.stringify(allitem);
								tempToast('info',"<p>Added on list</p>","<h4>Information!</h4>");
								btn.prev().val('');
								getPage(0);
								spListing();
							} else {
								tempToast('error',"<p>Item already on the list</p>","<h4>Information!</h4>");
								btn.prev().val('');
							}

						} catch(e){

						}
					}

				});
				$('body').on('click','.removeSp',function(){
					var row = $(this).parents('tr');
					var i = row.attr('data-index');
					alertify.confirm("Are you sure you want to delete this item?" , function(e){
						if(e){
							var all = JSON.parse(localStorage['prepare_sp_dis']);
							all.splice(i, 1);
							localStorage['prepare_sp_dis'] = JSON.stringify(all);
							row.remove();
							getPage(0);
							spListing();
						}
					});
				});
			$('body').on('click','#btnFinalize',function(){
				var all = localStorage['prepare_sp_dis'];
				var btncon = $(this);
				var btnoldval = btncon.html();
				var branch_id = $('#branch_id').val();
				btncon.attr('disabled',true);
				btncon.html("Loading...");
				alertify.confirm("Are you sure you want to process this request?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:"disassembleItem",branch_id:branch_id, item_list:all},
							success: function(data){
								alertify.alert(data);
								localStorage.removeItem('prepare_sp_dis');
								btncon.attr('disabled',false);
								btncon.html(btnoldval);
								spListing();
								getPage(0);
							},
							error:function(){
								alert('Something went wrong. The page will be refresh.');
								location.href='disassemble-composite.item.php';
								btncon.attr('disabled',false);
								btncon.html(btnoldval);

							}
						});
					} else {
						btncon.attr('disabled',false);
						btncon.html(btnoldval);
						spListing();
					}
				});
			});



				getPage(0);
				$('body').on('click','.paging',function(){
					var page = $(this).attr('page');
					$('#hiddenpage').val(page);
					getPage(page);
				});

				$('body').on('keyup','#txtSearch',function(){
					getPage(0);
				});
				

				$('#branch_id').select2({
					placeholder: 'Branch',
					allowClear: true,
					minimumInputLength: 2,
					ajax: {
						url: '../ajax/ajax_json.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								q: term,
								functionName:'branches'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.name ,
										slug: item.name ,
										id: item.id
									}
								})
							};
						}
					}
				});
				$('body').on('change','#branch_id',function(){
					getPage(0);
				});

				function getPage(p){
					$('.loading').show();
					var search = $('#txtSearch').val();
					var branch_id = $('#branch_id').val();
					$.ajax({
						url: '../ajax/ajax_paging.php',
						type:'post',
						beforeSend: function(){
							$('#holder').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
						},
						data:{page:p,search:search,branch_id:branch_id,functionName:'itemWithSpareListDis',cid: <?php echo $user->data()->company_id; ?>},
						success: function(data){
							$('#holder').html(data);
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
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>