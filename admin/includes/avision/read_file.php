<?php
	$html .= "<h3>" . $_FILES["file"]["name"] . "</h3>";
	$allowedExts = array("xls", "xlsx");
	$temp = explode(".", $_FILES["file"]["name"]);

	$extension = end($temp);

	if($_FILES["file"]["type"] == "application/vnd.ms-excel" || $_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" && in_array($extension, $allowedExts)) {
		$uploads_dir = "../service/";
		$filename = $_FILES["file"]["name"];

		$isUploaded = move_uploaded_file($_FILES["file"]["tmp_name"], $uploads_dir . $_FILES["file"]["name"]);
		if($isUploaded) {

			if(!file_exists($uploads_dir . $_FILES['file']['name'])) {
				exit("FILE NOT FOUND!." . PHP_EOL);
			}
			$objPHPExcel = PHPExcel_IOFactory::load($uploads_dir . $_FILES['file']['name']);

			$sheetNames = $objPHPExcel->getSheetNames();
			$arr = [];


			$has_error= false;
			$arr_to_insert = [];
			$arr_to_insert_info = [];
			$po_arr = [];
			$wh_info_ar = [];
			foreach($sheetNames as $index => $name) {

				$objPHPExcel->setActiveSheetIndex($index);
				$lastRow = $objPHPExcel->setActiveSheetIndex($index)->getHighestRow();
				$lastColumn = $objPHPExcel->setActiveSheetIndex($index)->getHighestColumn();
				$startRow = 2;

				$branch_id = 28;



				if($type == 1){ // lazada format

					$html .= "<table class='table' id='tblForApproval'>";
					$html .= "<tr><th>Ref Num</th><th>Item</th><th>Customer</th><th>Price</th><th></th></tr>";

				} else if ($type == 2){ // shopee format

					$html .= "<table class='table' id='tblForApproval'>";
					$html .= "<tr><th>Ref Num</th><th>Items</th><th>Customer</th><th>Price</th><th></th></tr>";

				}



				for($row = $startRow; $row <= $lastRow; $row++) {

					if($type == 1) { // lazada format

						$order_item_id = $objPHPExcel->getActiveSheet()->getCell("A" . $row)->getValue();
						$lazada_id = $objPHPExcel->getActiveSheet()->getCell("B" . $row)->getValue();
						$order_number = $objPHPExcel->getActiveSheet()->getCell("G" . $row)->getValue();
						$seller_sku = $objPHPExcel->getActiveSheet()->getCell("C" . $row)->getValue();
						$lazada_sku = $objPHPExcel->getActiveSheet()->getCell("D" . $row)->getValue();
						$item_name = $objPHPExcel->getActiveSheet()->getCell("AN" . $row)->getValue();
						$customer_name = $objPHPExcel->getActiveSheet()->getCell("I" . $row)->getValue();
						$customer_address = $objPHPExcel->getActiveSheet()->getCell("M" . $row)->getValue();
						$customer_contact_number = $objPHPExcel->getActiveSheet()->getCell("AD" . $row)->getValue();
						$paid_price = $objPHPExcel->getActiveSheet()->getCell("AJ" . $row)->getValue();
						$unit_price = $objPHPExcel->getActiveSheet()->getCell("AK" . $row)->getValue();
						$shipping_price = $objPHPExcel->getActiveSheet()->getCell("AL" . $row)->getValue();

						$tracking_number = $objPHPExcel->getActiveSheet()->getCell("AU" . $row)->getValue();
						$shipping_company = $objPHPExcel->getActiveSheet()->getCell("AQ" . $row)->getValue();
						$variation = $objPHPExcel->getActiveSheet()->getCell("AO" . $row)->getValue();
						$shipment_type_name = $objPHPExcel->getActiveSheet()->getCell("AR" . $row)->getValue();



						$order_item_id =  number_format($order_item_id,0,".","");
						$lazada_id =  number_format($lazada_id,0,".","");
						$order_number =  number_format($order_number,0,".","");
						$unit_price = number_format($unit_price,2);
						$paid_price = number_format($paid_price,2);
						$shipping_price = number_format($shipping_price,2);
						$variation = $variation ? $variation : 'None';
						$shipment_type_name = $shipment_type_name ? $shipment_type_name : 'None';
						$shipping_company = $shipping_company ? $shipping_company : 'None';
						//  check prod description
						$prod = new Product();
						$item_name = str_replace("'","",$item_name);
						$checker = $prod->isProductExistBydesc(trim($item_name),1,true);
						if(isset($checker->id) && $checker->id){
							$cls = "";
							$arr_to_insert_info[$order_number] = [
								'customer_name' => $customer_name,
								'customer_address' => $customer_address,
								'customer_contact_number' => $customer_contact_number
							];

							$arr_to_insert[$order_number][$checker->id][] = [
								'item_id' => $checker->id,
								'paid_price' => $paid_price,
								'unit_price' => $unit_price,
								'shipping' => $shipping_price,
								'order_item_id' => $order_item_id,
								'order_number' => $order_number,
								'lazada_id' => $lazada_id,
								'customer_name' => $customer_name,
								'customer_address' => $customer_address,
								'customer_contact_number' => $customer_contact_number,
							];
							$wh_info_ar[] = ['unit_price' =>  str_replace(',',"",$unit_price) ,'qty' => '1', 'client_po' => $order_number,'laz_lazada_id' =>$lazada_id,'laz_order_item_id' =>$order_item_id,'seller_sku'=>$seller_sku,'item_name'=>$item_name,'tracking_number'=>$tracking_number,'shipping_company'=>$shipping_company,'laz_variation'=>$variation,'laz_shipment_type_name'=>$shipment_type_name];


						} else {
							$cls = "bg-danger";
							$has_error = true;
						}
						if($cls){
							$cls = "<span class='badge badge-danger'>Item name does not exists in our database.</span>";
						}
						$html .= "<tr>";
						$html .= "<td style='width:250px;'>";
						$html .= "<span class='span-block'><strong>Order Item ID: </strong>$order_item_id </span>";
						$html .= "<span class='span-block'><strong>Lazada ID: </strong>$lazada_id </span>";
						$html .= "<span class='span-block'><strong>Order Number: </strong>$order_number </span>";
						$html .= "<span class='span-block'>$cls</span>";
						$html .= "</td>";

						$html .= "<td  style='width:45%;'>";
						$html .= "<span class='span-block'><strong>Seller SKU: </strong>$seller_sku </span>";
						$html .= "<span class='span-block'><strong>Lazada SKU: </strong>$lazada_sku </span>";
						$html .= "<span class='span-block'><strong>Item Name: </strong>$item_name </span>";
						$html .= "</td>";
						$html .= "<td>";
						$html .= "<span class='span-block'><strong>Billing Name: </strong>$customer_name </span>";
						$html .= "<span class='span-block'><strong>Billing Address: </strong>$customer_address </span>";
						$html .= "<span class='span-block'><strong>Contact Number: </strong>$customer_contact_number </span>";
						$html .= "</td>";
						$html .= "<td>";
						$html .= "<span class='span-block'><strong>Unit Price: </strong>$unit_price </span>";
						$html .= "<span class='span-block'><strong>Paid Price: </strong>$paid_price </span>";
						$html .= "<span class='span-block'><strong>Shipping Price: </strong>$shipping_price </span>";
						$html .= "</td>";
						$html .= "<td>";
						$html .= "</td>";
						$html .=  "</tr>";
						// end lazada format
					} else if ($type == 2){

						// order id ok

						$order_id = $objPHPExcel->getActiveSheet()->getCell("A" . $row)->getValue();
						$customer_name = $objPHPExcel->getActiveSheet()->getCell("AK" . $row)->getValue();
						$customer_address = $objPHPExcel->getActiveSheet()->getCell("AM" . $row)->getValue();
						$contact_number = $objPHPExcel->getActiveSheet()->getCell("AL" . $row)->getValue();
						$tracking_number = $objPHPExcel->getActiveSheet()->getCell("D" . $row)->getValue();
						$shipping_company = $objPHPExcel->getActiveSheet()->getCell("E" . $row)->getValue();

						$shipping_price = $objPHPExcel->getActiveSheet()->getCell("AG" . $row)->getValue();
						$paid_price = $objPHPExcel->getActiveSheet()->getCell("AF" . $row)->getValue();
						$unit_price = $objPHPExcel->getActiveSheet()->getCell("P" . $row)->getValue();
							if(!$order_id){
							$order_id = $prev['order_id'];
							$customer_name = $prev['customer_name'];
							$customer_address = $prev['customer_address'];
							$contact_number = $prev['contact_number'];
							$shipping_company = $prev['shipping_company'];
							$tracking_number = $prev['tracking_number'];
						}


						$prev = [
							'order_id' => $order_id,
							'customer_name' => $customer_name,
							'customer_address' => $customer_address,
							'contact_number' => $contact_number,
							'shipping_company' => $shipping_company,
							'tracking_number' => $tracking_number,

						];

						$shipping_price = $shipping_price ? $shipping_price :0;
						$item_name = $objPHPExcel->getActiveSheet()->getCell("L" . $row)->getValue();
						$qty = $objPHPExcel->getActiveSheet()->getCell("Q" . $row)->getValue();
						$sku = $objPHPExcel->getActiveSheet()->getCell("K" . $row)->getValue();

						$rebate = $objPHPExcel->getActiveSheet()->getCell("U" . $row)->getValue();


						$prod = new Product();
						$item_name = str_replace("'","",$item_name);
						$checker = $prod->isProductExistBydesc(trim($item_name),1,true);
						$item_code='';
						$has_error = false;
						$cls = "";
						if(isset($checker->id) && $checker->id){
							$item_id = $checker->id;
							$item_code = $checker->item_code;

						} else {
							$has_error = true;
							$cls = "<span class='badge badge-danger'>Item name does not exists in our database.</span>";

						}



						if(!$has_error){
							$arr_to_insert_info[$order_id] = [
								'rebate' => $rebate,
								'customer_name' => $customer_name,
								'customer_address' => $customer_address,
								'customer_contact_number' => $contact_number
							];

							$arr_to_insert[$order_id][$checker->id][] = [
								'item_id' => $checker->id,
								'paid_price' => $paid_price,
								'unit_price' => $unit_price,
								'qty' => $qty,
								'shipping' => $shipping_price,
								'customer_name' => $customer_name,
								'customer_address' => $customer_address,
								'customer_contact_number' => $contact_number,
							];

							$wh_info_ar[] = ['unit_price' => $paid_price,'qty' => $qty,'client_po' => $order_id,'seller_sku'=>$sku,'item_name'=>trim($item_name),'tracking_number'=>$tracking_number,'shipping_company'=>$shipping_company,'rebate' => $rebate];


						}





						$html .= "<tr>";
						$html .= "<td style='width:250px;'>";
						$html .= "<span class='span-block'><strong>Order ID: </strong>$order_id </span>";
						$html .= "</td>";

						$html .= "<td  style='width:45%;'>";
						$html .= $item_name;
						$html .= $cls;
						$html .= "</td>";
						$html .= "<td>";
						$html .= "<span class='span-block'><strong>Billing Name: </strong>$customer_name </span>";
						$html .= "</td>";
						$html .= "<td>";
						$html .= "<span class='span-block'><strong>Paid Price: </strong>$unit_price </span>";
						$html .= "<span class='span-block'><strong>Shipping Price: </strong>$shipping_price </span>";
						$html .= "</td>";
						$html .= "<td>";
						$html .= "</td>";
						$html .=  "</tr>";
					} // end shopee format



				}
				$html .= "</table>";

			}
		} else {
			$html .= "Not uploaded";
		}
	} else {
		$html .= "Wrong file type.";
	}



