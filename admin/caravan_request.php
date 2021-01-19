<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('caravan_request')) {
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$branch = new Branch();
	$branches = $branch->get_active('branches', array('company_id', '=', $user->data()->company_id));

?>

	<!-- Page content -->
<div id="page-content-wrapper">

<!-- Keep all page content within the page-content inset div! -->
<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Request </h1>
	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('caravan')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('caravan') . "</div>";
		}


	?>
	<div class="row">
		<div class="col-md-8"></div>
		<div class="col-md-2">
			<div class="form-group">
			<input type="text" class='form-control' id='caravan_id' placeholder='Enter Caravan ID #'>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<button class='btn btn-default' id='btnUse'>Reuse</button>
			</div>

		</div>
	</div>
	<div class="row">
			<div class="col-md-3">
				<div class="form-group">
				<input type="text" id='witness' placeholder='Witness name' class='form-control'/>
				</div>
			</div>
		<div class="col-md-3">
			<div class="form-group">
			<select name="bname" id="bid" class='form-control'>
				<option value=""></option>
				<?php
					$branch = new Branch();
					$branches = $branch->get_active('branches', array('company_id', '=', $user->data()->company_id));
				?>
				<?php foreach($branches as $b): ?>
					<option value="<?php echo escape($b->id); ?>"><?php echo escape($b->name); ?></option>
				<?php endforeach; ?>
			</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<input type="text" id='remarks' placeholder='Remarks' class='form-control'/>
			</div>
		</div>
		</div>

	<div class='row'>
		<div class="col-md-6">
			<div class="form-group">
			<input name="searchOrder" id="searchOrder" class='selectitem'>

				</div>
		</div>
		<div class="col-md-3">
			<div class="hidden-xs">
				<div id="imagecon">
					<span style='cursor:pointer; position:absolute;right:2px;top:2px;font-size:1.1em;' class='glyphicon glyphicon-remove-sign removeImage'></span>
					<img src="" alt="Image" />
				</div>
			</div>
			<input type="button" id='addtolist' value='Add' class='btn btn-default' />
		</div>

	</div>
	<!-- End Row 1-->		<!-- Start Row 2 -->
	<div class="row">
		<div class="col-md-12">
			<br />
			<div id="no-more-tables">
			<table id='cart' class='table' style='font-size:1em'>
				<thead>
				<tr>
					<th>BARCODE</th>
					<th>ITEM CODE</th>
					<th>QTY</th>
					<th>TOTAL PRICE</th>
					<th>BRANCH STOCK</th>

					<th></th>
				</tr>
				</thead>

				<tbody>

				</tbody>

			</table>
			</div>
		</div>
	</div>
	<div class="well" style='margin:3px;'><p id='stotal'></p></div>

	<!-- end of row 2-->		<!--  start of button row-->
	<div class="row">
		<div class="col-md-8">

		</div>
		<div class="col-md-4">
			<input type="button" id='void' value='VOID' class='btn btn-danger' />
			<input type="button" id='save' value='SAVE' class='btn btn-success' />
		</div>
	</div>
<br>
	<!-- end of button row-->

</div>
<!-- end page content wrapper-->

<script>

	$(function() {
		$('body').on('click','#btnUse',function(){
			var id = $('#caravan_id').val();

			$.ajax({
			    url:'../ajax/ajax_caravan.php',
			    type:'POST',
			    data: {functionName:'reUse',id:id},
				dataType:'json',
			    success: function(data){

					if(data.main){

						$('#witness').val(data.main.witness);
						$('#bid').select2('val',data.main.branch_id)
						$('#remarks').val(data.main.remarks);
						var item_html = "";
						for(var i in data.items){
							item_html += "<tr id='"+data.items[i].id+"'><td data-title='Barcode'>"+data.items[i].barcode+"</td>" +
								"<td data-title='Item'>"+data.items[i].item_code+"<br>" +
								"<small class='text-danger'>"+data.items[i].description+"</small></td>" +
								"<td data-title='Qty'><input type='text' class='form-control  qty' value='"+data.items[i].qty+"' style='width:80px;'></td>" +
								"<td data-title='Price' data-price='"+data.items[i].price+"'>"+data.items[i].price+"</td>" +
								"<td data-title='Stock'>0</td>" +
								"<td><span class='glyphicon glyphicon-remove-sign removeItem'></span></td>" +
								"</tr>";
						}
						$('#cart tbody').html(item_html);

					} else {
						tempToast('error','<p>Invalid ID</p>','<h3>WARNING!</h3>');
					}
			    },
			    error:function(){

			    }
			});


		});
		var ajaxOnProgress = false;
		noItemInCart();
		function noItemInCart() {
			if(!$("#cart tbody").children().length) {
				$("#cart tbody").append("<td data-title='Remarks' colspan='3' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
			}
		}
		function formatItem(o) {
			if (!o.id)
				return o.text; // optgroup
			else {
				var r = o.text.split(':');
				return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
			}
		}

		$("#bid").select2({
			placeholder: 'Select a Branch'
		});

		$('body').on('click', '.removeItem', function() {
			$(this).parents('tr').remove();
			noItemInCart();
		});

		$('#void').click(function() {
			$("#cart").find("tr:gt(0)").remove();
			noItemInCart();
		});
		function removeNoItemLabel() {
			$("#noitem").remove();
		}
		$('#save').click(function() {
			var btncontext = $(this);
			btncontext.attr('disabled',true);
			btncontext.val('Loading...');
			if($("#cart tbody tr").children().length) {
				var branch = $("#bid").val();
				var witness = $('#witness').val();
				var remarks = $('#remarks').val();
				var foundNoqty = 0;
				if(branch && witness) {
					var toOrder = new Array();

					$('#cart >tbody > tr').each(function(index) {
						var row = $(this);
						var item_id = $(this).prop('id');
						var qty = row.children().eq(2).find('input').val();
						var rgx = /^\d+$/
						if(qty == '' || qty == undefined){
							qty= 0;
						}
						if(!rgx.test(qty) || qty == 0){
						foundNoqty = parseInt(foundNoqty) + 1;
						}
	
						toOrder[index] = {
							item_id: item_id, qty: qty
						}
					});
					if(foundNoqty > 0) {
						tempToast('error','<p>Please Indicate the Quantity of the items</p>','<h3>WARNING!</h3>');
						btncontext.attr('disabled',false);
						btncontext.val('SAVE');
					} else {
						$('.loading').show();
						toOrder = JSON.stringify(toOrder);
						if(ajaxOnProgress) {
							return;
						}
						ajaxOnProgress = true;
						$.ajax({
							url: "../ajax/ajax_caravan_request.php",
							type: "POST",
							async: false,
							data: {
								toOrder: toOrder,
								branch: branch,
								witness:witness,
								remarks:remarks,
								company_id:<?php echo $user->data()->company_id; ?>
							},
							success: function(data) {
								tempToast('info','<p>'+data+'</p>','<h3>INFO!</h3>');
								ajaxOnProgress = false;
								localStorage.removeItem('cache_request_liquidation');
								setTimeout(function(){
									location.href = "caravan_request.php";
								}, 2000);
								btncontext.attr('disabled',false); // this is useless but i put it anyway
								btncontext.val('SAVE');// this is useless but i put it anyway
							},
							error: function() {
								// save in local storage
								alert('Saving transaction error');
								ajaxOnProgress = false;
								location.href = "caravan_request.php";
								btncontext.attr('disabled',false);// this is useless but i put it anyway
								btncontext.val('SAVE');// this is useless but i put it anyway
							}
						});
					}
				} else {
					tempToast('error','<p>Please choose branch and witness first</p>','<h3>WARNING!</h3>')
					btncontext.attr('disabled',false);
					btncontext.val('SAVE');
				}
			} else {
				tempToast('error','<p>No items in cart</p>','<h3>WARNING!</h3>');
				btncontext.attr('disabled',false);
				btncontext.val('SAVE');
			}
		});

		$("#addtolist").click(function() {


				var branch = $("#bid").val();
				var item_id = $("#searchOrder").val();
				var isoncart = false;
				var allqty = 0;
				$('#cart >tbody > tr').each(function(){
					var row_id = $(this).attr('id');
					if(row_id == item_id){
						isoncart = true;
						return;
					}
				});
				if(isoncart){
					tempToast('error','<p>Item is already in cart</p>','<h3>WARNING!</h3>');
					$('.loading').hide();
					return;
				}
				if(!branch || !item_id) {

					tempToast('error','<p>Please Choose branch and item first</p>','<h3>WARNING!</h3>');
					$('.loading').hide();
				} else {
					$('.loading').show();
					$.ajax({
						url: "../ajax/ajax_caravan.php",
						type: "POST",
						async: false,
						data: {
							item_id: item_id,
							branch_id: branch,
							functionName : 'getCurrentQty'
						},
						success: function(data) {
							allqty = data;
							ajaxOnProgress = false;
							$('.loading').hide();

						},
						error: function() {
							// save in local storage
							alert('Saving transaction error');
							$('.loading').hide();
							ajaxOnProgress = false;
						}
					});

					var optdata = $('#searchOrder').select2('data');
					var item_code = optdata.text;
					var splitted = item_code.split(':');
					removeNoItemLabel();
					var item_bc = splitted[0];
					var item_price = splitted[3];
					var itemcode = splitted[1];
					var de = splitted[2];
					$('#cart > tbody').append("<tr id='" + item_id + "'><td data-title='Barcode'>" + item_bc + "</td><td data-title='Item'>" + itemcode + "<br><small class='text-danger'>"+de+"</small></td><td data-title='Qty'><input type='text' class='form-control  qty' value='1' style='width:80px;'></td><td data-title='Price' data-price="+item_price+">"+number_format(item_price,2)+"</td><td data-title='Stock'>"+number_format(allqty)+"</td><td ><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");

				}

			$("#searchOrder").select2("val", "");
			$("#"+item_id).children().eq(2).find('input').focus().select();
			updateTotal();

		});

		function saveLocal(){
			var cartBody = $('#cart tbody').html();
			var stotal = $('#stotal').html();
			var pending = {
				cartBody:cartBody,
				stotal:stotal
			};
			localStorage['cache_request_liquidation'] = JSON.stringify(pending);
		}
		$('body').on('keyup','.qty',function(e){
			var p = e.keyCode;
			if(p != 8){
				var qty = $(this).val();
				var rgx = /^\d+$/

				if(!rgx.test(qty) || qty == 0){

					$(this).val(1);

					tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
					updateTotal();
					return;
				}
				$(this).attr('value',qty);
				updateTotal();
			}
		});
		$('body').on('blur','.qty',function(e){

				var qty = $(this).val();
				var rgx = /^\d+$/

				if(!rgx.test(qty) || qty == 0){
					$(this).val(1);

					tempToast('error','<p>Invalid quantity</p>','<h3>WARNING!</h3>');
					updateTotal();
					return;
				}
				updateTotal();
			
		});
		function updateTotal(){
			var total = 0;
			$('#cart > tbody > tr').each(function(){
				var row = $(this);
				var qty = row.children().eq(2).find('input').val();
				var price = row.children().eq(3).attr('data-price');
				var t = parseFloat(qty) * parseFloat(price);
				total += t;
				t =number_format(t,2);
				row.children().eq(3).text(t);
			});
			$('#stotal').html("Total: " + number_format(total,2));
			saveLocal();
		}
		checkPendingRequest();
		function checkPendingRequest(){
			if(localStorage['cache_request_liquidation']){
				var pending = JSON.parse(localStorage['cache_request_liquidation']);
				alertify.confirm("You have unsaved request. Do you want to load it?", function (asc) {
					if (asc) {
						$('#cart tbody').html(pending.cartBody);
						$('#stotal').html(pending.stotal);

					} else {

					}
				}, "");
			}
		}

	});
</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>