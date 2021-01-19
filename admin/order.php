<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('createorder')) {
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
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Order </h1>

	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('branchflash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('branchflash') . "</div>";
		}
	?>

	<?php
		$mem = new Member();
		$members = $mem->getMembers($user->data()->company_id);
		$mlist ='';
		foreach ($members as $m):
	?>
		<?php $mlist .= "<option value='".$m->id."'>" . $m->lastname . ", " . $m->firstname . " " . $m->middlename ."</option>"; ?>
	
	<?php
		endforeach;
	?>
	<?php

		$item = new Product();
		$items = $item->get_active('items', array('company_id', '=', $user->data()->company_id));
		$ilist = '';
		foreach($items as $i):
			if($i->item_type == 1 || $i->item_type == 2) continue;
			?>
			<?php $ilist .= "<option data-bc='".$i->barcode."' value='".$i->id."'>". escape($i->item_code)."</option>"; ?>
			<?php $ilist .= "<option data-bc='".$i->barcode."' value='".$i->id."'>". escape($i->description)."</option>"; ?>
		<?php endforeach; ?>
	<div class="row">

		<div class="col-md-3">
			<div class='form-group'>
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

		<div class="col-md-3">
			<div class='form-group'>
				<select class='form-control' id='searchMember' >
					<option></option>
					<?php echo $mlist; ?>
				</select>
				</div>
		</div>
		<div class="col-md-3">
			<div class='form-group'>
			<select class='form-control' id='searchOrder'>
					<option></option>
					<?php echo $ilist; ?>
			</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class='form-group'>
			<input type='button' id='addorder' value='Add' class='btn btn-default' />
				</div>
		</div>

	</div>
	<!-- End Row 1-->		<!-- Start Row 2 -->
	<div class="row">
		<div class="col-md-12">
			<br />
			<table id='cart' class='table' style='font-size:1em'>
				<thead>
				<tr>
					<th>BARCODE</th>
					<th>ITEM CODE</th>
					<th>QTY</th>
					<th></th>
				</tr>
				</thead>

				<tbody>

				</tbody>

			</table>
		</div>
	</div>
	<!-- end of row 2-->		<!--  start of button row-->
	<div class="row">
		<div class="col-md-8">

		</div>
		<div class="col-md-4">
			<input type="button" id='void' value='VOID' class='btn btn-danger' />
			<input type="button" id='save' value='SAVE' class='btn btn-success' />
		</div>
	</div>
	<!-- end of button row-->

</div>
<!-- end page content wrapper-->


<script>

	$(function() {
		var ajaxOnProgress = false;
		noItemInCart();
		function noItemInCart() {
			if(!$("#cart tbody").children().length) {
				$("#cart tbody").append("<td colspan='3' id='noitem' style='padding-top:10px;' ><span class='text-danger'>NO ITEMS IN CART</span></td>");
			}
		}

		$('body').on('click', '.removeItem', function() {
			$(this).parents('tr').remove();
			noItemInCart();

		});
		$('#void').click(function() {
			$("#cart").find("tr:gt(0)").remove();
			noItemInCart();
		});

		$('#save').click(function() {
			if($("#cart tbody tr").children().length) {
				var branch = $("#bid").val();
				var member_id = $("#searchMember").val();
			
				var foundNoqty = 0;
				if(branch && member_id) {
					var toOrder = new Array();
					$('#cart >tbody > tr').each(function(index) {
						var row = $(this);
						var item_id = $(this).prop('id');
						var qty = row.children().eq(2).find('input').val();

						if(!qty || qty == 0 || isNaN(qty) || qty == undefined) {
							foundNoqty = parseInt(foundNoqty) + 1;
						}
						toOrder[index] = {
							item_id: item_id, qty: qty
						}
					});
					if(foundNoqty > 0) {
						alert("Please Indicate the Quantity of the items");
					} else {
						toOrder = JSON.stringify(toOrder);
						if(ajaxOnProgress) {
							return;
						}
						ajaxOnProgress = true;
						$.ajax({
							url: "../ajax/ajax_order.php",
							type: "POST",
							async: false,
							data: {
								toOrder: toOrder,
								branch: branch,
								member_id: member_id,
								src_branch: localStorage['branch_id'],
								company_id:<?php echo $user->data()->company_id; ?>,
								type:1
							},
							success: function(data) {
								alert(data);
								ajaxOnProgress = false;
								location.reload();
							},
							error: function() {
								// save in local storage
								alert('Saving transaction error');
								ajaxOnProgress = false;
							}
						});
					}
				} else {
					alert('Please choose branch and member first');
				}
			} else {
				alert('No items in cart');
			}
		});

	

		$("#addorder").click(function() {
				var val = $('#searchOrder');
				var id = val.val();
				var branch = $("#bid").val();
				var mem_id = $("#searchMember").val();
				var isoncart = false;
				$('#cart >tbody > tr').each(function(){
					var row_id = $(this).attr('id');
					if(row_id == id){
						isoncart = true;
						$("#searchOrder").select2('val',null);
						return;
					}
				});
				if(isoncart){
					alert('Item is already in cart');
					return;
				}
				if(!branch || !id || !mem_id) {
					alert('Please Choose branch first');
					$("#searchOrder").select2('val',null);
				} else {
					
					var x = $("#searchOrder option:selected").text();
					removeNoItemLabel();
					var item_id = id;
					var item_bc = $("#searchOrder option:selected").attr('data-bc');
					$('#cart > tbody').append("<tr id='" + item_id + "'><td>" + item_bc + "</td><td>" + x + "</td><td><input type='text' class='form-control  qty' style='width:80px;'></td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
					$("#searchOrder").select2('val',null);
				}
		});
		function removeNoItemLabel() {
			$("#noitem").remove();
		}
		$("#searchMember").select2({
			placeholder: 'Select Member',
			allowClear: true
		});
		$("#bid").select2({
			placeholder: 'Select Branch',
			allowClear: true
		});
			$("#searchOrder").select2({
			placeholder: 'Select Item',
			allowClear: true
		});
	});
</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>