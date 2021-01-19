<?php
	// $user have all the properties and method of the current user


	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('m_terms_request')) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

?>
	<input type="hidden" id='MEMBER_LABEL' value='<?php echo MEMBER_LABEL; ?>'>
	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend>Request Information</legend>
							<div class="form-group">
								<label class="col-md-1 control-label" for="transaction_type">Transaction</label>
								<div class="col-md-3">
									<select name="transaction_type" id="transaction_type" class='form-control'>
										<option value="0">For all transaction</option>
										<option value="1">For single transaction</option>
									</select>
								</div>
								<label class="col-md-1 control-label" for="remarks">Remarks (Optional)</label>
								<div class="col-md-3">
									<input type="text" class='form-control' id='remarks' name='remarks'>
								</div>
								<div class="col-md-1"></div>
								<div class="col-md-3">
									<span class='span-block'><input type="checkbox" id='chkAll'> <label for='chkAll'>Check If all Members</label></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="member_id"><?php echo MEMBER_LABEL; ?></label>
								<div class="col-md-3">
									<div id="con_member">
									<input id="member_id" name="member_id"  type="hidden" style='width:100%;'>
									<span class="help-block">
										<div class="row">
											<div class="col-md-6">
												<?php echo MEMBER_LABEL; ?> name
											</div>
											<div class="col-md-6 text-right">
												<a href="#" id='multiple_selection'> Multiple Selection</a>
											</div>
										</div>
									</span>
									</div>
									<div id="con_member_multiple" style='display: none;'>
										<input id="member_search" name="member_search" placeholder='Client Search' type="text" class='form-control'>
										<span class="help-block">
										<div class="row">
											<div class="col-md-6">
												<?php echo MEMBER_LABEL; ?> name
											</div>
											<div class="col-md-6 text-right">
												<a href="#" id='single_selection'> Single Selection</a>
											</div>
										</div>

									</span>

									</div>


								</div>
								<label class="col-md-1 control-label" for="item_id">Item</label>
								<div class="col-md-3">
									<input id="item_id" name="item_id" class='selectitem'  type="hidden" style='width:100%;'>
									<span class="help-block">Item to adjust</span>
								</div>

								<label class="col-md-1 control-label" for="price">Current Price</label>
								<div class="col-md-3">
									<input id="price" name="price" class='form-control' type="text" disabled >
									<span class="help-block">SRP of the item</span>
								</div>

							</div>

							<div class="row" id='con_member_multiple2' style='display: none;'>
								<div class="col-md-1"></div>
								<div class="col-md-3">
									<div id="con_multiple_list" style='height: 300px;overflow-y: auto'>

									</div>
								</div>
								<div class="col-md-1"></div>
								<div class="col-md-3">
									<div id="con_multiple_list_final" style='height: 300px;overflow-y: auto'>
										<p><strong>Client List Added</strong></p>
										<table id='tblFinalMember' class='table'>

										</table>
									</div>
								</div>
								<div class="col-md-4"></div>


							</div>

							<div class="form-group">
								<label class="col-md-1 control-label" for="adjustment">Adjustment</label>
								<div class="col-md-3">
									<input id="adjustment" name="adjustment" class='form-control' type="text"  >
									<span class="help-block"><span class="help-block">Negative for discount (Ex. -100)</span></span>
								</div>
								<label class="col-md-1 control-label" for="adjusted_price">Adjusted Price</label>
								<div class="col-md-3">
									<input id="adjusted_price" name="adjusted_price" disabled class='form-control' type="text"  >
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="terms">Terms</label>
								<div class="col-md-3">
									<input id="terms" name="terms" value='0' class='form-control' type="text"  >
									<span class="help-block">Optional</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-1 control-label" for="quantity">Quantity</label>
								<div class="col-md-3">
									<input id="quantity" name="quantity" class='form-control' type="text"  >
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="type">Type</label>
								<div class="col-md-3">
									<select name="type" id="type" class='form-control'>
										<option value=""></option>
										<option value="1">For every</option>
										<option value="2">Greater than or equal</option>
									</select>
								</div>
								<div id='con_discount_type' style='display:none;'>
									<label class="col-md-1 control-label" for="discount_type">Discount Type</label>
									<div class="col-md-3">
										<select name="discount_type" id="discount_type" class='form-control'>
											<option value="0">By transaction </option>
											<option value="1">By item</option>
										</select>
									</div>
								</div>
							</div>

							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-1 control-label" for="button1id"></label>
								<div class="col-md-3">
									<button id='btnSubmit' style='display:none;' class="btn btn-default">Submit Request</button>
									<button id='btnAddCart' class="btn btn-default">Add Item</button>
								</div>
							</div>

						</fieldset>
					</form>
				</div>
			</div>
			<br>
			<div id='cart_item'>
			<table class="table table-condensed" id='tblTerms'>
				<thead>
				<tr>
					<th>Transaction Type</th>
					<th>Remarks</th>
					<th>Client</th>
					<th>Item</th>
					<th>Current Price</th>
					<th>Adjustment</th>
					<th>Adjusted Price</th>
					<th>Terms</th>
					<th>Quantity</th>
					<th>Price Type</th>
					<th>Discount Type</th>
					<th></th>
				</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
			<hr>
			<div><button id='btnSubmitAll' class='btn btn-primary'>Submit Request</button></div>
			</div>
		</div>
	</div><!-- end page content wrapper-->

	<script>
		$(function(){

			var is_multiple = false;

			$('body').on('click','#multiple_selection',function(){
				$('#con_member_multiple,#con_member_multiple2').show();
				$('#con_member').hide();
				is_multiple = true;
			});

			$('body').on('click','#single_selection',function(){
				$('#con_member_multiple,#con_member_multiple2').hide();
				$('#con_member').show();
				$('#con_multiple_list').html("");
				$('#con_multiple_list').show();
				$('#tblFinalMember').html("");
				is_multiple = false;
			});

			$('body').on('click','.memberRemove',function(){
				$(this).parents('tr').remove();
			});

			$('body').on('click','.btnAddMultiple',function(e){
				e.preventDefault();
				var con = $(this);
				var id = con.attr('data-id');
				var lastname = con.attr('data-lastname');
				var hitted = false;

				$('#tblFinalMember tr').each(function(){
					var row = $(this);
					var cur_id = row.attr('data-id');

					if(cur_id  == id){
						hitted = true;
					}

				});

				if(!hitted){
					$('#tblFinalMember').append("<tr data-lastname='"+lastname+"' data-id='"+id+"' ><td style='border-top:1px solid #ccc;'>"+lastname+"</td><td style='border-top:1px solid #ccc;'><i class='fa fa-close memberRemove cpointer'></i></td></tr>");
				} else {
					alert("Already added.");
				}


			});

			$('body').on('keyup','#member_search',function(){
				var s= $('#member_search').val();
				if(s){
					$.ajax({
						url:'../ajax/ajax_json.php',
						type:'POST',
						data: {functionName:'members2',q:s},
						dataType:'json',
						success: function(data){
							var html = "<table class='table'>";
							html += "<tr><th>Client Name</th><th></th></tr>";
							for(var i in data){
								html += "<tr><td>"+data[i].lastname+"</td><td><button data-lastname='"+data[i].lastname+"'  class='btn btn-default btn-sm btnAddMultiple' data-id='"+data[i].id+"' >Add</button></td></tr>";
							}
							html += "</table>";
							$('#con_multiple_list').html(html);

						},
						error:function(){

						}
					});
				} else {
					$('#con_multiple_list').html('');
				}

			});

			if(localStorage['tempToast']){
				tempToast("info",localStorage['tempToast'],"Info");
				localStorage.removeItem('tempToast');
			}
			updateCart();
			loadUnsave();
			function saveLocal(){
				localStorage['member_term_request_unsaved'] = $('#tblTerms > tbody').html();
			}
			function removeLocal(){
				localStorage.removeItem('member_term_request_unsaved');
			}
			function loadUnsave(){
				if(localStorage['member_term_request_unsaved']){
					alertify.confirm("You have unsaved request. Do you want to load it?",function(e){
						if(e){
							$('#tblTerms > tbody').html(localStorage['member_term_request_unsaved']);
							updateCart();
						}
					})
				}
			}

			function updateCart(){
				var cart_length = $("#tblTerms > tbody > tr").children().length;
				if(cart_length > 0){
					$('#cart_item').show();
				} else {
					$('#cart_item').hide();
				}
			}

			$('body').on('click','#btnAddCart',function(e){
				e.preventDefault();
				var transaction_type = $('#transaction_type').val();
				var remarks = $('#remarks').val();
				var member_id = $('#member_id').val();
				var item_id = $('#item_id').val();
				var price = $('#price').val();
				var adjustment = $('#adjustment').val();
				var adjusted_price = $('#adjusted_price').val();
				var terms = $('#terms').val();
				var qty = $('#quantity').val();
				var type = $('#type').val();
				var discount_type = $('#discount_type').val();
				var is_all = $('#chkAll').is(':checked');
				is_all = (is_all) ? 1 : 0;
				terms = (terms) ? terms : 0;
				if(is_multiple){
					$('#tblFinalMember tr').each(function(){
						var memrow = $(this);
						var member_text = memrow.attr('data-lastname');
						member_id = memrow.attr('data-id');
						if(item_id){
							var item_data = $('#item_id').select2('data');
							var item_text = item_data.text;
							var splitted_item = item_text.split(':');
						}


						var transaction_type_text=$('#transaction_type :selected').text();
						var type_text=$('#type :selected').text();
						var discount_type_text=$('#discount_type :selected').text();

						if(type == 1){
							discount_type_text ='N/A';
						}



						if(item_id && qty && type){
							price = (price) ? price : 0;
							adjusted_price = (adjusted_price) ? adjusted_price : 0;
							adjustment = (adjustment) ? adjustment : 0;
							if(adjustment.indexOf("%") > 0){
								adjustment = replaceAll(adjustment,"%",'');
								adjustment = adjustment / 100;
								adjustment = price * adjustment;
							}
							var form_data = "";
							form_data += " data-transaction_type='"+transaction_type+"' ";
							form_data += " data-remarks='"+remarks+"' ";
							form_data += " data-member_id='"+member_id+"' ";
							form_data += " data-is_all='"+is_all+"' ";
							form_data += " data-item_id='"+item_id+"' ";
							form_data += " data-price='"+price+"' ";
							form_data += " data-adjustment='"+adjustment+"' ";
							form_data += " data-adjusted_price='"+adjusted_price+"' ";
							form_data += " data-adjusted_price='"+adjusted_price+"' ";
							form_data += " data-terms='"+terms+"' ";
							form_data += " data-qty='"+qty+"' ";
							form_data += " data-type='"+type+"' ";
							form_data += " data-discount_type='"+discount_type+"' ";

							var to_append = "<tr "+form_data+">";
							to_append += "<td>"+transaction_type_text+"</td>";
							to_append += "<td>"+remarks+"</td>";
							to_append += "<td>"+ member_text +"</td>";
							to_append += "<td>"+splitted_item[1]+"<br>"+splitted_item[2]+"</td>";
							to_append += "<td>"+number_format(price,2)+"</td>";
							to_append += "<td>"+number_format(adjustment,2)+"</td>";
							to_append += "<td>"+number_format(adjusted_price,2)+"</td>";
							to_append += "<td>"+terms+"</td>";
							to_append += "<td>"+qty+"</td>";
							to_append += "<td>"+type_text+"</td>";
							to_append += "<td>"+discount_type_text+"</td>";
							to_append += "<td><button class='btn btn-danger btn-sm btnRemove'><i class='fa fa-close'></i></button></td>";
							to_append += "</tr>";

							$('#tblTerms > tbody').append(to_append);



						} else {
							tempToast("error",'Please complete the form',"Warning");
						}
					});

					$('#transaction_type').val('0');
					$('#remarks').val('');

					$('#item_id').select2('val',null);
					$('#price').val('');
					$('#adjustment').val('');
					$('#adjusted_price').val('');
					$('#terms').val('');
					$('#quantity').val('');
					$('#type').val('');
					$('#discount_type').val('');
					$('#chkAll').attr('checked',false);
					saveLocal();
					updateCart();
					$('#con_multiple_list').html("");

					$('#member_search').val("");
					$('#tblFinalMember').html("");

				} else {
					if(item_id){
						var item_data = $('#item_id').select2('data');
						var item_text = item_data.text;
						var splitted_item = item_text.split(':');
					}

					if(member_id){
						var member_data = $('#member_id').select2('data');
						var member_text = member_data.text;
					}

					if(is_all){
						member_text = 'All clients';
					}
					var transaction_type_text=$('#transaction_type :selected').text();
					var type_text=$('#type :selected').text();
					var discount_type_text=$('#discount_type :selected').text();

					if(type == 1){
						discount_type_text ='N/A';
					}



					if((is_all || member_id) && item_id && qty && type){
						price = (price) ? price : 0;
						adjusted_price = (adjusted_price) ? adjusted_price : 0;
						adjustment = (adjustment) ? adjustment : 0;
						if(adjustment.indexOf("%") > 0){
							adjustment = replaceAll(adjustment,"%",'');
							adjustment = adjustment / 100;
							adjustment = price * adjustment;
						}
						var form_data = "";
						form_data += " data-transaction_type='"+transaction_type+"' ";
						form_data += " data-remarks='"+remarks+"' ";
						form_data += " data-member_id='"+member_id+"' ";
						form_data += " data-is_all='"+is_all+"' ";
						form_data += " data-item_id='"+item_id+"' ";
						form_data += " data-price='"+price+"' ";
						form_data += " data-adjustment='"+adjustment+"' ";
						form_data += " data-adjusted_price='"+adjusted_price+"' ";
						form_data += " data-adjusted_price='"+adjusted_price+"' ";
						form_data += " data-terms='"+terms+"' ";
						form_data += " data-qty='"+qty+"' ";
						form_data += " data-type='"+type+"' ";
						form_data += " data-discount_type='"+discount_type+"' ";

						var to_append = "<tr "+form_data+">";
						to_append += "<td>"+transaction_type_text+"</td>";
						to_append += "<td>"+remarks+"</td>";
						to_append += "<td>"+ member_text +"</td>";
						to_append += "<td>"+splitted_item[1]+"<br>"+splitted_item[2]+"</td>";
						to_append += "<td>"+number_format(price,2)+"</td>";
						to_append += "<td>"+number_format(adjustment,2)+"</td>";
						to_append += "<td>"+number_format(adjusted_price,2)+"</td>";
						to_append += "<td>"+terms+"</td>";
						to_append += "<td>"+qty+"</td>";
						to_append += "<td>"+type_text+"</td>";
						to_append += "<td>"+discount_type_text+"</td>";
						to_append += "<td><button class='btn btn-danger btn-sm btnRemove'><i class='fa fa-close'></i></button></td>";
						to_append += "</tr>";

						$('#tblTerms > tbody').append(to_append)
						$('#transaction_type').val('0');
						$('#remarks').val('');

						$('#item_id').select2('val',null);
						$('#price').val('');
						$('#adjustment').val('');
						$('#adjusted_price').val('');
						$('#terms').val('');
						$('#quantity').val('');
						$('#type').val('');
						$('#discount_type').val('');
						$('#chkAll').attr('checked',false);

						saveLocal();
						updateCart();
					} else {
						tempToast("error",'Please complete the form',"Warning");
					}

				}


			});
			$('body').on('click','.btnRemove',function(){
				$(this).parents('tr').remove();
				updateCart();
			});
			$('body').on('click','#btnSubmitAll',function(){
				var con = $(this);
				button_action.start_loading(con);
				var arr = [];
				$('#tblTerms tbody tr').each(function(){
					var row = $(this);
					var transaction_type = row.attr('data-transaction_type')
					var remarks = row.attr('data-remarks')
					var member_id = row.attr('data-member_id')
					var is_all = row.attr('data-is_all')
					var item_id = row.attr('data-item_id')
					var price = row.attr('data-price')
					var adjustment = row.attr('data-adjustment')
					var adjusted_price = row.attr('data-adjusted_price')
					var terms = row.attr('data-terms')
					var qty = row.attr('data-qty')
					var type = row.attr('data-type')
					var discount_type = row.attr('data-discount_type')
					arr.push(
						{
							transaction_type:transaction_type,
							remarks:remarks,
							member_id:member_id,
							item_id:item_id,
							is_all:is_all,
							price:price,
							adjustment:adjustment,
							adjusted_price:adjusted_price,
							terms:terms,
							qty:qty,
							type:type,
							discount_type:discount_type,
						}
					)
				});
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'batchSubmitTerms', arr:JSON.stringify(arr)},
				    success: function(data){
					alert(data);
					    removeLocal();
					    location.href='member_term_request.php';
				    },
				    error:function(){

				    }
				});

			});
			var MEMBER_LABEL = $('#MEMBER_LABEL').val();
			$("#member_id").select2({
				placeholder: 'Search ' + MEMBER_LABEL,
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
							functionName:'members'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			$('body').on('change','#item_id',function(){
				if($(this).val()){
					var data = $(this).select2('data');
					var v = data.text;
					var splitted = v.split(':');
					var price = splitted[3];
					$('#price').val('Loading price...');

					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'getAdjustmentPrice',branch_id:localStorage['branch_id'],item_id:$(this).val(),member_id:0,qty:1},
						success: function(data){
							var dt = JSON.parse(data);
							var cur = dt.data;
							$('#btnAdd').html('Add Item');
							var splitted2 = cur.split(':');
							price = parseFloat(price) + parseFloat(splitted2[0]);
							$('#price').val(price);
						},
						error:function(){
							alert('It seems like you have a very slow internet connection.');
							location.reload();
						}
					});

				} else {
					$('#price').val('');
				}
			});
			$('body').on('keyup','#quantity,#terms',function(){
				var v = $(this).val();
				if(isNaN(v)){
					$(this).val('');
				}
			});



			$('body').on('blur','#adjustment',function(){
				var v = $(this).val();
				var member_id = $('#member_id').val();
				var item_id = $('#item_id').val();
				var price = $('#price').val();
				if(v.indexOf("%") > 0){
					v = replaceAll(v,"%",'');
					v = v / 100;
					v = price * v;
				}
				var is_all = $('#chkAll').is(':checked');
				is_all = (is_all) ? 1 : 0;
				if(!is_multiple){
					if((!member_id && !is_all) || !item_id){
						alertify.alert("Please add "+MEMBER_LABEL+" and item first.");
						$(this).val('');
						return;
					}
				}

				if(v && !isNaN(v)){

					var adjusted_price = parseFloat(v) + parseFloat(price);
					$('#adjusted_price').val(adjusted_price.toFixed(2));
				} else {
					$(this).val('');
					$('#adjusted_price').val(0);
				}
			});
			$('body').on('click','#type',function(){
				if($(this).val() == 2){
					$('#con_discount_type').show()
				} else {
					$('#con_discount_type').hide()
				}
			});
			$('body').on('click','#btnSubmit',function(e){
				e.preventDefault();
				var btncon = $(this);
				var btnoldval = btncon.html();
				btncon.attr('disabled',true);
				btncon.html('Loading...');

				var member_id = $('#member_id').val();
				var item_id = $('#item_id').val();
				var adjustment = $('#adjustment').val();
				var price = $('#price').val();
				if(adjustment.indexOf("%") > 0){
					adjustment = replaceAll(adjustment,"%",'');
					adjustment = adjustment / 100;
					adjustment = price * adjustment;
				}
				var adjusted_price = $('#adjusted_price').val();
				var terms = $('#terms').val();
				var qty = $('#quantity').val();
				var type = $('#type').val();
				var transaction_type = $('#transaction_type').val();
				var discount_type = $('#discount_type').val();
				var remarks = $('#remarks').val();
				if(type == 1){
					discount_type = 0;
				}

				var is_all = $('#chkAll').is(':checked');
				is_all = (is_all) ? 1 : 0;


				if((is_all || member_id) && item_id && qty && type){


					alertify.confirm("Are you sure you want to submit this request?",function(e){
						if(e){
							$.ajax({
							    url:'../ajax/ajax_query2.php',
							    type:'POST',
							    data: {
								    functionName:'requestMemberTerms',
								    member_id:member_id,
								    item_id:item_id,
								    adjustment:adjustment,
								    terms:terms,
								    qty:qty,
								    transaction_type:transaction_type,
								    type:type,
								    discount_type:discount_type,
								    remarks:remarks,
								    is_all:is_all
							    },
							    success: function(data){

									location.href='member_term_request.php';
								    localStorage['tempToast'] = "Request submitted successfully.";

							    },
							    error:function(){

								    btncon.attr('disabled',false);
								    btncon.html(btnoldval);
							    }
							});
						}
					});
				} else {
					alertify.alert("You have invalid data on your request.");
					btncon.attr('disabled',false);
					btncon.html(btnoldval);
				}
			});


			$('body').on('change','#chkAll',function(){
				var isCheck = $('#chkAll').is(':checked');
				var lbl = (isCheck) ? "Are you sure you want this request to apply for all members?": "Are you sure you want this request to apply to single client?";

				alertify.confirm(lbl,function(e){
					if(e){
						if(isCheck){
							$('#member_id').select2("enable",false)

						} else {
							$('#member_id').select2("enable",true)

						}
					} else {
						if(isCheck){
							$( "#chkAll" ).prop( "checked", false );
						} else {
							$( "#chkAll" ).prop( "checked", true );
						}
					}

				});

			});

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>