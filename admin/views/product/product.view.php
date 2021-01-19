<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage Product
			</h1>
		</div>
		<?php
			// get flash message if add or edited successfully
			$defpage = 0;
			$targetid = '';
			$prev_search = '';
			$prev_categ = '';

			if(Session::exists('productflash')) {
				$flashlbl = Session::flash('productflash');

				if(strpos($flashlbl,'|')){
					$flashexplode =explode("|",$flashlbl);
					$flbl = $flashexplode[0];
					$defpage = $flashexplode[1];
					$targetid = $flashexplode[2];
					$prev_search = $flashexplode[3];
					$prev_categ = $flashexplode[4];

				} else {
					$flbl = $flashlbl;
				}

				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" .$flbl . "</div>";
			}

		?>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/product_nav.php'; ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Products</div>
							<div class='col-md-6 text-right'>
								<?php if($user->hasPermission('dl_prod')){ ?>
								<button id='btnDownloadExcel' title='Download Excel' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input value="<?php echo $prev_search; ?>" type="text" id="searchSales" class='form-control' placeholder='Search..' />
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select class='form-control' name="limit_by" id="limit_by">
										<option value="">Select Limit</option>
										<option value="50">50</option>
										<option value="100">100</option>
										<option value="500">500</option>
										<option value="1000">1000</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="category_id" name="category_id" class="form-control ">
										<option value=""></option>
										<?php echo get_nested(makeRecursive($cc),false,'',$prev_categ); ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
							    <div class="form-group">
							        <input type="text" id='dt_from' name='dt_from'  placeholder='Date From' class='form-control'>
							    </div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='dt_to' name='dt_to'  placeholder='Date To' class='form-control'>
								</div>
							</div>
						</div>


						<input type="hidden" id='sort_by' /> <input type="hidden" id='ascdesc' value='1' />
						<input value='<?php echo $defpage; ?>' type="hidden" id="hiddenpage" />

						<div id="holder"></div>
					</div>

				</div>

			</div>
		</div>
	</div>
</div> <!-- end page content wrapper-->


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" style=''>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id='mtitle'></h4>
			</div>
			<div class="modal-body" id='mbody' style='text-align: center'>
				<div class="panel panel-default">
					<div class="panel-body">
						<h3>Item Information</h3>
						<div class="row">
							<div class="col-md-5">
								<br>

								<div class="panel panel-default" style='padding:0px;'>
									<div style='text-align:center;overflow-y: hidden;'>
									<img style='height:200px; width:auto' id='imgholder' src='' alt=''>
									</div>
									<div class="caption">
										<h3 id='dt-itemcode'></h3>

										<p></p>

										<p></p>
									</div>
								</div>
							</div>
							<div class="col-md-7">
								<table id='tbl-product-details' class='table'>


								</table>
							</div>
							<div style='clear: both;'></div>
							<hr>
							<h3>Price History</h3>
							<div id="price_history"></div>
							<hr>
							<h3>Inventories</h3>
							<div id="inventories_container"></div>
						</div>
					</div>
				</div>
			</div>

		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style=''>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id='mtitle2'></h4>
			</div>
			<div class="modal-body" id='mbody2' style='text-align: center'>

			</div>

		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

	$(document).ready(function() {

		$('#dt_from').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#dt_from').datepicker('hide');
			withDate();
		});

		$('#dt_to').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#dt_to').datepicker('hide');
			withDate();
		});

		function withDate(){
			var dt_from = $('#dt_from').val();
			var dt_to = $('#dt_to').val();
			if(dt_from && dt_to){
				var search = $('#searchSales').val();
				var sortby = $('#sort_by').val();
				var ascdesc = $('#ascdesc').val();

				if(!ascdesc) {
					if(sortby) sortby = sortby + 'asc';
				} else {
					if(sortby) sortby = sortby + 'desc';
				}
				getPage(0, search, sortby);
			}

		}


		$("#category_id").select2({
			placeholder: 'Choose Category',
			allowClear: true
		});
		$('body').on('click', '.productInfo', function() {
			var id = $(this).attr('data-id');
			var hid = $('#hidproduct' + id).val();
			var jsondata = JSON.parse(hid);
			var ts = Date.now();
			if(jsondata.hasimage == 1) {
				$('#imgholder').attr('src', '../item_images/' + id + '.jpg?'+ts);
			} else {
				$('#imgholder').attr('src', '../item_images/no_image.png');
			}
			$('#dt-itemcode').html(jsondata.item_code);
			var priceHistory = JSON.parse(jsondata.priceHistory);
			console.log(priceHistory);
			if(jsondata.item_type == -1) jsondata.item_type = 0;
			var arrtype = ['With Inventory', 'Without Inventory', 'Subcription', 'Consumable Amount', 'Consumable Freebies'];
			var ff = 'No';
			if(jsondata.for_freebies == 1)  ff = 'Yes';
			var it = (arrtype[jsondata.item_type]);
			var tbl = $('#tbl-product-details');
			var charbll = 'No Characteristics';
			if(jsondata.charjson) {
				var char = JSON.parse(jsondata.charjson);
				charbll = '';
				for(var ch in char) {
					charbll = charbll + char[ch].name + "<br> ";
				}
			}

			tbl.html('');
			tbl.append("<tr><td class='text-left'>Barcode</td><td class='text-left'><strong>" + jsondata.barcode + "</strong></td></tr>");
			tbl.append("<tr><td class='text-left'>Item code</td><td class='text-left'><strong>" + jsondata.item_code + "</strong></td></tr>");
			tbl.append("<tr><td class='text-left'>Description</td><td class='text-left'><strong>" + jsondata.description + "</strong></td></tr>");
			tbl.append("<tr><td class='text-left'>Selling Price</td><td class='text-left'><strong>" + number_format(jsondata.price, 2) + "</strong></td></tr>");
			tbl.append("<tr><td class='text-left'>Category</td><td class='text-left'><strong>" + jsondata.name + "</strong></td></tr>");
			tbl.append("<tr><td class='text-left'>Characteristics</td><td class='text-left'><strong>" + charbll + "</strong></td></tr>");
			tbl.append("<tr><td class='text-left'>Item Type</td><td class='text-left'><strong>" + it + "</strong></td></tr>");
			tbl.append("<tr><td class='text-left'>Avail. For Freebies</td><td class='text-left'><strong>" + ff + "</strong></td></tr>");
			if(priceHistory.length > 0){

				var phcon = $('#price_history');
				phcon.html('');
				var retprice ='';
				retprice += "<table class='table'><thead><tr><th class='text-center'>Price</th><th class='text-center'>Effectivity</th></tr></thead><tbody>";
				for(var i in priceHistory){
					retprice += "<tr><td>"+priceHistory[i].price+"</td><td>"+priceHistory[i].date+"</td></tr>";
				}
				retprice +="</tbody>";
				retprice +="</html>";
				phcon.html(retprice);

			}

			$('#myModal').modal('show');
			$.ajax({
				url:'../ajax/ajax_query2.php',
				type:'POST',
				beforeSend: function(){
					$('#inventories_container').html("Fetching inventory...")
				},
				data: {functionName:'getInventoryOfItem',item_id:id},
				success: function(data){
					$('#inventories_container').html(data);
				},
				error:function(){
					alert('Error Occur');
				}
			});
		});

		$('body').on('click', '.deleteProduct', function() {
			if(confirm("Are you sure you want to delete this record?")) {
				id = $(this).prop('id');
				$.post('../ajax/ajax_delete.php', {id: id, table: 'items'}, function(data) {
					if(data == "true") {
						location.reload();
					}
				});
			}
		});

		$('body').on('click', '.showImages', function(e) {
			e.preventDefault();
			var id = $(this).prop('id');
			var itemcode = $(this).prop('name');
			$("#mtitle2").empty();
			$("#mbody2").empty();
			$("#mtitle2").append(itemcode);
			var ts = Date.now();
			$("#mbody2").append("<div style='text-align:center;overflow-x: hidden;'><img  alt='" + itemcode + "' style='height:300px;width:auto;overflow:hidden;' src='../item_images/" + id + ".jpg?"+ts+"'/></div>");
			$('#myModal2').modal('show');

		});
		var targetid = '<?php echo $targetid; ?>';
		getPage($('#hiddenpage').val(), '', '');
		$('body').on('click', '.paging', function(e) {
			e.preventDefault();
			var page = $(this).attr('page');
			$('#hiddenpage').val(page);
			var search = $('#searchSales').val();
			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();
			if(!ascdesc) {
				if(sortby)	sortby = sortby + 'asc';
			} else {
				if(sortby)	sortby = sortby + 'desc';

			}
			getPage(page, search,sortby);
		});
		$('body').on('click', '.page_sortby', function(e) {
			e.preventDefault();
			var sortlabel = $(this).attr('data-sort');
			$('#sort_by').val(sortlabel);
			var page = $(this).attr('page');
			var search = $('#searchSales').val();
			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();
			if(ascdesc) {
				if(sortby) sortby = sortby + 'asc';
				$('#ascdesc').val('');
			} else {
				if(sortby)  sortby = sortby + 'desc';
				$('#ascdesc').val(1);
			}
			getPage(0, search, sortby);
		});
		var timer;

		$("#searchSales").keyup(function() {
			var searchtxt = $("#searchSales");

			var search = searchtxt.val();

			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();
			if(!ascdesc) {
				if(sortby) sortby = sortby + 'asc';
			} else {
				if(sortby) sortby = sortby + 'desc';
			}
			clearTimeout(timer);
			timer = setTimeout(function() {
				if(searchtxt.val()){
					searchtxt.val(searchtxt.val().trim());
				}
				getPage(0, search, sortby);
			}, 1000);

		});

		$("#limit_by,#category_id").change(function() {
			var search = $('#searchSales').val();
			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();

			if(!ascdesc) {
				if(sortby) sortby = sortby + 'asc';
			} else {
				if(sortby) sortby = sortby + 'desc';
			}
			getPage(0, search, sortby);
		});
		function getPage(p, search, sortby) {

			var limit_by = $('#limit_by').val();
			var category_id = $('#category_id').val();
			var dt_from = $('#dt_from').val();
			var dt_to = $('#dt_to').val();

			$.ajax({
				url: '../ajax/ajax_paging.php',
				type: 'post',
				beforeSend: function() {
					$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
				},
				data: {
					page: p,
					sortby: sortby,
					functionName: 'itemsPaginate',
					cid: <?php echo $user->data()->company_id; ?>,
					search: search,
					limit_by:limit_by,
					category_id:category_id,
					dt_from:dt_from,
					dt_to:dt_to,
				},
				success: function(data) {
					$('#holder').html(data);
					if(targetid != ''){
						$('html, body').animate({
							scrollTop: $("#item_" + targetid).offset().top
						}, 1000);
					}
				}
			});
		}

		$('body').on('change', '#branch_id', function() {
			branchTerminal($('#branch_id').val(), 2);
			var search = $('#searchSales').val();
			getPage(0, search, '');
		});
		$('body').on('change', '#terminals', function() {
			var search = $('#searchSales').val();
			getPage(0, search, '');
		});

		$('body').on('click','#btnDownloadExcel',function(){
			var search = $('#searchSales').val();
			var sortby = $('#sort_by').val();
			var ascdesc = $('#ascdesc').val();
			if(!ascdesc) {
				if(sortby) sortby = sortby + 'asc';
			} else {
				if(sortby) sortby = sortby + 'desc';
			}
			var limit_by = $('#limit_by').val();
			var category_id = $('#category_id').val();
			var dt_from = $('#dt_from').val();
			var dt_to = $('#dt_to').val();


			window.open(
				'excel_downloader.php?downloadName=products&search='+search+'&sortby='+sortby+'&category_id='+category_id+'&dt_from='+dt_from+'&dt_to='+dt_to,
				'_blank' //
			);
		});
	});

</script>