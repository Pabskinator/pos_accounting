<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>

	<link
		rel="stylesheet"
		href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
		integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
		crossorigin="anonymous">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<script
		src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
		integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
		crossorigin="anonymous">
	</script>

	<style>
		.get-serial	{
			right: 0;
			bottom: 0;
			margin: 20px;
			position: fixed;
		}

		.current-item {
			border: 1px solid #aaa;
		}
	</style>
</head>

<body>
<div class="container">
	<table class="table" id="orders-table">
		<thead>
		<tr>
			<td>ORDER</td>
			<td>ITEMS</td>
			<td>SERIALS</td>
		</tr>
		</thead>

		<tbody>

		</tbody>
	</table>

	<div class="get-serial">
		<input type="text" id="input-serial">

		<button class="btn btn-primary">GET SERIAL</button>
	</div>
</div>
</body>

<script>
	var currentOrderIndex = 0;
	var currentOrderItemIndex = 0;

	var orders = [
		{
			order_id: 6,
			items: [
				{
					item_id: 1,
					item_code: 'Item Code 1',
					description:'Item description 1',
					qty:2,
					with_serial: 0,
					serials:[]
				},

				{
					item_id: 10,
					item_code: 'Item Code 10',
					description:'Item description 10',
					qty:3,
					with_serial: 0,
					serials:[]
				}
			]
		},

		{
			order_id: 10,
			items: [
				{
					item_id: 1,
					item_code: 'Item Code 1',
					description:'Item description 1',
					qty:2,
					with_serial: 0,
					serials:[]
				},

				{
					item_id: 2,
					item_code: 'Item Code 2',
					description:'Item description 2',
					qty:4,
					with_serial: 1,
					serials:[]
				},

				{
					item_id: 8,
					item_code: 'Item Code 8',
					description:'Item description 8',
					qty:4,
					with_serial: 0,
					serials:[]
				},

				{
					item_id: 3,
					item_code: 'Item Code 3',
					description:'Item description 3',
					qty:5,
					with_serial: 1,
					serials:[]
				},
			]
		},

		{
			order_id: 11,
			items: [
				{
					item_id: 4,
					item_code: 'Item Code 4',
					description:'Item description 4',
					qty:2,
					with_serial: 1,
					serials:[]
				},

				{
					item_id: 5,
					item_code: 'Item Code 5',
					description:'Item description 5',
					qty:2,
					with_serial: 0,
					serials:[]
				},

				{
					item_id: 11,
					item_code: 'Item Code 11',
					description:'Item description 11',
					qty:4,
					with_serial: 1,
					serials:[]
				},
			]
		},

		{
			order_id: 12,
			items: [
				{
					item_id: 6,
					item_code: 'Item Code 6',
					description:'Item description 6',
					qty:2,
					with_serial: 0,
					serials:[]
				},

				{
					item_id: 7,
					item_code: 'Item Code 7',
					description:'Item description 7',
					qty:4,
					with_serial: 1,
					serials:[]
				},

				{
					item_id: 5,
					item_code: 'Item Code 5',
					description:'Item description 5',
					qty:2,
					with_serial: 0,
					serials:[]
				},
				{
					item_id: 9,
					item_code: 'Item Code 9',
					description:'Item description 9',
					qty:6,
					with_serial: 0,
					serials:[]
				},
			]
		}
	];


	$('#input-serial').val('ABC12321ASDFA');
	refreshTable();


	$('body').on('click', '.get-serial > button', function() {
		getSerial();
	});

	$('body').on('click', 'tr.item', function() {
		var orderIndex = $(this).attr('data-order-index');
		var itemIndex = $(this).attr('data-item-index');

		if(orders[orderIndex].items[itemIndex].serials.length < orders[orderIndex].items[itemIndex].qty) {
			currentOrderIndex = $(this).attr('data-order-index');
			currentOrderItemIndex = $(this).attr('data-item-index');
		}
	});

	$('body').on('click', '.serial > p > span:nth-child(2)', function() {
		var p = $(this).parents('p');
		var orderIndex = p.attr('data-order-index');
		var itemIndex = p.attr('data-item-index');
		var serialIndex = p.attr('data-serial-index');

		orders[orderIndex]
			.items[itemIndex]
			.serials
			.splice(serialIndex, 1);

		refreshTable();
	});

	function refreshTable() {
		var ordersTable = $('#orders-table > tbody');
		var innerHTML = '';

		orders.forEach(function(order, index) {
			innerHTML += `
					<tr>
						<td colspan="3">Order id: ${order.order_id}</td>
					</tr>

					${displayItems(order.items, index)}
				`;
		});

		ordersTable.html(innerHTML);
	}

	function displayItems(items, orderIndex) {
		var html = '';

		items.forEach(function(item, index) {
			html += `
					<tr
						class="item"
						data-order-index="${orderIndex}"
						data-item-index="${index}">

						<td>&nbsp;</td>

						<td>
							<p>Item id: ${item.item_id}</p>
							<p>Desc: ${item.description}</p>
							<p>Qty: ${item.qty}</p>
						</td>

						<td class="serial">${displaySerials(item, orderIndex, index)}</td>
					</tr>
				`;
		});

		return html;
	}

	function displaySerials(item, orderIndex, itemIndex) {
		var html = '';

		if(!item.with_serial) {
			html += '<label class="label label-danger">No serial</label>'
		} else {
			item.serials.forEach(function(serial, index) {
				html += `
						<p
							data-order-index="${orderIndex}"
							data-item-index="${itemIndex}"
							data-serial-index="${index}">

							<span>${serial}</span>
							<span class="glyphicon glyphicon-remove"></span>
						</p>
					`;
			});
		}

		return html;
	}

	function getSerial() {
		if(currentOrderIndex < orders.length) {
			while(!orders[currentOrderIndex].items[currentOrderItemIndex].with_serial) {
				currentOrderItemIndex++;

				incrementOrder();
			}

			appendSerials();
		}
	}

	function appendSerials() {
		var currentOrderItem = orders[currentOrderIndex].items[currentOrderItemIndex];

		if(currentOrderItem.serials.length < currentOrderItem.qty) {
			currentOrderItem.serials.push($('#input-serial').val());
			refreshTable();

			if(currentOrderItem.serials.length == currentOrderItem.qty) {
				currentOrderItemIndex++;

				incrementOrder();
			}
		}
	}

	function incrementOrder() {
		if(currentOrderItemIndex == orders[currentOrderIndex].items.length) {
			currentOrderIndex++;
			currentOrderItemIndex = 0;
		}
	}
</script>
</html>