function printAvisionDr(data, title){

	var date_obj = new Date();
	var current_date = (parseInt(date_obj.getMonth()) + parseInt(1)) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
	var po_number = data.client_po;

	var html = "";
	var ctr = 0;
	var page = 1;
	var grand_total = 0;
	var page_content = {page:0,content:[]};
	var pages = [];
	var item_per_page = 10;
	var cdr = $('#custom_dr').val();
	var nextdr = parseInt(localStorage['dr']) + 1;
	var control_num = (cdr) ? cdr : nextdr;

	if(data.dr){
		control_num = data.dr;
	}

	for(var i in data.item_list){
		if(!data.item_list[i].qty) continue;

		ctr++;

		var total = data.item_list[i].total;
		grand_total += parseFloat(total);
		page_content.page = page;
		page_content.content.push(data.item_list[i]);

		if(ctr % item_per_page == 0){
			ctr = 0;
			page++;
			pages.push(page_content);
			page_content = {page:0,content:[]};
		}
	}
	if(page_content.pages != 0){
		pages.push(page_content);
	}

	for(var p in pages){
		var paging = "";
		var page_subtotal = 0;

		paging += "<div style='height:49%;position:relative;margin-top:10px;'>";
		paging += "<div style='position:absolute;top:5px;right: 10px;'>"+current_date+"</div>";
		paging += "<div style='position:absolute;top:5px;left: 10px;'>"+po_number+"</div>";
		paging += "<h4 class='text-center'>"+title+"</h4>";
		paging += "<table style='font-size:10px;' class='table table-bordered table-condensed'>";
		paging += "<tr><td style='width:50%;'>Client: <strong>"+data.member_name+"</strong></td><td style='width:50%;'>DR #: <strong>"+control_num+"</strong></td></tr>";
		paging += "<tr><td style='width:100%;' colspan='2'>Address: <strong>"+data.station_name+"</strong></td></tr>";
		paging += "</table>";
		paging += "<table style='font-size:10px;' class='table table-condensed table-bordered'>";
		paging += "<tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>";
		var cur_page  = pages[p].content;
		var ctr = 0;

		for(var cp in cur_page){
			ctr++;
			var description = cur_page[cp].description;
			var qty = cur_page[cp].qty;
			var price = cur_page[cp].price;
			var total = cur_page[cp].total;
			page_subtotal += parseFloat(total);
			paging += "<tr><td>"+description+"</td><td>"+qty+"</td><td>"+number_format(price,2)+"</td><td>"+number_format(total,2)+"</td></tr>";
		}
		if(ctr < item_per_page){
			for(var j = ctr; j < item_per_page;j++){
				paging += "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			}
		}
		paging += "<tr><td>Page "+pages[p].page+" of "+pages.length+" </td><td>&nbsp;</td><td>&nbsp;</td><td>"+number_format(page_subtotal,2)+"</td></tr>";
		paging += "</table>";
		paging += "<table style='font-size:10px;' class='table table-bordered table-condensed'>";
		paging += "<tr><th style='width:32%'>Released By:  </th><th style='width:32%'>Checked By: </th><th style='width:32%'>Received By: </th></tr>";
		paging += "</table>";

		paging += "</div>";

		paging += paging;

		html += "<div style='page-break-after: always'>"+ paging+"</div>";
	}


	popUpPrintWithStyle(html);
}

function popUpPrintWithStyle(data) {
	var mywindow = window.open('', 'new div', '');
	mywindow.document.write('<html><head><title></title><style></style>');
	mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
	mywindow.document.write('<style>table.table-bordered tr td,table.table-bordered tr th {border : 1px solid #000 !important;}</style>');
	mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');
	setTimeout(function() {
		mywindow.print();
		mywindow.close();

	}, 300);
	return true;
}