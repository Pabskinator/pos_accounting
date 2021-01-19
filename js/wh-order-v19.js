var vm = new Vue({
	el: '#WarehouseController', data: {
		container: {
			requestView: true,
			approvalView: false,
			showApproved: false,
			showWarehouse: false,
			showShipping: false,
			showLog: false,
			showPickup: false,
			showService: false
		},
		current_status_order:0,
		current_page:1,
		current_credit_list : [],
		printWithPrice: false,
		request: {
			spec_station_id: 0,
			spec_sales_type: 0,
			member_id: '',
			branch_id: '',
			branch_id_to: '',
			shipping_company_id: '',
			remarks: '',
			item_id: '',
			qty: '',
			orig_qty: '',
			preferred_unit: '',
			client_po: '',
			for_pickup: '',
			is_reserve: '',
			from_pending: 0,
			station_id: 0,
			gen_sales_type: '0',
			addtl_disc:'',
			price_group_id:'0',
			is_surplus:'0',
			group_adjustment:[],
			group_adjustment_total:0
		},
		is_hold: 0,
		charge_label: 0,
		member_info:{personal_address:'',credit_limit:'',terms:'',contact_number:'',region:''},
		nav_active:'active',
		nav: {request: true, approve:false,warehouse:false,shipping:false,del: false,pickup:false, service:false},
		items: [],
		orders: [],
		orders_log: [],
		orders_pickup: [],
		orders_service: [],
		orderDetails: [],
		scheduleDates: [],
		rackings: [],
		bundles: [],
		group_adjustment_list:[],
		success: false,
		current_order_obj: [],
		current_order_det: [],
		current_order: 0,
		current_status: 0,
		current_isScheduled: 0,
		adjustment_default: 1,
		current_stock_out: 0,
		current_canBeResched: 1,
		details_ready: false,
		dates_ready: false,
		insufficient: false,
		print_data: [],
		invoice: 0,
		sv: 0,
		dr: 0,
		pr: 0,
		sr: 0,
		ts: 0,
		ajaxRequest: false,
		showLoading: false,
		shippingSearchTxt: '',
		search_text: '',
		schedule_date: '',
		re_schedule_date: '',
		is_member: '',
		user_member_id: '',
		is_priority: 0,
		truck_id: '',
		log_truck_id: '',
		helper_id: [],
		driver_id: 0,
		trucks: [],
		helpers: [],
		drivers: [],
		re_truck_id: '',
		batch_truck_id: '',
		re_driver_id: '',
		batch_driver_id: '',
		re_for_pick_up: 0,
		re_helper_id: '',
		countdel: 0,
		countpickup: 0,
		countservice: 0,
		log_from: '',
		log_to: '',
		log_search: '',
		log_from_pickup: '',
		log_to_pickup: '',
		log_search_pickup: '',
		new_item_order: '',
		new_qty_order: '',
		order_updating: 0,
		branch_id_filter: 0,
		salestype_filter:0,
		for_pickup_filter: 0,
		assemble_filter: 0,
		invoice_prefix: '',
		dr_prefix: '',
		pr_prefix: '',
		invoice_label: '',
		dr_label: '',
		pr_label: '',
		CASHIER_HELPER: '0',
		ORDER_FOR_ALL: '0',
		del_filter_type: '',
		pickup_filter_type: '',
		my_auth: 0,
		order_limit: 30,
		cart_item_ctr: 0,
		current_user_id: 0,
		PENDING_MEMBER: 0,
		reserve_only: 0,
		different_unit: 0,
		multiplier_qty: [],
		dif_qty:1,
		order_id_to_use: '',
		backload_data: [],
		serials: [],
		order_info:[],
		member_pending_items:[],
		pending_counts: {for_approval: 0, shipping: 0, warehouse: 0},
		warehouse_dt1:'',
		warehouse_dt2:'',
		warehouse_showall:false,
		surplus_rack:0,
		surplus_allowed:0,
		ADDTL_VIEW:0,
		override_payment_date:'',
		bc_scan: {serial:{item_id:0,cnt:0,qty:0},request_order:false},
		filters: {
			forApproval: function(order) {
				var ret = order.status == 1 && order.is_reserve == 0 && order.for_approval_walkin == 0;
				if(this.branch_id_filter.value != 0 || this.salestype_filter.value != 0 || this.for_pickup_filter.value || this.assemble_filter.value) {
					if(this.branch_id_filter.value) {
						ret = ret && order.branch_id == this.branch_id_filter.value;
					}
					if(this.salestype_filter.value) {

						ret = ret && order.salestype == this.salestype_filter.value;
					}
					if(this.for_pickup_filter.value == 1) {
						ret = ret && order.for_pickup == 'For Pickup';
					} else if(this.for_pickup_filter.value == 2) {
						ret = ret && order.for_pickup == '';
					}else if(this.for_pickup_filter.value == 3) {
						ret = ret && order.for_pickup == 'Cashier Transaction';
					}else if(this.for_pickup_filter.value == 4) {
						ret = ret && order.for_pickup != 'Cashier Transaction';
					}
					if(this.assemble_filter.value == 1) {
						ret = ret && order.has_assemble_item == 1;
					} else if(this.assemble_filter.value == 2) {
						ret = ret && order.has_assemble_item == 0;
					}

					return ret;
				}
				return order.status == 1 && order.is_reserve == 0 && order.for_approval_walkin == 0;
			}, forApprovalReserve: function(order) {
				var ret = order.status == 1 && order.is_reserve == 1 && order.reserved_date != 0;
				return ret;
			}, forApprovalMemberReserved: function(order) {
				var ret = order.status == 1 && order.is_reserve == 1 && order.reserved_date != 0;
				return ret;
			}, forApprovalReservePending: function(order) {
				var ret = order.status == 1 && order.is_reserve == 1 && order.reserved_date == 0;
				return ret;
			}, forApprovalMemberReservedPending: function(order) {
				var ret = order.status == 1 && order.is_reserve == 1 && order.reserved_date == 0;
				return ret;
			}, forApprovalWalkin: function(order) {
				var ret = order.status == 1 && order.for_approval_walkin == 1;
				return ret;
			}, approved: function(order) {
				var ret = order.status == 3;
				if(this.branch_id_filter.value != 0 || this.salestype_filter.value != 0 || this.for_pickup_filter.value || this.assemble_filter.value) {
					if(this.branch_id_filter.value) {
						ret = ret && order.branch_id == this.branch_id_filter.value;
					}
					if(this.salestype_filter.value) {

						ret = ret && order.salestype == this.salestype_filter.value;
					}
					if(this.for_pickup_filter.value == 1) {
						ret = ret && order.for_pickup == 'For Pickup';
					} else if(this.for_pickup_filter.value == 2) {
						ret = ret && order.for_pickup == '';
					}else if(this.for_pickup_filter.value == 3) {
						ret = ret && order.for_pickup == 'Cashier Transaction';
					}
					if(this.assemble_filter.value == 1) {
						ret = ret && order.has_assemble_item == 1;
					} else if(this.assemble_filter.value == 2) {
						ret = ret && order.has_assemble_item == 0;
					}
					return ret;
				}
				return order.status == 3;
			}, shipping: function(order) {
				return order.status == 2;
			}, forApprovalMember: function(order) {
				return order.status == 1 && order.member_id == this.user_member_id.value;
			}, approvedMember: function(order) {
				return order.status == 3 && order.member_id == this.user_member_id.value;
			}, shippingMember: function(order) {
				return order.status == 2 && order.member_id == this.user_member_id.value;
			}
		}
	}, ready: function() {
		localStorage.removeItem('op_payment_cheque');
		localStorage.removeItem('op_payment_credit');
		localStorage.removeItem('op_payment_bt');
		localStorage.removeItem('op_payment_cash');

		this.surplus_rack = $('#SURPLUS_RACK').val();
		this.CASHIER_HELPER = $('#CASHIER_HELPER').val();
		if($('#get_from_surplus').length > 0){
			this.ADDTL_VIEW = 0;
		} else {
			this.ADDTL_VIEW = $('#ADDTL_VIEW').val();
		}

		this.charge_label = $('#CHARGE_LABEL').val();

		this.ORDER_FOR_ALL = $('#ORDER_FOR_ALL').val();
		var order_limit = $('#ORDER_LIMIT').val();
		this.adjustment_default = $('#ADJUSTMENT_DEFAULT').val();
		this.reserve_only = $('#RESERVE_ONLY').val();
		this.is_member = $('#is_member').val();
		this.user_member_id = $('#user_member_id').val();
		this.user_fullname = $('#user_fullname').val();
		this.invoice_prefix = $('#INVOICE_PREFIX').val();
		this.dr_prefix = $('#DR_PREFIX').val();
		this.pr_prefix = $('#PR_PREFIX').val();
		this.invoice_label = $('#INVOICE_LABEL').val();
		this.dr_label = $('#DR_LABEL').val();
		this.pr_label = $('#PR_LABEL').val();
		this.my_auth = $('#APPROVAL_AUTH').val();
		this.PENDING_MEMBER = $('#PENDING_MEMBER').val();
		this.current_user_id = $('#current_user_id').val();
		this.different_unit = $('#DIFFERENT_UNIT').val();

		this.order_limit = (order_limit) ? order_limit : 30;
	//	this.order_limit = 30;
		var mem_select2 = $('#member_id');
		var op_member_id = $('#op_member_id');
		var branch_select2 = $('#branch_id');
		var branch_to_select2 = $('#branch_id_to');
		var shipping_select2 = $('#shipping_company_id');
		var update_shipping_select2 = $('#update_shipping_company');
		var helper_select2 = $('#helper_id');
		var my_client_only = 1;
		//this.pending_approved.length = 12;
		if(this.reserve_only == 1){
			this.request.is_reserve = 1;
			$('#fr0').attr('disabled',true);
			$('#fr1').attr('disabled',true);

		}
		if(this.CASHIER_HELPER == 1){
			this.request.for_pickup = 2;
			$('#fp0').attr('disabled',true);
			$('#fp2').attr('disabled',true);
			$('#fp1').attr('disabled',true);
			my_client_only = 0;
		}
		if(this.ORDER_FOR_ALL == 1){

			my_client_only = 0;
		}
		branch_select2.select2({
			'placeholder': 'From', allowClear: true
		});
		branch_to_select2.select2({
			'placeholder': 'To', allowClear: true
		});
		shipping_select2.select2({
			'placeholder': 'Shipping Company', allowClear: true
		});
		update_shipping_select2.select2({
			'placeholder': 'Shipping Company', allowClear: true
		});
		helper_select2.select2({
			'placeholder': 'Select Helper', allowClear: true
		});
		/*mem_select2.select2({
			'placeholder' :'Select Member',
			allowClear: true
		});*/

		var MEMBER_LABEL = $('#MEMBER_LABEL').val();

		mem_select2.select2({
			placeholder: 'Search ' + MEMBER_LABEL, allowClear: true, minimumInputLength: 2,

			ajax: {
				url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
					return {
						q: term, functionName: 'members', my_client: my_client_only
					};
				}, results: function(data) {
					return {
						results: $.map(data, function(item) {

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

		op_member_id.select2({
			placeholder: 'Search ' + MEMBER_LABEL, allowClear: true, minimumInputLength: 2, ajax: {
				url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
					return {
						q: term, functionName: 'members'
					};
				}, results: function(data) {
					return {
						results: $.map(data, function(item) {
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
		$('#override_payment_date').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#override_payment_date').datepicker('hide');
			vm.override_payment_date = $('#override_payment_date').val();
		});

		if(this.is_member == 1) {
			this.request.member_id = this.user_member_id;
			//mem_select2.select2('val',this.user_member_id);
			mem_select2.select2('disable', true);
			mem_select2.select2('data', {id: this.user_member_id, text: this.user_fullname});
			//setup his branch
		}
		$('#schedule_date').datepicker({
			autoclose: true
		}).on('changeDate', function(ev) {
			$('#schedule_date').datepicker('hide');
		});
		$('#re_schedule_date').datepicker({
			autoclose: true
		}).on('changeDate', function(ev) {
			$('#re_schedule_date').datepicker('hide');
		});

		$('#warehouse_dt1').datepicker({
			autoclose: true
		}).on('changeDate', function(ev) {
			$('#warehouse_dt1').datepicker('hide');
		});
		$('#warehouse_dt2').datepicker({
			autoclose: true
		}).on('changeDate', function(ev) {
			$('#warehouse_dt2').datepicker('hide');
		});

		var vuecon = this;
		//vuecon.fetchedOrder();
		vuecon.getLayout();
		vuecon.getInvoiceDrPr();
		vuecon.unsavedRequest();
		vuecon.getTrucks();
		vuecon.orderCount();

		if(vuecon.different_unit == 1){
			vuecon.hasUnitGroup();
			$('body').on ('change','#item_id',function(){

				var id = $('#item_id').val();
				vuecon.surplus_allowed = 0;

				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getItemInfo',item_id:id,branch_id:vuecon.request.branch_id},
					success: function(data){

						if(vuecon.surplus_rack == 1){
							vuecon.surplus_allowed = data.surplus.allowed;

						}
						vuecon.multiplier_qty = data.units;
						vuecon.dif_qty = 1;
					},
					error:function(){

					}
				})
			});
		}

		$('#getpricemodal').on('shown.bs.modal', function() {
			$(document).off('focusin.modal');
		});

		$('body').on('click','.paging',function(){
			var page = $(this).attr('page');
			vuecon.current_page = page;
			vuecon.fetchedOrder(vuecon.current_status_order);
		});

		localStorage.removeItem("scan");
		vuecon.barcodeListener();
		$('body').on('change','#chkToggleManifest',function(){
			if($('#chkToggleManifest').is(':checked')){
				$('.chkCarrier').each(function(){
					$(this).prop('checked',true);
				});
			} else {
				$('.chkCarrier').each(function(){
					$(this).prop('checked',false);
				});
			}
		});
	// END CREATED
	}, computed: {
		validation: function() {
			//	member_id : !! this.request.member_id,
			return {
				branch_id: !!this.request.branch_id,
				item_id: !!this.request.item_id,
				for_pickup: this.request.for_pickup !== "",
				is_reserve: this.request.is_reserve !== "",
				qty: !!this.request.qty || isNaN(this.request.qty)
			}
		}, validateInvoice: function(order) {
			return (order.stock_out == 1 && order.payment_id != 0 && order.invoice != 0);
		}, validateDr: function(order) {
			return (order.stock_out == 1 && order.payment_id != 0 && order.dr != 0);
		}, isValid: function() {
			var validation = this.validation;
			return Object.keys(validation).every(function(val) {
				return validation[val];
			}
			);
		}, isSuccess: function() {
			return this.success;
		}, cur_terminal_id: function() {
			return localStorage['terminal_id'];
		}, totalAmount: function() {
			var total = 0;
			for(var i in this.items) {
				total = parseFloat(total) + (this.items[i].qty * this.items[i].price) + parseFloat(this.items[i].adjustmentmem);
			}
			return number_format(total, 2);
		}, totalAdjustment: function() {
			var adj = 0;
			for(var i in this.items) {
				adj = parseFloat(adj) + parseFloat(this.items[i].adjustmentmem);
			}
			return number_format(adj, 2);
		}, grossAmount: function() {
			var g = 0;
			for(var i in this.items) {
				g = parseFloat(g) + (this.items[i].qty * this.items[i].price);
			}
			return number_format(g, 2);
		}, pending_for_approval: function() {
			return (this.is_member == 0) ? this.orders.filter(this.filters.forApproval) : this.orders.filter(this.filters.forApprovalMember);
		}, pending_for_approval_reserved: function() {
			return (this.is_member == 0) ? this.orders.filter(this.filters.forApprovalReserve) : this.orders.filter(this.filters.forApprovalMemberReserved);
		}, pending_for_approval_reserved_pending: function() {
			return (this.is_member == 0) ? this.orders.filter(this.filters.forApprovalReservePending) : this.orders.filter(this.filters.forApprovalMemberReservedPending);
		}, pending_for_approval_walkin: function() {
			return (this.is_member == 0) ? this.orders.filter(this.filters.forApprovalWalkin) : [];
		}, pending_approved: function() {
			return (this.is_member == 0) ? this.orders.filter(this.filters.approved) : this.orders.filter(this.filters.approvedMember);
		}, pending_shipping: function() {
			return (this.is_member == 0) ? this.orders.filter(this.filters.shipping) : this.orders.filter(this.filters.shippingMember);
		}, current_item_status: function() {
			return this.current_status;
		}, current_item_isScheduled: function() {
			return this.current_isScheduled;
		}, current_item_stock_out: function() {
			return this.current_stock_out;
		}, current_item_canBeResched: function() {
			return this.current_canBeResched;
		}, current_item_truck_id: function() {
			return this.current_truck_id;
		}, current_is_member: function() {
			return this.is_member;
		}, current_member_id: function() {
			return this.user_member_id;
		}, computed_order: function() {
			return this.current_order_det;
		}, same_branch: function() {
			return this.current_order_det.branch_id == localStorage['branch_id'] || (this.current_order_det.member_id == 0 && this.current_order_det.to_branch_id == localStorage['branch_id'] ) ;
		}, current_updating: function() {
			return this.order_updating;
		}, current_auth: function() {
			var auth = this.my_auth;
			var cur_branch = this.current_order_det.branch_id;
			if(auth.indexOf(',') > 0) {
				auth = auth.split(',');
				if(auth.length > 0) {
					for(var i in auth) {
						if(auth[i] == cur_branch) {
							return true;
						}
					}
				}
			} else {
				if(auth == cur_branch) {
					return true;
				}
			}
			return false;
		}
	}, methods: {
		hasUnitGroup: function(){
			var vuecon = this;


				$('body').on('change','#new_item_order',function(){
					var id = $('#new_item_order').val();


					$.ajax({
						url:'../ajax/ajax_wh_order.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getItemInfo',item_id:id,branch_id:vuecon.current_order_det.branch_id},
						success: function(data){

							vuecon.multiplier_qty = data.units;
							vuecon.dif_qty = 1;
						},
						error:function(){

						}
					})

				});


		},
overridePrice : function(o){
	if(parseFloat(o.override_price) >0){
		o.member_adjustment = (parseFloat(replaceAll(o.qty,",","")) * (parseFloat(o.override_price) - parseFloat(replaceAll(o.adjusted_price,",","")))).toFixed(2);
	} else {

	}
},
barcodeListener: function(){
	 var millis = 300;
	 var self = this;
	document.addEventListener('keydown',function(event)
	{


		if(event.ctrlKey && event.keyCode==74)
		{
			event.preventDefault();
			console.log('Entered ctrl+j');
		}

	});
	document.onkeypress = function(e) {
		e = e || window.event;
		var charCode = (typeof e.which == "number") ? e.which : e.keyCode;

		if(localStorage.getItem("scan") && localStorage.getItem("scan") != 'null') {
			localStorage.setItem("scan", localStorage.getItem("scan") + String.fromCharCode(charCode));
		} else {
			localStorage.setItem("scan", String.fromCharCode(charCode));
			setTimeout(function() {
				localStorage.removeItem("scan");
			}, millis);
		}
		if (e.keyCode === 13) {
			if(localStorage.getItem("scan").length >= 8) {
				self.barcodeScan(localStorage.getItem("scan"));
				localStorage.removeItem("scan");
			}
		}

	}
},
barcodeScan: function(key) {
		var self = this;
		if(self.bc_scan.serial.item_id){

				try {
					var without_serial = self.bc_scan.serial.cnt;
					var with_serial = self.bc_scan.serial.qty - self.bc_scan.serial.cnt;
					var next = with_serial;

					self.serials[next].serial_no = key;
					self.bc_scan.serial.cnt--;
					console.log(self.bc_scan.serial.cnt);
					if(self.bc_scan.serial.cnt === 0){
						self.saveSerials();
					}

				} catch(e){
					console.log("Serial unknown index. Focus on textbox you want to update");
				}
		}
	},
	showBatch: function(){
		var arr = [];
		$('.chkBatch').each(function(){
			var chk = $(this);

			if(chk.is(':checked')){
				arr.push(chk.attr('data-id'))
			}
		});
		if(arr.length){
			var self = this;
			 self.batch_truck_id = '';
			self.batch_driver_id ='';
			$('#batch_date').val('');
			$('#myModalBatch').modal('show');
		} else {
			tempToast('error', "<p>Please choose transactions to process first..</p>", "<h4>Error!!</h4>");
		}


	},
	declineBatch: function(){
		var arr = [];
		$('.chkBatch').each(function(){
			var chk = $(this);

			if(chk.is(':checked')){
				arr.push(chk.attr('data-id'))
			}
		});
		var self = this;
		alertify.confirm("Are you sure you want to decline this request",function(e){
			if(e){
				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					data: {functionName:'batchDecline',arr:JSON.stringify(arr)},
					success: function(data){

						tempToast('info', "<p>"+data+"</p>", "<h4>Information!</h4>");
						self.fetchedOrder(1);
						self.orderCount();
						$('#myModalBatch').modal('hide');
					},
					error:function(){

					}
				});
			}
		});

	},
	submitBatch: function(){
		var arr = [];
			$('.chkBatch').each(function(){
				var chk = $(this);

				if(chk.is(':checked')){
					arr.push(chk.attr('data-id'))
				}
			});
		var self = this;
		var truck_id = self.batch_truck_id;
		var driver_id = self.batch_driver_id;
		var sched = $('#batch_date').val();
		$.ajax({
		    url:'../ajax/ajax_wh_order.php',
		    type:'POST',
		    data: {functionName:'batchApprove',truck_id:truck_id,driver_id:driver_id,sched:sched,arr:JSON.stringify(arr)},
		    success: function(data){

			    tempToast('info', "<p>"+data+"</p>", "<h4>Information!</h4>");
			    self.fetchedOrder(1);
			    self.orderCount();
			    $('#myModalBatch').modal('hide');
		    },
		    error:function(){

		    }
		});

	},

	printCarrierManifest: function(){
		var vm = this;
		var arr = [];
		if($('#salestype_filter2').val()){
			var type_name = $('#salestype_filter2 > option:selected').text();
			$('.chkCarrier').each(function(){
				var chk = $(this);

				if(chk.is(':checked')){
					arr.push(chk.attr('data-id'))
				}
			});

			if(arr.length){
				alertify.confirm("Are you sure you want to print this manifest?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_wh_order.php',
							type:'POST',
							data: {functionName:'showCarrierManifest',type_name:type_name,arr: JSON.stringify(arr)},
							success: function(data){
								vm.popUpPrintWithStyle(data);
							},
							error:function(){

							}
						})
					}
				});

			} else {
				tempToast('error', "<p>Please choose transactions to process first..</p>", "<h4>Error!!</h4>");
			}
		} else {
			tempToast('error', "<p>Select Store Type First</p>", "<h4>Error!!</h4>");
		}



	},
	warehouseSearchRecord: function(){
		var vm = this;
		vm.warehouse_showall = false;
		vm.fetchedOrder(3)

	}, warehouseShowAll: function(){
		var vm = this;
		vm.warehouse_showall = true;
		vm.warehouse_dt1 = '';
		vm.warehouse_dt2 = '';
		vm.fetchedOrder(3);
	},
	total_current_adjustment: function(order) {
		var adjustment = order.member_adjustment;
		if(adjustment.indexOf("%") > 0){
			adjustment = replaceAll(adjustment,"%",'');
			adjustment = adjustment / 100;
			adjustment = order.adjusted_price * adjustment;
			adjustment = adjustment * order.qty;
		}
		if(this.adjustment_default == 1){
			return (order.total * 1) + (adjustment * 1);
		} else {
			return (order.total * 1) - (adjustment * 1);
		}

	},
		saveTablePending: function(){
			var vuecon = this;
			var member_pending = vuecon.member_pending_items;
			var request = vuecon.request;
			var btn = $('#btnSavePendingMember');

			if(request.member_id && request.branch_id){
				button_action.start_loading(btn);
				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					dataType:'json',
					data: {functionName : 'checkStockPending',branch_id: request.branch_id,member_id:request.member_id,pending:JSON.stringify(member_pending)},
					success: function(data){
						if(data){
							vuecon.member_pending_items = [];
							$('#myModalMemberPending').modal('hide');
							for(var i in data){
								vuecon.items.push(data[i]);
							}
							vuecon.disableMemberAndBranch();
							button_action.end_loading(btn);
						} else {
							button_action.end_loading(btn);
						}
					},
					error:function(){

					}
				});
			}
		},
		checkPendingOrder: function () {
			var vuecon = this;
			var request = vuecon.request;
			if(request.member_id && request.branch_id){

				$.ajax({
					url:'../ajax/ajax_wh_order.php',
					type:'POST',
					dataType:'json',
					data: {functionName : 'getMemberPendingOrder',request:JSON.stringify(request)},
					success: function(data){
						if(data.success){
							$('#myModalMemberPending').modal('show');
							vuecon.member_pending_items = data.details;
						}
					},
					error:function(){

					}
				});

			}

		},
		btnUserOrder : function (){
			var vm = this;
			var con = $(this);
			button_action.start_loading(con);
			var id = vm.order_id_to_use;
			var mem_select2 = $('#member_id');
			var branch_select2= $('#branch_id');


			if(id || !isNaN(id)){
				$.ajax({
				    url:'../ajax/ajax_wh_order.php',
				    type:'POST',
				    dataType:'json',
				    data: {functionName:'usePrevOrder',id:id},
				    success: function(data){
						vm.request.member_id = data.main_data.member_id;
						vm.request.branch_id = data.main_data.branch_id;
						vm.request.price_group_id = data.main_data.price_group_id;
					    mem_select2.select2('disable', true);
					    branch_select2.select2('disable', true);
					    mem_select2.select2('data', {id: data.main_data.member_id, text:  data.main_data.member_name});
						vm.items = [];
					    for(var i in data.details){
						    vm.items.push({
							    item_id: data.details[i].item_id,
							    qty: data.details[i].qty,
							    item_code: data.details[i].item_code,
							    price: data.details[i].adjusted_price,
							    total:  data.details[i].adjusted_total,
							    remaining:  data.details[i].remaining,
							    adjustmentmem:  data.details[i].member_adjustment
						    });
					    }
					    if(data.msg){
						    tempToast('error', "<p>"+data.msg+"</p>", "<h4>Error!</h4>");
					    }

				    },
				    error:function(){
					    tempToast('error', "<p>Invalid data.</p>", "<h4>Error!</h4>");
				    }
				});
			}
		},

		updateOrderInfo: function(order){
			this.order_info = order;

			if(this.order_info.remarks == "<i class='fa fa-ban'></i>") this.order_info.remarks = '';
			this.order_info.client_po = replaceAll(this.order_info.client_po,'PO#: ','');
			this.order_info.delivery_date = order.delivery_date;
			this.order_info.warranty_card_number = order.warranty_card_number;
			$('#myModalUpdateInfo').modal('show');
		},

		getPrevConsumable: function(order){
			$('#myModalConsumable').modal('show');
			$('#body_consumable').html('Loading...');
			$.ajax({
			    url:'../ajax/ajax_wh_order.php',
			    type:'POST',
			    data: {functionName:'getPrevConsumable',member_id:order.member_id},
			    success: function(data){
				    $('#body_consumable').html(data);
			    },
			    error:function(){

			    }
			})

		},showPendingCredit: function(order){
			$('#myModalCredit').modal('show');
		},
		updateOrderInfoSave: function(){
			var info = this.order_info;
			var con = $('#btnUpdateOrderInfoSave');
			button_action.start_loading(con);
			$.ajax({
			    url:'../ajax/ajax_wh_order.php',
			    type:'POST',
			    data: {functionName:'updateOrderInfoSave',data:JSON.stringify(info)},
			    success: function(data){
					alertify.alert(data);
				    button_action.end_loading(con);
			    },
			    error:function(){
				    button_action.end_loading(con);
			    }
			});
		},
		orderCount: function() {
			var vuecon = this;
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'getOrderCount'},
				success: function(data) {

					var withApp = false;
					var withShip = false;
					var withWh = false;
					if(data.length) {
						for(var i in data) {
							if(data[i].status == 1) {
								vuecon.pending_counts.for_approval = data[i].cnt;
								withApp = true;
							} else if(data[i].status == 2) {
								vuecon.pending_counts.shipping = data[i].cnt;
								withShip = true;
							} else if(data[i].status == 3) {
								vuecon.pending_counts.warehouse = data[i].cnt;
								withWh = true;
							}
						}
					}

					if(!withApp){
						vuecon.pending_counts.for_approval =0;
					}
					if (!withShip){
						vuecon.pending_counts.shipping =0;
					}
					if (!withWh){
						vuecon.pending_counts.warehouse =0;
					}
				},
				error: function() {

				}
			})
		}, backload: function(order) {

			$('#back-load-modal').modal('show');


			var vuecon = this;
			vuecon.current_order_det = order;

			$.ajax({
				url: '../ajax/ajax_wh_order.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'showBackloadWh',order_id: order.id},
				success: function(data) {
					vuecon.$set('backload_data', data);
				},
				error: function() {

				}
			});

		},saveBackload: function() {
			var bundle_backload  = [];
			var error_bundle = false;
			$('.tobackload_bundle').each(function(){
				var con = $(this);
				var qty = con.val();
				var orig_qty = con.attr('data-qty');
				if(parseFloat(qty) > parseFloat(orig_qty)){
					error_bundle = true;
				}

				var item_id_child = con.attr('data-item_id_child');
				var rack_id = con.attr('data-rack_id');
				var item_id_parent = con.attr('data-item_id_parent');
				var id = con.attr('data-id');
				var rack = con.attr('data-rack');

				bundle_backload.push({
					qty : qty,
					rack_id :rack_id ,
					item_id_child:item_id_child,
					item_id_parent:item_id_parent,
					id:id,
					rack:rack,
					orig_qty:orig_qty
				});

			});
			if(error_bundle){
				tempToast('error', "<p>Invalid backload quantity</p>", "<h4>Error!</h4>");
				return;
			}

			var vuecon = this;
			var btn = $('#btnSaveBackload');
			button_action.start_loading(btn);

			$.ajax({
			    url:'../ajax/ajax_wh_order.php',
			    type:'POST',
			    data: {functionName:'saveBackload', backload_child: JSON.stringify(bundle_backload), orders:JSON.stringify(vuecon.backload_data),order_id:vuecon.current_order_det.id},
			    success: function(data){
				    tempToast('info', "<p>"+data+"</p>", "<h4>Info!</h4>");
				    $('#back-load-modal').modal('hide');
				    button_action.end_loading(btn);
			    },
			    error:function(){
				    button_action.end_loading(btn);
			    }
			});
		},checkBackQty: function(qty,back_qty,backload_qty,order) {
			if((parseFloat(back_qty) + parseFloat(backload_qty)) > parseFloat(qty)){
				tempToast('error', "<p>Invalid quantity.</p>", "<h4>Error!</h4>");
				order.back_qty = 0;
			}
		}, toggleCheckItem: function(order) {
			order.is_check = (order.is_check == 1) ? 0 : 1;
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'toggleCheckItem', order_det_id: order.id},
				success: function(data) {

				},
				error: function() {
					alert('Error Occur');
				}
			});
		}, toggleHideDiscount: function(order) {
			order.hide_discount = (order.hide_discount == 1) ? 0 : 1;
			$.ajax({
				url: '../ajax/ajax_wh_order.php',
				type: 'POST',
				data: {functionName: 'toggleDiscount', order_det_id: order.id},
				success: function(data) {

				},
				error: function() {
					alert('Error Occur');
				}
			});
		}, addOrderDetails: function() {
			var curorder = this.current_order_det;
			var vuecon = this;
			var item_id = vuecon.new_item_order;
			var qty = vuecon.new_qty_order;
			var details = this.orderDetails;
			if(details){
				for(var i in details){
					if(details[i].item_id == item_id){
						tempToast('info', "<p>Item Already Exists</p>", "<h4>Error!</h4>");
						return;
					}
				}
			}
			var orig_qty = qty;
			var preferred_unit = "";

			if(vuecon.different_unit == 1)
			{
				orig_qty = qty;
				preferred_unit = $('#dif_qty2 option:selected').text();
				qty = vuecon.dif_qty * qty;
			}
			$.ajax({
				url: '../ajax/ajax_query.php',
				type: 'POST',
				data: {functionName: 'itemItemOrders',preferred_unit:preferred_unit,unit_qty:orig_qty, item_id: item_id, qty: qty, cur_order: JSON.stringify(curorder)},
				success: function(data) {
					tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
					vuecon.new_item_order ='';
					$('#new_item_order').select2('val',null);
					vuecon.new_qty_order ='';
					vuecon.getDetailscomp(curorder.id, curorder.status);
					vuecon.fetchedOrder(curorder.status);
				},
				error: function() {

				}
			});
		}, deleteOrderDetails: function(order) {
			var id = order.id;
			var curorder = this.current_order_det;
			var vuecon = this;
			var addtl = '';
			if(vuecon.PENDING_MEMBER == 1){
				addtl= "<br> <p style='padding:20px;'><input type='checkbox' id='deleteCheckboxPending'> <label for='deleteCheckboxPending'> Include to pending order</label></p>";
			}
			alertify.confirm("Are you sure you want to delete this record? " + addtl, function(e) {
				if(e) {
					var v = $('#deleteCheckboxPending').is(":checked");
					if(v){
						v = 1;
					} else {
						v = 0;
					}
					$.ajax({
						url: '../ajax/ajax_query.php',
						type: 'POST',
						data: {functionName: 'deleteItemOrders',to_pending:v, id: id, cur_order: JSON.stringify(curorder)},
						success: function(data) {
							tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
							vuecon.getDetailscomp(curorder.id, curorder.status);
							vuecon.fetchedOrder(curorder.status);
						},
						error: function() {

						}
					});
				}
			})

		}, updateDetails: function() {
			if(this.order_updating == 1) {
				this.order_updating = 0;
				var od = this.orderDetails;
				var curorder = this.current_order_det;
				var vuecon = this;
				$.ajax({
					url: '../ajax/ajax_query.php',
					type: 'POST',
					data: {
						functionName: 'updateItemOrders',
						cur_order: JSON.stringify(curorder),
						od: JSON.stringify(od)
					},
					success: function(data) {
						tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
						vuecon.getDetailscomp(curorder.id, curorder.status);
						vuecon.fetchedOrder(curorder.status);
					},
					error: function() {

					}
				});
			} else {
				this.order_updating = 1;
			}

		}, printDelLog: function() {

			if(this.orders_log.length > 0) {
				var print_string = "";
				var print_head = "";
				var print_tail="";

				var date_from = this.log_from;
				var date_to = this.log_to;
				var finaldate = "";
				if(date_from.trim() == date_to.trim()) {
					finaldate = date_from;
				} else {
					finaldate = date_from + " - " + date_to;
				}
				print_head += "<div class='container-fluid'>";
				print_head += "<h1 class='text-center'><img width='35' height='35' src='../css/img/logo.png' /> " + localStorage['company_name'] + "</h1>";
				print_head += "<h3 class='text-center'>Deliveries Print Out</h3>";

				print_string += "<table class='table table-bordered'>";
				print_string += "<thead>";
				print_string += "<tr>";
				//print_string += "<th>ID</th><th>Branch</th><th>Requested By</th><th>Client</th><th>Order Date</th><th>Delivery Date</th><th>Remarks</th><th>Truck</th>";
				print_string += "<th>ID</th><th>Client</th><th>Ctrl #</th><th>Amount</th><th>Remarks</th><th></th>";
				print_string += "</tr>";
				print_string += "</thead>";
				print_string += "<tbody>";
				var truck_name= "";
				var driver_name= "";
				var sales_type_name= "";
				var helpers ="";
				for(var i in this.orders_log) {
					var ctr_number = "";
					if(this.orders_log[i].truck_name){
						truck_name = this.orders_log[i].truck_name + " " + this.orders_log[i].truck_description;
					}
					if(this.orders_log[i].driver){
						driver_name = this.orders_log[i].driverval;
					}
					if(this.orders_log[i].helperval){
						helpers = this.orders_log[i].helperval;
					}
					if(this.orders_log[i].sales_type_name){
						sales_type_name = this.orders_log[i].sales_type_name;
					}

					if(this.orders_log[i].invoice || this.orders_log[i].dr || this.orders_log[i].pr){
						if(this.orders_log[i].invoice != 0){
							ctr_number = this.orders_log[i].invoice;
						} else if (this.orders_log[i].dr != 0){
							ctr_number = this.orders_log[i].dr;
						}else if (this.orders_log[i].pr != 0){
							ctr_number = this.orders_log[i].pr;
						}

					}
					print_string += "<tr>";
					print_string += "<td>" + this.orders_log[i].id + "</td>";
					//	print_string += "<td>"+this.orders_log[i].branch_name+"</td>";
					//	print_string += "<td>"+this.orders_log[i].fullnameUser+"</td>";
					print_string += "<td>" + this.orders_log[i].fullname + "<small style='display:block;' class='text-danger span-block'>" + this.orders_log[i].personal_address + "</small></td>";
					//	print_string += "<td>"+this.orders_log[i].ordered_date+"</td>";
					//	print_string += "<td>"+this.orders_log[i].is_scheduled+"</td>";
					print_string += "<td>" + ctr_number + "</td>";
					print_string += "<td>" + this.orders_log[i].total_price + "</td>";
					print_string += "<td>" + this.orders_log[i].remarks + "</td>";
					print_string += "<td></td>";
					print_string += "</tr>";
				}
				print_string += "</tbody>";
				print_string += "</table>";
				print_string += "</div>";
				print_head += "<div class='row'>";
				print_head += "<div class='col-md-6' style='width:48%;float:left;'>";
				print_head += "<p class='text-left'>Schedule: " + finaldate + "</p>";
				print_head += "</div>";
				print_head += "<div class='col-md-6' style='width:48%;float:left;'>";
				print_head += "<p class='text-left'>Truck: " + truck_name + "</p>";
				print_head += "</div>";
				print_head += "</div>";
				print_head += "<div class='row'>";
				print_head += "<div class='col-md-6' style='width:48%;float:left;'>";
				print_head += "<p class='text-left'>Driver: " + driver_name + "</p>";
				print_head += "</div>";
				print_head += "<div class='col-md-6' style='width:48%;float:left;'>";
				print_head += "<p class='text-left'>Type: " + sales_type_name + "</p>";
				print_head += "</div>";
				print_head += "</div>";
				print_head += "<div class='row'>";
				print_head += "<div class='col-md-12' style='width:48%;float:left;'>";
				print_head += "<p class='text-left'>Helper: " + helpers + "</p>";
				print_head += "</div>";

				print_head += "</div>";


				print_tail += "<div class='row'>";
				print_tail += "<div class='col-md-3' style='width:24%;float:left;'>";
				print_tail += "<p class='text-left'>Prepared by:</p>";
				print_tail += "</div>";
				print_tail += "<div class='col-md-3' style='width:24%;float:left;'>";
				print_tail += "<p class='text-left'>Received by:</p>";
				print_tail += "</div>";
				print_tail += "<div class='col-md-3' style='width:24%;float:left;'>";
				print_tail += "<p class='text-left'>Noted by:</p>";
				print_tail += "</div>";
				print_tail += "</div>";

				print_tail += "<div class='row'>";
				print_tail += "<div class='col-md-3' style='width:24%; float:left;'  >";
				print_tail += "<p class='text-left' style='border-bottom:1px solid #000'>&nbsp;</p>";
				print_tail += "</div>";
				print_tail += "<div class='col-md-3' style='width:24%;float:left;'>";
				print_tail += "<p class='text-left' style='border-bottom:1px solid #000'>&nbsp;</p>";
				print_tail += "</div>";
				print_tail += "<div class='col-md-3' style='width:24%;float:left;'>";
				print_tail += "<p class='text-left' style='border-bottom:1px solid #000'>&nbsp;</p>";
				print_tail += "</div>";
				print_tail += "</div>";

				this.popUpPrintWithStyle(print_head + print_string + print_tail);
			}
		}, printWarehouse: function() {
			window.open('../ajax/ajax_warehouse.php?functionName=printWarehousePending', '_blank' // <- This is what makes it open in a new window.
			);
		}, filterDelLog: function() {
			this.fetchedOrderLog();
		}, filterPickupLog: function() {
			this.fetchedOrderPickup();
		},backToWarehouse: function(){
			var btncon = $('#btnBackToWarehouse');
			var btnoldval = btncon.html();
			var vuecon = this;
			var order_id = vuecon.current_order;
			btncon.attr('disabled', true);
			btncon.html('Loading...');
			alertify.confirm("Are you sure you want to return this request to warehouse?", function(e){
				if(e){
					$.ajax({
					    url:'../ajax/ajax_wh_order.php',
					    type:'POST',
					    data: {functionName:'backToWarehouse',order_id:order_id},
					    success: function(data){
						    tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
						    btncon.attr('disabled', false);
						    btncon.html(btnoldval);
						    $('#myModal').modal('hide');
						    if(vuecon.current_order_det.for_pickup == 0){
							    vuecon.fetchedOrderLog();
						    } else {
							    vuecon.fetchedOrderPickup();
						    }

					    },
					    error:function(){

					    }
					})
				} else {
					btncon.attr('disabled', false);
					btncon.html(btnoldval);
				}
			});
		}, scheduleOrder: function() {

			var btncon = $('#btnScheduleOrder');
			var btnoldval = btncon.html();
			var vuecon = this;

			btncon.attr('disabled', true);
			btncon.html('Loading...');

			if(vuecon.schedule_date && ((vuecon.truck_id && vuecon.helper_id && vuecon.driver_id) || vuecon.current_order_det.shipping_name || vuecon.current_order_det.for_pickup != '' || true)) {
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'POST',
					data: {
						functionName: 'scheduleOrderWh',
						order_id: vuecon.current_order,
						schedule_date: vuecon.schedule_date,
						truck_id: vuecon.truck_id,
						helpers_id: JSON.stringify(vuecon.helper_id),
						driver_id: vuecon.driver_id,
						order_details: JSON.stringify(vuecon.orderDetails),
					},
					success: function(data) {
						tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
						btncon.attr('disabled', false);
						btncon.html(btnoldval);
						$('#myModal').modal('hide');
						vuecon.fetchedOrder(vuecon.current_order_det.status);
						vuecon.orderCount();
					},
					error: function() {
						tempToast('error', "<p>Error occur. Please try again.</p>", "<h4>Error!</h4>");
						$('#myModal').modal('hide');
						btncon.attr('disabled', false);
						btncon.html(btnoldval);
					}
				});
			} else {
				alertify.alert("Please complete the form.", function() {
					btncon.attr('disabled', false);
					btncon.html(btnoldval);
				});

			}

		}, reScheduleOrder: function() {

			var btncon = $('#btnReScheduleOrder');
			var btnoldval = btncon.html();
			btncon.attr('disabled', true);
			btncon.html('Loading...');
			var vuecon = this;
			if(vuecon.re_schedule_date) {
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'POST',
					data: {
						functionName: 'reScheduleOrderWh',
						order_id: vuecon.current_order,
						schedule_date: vuecon.re_schedule_date,
						re_truck_id: vuecon.re_truck_id,
						re_driver_id: vuecon.re_driver_id,
						re_for_pick_up: vuecon.re_for_pick_up,
						re_helper_id: JSON.stringify(vuecon.re_helper_id)
					},
					success: function(data) {
						tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
						btncon.attr('disabled', false);
						btncon.html(btnoldval);
						$('#myModalDates').modal('hide');
						vuecon.fetchedOrderLog();
						vuecon.fetchedOrderPickup();
					},
					error: function() {
						alertify.error('<h4>Error Occur. Please Try again.</h4>');
						$('#myModalDates').modal('hide');
					}
				});
			} else {
				alertify.alert("Please complete the form.", function() {
					btncon.attr('disabled', false);
					btncon.html(btnoldval);
				});

			}

		}, paymentDetails: function(order, e) {
			e.preventDefault();
			var payment_id = order.payment_id;
			$('.right-panel-pane').fadeIn(100);
			$.ajax({
				url: '../ajax/ajax_paymentDetails.php', type: 'POST', beforeSend: function() {
					$('#right-pane-container').html('Fetching record. Please wait.');
				}, data: {id: payment_id}, success: function(data) {
					$('#right-pane-container').html(data);

				}
			});
		}, unsavedRequest: function() {
			var vuecon = this;
			if(localStorage['wh_backup_items']) {
				alertify.confirm('You have unsaved request. Do you want to load it?', function(e) {
					if(e) {
						vuecon.request = JSON.parse(localStorage['wh_backup_request']);
						vuecon.items = JSON.parse(localStorage['wh_backup_items']);
						vuecon.disableMemberAndBranch();
					}
				});
			}
		}, bundleDetails: function(item) {
			var vuecon = this;
			$('#myModalBundle').modal('show');
			$('#bbody').html("Loading...");
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'getBundleItem', item_id: item.item_id, item_description: item.item_code},
				success: function(data) {
					$('#bbody').html(data);
				},
				error: function() {
					alert('Error Occur');
				}
			})

		}, getLayout: function() {
			/* if(localStorage['company_id']) {
				$.ajax({
					url: "../ajax/ajax_query.php",
					type: "POST",
					data: {cid: localStorage['company_id'], functionName: 'getDocumentLayout'},
					success: function(data) {
						var formatStyle = JSON.parse(data);
						localStorage["invoice_format"] = formatStyle.invoice;
						localStorage["dr_format"] = formatStyle.dr;
						localStorage["ir_format"] = formatStyle.ir;

						if(formatStyle.sv){
							localStorage["sv_format"] = formatStyle.sv;
						}
						if(formatStyle.sr){
							localStorage["sr_format"] = formatStyle.sr;
						}
						if(formatStyle.ts){
							localStorage["ts_format"] = formatStyle.ts;
						}
					}
				});
			} */

		}, getTrucks: function() {
			var vuecon = this;
			$.ajax({
				url: "../ajax/ajax_query2.php",
				type: "POST",
				dataType: 'json',
				data: {functionName: 'getTrucks'},
				success: function(data) {

					vuecon.trucks = data.trucks;
					vuecon.helpers = data.helpers;

					vuecon.drivers = data.drivers;
					vuecon.countdel = data.count_del;

					vuecon.countpickup = data.countpickup;
					vuecon.countservice = data.countservice;

					localStorage["invoice_format"] = data.invoice;
					localStorage["dr_format"] = data.dr;
					localStorage["ir_format"] = data.ir;
					localStorage["sv_format"] = data.sv;
					localStorage["sr_format"] = data.sr;
					localStorage["ts_format"] = data.ts;
					localStorage["news_format"] = data.extra;

				}
			});
		}, getInvoiceDrPr: function() {
			if(localStorage['terminal_id']) {
				var vuecon = this;
				$.ajax({
					url: "../ajax/ajax_get_branchAndTerminal.php",
					type: "POST",
					data: {cid: localStorage['terminal_id'], type: 3},
					success: function(data) {
						var invarr = data.split(":");
						localStorage["invoice"] = invarr[0];
						localStorage["end_invoice"] = invarr[1];
						localStorage["dr"] = invarr[2];
						localStorage["end_dr"] = invarr[3];
						localStorage["invoice_limit"] = invarr[4];
						localStorage["dr_limit"] = invarr[5];
						localStorage["ir"] = invarr[6];
						localStorage["end_ir"] = invarr[7];
						localStorage["ir_limit"] = invarr[8];
						localStorage["speed_opt"] = invarr[9];
						localStorage["use_printer"] = invarr[10];
						localStorage["data_sync"] = invarr[11];
						localStorage["news_print"] = invarr[12];
						localStorage["print_inv"] = invarr[13];
						localStorage["print_dr"] = invarr[14];
						localStorage["print_ir"] = invarr[15];
						localStorage["pref_inv"] = invarr[16];
						localStorage["pref_dr"] = invarr[17];
						localStorage["pref_ir"] = invarr[18];
						localStorage["suf_inv"] = invarr[19];
						localStorage["suf_dr"] = invarr[20];
						localStorage["suf_ir"] = invarr[21];
						localStorage["sv"] = invarr[22];
						localStorage["sv_limit"] = invarr[23];
						localStorage["suf_sv"] = invarr[24];
						localStorage["pref_sv"] = invarr[25];
						localStorage["sr"] = invarr[26];
						localStorage["sr_limit"] = invarr[27];
						localStorage["suf_sr"] = invarr[28];
						localStorage["pref_sr"] = invarr[29];
						localStorage["ts"] = invarr[30];
						localStorage["ts_limit"] = invarr[31];
						localStorage["suf_ts"] = invarr[32];
						localStorage["pref_ts"] = invarr[33];

						if(localStorage['invoice']) {
							vuecon.invoice = parseInt(localStorage['invoice']) + 1;
						}
						if(localStorage['dr']) {
							vuecon.dr = parseInt(localStorage['dr']) + 1;
						}
						if(localStorage['ir']) {
							vuecon.pr = parseInt(localStorage['ir']) + 1;
						}
						if(localStorage['sv']) {
							vuecon.sv = parseInt(localStorage['sv']) + 1;
						}
						if(localStorage['sr']) {
							vuecon.sr = parseInt(localStorage['sr']) + 1;
						}
						if(localStorage['ts']) {
							vuecon.ts = parseInt(localStorage['ts']) + 1;
						}
					}
				});
				$('#conOverride').show();
			} else {
				$('#conOverride').hide();
			}
		}, popUpPrintWithStyle: function(data) {
			var mywindow = window.open('', 'new div', '');
			mywindow.document.write('<html><head><title></title><style></style>');
			mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
			mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
			mywindow.document.write(data);
			mywindow.document.write('</body></html>');
			setTimeout(function() {
				mywindow.print();
				mywindow.close();

			}, 300);
			return true;
		}, popUpPrint: function(data, withStyle) {
			var mywindow = window.open('', 'new div', '');
			mywindow.document.write('<html><head><title></title><style></style>');
			if(withStyle) {
				/*optional stylesheet*/
				mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
			}
			mywindow.document.write('</head><body style="padding:0;margin:0;">');
			mywindow.document.write(data);
			mywindow.document.write('</body></html>');
			mywindow.print();
			mywindow.close();
			return true;
		}, printGroup: function(order_id) {
			var chkInvoice = $('#chkInvoice').is(':checked');
			var chkDr = $('#chkDr').is(':checked');
			var chkPr = $('#chkPr').is(':checked');
			var chkSv = $('#chkSv').is(':checked');
			var chkTs = $('#chkTs').is(':checked');
			var chkSr = $('#chkSr').is(':checked');

			var vuecon = this;
			var to_print = false;
			if(!chkInvoice) {
				vuecon.invoice = 0;
			} else {
				to_print = true;
			}
			if(!chkDr) {
				vuecon.dr = 0;
			} else {
				to_print = true;
			}
			if(!chkPr) {
				vuecon.pr = 0;
			} else {
				to_print = true;
			}

			if(!chkSv) {
				vuecon.sv = 0;
			} else {
				to_print = true;
			}
			if(!chkTs) {
				vuecon.ts = 0;
			} else {
				to_print = true;
			}
			if(!chkSr) {
				vuecon.sr = 0;
			} else {
				to_print = true;
			}

			if(to_print) {
				if(vuecon.ajaxRequest) {
					return;
				}
				vuecon.ajaxRequest = true;
				$('.loading').show();
				var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
				var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
				var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
				var pref_sv = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
				var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
				var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
				var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
				var suf_sv = (localStorage['suf_sv']) ? localStorage['suf_sv'] : '';
				var custom_date = $('#custom_date').val();
				$.ajax({
					url: '../ajax/ajax_query2.php', type: 'POST', data: {
						functionName: 'getItemForInvoicePrintingWh',
						order_id: order_id,
						order_type: 5,
						invoice: vuecon.invoice,
						dr: vuecon.dr,
						pr: vuecon.pr,
						sv: vuecon.sv,
						ts: vuecon.ts,
						sr: vuecon.sr,
						terminal_id: localStorage['terminal_id'],
						rePrint: 0,
						custom_date: custom_date,
						pref_inv: pref_inv,
						pref_dr: pref_dr,
						pref_ir: pref_ir,
						pref_sv: pref_sv,
						suf_inv: suf_inv,
						suf_dr: suf_dr,
						suf_ir: suf_ir,
						suf_sv: suf_sv
					}, dataType: 'json', success: function(data) {

						vuecon.print_data = data;

						$('.loading').hide();
						var newsprint_type = 0;
						if(chkInvoice) {
							newsprint_type = 4;
							var config_company_name = $('#config_company_name').val();
							if(config_company_name == 'cebuhiq'){
								vuecon.printElemCebu();
							} else {
								vuecon.printElem();
							}
						}
						if(chkDr) {
							vuecon.printElemDr();
							newsprint_type = 1;
						}
						if(chkPr) {
							vuecon.printElemPr();
							newsprint_type = 2;
						}
						if(chkSv) {
							vuecon.printElemSv();
							newsprint_type = 1;
						}
						if(chkTs) {
							vuecon.printElemTS();
							newsprint_type = 1;
						}

						if(chkSr) {
							vuecon.printElemSR();
							newsprint_type = 1;
						}

						if(localStorage['news_print'] && localStorage['news_print'] == 1) {
							var config_company_name = $('#config_company_name').val();
							if(config_company_name == 'cebuhiq'){
								vuecon.printElemNewsPrintCebu(newsprint_type);
							} else {
								vuecon.printElemNewsPrint(newsprint_type);
							}

						}
						vuecon.ajaxRequest = false;
						if(vuecon.nav.del == true){
							vuecon.fetchedOrderLog();
						} else if(vuecon.nav.pickup == true){
							vuecon.fetchedOrderPickup()
						}  else {
							vuecon.fetchedOrder(1);
						}

						vuecon.getInvoiceDrPr();
						tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
					}, error: function() {
						alertify.error('Please set your computer as terminal first.');
						vuecon.ajaxRequest = false;
						$('.loading').hide();

					}
				});
			} else {
				vuecon.fetchedOrder(1);
				vuecon.getInvoiceDrPr();
			}
		}, rePrintInvoice: function(order, type) {
			var vuecon = this;
			if(vuecon.ajaxRequest) {
				return;
			}

			vuecon.ajaxRequest = true;
			$('.loading').show();
			var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
			var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
			var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
			var pref_sv = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
			var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
			var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
			var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
			var suf_sv = (localStorage['suf_sv']) ? localStorage['suf_sv'] : '';
			var custom_date = $('#custom_date').val();
			$.ajax({
				url: '../ajax/ajax_query2.php', type: 'POST', data: {
					functionName: 'getItemForInvoicePrintingWh',
					order_id: order.id,
					order_type: type,
					invoice: vuecon.invoice,
					dr: vuecon.dr,
					pr: vuecon.pr,
					sv: vuecon.sv,
					sr: vuecon.sr,
					ts: vuecon.ts,
					terminal_id: localStorage['terminal_id'],
					rePrint: 1,
					pref_inv: pref_inv,
					pref_dr: pref_dr,
					pref_ir: pref_ir,
					pref_sv: pref_sv,
					custom_date: custom_date,
					suf_inv: suf_inv,
					suf_dr: suf_dr,
					suf_ir: suf_ir,
					suf_sv: suf_sv
				}, dataType: 'json', success: function(data) {
					vuecon.print_data = data;
					var config_company_name = $('#config_company_name').val();
					$('.loading').hide();
					if(type == 1) {


						if(config_company_name == 'cebuhiq'){

							vuecon.printElemCebu();
							vuecon.printElemNewsPrintCebu(4);
						} else {
							vuecon.printElem();
						}
					} else if(type == 2) {
						if(config_company_name == 'avision'){
							window.printAvisionDr(data,'Delivery Receipt');
						} else {
							vuecon.printElemDr();

							if(config_company_name == 'cebuhiq'){
								vuecon.printElemNewsPrintCebu(1);
							} else {
								vuecon.printElemNewsPrint(1);
							}
						}


					}else if(type == 3) {
						vuecon.printElemPr();
						if(config_company_name == 'cebuhiq'){
							vuecon.printElemNewsPrintCebu(2);
						} else {
							vuecon.printElemNewsPrint(2);
						}

					} else if(type == 4) {
						vuecon.printElemSv();
						if(config_company_name == 'cebuhiq'){
							vuecon.printElemNewsPrintCebu(3);
						} else {
							vuecon.printElemNewsPrint(3);
						}

					} else if(type == 6) {
						vuecon.printElemSR();

					} else if(type == 7) {
						vuecon.printElemTS();


					}
					tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
					vuecon.ajaxRequest = false;
				}, error: function() {
					alertify.error('Error Occur. Please try again.');
					vuecon.ajaxRequest = false;
					$('.loading').hide();
				}
			});
		}, printInvoice: function(order, type) {
			var vuecon = this;
			if(vuecon.ajaxRequest) {
				return;
			}
			vuecon.ajaxRequest = true;
			var config_company_name = $('#config_company_name').val();
			$('.loading').show();
			if(type == 1) {
				alertify.confirm("The next invoice is " + vuecon.invoice + ". Do you want to continue?", function(e) {
					if(e) {
						// update inv #
						var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
						var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
						var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
						var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
						var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
						var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
						var custom_date = $('#custom_date').val();
						$.ajax({
							url: '../ajax/ajax_query2.php', type: 'POST', data: {
								functionName: 'getItemForInvoicePrintingWh',
								order_id: order.id,
								order_type: type,
								invoice: vuecon.invoice,
								dr: vuecon.dr,
								pr: vuecon.pr,
								sv: vuecon.sv,
								sr: vuecon.sr,
								ts: vuecon.ts,
								terminal_id: localStorage['terminal_id'],
								rePrint: 0,
								pref_inv: pref_inv,
								pref_dr: pref_dr,
								custom_date: custom_date,
								pref_ir: pref_ir
							}, dataType: 'json', success: function(data) {

								vuecon.print_data = data;
								$('.loading').hide();
								if(config_company_name == 'cebuhiq'){

									vuecon.printElemCebu();

								} else {
									vuecon.printElem();
								}
								if(localStorage['news_print'] && localStorage['news_print'] == 1) {
									if(config_company_name == 'cebuhiq'){
										vuecon.printElemNewsPrintCebu(4);
									} else {
										vuecon.printElemNewsPrint(0);
									}

								}
								vuecon.ajaxRequest = false;
								if(vuecon.nav.del == true){
									vuecon.fetchedOrderLog();
								} else if(vuecon.nav.pickup == true){
									vuecon.fetchedOrderPickup()
								} else if(vuecon.nav.warehouse == true){
									vuecon.fetchedOrder(3);
								}  else {
									vuecon.fetchedOrder(1);
								}
								vuecon.getInvoiceDrPr();
								tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
							}, error: function() {
								alertify.error('Please set your computer as terminal first.');
								vuecon.ajaxRequest = false;
								$('.loading').hide();

							}
						});
					} else {
						$('.loading').hide();
					}
				});
			} else if(type == 2) {
				alertify.confirm("The next dr is " + vuecon.dr + ". Do you want to continue?", function(e) {
					if(e) {
						// update inv #
						var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
						var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
						var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
						var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
						var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
						var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
						var custom_date = $('#custom_date').val();
						$.ajax({
							url: '../ajax/ajax_query2.php', type: 'POST', data: {
								functionName: 'getItemForInvoicePrintingWh',
								order_id: order.id,
								order_type: type,
								invoice: vuecon.invoice,
								dr: vuecon.dr,
								pr: vuecon.pr,
								sv: vuecon.sv,
								sr: vuecon.sr,
								ts: vuecon.ts,
								terminal_id: localStorage['terminal_id'],
								rePrint: 0,
								pref_inv: pref_inv,
								pref_dr: pref_dr,
								pref_ir: pref_ir,
								custom_date: custom_date,
								suf_inv: suf_inv,
								suf_dr: suf_dr,
								suf_ir: suf_ir
							}, dataType: 'json', success: function(data) {

								vuecon.print_data = data;
								$('.loading').hide();
								if(config_company_name == 'avision'){
									window.printAvisionDr(data,'Delivery Receipt');
								} else {
									vuecon.printElemDr();
									if(localStorage['news_print'] && localStorage['news_print'] == 1) {
										if(config_company_name == 'cebuhiq'){
											vuecon.printElemNewsPrintCebu(1);
										} else {
											vuecon.printElemNewsPrint(1);
										}
									}
								}

								vuecon.ajaxRequest = false;
								if(vuecon.nav.del == true){
									vuecon.fetchedOrderLog();
								} else if(vuecon.nav.pickup == true){
									vuecon.fetchedOrderPickup()
								} else if(vuecon.nav.warehouse == true){
									vuecon.fetchedOrder(3);
								}  else {
									vuecon.fetchedOrder(1);
								}
								vuecon.getInvoiceDrPr();
								tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
							}, error: function() {
								alertify.error('Please set your computer as terminal first.');
								vuecon.ajaxRequest = false;
								$('.loading').hide();
							}
						});
					} else {
						$('.loading').hide();
					}
				});
			} else if(type == 3) {
				alertify.confirm("The next PR is " + vuecon.pr + ". Do you want to continue?", function(e) {
					if(e) {
						// update inv #
						var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
						var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
						var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
						var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
						var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
						var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
						var custom_date = $('#custom_date').val();
						$.ajax({
							url: '../ajax/ajax_query2.php', type: 'POST', data: {
								functionName: 'getItemForInvoicePrintingWh',
								order_id: order.id,
								order_type: type,
								invoice: vuecon.invoice,
								dr: vuecon.dr,
								pr: vuecon.pr,
								sv: vuecon.sv,
								sr: vuecon.sr,
								ts: vuecon.ts,
								terminal_id: localStorage['terminal_id'],
								rePrint: 0,
								pref_inv: pref_inv,
								pref_dr: pref_dr,
								pref_ir: pref_ir,
								custom_date: custom_date,
								suf_inv: suf_inv,
								suf_dr: suf_dr,
								suf_ir: suf_ir
							}, dataType: 'json', success: function(data) {

								vuecon.print_data = data;
								$('.loading').hide();
								vuecon.printElemPr();
								if(localStorage['news_print'] && localStorage['news_print'] == 1) {
									if(config_company_name == 'cebuhiq'){
										vuecon.printElemNewsPrintCebu(2);
									} else {
										vuecon.printElemNewsPrint(2);
									}
								}
								vuecon.ajaxRequest = false;
								if(vuecon.nav.del == true){
									vuecon.fetchedOrderLog();
								} else if(vuecon.nav.pickup == true){
									vuecon.fetchedOrderPickup()
								}  else {
									vuecon.fetchedOrder(1);
								}
								vuecon.getInvoiceDrPr();
								tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
							}, error: function() {
								alertify.error('Please set your computer as terminal first.');
								vuecon.ajaxRequest = false;
								$('.loading').hide();
							}
						});
					} else {
						$('.loading').hide();
					}
				});
			}else if(type == 4) {
				alertify.confirm("The next SV is " + vuecon.sv + ". Do you want to continue?", function(e) {
					if(e) {
						// update inv #
						var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
						var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
						var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
						var pref_sv = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
						var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
						var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
						var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
						var suf_sv = (localStorage['suf_sv']) ? localStorage['suf_sv'] : '';
						var custom_date = $('#custom_date').val();
						$.ajax({
							url: '../ajax/ajax_query2.php', type: 'POST', data: {
								functionName: 'getItemForInvoicePrintingWh',
								order_id: order.id,
								order_type: type,
								invoice: vuecon.invoice,
								dr: vuecon.dr,
								pr: vuecon.pr,
								sv: vuecon.sv,
								sr: vuecon.sr,
								ts: vuecon.ts,
								terminal_id: localStorage['terminal_id'],
								rePrint: 0,
								pref_inv: pref_inv,
								pref_dr: pref_dr,
								pref_ir: pref_ir,
								pref_sv: pref_sv,
								custom_date: custom_date,
								suf_inv: suf_inv,
								suf_dr: suf_dr,
								suf_sv: suf_sv,
								suf_ir: suf_ir
							}, dataType: 'json', success: function(data) {

								vuecon.print_data = data;
								$('.loading').hide();
								vuecon.printElemSv();
								if(localStorage['news_print'] && localStorage['news_print'] == 1) {
									if(config_company_name == 'cebuhiq'){
										vuecon.printElemNewsPrintCebu(3);
									} else {
										vuecon.printElemNewsPrint(3);
									}
								}
								vuecon.ajaxRequest = false;
								if(vuecon.nav.del == true){
									vuecon.fetchedOrderLog();
								} else if(vuecon.nav.pickup == true){
									vuecon.fetchedOrderPickup()
								}  else if(vuecon.nav.warehouse == true){
									vuecon.fetchedOrder(3);
								} else {
									vuecon.fetchedOrder(1);
								}
								vuecon.getInvoiceDrPr();
								tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
							}, error: function() {
								alertify.error('Please set your computer as terminal first.');
								vuecon.ajaxRequest = false;
								$('.loading').hide();
							}
						});
					} else {
						$('.loading').hide();
					}
				});
			}else if(type == 6) {
				alertify.confirm("The next SR is " + vuecon.sr + ". Do you want to continue?", function(e) {
					if(e) {
						// update inv #
						var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
						var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
						var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
						var pref_sv = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
						var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
						var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
						var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
						var suf_sv = (localStorage['suf_sv']) ? localStorage['suf_sv'] : '';
						var custom_date = $('#custom_date').val();
						$.ajax({
							url: '../ajax/ajax_query2.php', type: 'POST', data: {
								functionName: 'getItemForInvoicePrintingWh',
								order_id: order.id,
								order_type: type,
								invoice: vuecon.invoice,
								dr: vuecon.dr,
								pr: vuecon.pr,
								sv: vuecon.sv,
								sr: vuecon.sr,
								ts: vuecon.ts,
								terminal_id: localStorage['terminal_id'],
								rePrint: 0,
								pref_inv: pref_inv,
								pref_dr: pref_dr,
								pref_ir: pref_ir,
								pref_sv: pref_sv,
								custom_date: custom_date,
								suf_inv: suf_inv,
								suf_dr: suf_dr,
								suf_sv: suf_sv,
								suf_ir: suf_ir
							}, dataType: 'json', success: function(data) {

								vuecon.print_data = data;
								$('.loading').hide();
								vuecon.printElemSR();
								vuecon.ajaxRequest = false;
								if(vuecon.nav.del == true){
									vuecon.fetchedOrderLog();
								} else if(vuecon.nav.pickup == true){
									vuecon.fetchedOrderPickup()
								}  else {
									vuecon.fetchedOrder(1);
								}
								vuecon.getInvoiceDrPr();
								tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
							}, error: function() {
								alertify.error('Please set your computer as terminal first.');
								vuecon.ajaxRequest = false;
								$('.loading').hide();
							}
						});
					} else {
						$('.loading').hide();
					}
				});
			} else if(type == 7) {
				alertify.confirm("The next TS is " + vuecon.ts + ". Do you want to continue?", function(e) {
					if(e) {
						// update inv #
						var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
						var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
						var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
						var pref_sv = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
						var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
						var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
						var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
						var suf_sv = (localStorage['suf_sv']) ? localStorage['suf_sv'] : '';
						var custom_date = $('#custom_date').val();
						$.ajax({
							url: '../ajax/ajax_query2.php', type: 'POST', data: {
								functionName: 'getItemForInvoicePrintingWh',
								order_id: order.id,
								order_type: type,
								invoice: vuecon.invoice,
								dr: vuecon.dr,
								pr: vuecon.pr,
								sv: vuecon.sv,
								sr: vuecon.sr,
								ts: vuecon.ts,
								terminal_id: localStorage['terminal_id'],
								rePrint: 0,
								pref_inv: pref_inv,
								pref_dr: pref_dr,
								pref_ir: pref_ir,
								pref_sv: pref_sv,
								custom_date: custom_date,
								suf_inv: suf_inv,
								suf_dr: suf_dr,
								suf_sv: suf_sv,
								suf_ir: suf_ir
							}, dataType: 'json', success: function(data) {

								vuecon.print_data = data;
								$('.loading').hide();
								vuecon.printElemSR();
								vuecon.ajaxRequest = false;
								if(vuecon.nav.del == true){
									vuecon.fetchedOrderLog();
								} else if(vuecon.nav.pickup == true){
									vuecon.fetchedOrderPickup()
								}  else {
									vuecon.fetchedOrder(1);
								}
								vuecon.getInvoiceDrPr();
								tempToast('info', "<p>Action completed successfully</p>", "<h4>Information!</h4>");
							}, error: function() {
								alertify.error('Please set your computer as terminal first.');
								vuecon.ajaxRequest = false;
								$('.loading').hide();
							}
						});
					} else {
						$('.loading').hide();
					}
				});
			}


		}, str_pad: function(pad, str, padLeft) {
			if(typeof str === 'undefined')
				return pad;
			if(padLeft) {
				return (pad + str).slice(-pad.length);
			} else {
				return (str + pad).substring(0, pad.length);
			}
		}, printElem: function() {
			if(localStorage['print_inv'] == 0) {
				return true; // dont print invoice
			}
			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var member_id_test = data.member_id;
			var styling = JSON.parse(localStorage['invoice_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var output = data.date_sold;
			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

			var memlisttest = '';
			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}
			if(memlisttest) {
				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}
			}

			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var logovisible = (styling['logo']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var logoBold = (styling['logo']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
			if(styling['logo']['visible']) printhtml = printhtml + "<div style='"+logovisible+"'><img src='http://"+$('#_HOST').val()+"/css/img/logo.jpg' style='" + logovisible + logoBold + "position:absolute;top:" + styling['logo']['top'] + "px; left:" + styling['logo']['left'] + "px;width:" + styling['logo']['width'] + "px;height:" + styling['logo']['height'] + "px;' /></div>";
			printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'> <br/><br/>" + output + " </div><div style='clear:both;'></div>";
			printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
			printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
			printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
			printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
			printhtml = printhtml + "<table id='itemscon' style='" + itemtablevisible + itemtableBold + "position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";

			var countallitem = $('#cart > tbody > tr').length;
			var invoicelimit = parseFloat(localStorage['invoice_limit']);
			var drlimit = localStorage['dr_limit'];
			var lamankadainvoice = [];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax = 0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			invoicelimit = parseInt(invoicelimit) + 1;
			var testdata = data.item_list;
			var company_id = localStorage['company_id'];

			if(this.printWithPrice == true){
				tdtotalvisible = 'display:none;';
				tdpricevisible = 'display:none;';
				paymentsvisible = 'display:none;';
				payments2visible = 'display:none;';
				payments3visible = 'display:none;';
			}

			for(var i in testdata) {
				var itemcode = testdata[i].item_code;
				var description = testdata[i].description;
				var b = testdata[i].barcode;
				var unit_name = testdata[i].unit_name;
				unit_name = (unit_name) ? unit_name : '';
				var qty = testdata[i].qty + "<span style='margin-left:60px;'>" + unit_name + "</span>";
				var price = testdata[i].price;
				var discount = testdata[i].discount;
				var total = testdata[i].total;
				var ind_rack = testdata[i].racking;
				var origtotal = total;


				var discount_type = testdata[i].discount_type;
				var discount_label_1 = "";
				var discount_label_2 = "";
				if(!(itemcode && price)){
					continue;
				}
				try {

					if(discount_type.length){
						price = testdata[i].original_price;
						origtotal = price * testdata[i].qty;
						var tmp_price =0;
						for(var dd in discount_type){
							var temp_disc = discount_type[dd];
							temp_disc = ((temp_disc / 100) * (price - tmp_price)) * testdata[i].qty ;
							discount_label_1 += "<br>" + number_format(temp_disc,2);
							discount_label_2 += "<br>Less "+ number_format(discount_type[dd],2)+ ":";
							tmp_price = parseFloat(tmp_price) + parseFloat(temp_disc/testdata[i].qty);
						}
						discount_label_2 += "<br>Net: ";
						discount_label_1 += "<br>" + ((parseFloat(origtotal) + parseFloat(testdata[i].discount)).toFixed(2));
					}
				} catch(e){

				}

				if(parseFloat(discount) > 0) {
					var perunitdisc = parseFloat(discount) / parseFloat(qty);
					var labeldisc = "<br/>(Disc. " + number_format(perunitdisc, 2) + ")";
					var labeldisc2 = "<br/>(" + number_format(discount, 2) + ")";
				} else {
					var labeldisc = '';
					var labeldisc2 = '';
				}
				labeldisc = '';
				labeldisc2 = '';
				if(rowctr % invoicelimit == 0) {

					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
					pagectr = parseInt(pagectr) + 1;
					pagesubtotal = 0;

				}
				pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
				if(company_id == 14){
					// aquabest
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<tr ><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) + " " + labeldisc2 + "</td></tr>";
				} else {
					lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<tr ><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) +discount_label_2+ "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) +discount_label_1+ " " + labeldisc2 + "</td></tr>";
				}
				rowctr = parseInt(rowctr) + 1;
			}
			if(pagesubtotal > 0) {
				var consumable_payment =  data.consumable_total;
				if(parseFloat(consumable_payment) > 0){
					pagesubtotal = pagesubtotal - consumable_payment;
				}
				var subtotal = (pagesubtotal / vat);
				var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagesubtotal = pagesubtotal.toFixed(2);
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "</table>";
				var con_label_payment ='';
				if(consumable_payment){
					con_label_payment = "("+consumable_payment+")";
				}
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>"+con_label_payment+"</div>";

				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
				lamankadainvoice[pagectr] = lamankadainvoice[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
			}
			var printhtmlend = "";
			var reservedbyname = '';

			//var company_id = localStorage['company_id'];
			var agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'];
			if(company_id == 14){
				agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'] +"<br>"+cashier_name;
			}
			remarksvisible += "width:750px;overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all;  word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";

			printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" +agent_user_name + "</div>";
			printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "</div>";
			printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";

			//additional

			var termstxt = '';
			var ponumtxt = data.client_po;
			var tintxt = '';

			var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
			var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
			var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
			printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
			printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";

			var is_charge = data.is_charge;
			var charge_label = "";

			if(this.charge_label == 1){

				if(is_charge == 1){
					charge_label = "=====Cash On Delivery======";
				} else if (is_charge == 2){
					charge_label = "======CHARGE======";
				} else if (is_charge == 3){
					charge_label = "======PDC======";
				}

				if(styling['lbl']){

					var lblvisible = (styling['lbl']['visible']) ? 'display:inline-block;' : 'display:none;';
					var lblbold = (styling['lbl']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					printhtmlend = printhtmlend + "<div style='" + lblvisible + lblbold + "position:absolute;left:" + styling['lbl']['left'] + "px;top:" + styling['lbl']['top'] + "px;font-size:" + styling['lbl']['fontSize'] + "px;'>" + charge_label + "</div>";

				}

			}

			printhtmlend = printhtmlend + "</div>";

			var finalprint = "";

			var ctr_counter = 0;

			for(var i in lamankadainvoice) {
				var cinvoice = $('#custom_invoice').val();
				var nextinvoice = parseInt(localStorage['invoice']) + 1;
				var control_num = (cinvoice) ? cinvoice : nextinvoice;
				control_num = parseFloat(control_num) + parseFloat(ctr_counter);
				var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
				var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
				control_num = this.str_pad('000000', control_num, true);
				control_num = pref_inv + control_num + suf_inv;
				lamankadainvoice[i] = lamankadainvoice[i] + "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";
				finalprint = finalprint + printhtml + lamankadainvoice[i] + printhtmlend;
				ctr_counter++;
			}

			this.popUpPrint(finalprint);


		}, printElemCebu: function() {

			if(localStorage['print_inv'] == 0) {
				return true; // dont print invoice
			}

			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var member_id_test = data.member_id;
			var styling = JSON.parse(localStorage['invoice_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var output = data.date_sold;
			var printhtml = "";

			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

			var memlisttest = '';
			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}

			if(memlisttest) {
				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}
			}

			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var logovisible = (styling['logo']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var logoBold = (styling['logo']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;'>";
			if(styling['logo']['visible']) printhtml = printhtml + "<div style='"+logovisible+"'><img src='http://"+$('#_HOST').val()+"/css/img/logo.jpg' style='" + logovisible + logoBold + "position:absolute;top:" + styling['logo']['top'] + "px; left:" + styling['logo']['left'] + "px;width:" + styling['logo']['width'] + "px;height:" + styling['logo']['height'] + "px;' /></div>";
			printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'> <br/><br/>" + output + " </div><div style='clear:both;'></div>";
			printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;width:450px;'>" + member_name + "</div>";
			printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
			printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
			printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
			printhtml = printhtml + "<table id='itemscon' style='position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";

			var countallitem = $('#cart > tbody > tr').length;
			var invoicelimit = parseFloat(localStorage['invoice_limit']);
			var drlimit = localStorage['dr_limit'];
			var lamankadainvoice = [];
			var pagectr = 1;
			var rowctr = 1;
			var pagesubtotal = 0;
			var pagetax = 0;
			var pagegrandtotal = 0;
			var vat = 1.12;
			invoicelimit = parseInt(invoicelimit) + 1;
			var testdata = data.item_list;
			var company_id = localStorage['company_id'];

			if(this.printWithPrice == true){
				tdtotalvisible = 'display:none;';
				tdpricevisible = 'display:none;';
				paymentsvisible = 'display:none;';
				payments2visible = 'display:none;';
				payments3visible = 'display:none;';
			}


			var testdata = data.item_list;
			var same_discount = data.same_discount;
			var group_discount_type = [];
			var price_group_id = 0;
			for(var i in testdata) {
				var itemcode = testdata[i].item_code;
				var description = testdata[i].description;
				var b = testdata[i].barcode;
				var unit_name = testdata[i].unit_name;
				unit_name = (unit_name) ? unit_name : '';
				testdata[i].qty = replaceAll(testdata[i].qty,',','');
				var qty = testdata[i].qty;
				var price = testdata[i].price;
				var discount = testdata[i].discount;
				var total = testdata[i].total;
				var origtotal = total;
				var discount_type = testdata[i].discount_type;

				var dicount_percentage = "";
				var tmp_discount_label = "0";

				if(discount  && testdata[i].qty){
					var ind_discount = (discount / testdata[i].qty);
					dicount_percentage = (ind_discount / testdata[i].original_price) * 100;
					dicount_percentage = number_format(dicount_percentage,2,".","");
					if(testdata[i].discount_type){
						tmp_discount_label = testdata[i].discount_type.join();
					}
					if(testdata[i].hide_discount == 1){


						testdata[i].original_total = parseFloat(testdata[i].original_total) + parseFloat(discount);
						testdata[i].original_price = parseFloat(testdata[i].original_price) + parseFloat(ind_discount);
						discount = 0;
					}
				}

				if(itemcode && qty){
					tmp_discount_label = (tmp_discount_label) ? tmp_discount_label : "0";

					price_group_id = testdata[i].price_group_id;
					var todis = {
						barcode  : testdata[i].barcode,
						item_code  : itemcode,
						description  : description,
						qty  : qty,
						discount  : discount,
						price_group_id  : testdata[i].price_group_id,
						parent_category_id  : testdata[i].parent_category_id,
						unit_name  : unit_name,
						price  : testdata[i].original_price,
						total  : testdata[i].original_total,
						tmp_discount_label: tmp_discount_label
					};
					group_discount_type.push(todis);
				}

			}

			var prev_checker="";
			var generated_html="";
			var total_group = 0;
			var total_discount = 0;
			var total_overall = 0;
			console.log(group_discount_type);
			for(var i in group_discount_type){

				if(prev_checker !=="" && prev_checker !== group_discount_type[i].tmp_discount_label && price_group_id != 8){

					generated_html += "<tr>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";

					generated_html += "<td><strong>Gross</strong></td>";
					generated_html += "<td style='text-align:right;'>"+number_format(total_group,2)+"</td>";
					generated_html += "</tr>";

					if(prev_checker.indexOf(',') > -1){

						var splitted = prev_checker.split(",");
						var tmp = total_group;
						for(var j in splitted){
							var cur_disc = (splitted[j] / 100) * tmp;
							tmp = tmp - number_format(cur_disc,2,".","");
							generated_html += "<tr>";
							generated_html += "<td></td>";
							generated_html += "<td></td>";
							generated_html += "<td></td>";
							generated_html += "<td><strong>Disc "+ number_format(splitted[j],2)+"%</strong></td>";
							generated_html += "<td style='text-align:right;'>"+number_format(cur_disc,2)+"</td>";
							generated_html += "</tr>";
						}

					} else {

						generated_html += "<tr>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";


						generated_html += "<td ><strong>Disc "+ number_format(prev_checker,2)+"%</strong></td>";
						generated_html += "<td style='text-align:right;'>"+number_format(total_discount,2)+"</td>";
						generated_html += "</tr>";

					}

					// end discount
					generated_html += "<tr>";

					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";

					generated_html += "<td ><strong>Net</strong></td>";
					generated_html += "<td style='text-align:right;'>"+number_format((parseFloat(total_group)+ parseFloat(total_discount)),2)+"</td>";
					generated_html += "</tr>";
					total_group= 0;
					total_discount= 0;

				}

				var cur_price = group_discount_type[i].price;
				var cur_total = group_discount_type[i].total;


				if(cur_price == 0){

					if(parseFloat(group_discount_type[i].discount) > 0){
						cur_price = group_discount_type[i].discount;
						cur_total = parseFloat(cur_price) * parseFloat(group_discount_type[i].qty);
						group_discount_type[i].discount = 0;
					}
				} else if (parseFloat(group_discount_type[i].discount) > 0 || group_discount_type[i].price_group_id == 8){

					var ind_discount = group_discount_type[i].discount  / group_discount_type[i].qty;
					cur_price = parseFloat(cur_price) + parseFloat(ind_discount);
					cur_total = parseFloat(cur_price) * parseFloat(group_discount_type[i].qty);
					group_discount_type[i].discount = 0;

				}
				var display_bc = "";
				if(group_discount_type[i].barcode && group_discount_type[i].barcode.indexOf("CH") == -1){
					display_bc = group_discount_type[i].barcode + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				generated_html += "<tr>";
				generated_html += "<td style='width:320px;'>"+ display_bc  + group_discount_type[i].description+"</td>";
				generated_html += "<td style='width:40px;'>"+group_discount_type[i].unit_name +"</td>";
				generated_html += "<td style='width:40px;'>"+number_format(group_discount_type[i].qty,2)+"</td>";
				generated_html += "<td style='text-align:right;width:90px;'>"+ number_format(cur_price,2)+"</td>";
				generated_html += "<td style='text-align:right;width:110px;'>"+number_format(cur_total,2)+"</td>";
				generated_html += "</tr>";
				total_group = parseFloat(cur_total) + parseFloat(total_group);
				total_discount = parseFloat(group_discount_type[i].discount) + parseFloat(total_discount);
				prev_checker = group_discount_type[i].tmp_discount_label;
				total_overall = (parseFloat(cur_total) + parseFloat(group_discount_type[i].discount)) + parseFloat(total_overall);

			}

			if(total_group && price_group_id != 8){

				generated_html += "<tr>";
				generated_html += "<td></td>";
				generated_html += "<td></td>";
				generated_html += "<td></td>";

				generated_html += "<td ><strong>Gross</strong></td>";
				generated_html += "<td style='text-align:right;'>"+number_format(total_group,2)+"</td>";
				generated_html += "</tr>";
				if(prev_checker.indexOf(',') > -1){
					var splitted = prev_checker.split(",");
					var tmp = total_group;
					for(var j in splitted){
						var cur_disc = (splitted[j] / 100) * tmp;
						tmp = tmp - number_format(cur_disc,2,".","");
						generated_html += "<tr>";

						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";

						generated_html += "<td ><strong>Disc "+ number_format(splitted[j],2)+"%</strong></td>";
						generated_html += "<td style='text-align:right;'>"+number_format(cur_disc,2)+"</td>";
						generated_html += "</tr>";
					}
				} else {
					generated_html += "<tr>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";

					generated_html += "<td ><strong>Disc "+ number_format(prev_checker,2)+"%</strong></td>";
					generated_html += "<td style='text-align:right;'>"+ number_format(total_discount,2)+"</td>";
					generated_html += "</tr>";
				}

				generated_html += "<tr>";
				generated_html += "<td></td>";
				generated_html += "<td></td>";
				generated_html += "<td></td>";

				generated_html += "<td><strong>Net</strong></td>";
				generated_html += "<td style='text-align:right;'>"+number_format((parseFloat(total_group)+ parseFloat(total_discount)),2)+"</td>";
				generated_html += "</tr>";

				total_group= 0;
				total_discount= 0;

			}

			generated_html += "<tr><td></td><td></td><td></td><td></td><td>&nbsp;</td></tr>";
			generated_html += "<tr><td></td><td></td><td></td><td>Grand Total</td><td style='text-align:right;'>"+number_format(total_overall,2)+"</td></tr>";

			var printhtmlend = "";
			var reservedbyname = '';
			reservedbyname = data.sales_type;

			//var company_id = localStorage['company_id'];
			var agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'];
			if(company_id == 14){
				agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'] +"<br>"+cashier_name;
			}
			remarksvisible += "width:750px;overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all;  word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";

			printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" +agent_user_name + "</div>";
			printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "</div>";
			printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";

			var subtotal = total_overall / 1.12;
			var vatable = total_overall - subtotal;

			var upper_payment = number_format(total_overall,2) + "<br><br><br>" + number_format(vatable,2) + "<br><br><br>" + number_format(subtotal,2);
			var middle_payment = number_format(total_overall,2) + "<br><br><br>" + number_format(subtotal,2);
			var lower_payment = number_format(vatable,2) + "<br><br><br>" + number_format(total_overall,2);

			printhtmlend = printhtmlend + "<div style='" + paymentsvisible + paymentsBold + "position:absolute;left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>"+ upper_payment+"</div>";
			printhtmlend = printhtmlend + "<div style='" + payments2visible + payments2Bold + "position:absolute;left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>"+middle_payment+"</div>";
			printhtmlend = printhtmlend + "<div style='" + payments3visible + payments3Bold + "position:absolute;left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>"+lower_payment+"</div>";

			//additional

			var termstxt = '';
			var ponumtxt = data.client_po;
			var tintxt = data.tin_no;

			var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
			var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
			var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
			printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
			printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";

			var is_charge = data.is_charge;
			var charge_label = "";

			if(this.charge_label == 1){

				if(is_charge == 1){
					charge_label = "=====Cash On Delivery======";
				} else if (is_charge == 2){
					charge_label = "======CHARGE======";
				} else if (is_charge == 3){
					charge_label = "======PDC======";
				}

				if(styling['lbl']){

					var lblvisible = (styling['lbl']['visible']) ? 'display:inline-block;' : 'display:none;';
					var lblbold = (styling['lbl']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					printhtmlend = printhtmlend + "<div style='" + lblvisible + lblbold + "position:absolute;left:" + styling['lbl']['left'] + "px;top:" + styling['lbl']['top'] + "px;font-size:" + styling['lbl']['fontSize'] + "px;'>" + charge_label + "</div>";

				}
				var unit_group_total = data.item_list_sum;
				if(styling['lbl2']){
					var lbl2visible = (styling['lbl2']['visible']) ? 'display:inline-block;' : 'display:none;';
					var lbl2bold = (styling['lbl2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					printhtmlend = printhtmlend + "<div style='" + lbl2visible + lbl2bold + "position:absolute;left:" + styling['lbl2']['left'] + "px;top:" + styling['lbl2']['top'] + "px;font-size:" + styling['lbl2']['fontSize'] + "px;'>" + unit_group_total + "</div>";
				}

			}

			printhtmlend = printhtmlend + "</div>";
			var finalprint = "";
			var ctr_counter = 0;

			var cinvoice = $('#custom_invoice').val();
			var nextinvoice = parseInt(localStorage['invoice']) + 1;
			var control_num = (cinvoice) ? cinvoice : nextinvoice;
			control_num = parseFloat(control_num) + parseFloat(ctr_counter);
			var pref_inv = (localStorage['pref_inv']) ? localStorage['pref_inv'] : '';
			var suf_inv = (localStorage['suf_inv']) ? localStorage['suf_inv'] : '';
			control_num = this.str_pad('000000', control_num, true);
			control_num = pref_inv + control_num + suf_inv;
			printhtmlend = printhtmlend + "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";

			finalprint = printhtml + generated_html  + printhtmlend;

			this.popUpPrint(finalprint);


		}, printElemPr: function() {

			var local_datetime =  new Date().toLocaleString();

			if(localStorage['print_ir'] == 0) {
				return true; // dont print invoice
			}

			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var styling = JSON.parse(localStorage['ir_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var member_id_test = data.member_id;
			var output = data.date_sold;
			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

			var memlisttest = '';
			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}

			if(memlisttest) {

				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}

			}

			var styling = JSON.parse(localStorage['ir_format']);
			//var fontFamily = "font-family: \"Times New Roman\", Times, serif;letter-spacing:1px;";
			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			var logovisible = (styling['logo']['visible']) ? 'display:block;' : 'display:none;';
			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var logoBold = (styling['logo']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var howMany = 1;
			var combinePage = "";
			var due_date = data.due_date;

			var company_id = localStorage['company_id'];
			if(this.printWithPrice == true){
				tdtotalvisible = 'display:none;';
				tdpricevisible = 'display:none;';
				paymentsvisible = 'display:none;';
				payments2visible = 'display:none;';
				payments3visible = 'display:none;';
			}
			for(var countPage = 1; countPage <= howMany; countPage++) { // all page
				if(countPage == 1) { // hide price and total
					//tdpricevisible = 'display:none;';
					//tdtotalvisible = 'display:none;';
					tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
					tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';
				} else {
					tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
					tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';
				}

				var printhtml = "";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				if(styling['logo']['visible']) printhtml = printhtml + "<div style='"+logovisible+"'><img src='http://"+$('#_HOST').val()+"/css/img/logo.jpg' style='" + logovisible + logoBold + "position:absolute;top:" + styling['logo']['top'] + "px; left:" + styling['logo']['left'] + "px;width:" + styling['logo']['width'] + "px;height:" + styling['logo']['height'] + "px;' /></div>";
				printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'><br/><br/>" + output + " </div><div style='clear:both;'></div>";
				printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
				printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
				printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
				printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
				printhtml = printhtml + "<table id='itemscon' cellspacing='0' style='border-collapse:separate;border-spacing:0 -5px;position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";
				var countallitem = $('#cart > tbody > tr').length;
				var drlimit = localStorage['ir_limit'];
				var lamankadadr = [];
				var pagectr = 1;
				var rowctr = 1;
				var pagesubtotal = 0;
				var pagetax = 0;
				var pagegrandtotal = 0;
				var vat = 1.12;
				drlimit = parseInt(drlimit) + 1;

				var testdata = data.item_list;
				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>"+unit_name+"</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;


					var discount_type = testdata[i].discount_type;
					var discount_label_1 = "";
					var discount_label_2 = "";

					if(!(itemcode && price)){
						continue;
					}



					if(parseFloat(discount) > 0) {
						var perunitdisc = parseFloat(discount) / parseFloat(qty);
						var labeldisc = "<br/>(Disc. " + number_format(perunitdisc, 2) + ")";
						var labeldisc2 = "<br/>(" + number_format(discount, 2) + ")";
					} else {
						var labeldisc = '';
						var labeldisc2 = '';
					}

					 labeldisc = '';
					 labeldisc2 = '';

					if(rowctr % drlimit == 0) {
						var subtotal = (pagesubtotal / vat);
						var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
						subtotal = subtotal.toFixed(2);
						vatable = vatable.toFixed(2);
						pagesubtotal = pagesubtotal.toFixed(2);
						lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
						pagectr = parseInt(pagectr) + 1;
						pagesubtotal = 0;
					}
					pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
					if(company_id == 14){
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) + " " + labeldisc2 + "</td></tr>";

					} else {
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + discount_label_2 + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) + discount_label_1 + " " + labeldisc2 + "</td></tr>";

					}


					rowctr = parseInt(rowctr) + 1;
				}
				;
				if(pagesubtotal > 0) {
					var consumable_payment =  data.consumable_total;
					if(parseFloat(consumable_payment) > 0){
						pagesubtotal = pagesubtotal - consumable_payment;
					}
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
				}
				var printhtmlend = "";

				var reservedbyname = '';
				reservedbyname = data.sales_type;

				//var company_id = localStorage['company_id'];
				var agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'];
				if(company_id == 14){
					agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'] +"<br>"+cashier_name;
				}
				remarksvisible += "width:750px;overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all;  word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";

				printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" + agent_user_name + "</div>";
				printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "<br>" +local_datetime+ "</div>";
				printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";
				//additional
				//additional

				var termstxt = data.terms + "-" + data.due_date;

				var ponumtxt = data.client_po;
				var tintxt = '';

				var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';


				printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";
				var is_charge = data.is_charge;
				var charge_label = "";

				if(this.charge_label == 1){
					if(is_charge == 1){
						charge_label = "=====Cash On Delivery======";
					} else if (is_charge == 2){
						charge_label = "======CHARGE======";
					}else if (is_charge == 3){
						charge_label = "======PDC======";
					}
					if(styling['lbl']){
						var lblvisible = (styling['lbl']['visible']) ? 'display:inline-block;' : 'display:none;';
						var lblbold = (styling['lbl']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
						printhtmlend = printhtmlend + "<div style='" + lblvisible + lblbold + "position:absolute;left:" + styling['lbl']['left'] + "px;top:" + styling['lbl']['top'] + "px;font-size:" + styling['lbl']['fontSize'] + "px;'>" + charge_label + "</div>";
					}

				}
				printhtmlend = printhtmlend + "</div>";
				var finalprint = "";
				var ctr_counter = 0;
				for(var i in lamankadadr) {
					var cdr = $('#custom_pr').val();
					var nextdr = parseInt(localStorage['ir']) + 1;
					var control_num = (cdr) ? cdr : nextdr;
					if(data.pr && data.pr != "" && data.pr != "0" ){
						control_num = data.pr;
					}
					control_num = parseInt(control_num) + parseInt(ctr_counter);
					var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
					var suf_ir = (localStorage['suf_ir']) ? localStorage['suf_ir'] : '';
					control_num = this.str_pad('000000', control_num, true);
					control_num = pref_ir + control_num + suf_ir;
					control_num += "<span style='display:block;'>Order ID: " +  data.order_id+"</span>";
					lamankadadr[i] +=  "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";

					finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
					ctr_counter++;
				}
				finalprint = replaceAll(finalprint, 'undefined', '');
				combinePage += "<div>" + finalprint + "</div>";
			}
			this.popUpPrint(combinePage);
		}, printElemSv: function() {

			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var styling = JSON.parse(localStorage['sv_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var member_id_test = data.member_id;
			var output = data.date_sold;
			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

			var memlisttest = '';
			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}
			if(memlisttest) {
				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}
			}
			var styling = JSON.parse(localStorage['sv_format']);
			//var fontFamily = "font-family: \"Times New Roman\", Times, serif;letter-spacing:1px;";
			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			var logovisible = (styling['logo']['visible']) ? 'display:block;' : 'display:none;';
			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var logoBold = (styling['logo']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var howMany = 1;
			var combinePage = "";

			var company_id = localStorage['company_id'];
			if(this.printWithPrice == true){
				tdtotalvisible = 'display:none;';
				tdpricevisible = 'display:none;';
				paymentsvisible = 'display:none;';
				payments2visible = 'display:none;';
				payments3visible = 'display:none;';
			}
			for(var countPage = 1; countPage <= howMany; countPage++) { // all page
				if(countPage == 1) { // hide price and total
					tdpricevisible = 'display:none;';
					tdtotalvisible = 'display:none;';
				} else {
					tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
					tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';
				}

				var printhtml = "";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				if(styling['logo']['visible']) printhtml = printhtml + "<div style='"+logovisible+"'><img src='http://"+$('#_HOST').val()+"/css/img/logo.jpg' style='" + logovisible + logoBold + "position:absolute;top:" + styling['logo']['top'] + "px; left:" + styling['logo']['left'] + "px;width:" + styling['logo']['width'] + "px;height:" + styling['logo']['height'] + "px;' /></div>";
				printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'><br/><br/>" + output + " </div><div style='clear:both;'></div>";
				printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
				printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
				printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
				printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
				printhtml = printhtml + "<table id='itemscon' cellspacing='0' style='border-collapse:separate;border-spacing:0 -5px;position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";
				var countallitem = $('#cart > tbody > tr').length;
				var drlimit = localStorage['sv_limit'];
				var lamankadadr = [];
				var pagectr = 1;
				var rowctr = 1;
				var pagesubtotal = 0;
				var pagetax = 0;
				var pagegrandtotal = 0;
				var vat = 1.12;
				drlimit = parseInt(drlimit) + 1;

				var testdata = data.item_list;
				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>"+unit_name+"</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;
					if(parseFloat(discount) > 0) {
						var perunitdisc = parseFloat(discount) / parseFloat(qty);
						var labeldisc = "<br/>(Disc. " + number_format(perunitdisc, 2) + ")";
						var labeldisc2 = "<br/>(" + number_format(discount, 2) + ")";
					} else {
						var labeldisc = '';
						var labeldisc2 = '';
					}

					labeldisc = '';
					labeldisc2 = '';

					if(rowctr % drlimit == 0) {
						var subtotal = (pagesubtotal / vat);
						var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
						subtotal = subtotal.toFixed(2);
						vatable = vatable.toFixed(2);
						pagesubtotal = pagesubtotal.toFixed(2);
						lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
						pagectr = parseInt(pagectr) + 1;
						pagesubtotal = 0;
					}
					pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
					if(company_id == 14){
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) + " " + labeldisc2 + "</td></tr>";

					} else {
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) + " " + labeldisc2 + "</td></tr>";

					}


					rowctr = parseInt(rowctr) + 1;
				}
				;
				if(pagesubtotal > 0) {
					var consumable_payment =  data.consumable_total;
					if(parseFloat(consumable_payment) > 0){
						pagesubtotal = pagesubtotal - consumable_payment;
					}
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + pagesubtotal + "</div>";
				}
				var printhtmlend = "";

				var reservedbyname = '';
				reservedbyname = data.sales_type;
				var agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'];
				if(company_id == 14){
					agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'] +"<br>"+cashier_name;
				}
				remarksvisible += "width:750px;overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all;  word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";

				printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" +agent_user_name + "</div>";
				printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "</div>";
				printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";
				//additional
				//additional

				var termstxt = '';
				var ponumtxt = data.client_po;
				var tintxt = '';

				var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

				printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";

				printhtmlend = printhtmlend + "</div>";
				var finalprint = "";
				var ctr_counter = 0;
				for(var i in lamankadadr) {
					var cdr = $('#custom_sv').val();
					var nextdr = parseInt(localStorage['sv']) + 1;
					var control_num = (cdr) ? cdr : nextdr;
					if(data.sv && data.sv != "" && data.sv != "0" ){
						control_num = data.sv;
					}
					control_num = parseFloat(control_num) + parseFloat(ctr_counter);
					var pref_ir = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
					var suf_ir = (localStorage['suf_sv']) ? localStorage['suf_sv'] : '';
					control_num = this.str_pad('000000', control_num, true);
					control_num = pref_ir + control_num + suf_ir;
					control_num += "<span style='display:block;'>Order ID: " +  data.order_id+"</span>";
					lamankadadr[i] = lamankadadr[i] + "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";

					finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
					ctr_counter++;
				}
				finalprint = replaceAll(finalprint, 'undefined', '');
				combinePage += "<div>" + finalprint + "</div>";
			}
			this.popUpPrint(combinePage);
		}, printElemSR: function() {

			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var layout = JSON.parse(localStorage['sr_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var member_id_test = data.member_id;
			var output = data.date_sold;
			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];


			var nextsr = parseInt(localStorage['sr']) + 1;
			var control_num = nextsr;
			if(data.sr && data.sr != "" && data.sr != "0" ){
				control_num = data.sr;
			}

			var model_number = "";
			var serial_number = "";
			var scs = "";
			var sar = "";
			var client_name = member_name;
			var dt = output;
			var dr = control_num;


			var itemtablestyle = "style='position:absolute;top:" + layout['itemtable'].top+"px;left:"+layout['itemtable'].left+"px;font-size:"+layout['itemtable'].fontSize+"px;'";
			var model_number_style = "style='position:absolute;top:" + layout['terms'].top+"px;left:"+layout['terms'].left+"px;font-size:"+layout['terms'].fontSize+"px;'";
			var serial_number_style = "style='position:absolute;top:" + layout['ponum'].top+"px;left:"+layout['ponum'].left+"px;font-size:"+layout['ponum'].fontSize+"px;'";
			var scs_number_style = "style='position:absolute;top:" + layout['tin'].top+"px;left:"+layout['tin'].left+"px;font-size:"+layout['tin'].fontSize+"px;'";
			var sar_number_style = "style='position:absolute;top:" + layout['lbl'].top+"px;left:"+layout['lbl'].left+"px;font-size:"+layout['lbl'].fontSize+"px;'";
			var member_number_style = "style='position:absolute;top:" + layout['membername'].top+"px;left:"+layout['membername'].left+"px;font-size:"+layout['membername'].fontSize+"px;'";
			var total_style = "style='position:absolute;top:" + layout['payments'].top+"px;left:"+layout['payments'].left+"px;font-size:"+layout['payments'].fontSize+"px;'";
			var date_style = "style='position:absolute;top:" + layout['date'].top+"px;left:"+layout['date'].left+"px;font-size:"+layout['date'].fontSize+"px;'";
			var num_style = "style='position:absolute;top:" + layout['drnum'].top+"px;left:"+layout['drnum'].left+"px;font-size:"+layout['drnum'].fontSize+"px;'";





			var company_id = localStorage['company_id'];


			var printhtml = "";
			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
			printhtml= printhtml +  "<div "+model_number_style+">"+  model_number+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+serial_number_style+">"+  serial_number+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+scs_number_style+">"+  scs+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+sar_number_style+">"+  sar+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+member_number_style+">"+  client_name+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+date_style+">"+  dt+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+num_style+">"+  dr+ " </div><div style='clear:both;'></div>";
			printhtml += "<table "+itemtablestyle+">";







			var grand_total = 0;
			var testdata = data.item_list;
			for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>" + unit_name + "</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;


				grand_total = parseFloat(total) + parseFloat(grand_total);
				printhtml += "<tr>";
				printhtml += "<td style='width:"+layout['tdqty'].width+"px;padding-left:"+layout['tdqty'].left+"px;'>"+qty+"</td>";
				printhtml += "<td style='width:"+layout['tddescription'].width+"px;padding-left:"+layout['tddescription'].left+"px;'>"+description+"</td>";
				printhtml += "<td style='width:"+layout['tdprice'].width+"px;padding-left:"+layout['tdprice'].left+"px;'>"+number_format(price,2)+"</td>";
				printhtml += "<td style='width:"+layout['tdtotal'].width+"px;padding-left:"+layout['tdtotal'].left+"px;'>"+number_format(total,2)+"</td>";
				printhtml += "</tr>";


			}
			printhtml= printhtml +  "<div "+total_style+">"+  number_format(grand_total,2)+ " </div><div style='clear:both;'></div>";
			printhtml += "</table>";

			this.popUpPrint(printhtml);
		}, printElemTS: function() {

			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var layout = JSON.parse(localStorage['ts_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var member_id_test = data.member_id;
			var output = data.date_sold;
			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];


			var nextts = parseInt(localStorage['ts']) + 1;
			var cts = $('#custom_ts').val();
			var control_num = (cts) ? cts : nextts;
			console.log("sdfsdfsdf:" + control_num);
			if(data.ts && data.ts != "" && data.ts != "0" ){
				control_num = data.ts;
			}

			var client_name = member_name;
			var dt = output;
			var dr = control_num;



			var itemtablestyle = "style='position:absolute;top:" + layout['itemtable'].top+"px;left:"+layout['itemtable'].left+"px;font-size:"+layout['itemtable'].fontSize+"px;'";
			var member_number_style = "style='position:absolute;top:" + layout['membername'].top+"px;left:"+layout['membername'].left+"px;font-size:"+layout['membername'].fontSize+"px;'";
			var total_style = "style='position:absolute;top:" + layout['payments'].top+"px;left:"+layout['payments'].left+"px;font-size:"+layout['payments'].fontSize+"px;'";
			var date_style = "style='position:absolute;top:" + layout['date'].top+"px;left:"+layout['date'].left+"px;font-size:"+layout['date'].fontSize+"px;'";
			var num_style = "style='position:absolute;top:" + layout['drnum'].top+"px;left:"+layout['drnum'].left+"px;font-size:"+layout['drnum'].fontSize+"px;'";




			var company_id = localStorage['company_id'];


			var printhtml = "";
			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";

			printhtml= printhtml +  "<div "+member_number_style+">"+  client_name+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+date_style+">"+  dt+ " </div><div style='clear:both;'></div>";
			printhtml= printhtml +  "<div "+num_style+">"+  dr+ " </div><div style='clear:both;'></div>";

			printhtml += "<table "+itemtablestyle+">";







			var grand_total = 0;
			var testdata = data.item_list;
			for(var i in testdata) {
				var itemcode = testdata[i].item_code;
				var description = testdata[i].description;
				var b = testdata[i].barcode;
				var unit_name = testdata[i].unit_name;
				unit_name = (unit_name) ? unit_name : '';
				var qty = testdata[i].qty + "<td style='width:60px;'>" + unit_name + "</td>";
				var price = testdata[i].price;
				var discount = testdata[i].discount;
				var total = testdata[i].total;
				var origtotal = total;


				grand_total = parseFloat(total) + parseFloat(grand_total);
				printhtml += "<tr>";
				printhtml += "<td style='width:"+layout['tdqty'].width+"px;padding-left:"+layout['tdqty'].left+"px;'>"+qty+"</td>";
				printhtml += "<td style='width:"+layout['tddescription'].width+"px;padding-left:"+layout['tddescription'].left+"px;'>"+description+"</td>";
				printhtml += "<td style='width:"+layout['tdprice'].width+"px;padding-left:"+layout['tdprice'].left+"px;'>"+number_format(price,2)+"</td>";
				printhtml += "<td style='width:"+layout['tdtotal'].width+"px;padding-left:"+layout['tdtotal'].left+"px;'>"+number_format(total,2)+"</td>";
				printhtml += "</tr>";


			}
			printhtml= printhtml +  "<div "+total_style+">"+  number_format(grand_total,2)+ " </div><div style='clear:both;'></div>";
			printhtml += "</table>";

			this.popUpPrint(printhtml);
		},printElemDr: function() {


			var local_datetime =  new Date().toLocaleString();

			if(localStorage['print_dr'] == 0) {
				return true; // dont print invoice
			}

			var withoutformlayout = $('#with_form_style').val();
			var w_border = '', w_date = '<br/><br/>', w_member = '', w_member_address = '', w_table_head = '', w_header = '', wh_tablecss = '';
			var w_vat = '', w_vatable = '', w_total = '';

			if(withoutformlayout == 1) {

				w_border = "border:1px solid #ccc;";
				wh_tablecss = "margin: 0 auto;border-collapse:collapse;";
				w_date = "<span>Date: </span>";
				w_member = "<span>Client: </span>";
				w_member_address = "<span>Address: </span>";
				w_vat = "<span style='display:inline-block;width:70px;'>Vat: </span>";
				w_vatable = "<span style='display:inline-block;width:70px;'>Vatable: </span>";
				w_total = "<span style='display:inline-block;width:70px;'>Total: </span>";
				w_header = "<h1 style='font-weight:normal;text-align:center;margin-top:0px;'>" + $('#co_name').val();
				w_header += "<span style='font-weight:normal;display:block;font-size:14px;text-align:center;'>" + $('#co_desc').val() + "</span>";
				w_header += "<span style='font-weight:normal;display:block;font-size:14px;text-align:center;'>" + $('#co_address').val() + "</span>";
				w_header += "</h3>";

			}

			//var fontFamily = "font-family: \"Times New Roman\", Times, serif;letter-spacing:1px;";

			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			var data = this.print_data;
			var member_name = data.member_name;
			var terms = data.terms;
			var unit_group_total = data.item_list_sum;


			var cashier_name = data.cashier_name;
			var styling = JSON.parse(localStorage['dr_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var member_id_test = data.member_id;
			var output = data.date_sold;


			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

			var memlisttest = '';

			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}

			if(memlisttest) {
				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}
			}


			var styling = JSON.parse(localStorage['dr_format']);
			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var logovisible = (styling['logo']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var logoBold = (styling['logo']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var howMany = 1;
			var combinePage = "";
			var company_id = localStorage['company_id'];
			var due_date = data.due_date;

			//tdpricevisible
			//paymentsvisible,payments2visible,payments3visible


			for(var countPage = 1; countPage <= howMany; countPage++) { // all page
				if(countPage == 1) { // hide price and total
					//tdpricevisible = 'display:none;';
					//tdtotalvisible = 'display:none;';
					tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
					tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

				} else {
					tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
					tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';
				}
				if(this.printWithPrice == true){
					tdtotalvisible = 'display:none;';
					tdpricevisible = 'display:none;';
					paymentsvisible = 'display:none;';
					payments2visible = 'display:none;';
					payments3visible = 'display:none;';
				}
				var printhtml = "";

				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;" + w_header;
				if(styling['logo']['visible']) printhtml = printhtml + "<div style='"+logovisible+"'><img src='http://"+$('#_HOST').val()+"/css/img/logo.jpg' style='" + logovisible + logoBold + "position:absolute;top:" + styling['logo']['top'] + "px; left:" + styling['logo']['left'] + "px;width:" + styling['logo']['width'] + "px;height:" + styling['logo']['height'] + "px;' /></div>";
				printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'>" + w_date + output + " </div><div style='clear:both;'></div>";
				printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + w_member + member_name + "</div>";
				printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + w_member_address + station_name + "</div>";
				printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
				printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
				printhtml = printhtml + "<table id='itemscon'  style='" + wh_tablecss + "position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";

				if(withoutformlayout == 1) {
					w_table_head = "<tr ><th style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>Qty</th><th style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>Item</th><th style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> Description </th><th style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>Price</th><th style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>Total</th></tr>";
				}
				printhtml = printhtml + w_table_head;


				var countallitem = $('#cart > tbody > tr').length;
				var drlimit = localStorage['dr_limit'];
				var lamankadadr = [];
				var pagectr = 1;
				var rowctr = 1;
				var pagesubtotal = 0;
				var pagetax = 0;
				var pagegrandtotal = 0;
				var vat = 1.12;
				drlimit = parseInt(drlimit) + 1;

				var testdata = data.item_list;

				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;

					var is_freebie = "";
					is_freebie = (testdata[i].is_freebie == 1) ? "<span style='display:block;font-size:8px;padding:0px;margin:0px;'>(Free)</span>" : '';

					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = "";
					if(withoutformlayout == 1) {
						qty = testdata[i].qty + "<span> "+unit_name+"</span>";
					} else {
						 qty = testdata[i].qty + "<td style='width:45px;"+w_border+"'>"+unit_name+"</td>";
					}



					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;
					var discount_type = testdata[i].discount_type;
					var discount_label_1 = "";
					var discount_label_2 = "";
					if(!(itemcode && price)){
						continue;
					}
					/*try {

						if(discount_type.length){
							price = testdata[i].original_price;
							origtotal = price * testdata[i].qty;
							var tmp_price =0;
							for(var dd in discount_type){
								var temp_disc = discount_type[dd];
								temp_disc = ((temp_disc / 100) * (price - tmp_price)) * testdata[i].qty ;
								discount_label_1 += "<br>" + number_format(temp_disc,2);
								discount_label_2 += "<br>Less "+ number_format(discount_type[dd],2)+ ":";
								tmp_price = parseFloat(tmp_price) + parseFloat(temp_disc/testdata[i].qty);
							}
							discount_label_2 += "<br>Net: ";
							discount_label_1 += "<br>" + ((parseFloat(origtotal) + parseFloat(testdata[i].discount)).toFixed(2));
						}
					} catch(e){

					} */


					if(parseFloat(discount) > 0) {
						var perunitdisc = parseFloat(discount) / parseFloat(qty);
						var labeldisc = "<br/>(Disc. " + number_format(perunitdisc, 2) + ")";
						var labeldisc2 = "<br/>(" + number_format(discount, 2) + ")";

					} else {
						var labeldisc = '';
						var labeldisc2 = '';

					}

					labeldisc = '' ;
					labeldisc2 = '' ;

					if(rowctr % drlimit == 0) {
						var subtotal = (pagesubtotal / vat);
						var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
						subtotal = subtotal.toFixed(2);
						vatable = vatable.toFixed(2);
						pagesubtotal = pagesubtotal.toFixed(2);
						lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + w_vatable + subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + w_vat + vatable + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + w_total + pagesubtotal + "</div>";
						pagectr = parseInt(pagectr) + 1;
						pagesubtotal = 0;
					}
					pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
					if(company_id == 14){ // aquabest
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + "</td><td style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) + " " + labeldisc2 + is_freebie+"</td></tr>";

					} else {
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>" + number_format(price, 2) + discount_label_2 +"</td><td style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>" + number_format(origtotal, 2) +discount_label_1+ " " + labeldisc2 + is_freebie+ "</td></tr>";

					}

					rowctr = parseInt(rowctr) + 1;
				}
				;
				if(pagesubtotal > 0) {
					var consumable_payment =  data.consumable_total;
					if(parseFloat(consumable_payment) > 0){
						pagesubtotal = pagesubtotal - consumable_payment;
					}
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);
					if(withoutformlayout == 1) {
						for(var padrow = rowctr; padrow <= drlimit; padrow++) {
							lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + w_border + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'>&nbsp;<span style='padding-left:20px;'></span> </td><td style='" + w_border + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;'>&nbsp;</td><td style='" + w_border + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;'>&nbsp;</td></tr>";
						}
					}
					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + w_vatable + subtotal + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + w_vat + vatable + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + w_total + pagesubtotal + "</div>";
				}
				var printhtmlend = "";
				var reservedbyname = '';
				reservedbyname = data.sales_type + " " + reservedbyname;

				var agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'];

				if(company_id == 14){
					agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'] +"<br>"+cashier_name;
				}

				remarksvisible += "width:750px;overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all;  word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";
				printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" + agent_user_name +"</div>";
				printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks+ "<br> "+local_datetime+ "</div>";
				printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";

				var termstxt = data.terms + "-" + due_date;
				var ponumtxt = data.client_po;
				var tintxt = data.tin_no;

				var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';

				var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';



				printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";

				var is_charge = data.is_charge;
				var charge_label = "";

				if(this.charge_label == 1){
					if(is_charge == 1){
						charge_label = "=====Cash On Delivery======";
					} else if (is_charge == 2){
						charge_label = "======CHARGE======";
					}else if (is_charge == 3){
						charge_label = "======PDC======";
					}
					if(styling['lbl']){
						var lblvisible = (styling['lbl']['visible']) ? 'display:inline-block;' : 'display:none;';
						var lblbold = (styling['lbl']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
						printhtmlend = printhtmlend + "<div style='" + lblvisible + lblbold + "position:absolute;left:" + styling['lbl']['left'] + "px;top:" + styling['lbl']['top'] + "px;font-size:" + styling['lbl']['fontSize'] + "px;'>" + charge_label + "</div>";
					}
				}
				if(styling['lbl2']){
					var lbl2visible = (styling['lbl2']['visible']) ? 'display:inline-block;' : 'display:none;';
					var lbl2bold = (styling['lbl2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
					printhtmlend = printhtmlend + "<div style='" + lbl2visible + lbl2bold + "position:absolute;left:" + styling['lbl2']['left'] + "px;top:" + styling['lbl2']['top'] + "px;font-size:" + styling['lbl2']['fontSize'] + "px;'>" + unit_group_total + "</div>";
				}
				printhtmlend = printhtmlend + "</div>";
				var finalprint = "";
				var ctr_counter = 0;
				for(var i in lamankadadr) {
					var cdr = $('#custom_dr').val();
					var nextdr = parseInt(localStorage['dr']) + 1;
					var control_num = (cdr) ? cdr : nextdr;
					if(data.dr && data.dr != "" && data.dr != "0" ){
						control_num = data.dr;
					}
					control_num = parseInt(control_num) + parseInt(ctr_counter);
					var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
					var suf_dr = (localStorage['suf_dr']) ? localStorage['suf_dr'] : '';
					control_num = this.str_pad('000000', control_num, true);
					control_num = pref_dr + control_num + suf_dr;

					var str = "" + control_num;
					var pad = "000000";
					control_num = "#" + pad.substring(0, pad.length - str.length) + str;
					control_num = this.dr_label + " " + control_num;
					control_num += "<span style='display:block;'>Order ID: " +  data.order_id+"</span>";
					lamankadadr[i] = lamankadadr[i] + "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";
					finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
					ctr_counter++;
				}
				finalprint = replaceAll(finalprint, 'undefined', '');
				combinePage += "<div>" + finalprint + "</div>";
			}

			this.popUpPrint(combinePage);

		}, printElemNewsPrint: function(newsprint_type) {
			var local_datetime =  new Date().toLocaleString();
			if(localStorage['print_dr'] == 0) {
				return true; // dont print invoice
			}
			var PR_LABEL = this.pr_label;
			var DR_LABEL = this.dr_label;
			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var terms = data.terms;
			var styling = JSON.parse(localStorage['news_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var member_id_test = data.member_id;
			var special_discount_total = data.special_discount_total;

			var output = data.date_sold;
			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

			var memlisttest = '';
			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}
			if(memlisttest) {
				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}
			}
			var styling = JSON.parse(localStorage['news_format']);
			//var fontFamily = "font-family: \"Times New Roman\", Times, serif;letter-spacing:1px;";
			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var howMany = 1;
			var combinePage = "";
			var due_date = data.due_date;

			var config_company_name = $('#config_company_name').val();

			if(this.printWithPrice == true){
				tdtotalvisible = 'display:none;';
				tdpricevisible = 'display:none;';
				paymentsvisible = 'display:none;';
				payments2visible = 'display:none;';
				payments3visible = 'display:none;';
			}
			for(var countPage = 1; countPage <= howMany; countPage++) { // all page

				var printhtml = "";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'><br/><br/>" + output + " </div><div style='clear:both;'></div>";
				printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
				printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
				printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
				printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
				printhtml = printhtml + "<table id='itemscon' style='position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";
				var countallitem = $('#cart > tbody > tr').length;
				var drlimit = localStorage['dr_limit'];
				var lamankadadr = [];
				var pagectr = 1;
				var rowctr = 1;
				var pagesubtotal = 0;
				var pagetax = 0;
				var pagegrandtotal = 0;
				var pageorigtotal = 0;
				var pagetotaldiscount = 0;

				var vat = 1.12;
				drlimit = parseInt(drlimit) + 1;

				var testdata = data.item_list;
				var same_discount = data.same_discount;
				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>"+unit_name+"</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;
					var discount_type = testdata[i].discount_type;
					var discount_label_1 = "";
					var discount_label_2 = "";
					testdata[i].qty = replaceAll( testdata[i].qty,",","");
					if(testdata[i].original_price && testdata[i].qty){
						pageorigtotal = parseFloat(pageorigtotal) + (parseFloat(testdata[i].original_price) * parseFloat(testdata[i].qty));
					}

					try {

						if(discount_type.length ){
							price = testdata[i].original_price;

							origtotal = price * testdata[i].qty;
							if(same_discount == 0){
								var tmp_price =0;
								for(var dd in discount_type){
									var temp_disc = discount_type[dd];
									temp_disc = ((temp_disc / 100) * (price - tmp_price)) * testdata[i].qty ;
									discount_label_1 += "<br>" + number_format(temp_disc,2);
									discount_label_2 += "<br>Less "+ number_format(discount_type[dd],2)+ ":";
									tmp_price = parseFloat(tmp_price) + parseFloat(temp_disc/testdata[i].qty);
								}

								discount_label_2 += "<br>Net: ";
								discount_label_1 += "<br>" + ((parseFloat(origtotal) + parseFloat(testdata[i].discount)).toFixed(2));
							}


						}
					} catch(e){

					}

					if(parseFloat(discount) > 0) {
						var perunitdisc = parseFloat(discount) / parseFloat(qty);
						var labeldisc = "<br/>(Disc. " + number_format(perunitdisc, 2) + ")";
						var labeldisc2 = "<br/>(" + number_format(discount, 2) + ")";
					} else {
						var labeldisc = '';
						var labeldisc2 = '';
					}
					labeldisc = '';
					labeldisc2 = '';

					if(rowctr % drlimit == 0) {
						var subtotal = (pagesubtotal / vat);
						var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
						subtotal = subtotal.toFixed(2);
						vatable = vatable.toFixed(2);
						pagesubtotal = pagesubtotal.toFixed(2);


						lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
						if(config_company_name == 'cebuhiq'){
							var lbltotalbreakdown = "";
							if(discount_type.length){
								lbltotalbreakdown=" Grand Total: " + number_format(pageorigtotal,2) +"<br>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;Discount: " + number_format(pageorigtotal - pagesubtotal,2) +"<br>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; Net Total: " + number_format(pagesubtotal,2);
							} else {
								lbltotalbreakdown="<br><br>Grand Total: " + number_format(pagesubtotal,2);

							}
							lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + special_discount_label+ " &nbsp;&nbsp;"+lbltotalbreakdown+"</div>";
							lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+ (parseInt(styling['payments3']['top']) + parseInt(38)) +"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
							lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+(parseInt(styling['payments3']['top']) + parseInt(41))+"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";

						} else {
							lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>&nbsp;&nbsp; Grand Total: " + number_format(pagesubtotal,2) + "</div>";
							lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+ (parseInt(styling['payments3']['top']) + parseInt(12)) +"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
							lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+(parseInt(styling['payments3']['top']) + parseInt(15))+"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";

						}

						pagectr = parseInt(pagectr) + 1;
						pagesubtotal = 0;
						pageorigtotal = 0;
					}
					if(itemcode && price){
						pagesubtotal = parseFloat(pagesubtotal) + parseFloat(total);
					}

					lamankadadr[pagectr] = lamankadadr[pagectr] + "<tr ><td style='" + tdqtyvisible + tdqtyBold + "position:relative;width:" + styling['tdqty']['width'] + "px;padding-left:" + styling['tdqty']['left'] + "px;'>" + qty + "</td><td style='" + tdbarcodevisible + tdbarcodeBold + "position:relative;width:" + styling['tdbarcode']['width'] + "px;padding-left:" + styling['tdbarcode']['left'] + "px;'>" + itemcode + "</td><td style='" + tddescriptionvisible + tddescriptionBold + "position:relative;width:" + styling['tddescription']['width'] + "px;padding-left:" + styling['tddescription']['left'] + "px;'> " + description + " <span style='padding-left:20px;'>" + labeldisc + "</span> </td><td style='" + tdpricevisible + tdpriceBold + "position:relative;width:" + styling['tdprice']['width'] + "px;padding-left:" + styling['tdprice']['left'] + "px;text-align:right;'>" + ((price && itemcode) ? (number_format(price, 2) +discount_label_2) : testdata[i].price_label) + "</td><td style='" + tdtotalvisible + tdtotalBold + "position:relative;width:" + styling['tdtotal']['width'] + "px;padding-left:" + styling['tdtotal']['left'] + "px;text-align:right;'>" + number_format(origtotal, 2) +discount_label_1+ " " + labeldisc2 + "</td></tr>";
					rowctr = parseInt(rowctr) + 1;
				}

				if(pagesubtotal > 0) {
					var consumable_payment =  data.consumable_total;
					if(parseFloat(consumable_payment) > 0){
						pagesubtotal = pagesubtotal - consumable_payment;
					}
					var special_discount_label = "";
					if(special_discount_total){
						pagesubtotal = pagesubtotal - special_discount_total;
						special_discount_label = "<span style='display:block;margin-top:-15px;'> &nbsp;&nbsp; Discount: " + (parseFloat(special_discount_total).toFixed(2)) + "</span>";

						special_discount_total = '';
					}
					var subtotal = (pagesubtotal / vat);
					var vatable = parseFloat(pagesubtotal) - parseFloat(subtotal);
					subtotal = subtotal.toFixed(2);
					vatable = vatable.toFixed(2);
					pagesubtotal = pagesubtotal.toFixed(2);

					lamankadadr[pagectr] = lamankadadr[pagectr] + "</table>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+paymentsvisible+paymentsBold+"position:absolute; list-style-type: none; left:"+styling['payments']['left']+"px;top:"+((styling['payments']['top']) - 12) +"px;font-size:"+styling['payments']['fontSize']+"px;'>("+consumable_payment+")</div>";

					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + paymentsvisible + paymentsBold + "position:absolute; list-style-type: none; left:" + styling['payments']['left'] + "px;top:" + styling['payments']['top'] + "px;font-size:" + styling['payments']['fontSize'] + "px;'>" + subtotal + "</div>";
					lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments2visible + payments2Bold + "position:absolute; list-style-type: none; left:" + styling['payments2']['left'] + "px;top:" + styling['payments2']['top'] + "px;font-size:" + styling['payments2']['fontSize'] + "px;'>" + vatable + "</div>";
					if(config_company_name == 'cebuhiq'){
						var lbltotalbreakdown = "";
						if(discount_type.length){
							lbltotalbreakdown=" Grand Total: " + number_format(pageorigtotal,2) +"<br>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;Discount: " + number_format(pageorigtotal - pagesubtotal,2) +"<br>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; Net Total: " + number_format(pagesubtotal,2);
						} else {
							lbltotalbreakdown="<br><br>Grand Total: " + number_format(pagesubtotal,2);

						}
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + special_discount_label+ " &nbsp;&nbsp;"+lbltotalbreakdown+"</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+ (parseInt(styling['payments3']['top']) + parseInt(38)) +"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+(parseInt(styling['payments3']['top']) + parseInt(41))+"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
					} else {
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='" + payments3visible + payments3Bold + "position:absolute; list-style-type: none; left:" + styling['payments3']['left'] + "px;top:" + styling['payments3']['top'] + "px;font-size:" + styling['payments3']['fontSize'] + "px;'>" + special_discount_label+ " &nbsp;&nbsp; Grand Total: " + number_format(pagesubtotal,2)+ "</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+ (parseInt(styling['payments3']['top']) + parseInt(12)) +"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
						lamankadadr[pagectr] = lamankadadr[pagectr] + "<div style='"+payments3visible+payments3Bold+"position:absolute; list-style-type: none; left:"+styling['payments3']['left']+"px;top:"+(parseInt(styling['payments3']['top']) + parseInt(15))+"px;font-size:"+styling['payments3']['fontSize']+"px;width:255px;border-bottom:1px solid #000;'>&nbsp;</div>";
					}

				}
				var printhtmlend = "";
				var reservedbyname = '';
				reservedbyname = data.sales_type + " " + reservedbyname;
				var company_id = localStorage['company_id'];
				var agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'];
				if(company_id == 14){
					agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'] +"<br>"+cashier_name;
				}
				remarksvisible += "width:750px;width:600px;overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all;  word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";

				printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" + agent_user_name + "</div>";
				printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "<br>"+local_datetime+"</div>";
				printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";
				//additional
				//additional
				var cdr = $('#custom_dr').val();
				var cpr = $('#custom_pr').val();
				var csv = $('#custom_sv').val();
				var nextdr = parseInt(localStorage['dr']) + 1;
				var nextir = parseInt(localStorage['ir']) + 1;
				var nextsv = parseInt(localStorage['sv']) + 1;
				var control_num = '';
				if(newsprint_type == 1){
					var drnumctr =  nextdr;
						drnumctr = (cdr) ? cdr : nextdr;
					if(data.dr && data.dr != "" && data.dr != "0" ){
						drnumctr = data.dr;
					}

					var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
					drnumctr = this.str_pad('000000',drnumctr,true);
					drnumctr = pref_dr + drnumctr;
					control_num = DR_LABEL + " " + drnumctr;
				} else if(newsprint_type == 2){

					var irctrnum =  nextir;
					irctrnum = (cpr) ? cpr : nextir;
					if(data.pr && data.pr != "" && data.pr != "0"){
						irctrnum = data.pr;
					}

					var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
					irctrnum = this.str_pad('000000',irctrnum,true);
					irctrnum = pref_ir + irctrnum;
					control_num = PR_LABEL + " " +irctrnum;
				} else if(newsprint_type == 3){

					var svctrnum =  nextsv;
					svctrnum = (csv) ? csv : nextsv;
					if(data.sv && data.sv != "" && data.sv != "0"){
						svctrnum = data.sv;
					}

					var pref_sv = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
					svctrnum = this.str_pad('000000',svctrnum,true);
					svctrnum = pref_sv + svctrnum;
					control_num = "OV" + " " +svctrnum;
				}

				control_num += "<span style='display:block;'>Order ID: " +  data.order_id+"</span>";

				var termstxt = data.terms + "-" + due_date;

				var ponumtxt = data.client_po;
				var tintxt = data.tin_no;

				var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

				printhtmlend = printhtmlend + "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";
				printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";


				var is_charge = data.is_charge;
				var charge_label = "";

				if(this.charge_label == 1) {
					if(is_charge == 1) {
						charge_label = "=====Cash On Delivery======";
					} else if(is_charge == 2) {
						charge_label = "======CHARGE======";
					} else if(is_charge == 3) {
						charge_label = "======PDC======";
					}
					if(styling['lbl']) {
						var lblvisible = (styling['lbl']['visible']) ? 'display:inline-block;' : 'display:none;';
						var lblbold = (styling['lbl']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
						printhtmlend = printhtmlend + "<div style='" + lblvisible + lblbold + "position:absolute;left:" + styling['lbl']['left'] + "px;top:" + styling['lbl']['top'] + "px;font-size:" + styling['lbl']['fontSize'] + "px;'>" + charge_label + "</div>";
					}
				}
				printhtmlend = printhtmlend + "</div>";
				var finalprint = "";
				for(var i in lamankadadr) {
					finalprint = finalprint + printhtml + lamankadadr[i] + printhtmlend;
				}
				finalprint = replaceAll(finalprint, 'undefined', '');
				combinePage += "<div>" + finalprint + "</div>";
			}
			this.popUpPrint(combinePage);
		}, printElemNewsPrintCebu: function(newsprint_type) {
			var local_datetime =  new Date().toLocaleString();

			if(localStorage['print_dr'] == 0) {
				return true; // dont print invoice
			}
			var PR_LABEL = this.pr_label;
			var DR_LABEL = this.dr_label;
			var INVOICE_LABEL = this.invoice_label;
			var data = this.print_data;
			var member_name = data.member_name;
			var cashier_name = data.cashier_name;
			var terms = data.terms;
			var styling = JSON.parse(localStorage['news_format']);
			var remarks = data.remarks;
			var station_address = data.station_address;
			var station_id = data.station_id;
			var station_name = data.station_name;
			var member_id_test = data.member_id;
			var special_discount_total = data.special_discount_total;

			var output = data.date_sold;
			var printhtml = "";
			if(!member_name) member_name = '';
			if(!station_address) station_address = '';
			if(!station_id) station_id = '';
			if(!station_name) station_name = '';

			var mem_name_split;
			mem_name_split = member_name.split(',');
			member_name = mem_name_split[0];

			var memlisttest = '';
			if(localStorage['members']) {
				memlisttest = JSON.parse(localStorage['members']);
			}

			if(memlisttest) {
				for(var i in memlisttest) {
					var cur = memlisttest[i];
					if(cur.id == member_id_test) {
						station_name = cur.personal_address + "<br>Contact Person: " + cur.firstname + " " + cur.lastname + "<br>Contact #: " + cur.contact_number;
					}
				}
			}

			var styling = JSON.parse(localStorage['news_format']);
			//var fontFamily = "font-family: \"Times New Roman\", Times, serif;letter-spacing:1px;";
			var fontFamily = "font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;";
			var datevisible = (styling['date']['visible']) ? 'display:block;' : 'display:none;';
			var membernamevisible = (styling['membername']['visible']) ? 'display:block;' : 'display:none;';
			var memberaddressvisible = (styling['memberaddress']['visible']) ? 'display:block;' : 'display:none;';
			var stationnamevisible = (styling['stationname']['visible']) ? 'display:block;' : 'display:none;';
			var stationaddressvisible = (styling['stationaddress']['visible']) ? 'display:block;' : 'display:none;';
			var itemtablevisible = (styling['itemtable']['visible']) ? 'display:block;' : 'display:none;';
			var paymentsvisible = (styling['payments']['visible']) ? 'display:block;' : 'display:none;';
			var payments2visible = (styling['payments2']['visible']) ? 'display:block;' : 'display:none;';
			var payments3visible = (styling['payments3']['visible']) ? 'display:block;' : 'display:none;';
			var cashiervisible = (styling['cashier']['visible']) ? 'display:block;' : 'display:none;';
			var remarksvisible = (styling['remarks']['visible']) ? 'display:block;' : 'display:none;';
			var reservedvisible = (styling['reserved']['visible']) ? 'display:block;' : 'display:none;';
			var tdbarcodevisible = (styling['tdbarcode']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdqtyvisible = (styling['tdqty']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tddescriptionvisible = (styling['tddescription']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdpricevisible = (styling['tdprice']['visible']) ? 'display:inline-block;' : 'display:none;';
			var tdtotalvisible = (styling['tdtotal']['visible']) ? 'display:inline-block;' : 'display:none;';

			var dateBold = (styling['date']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var membernameBold = (styling['membername']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var memberaddressBold = (styling['memberaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationnameBold = (styling['stationname']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var stationaddressBold = (styling['stationaddress']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var itemtableBold = (styling['itemtable']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var paymentsBold = (styling['payments']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments2Bold = (styling['payments2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var payments3Bold = (styling['payments3']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var cashierBold = (styling['cashier']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var remarksBold = (styling['remarks']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var reservedBold = (styling['reserved']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdbarcodeBold = (styling['tdbarcode']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdqtyBold = (styling['tdqty']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tddescriptionBold = (styling['tddescription']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdpriceBold = (styling['tdprice']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
			var tdtotalBold = (styling['tdtotal']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

			var combinePage = "";
			var due_date = data.due_date;

			var config_company_name = $('#config_company_name').val();

			if(this.printWithPrice == true){
				tdtotalvisible = 'display:none;';
				tdpricevisible = 'display:none;';
				paymentsvisible = 'display:none;';
				payments2visible = 'display:none;';
				payments3visible = 'display:none;';
			}


				var printhtml = "";
				printhtml = printhtml + "<div id='maindivforprinting' style='page-break-before: always;position:relative;"+fontFamily+"'>&nbsp;";
				printhtml = printhtml + "<div style='" + datevisible + dateBold + "position:absolute;top:" + styling['date']['top'] + "px; left:" + styling['date']['left'] + "px;font-size:" + styling['date']['fontSize'] + "px;'><br/><br/>" + output + " </div><div style='clear:both;'></div>";
				printhtml = printhtml + "<div style='" + membernamevisible + membernameBold + "position:absolute;top:" + styling['membername']['top'] + "px; left:" + styling['membername']['left'] + "px;font-size:" + styling['membername']['fontSize'] + "px;'>" + member_name + "</div>";
				printhtml = printhtml + "<div style='" + memberaddressvisible + memberaddressBold + "position:absolute;top:" + styling['memberaddress']['top'] + "px; left:" + styling['memberaddress']['left'] + "px;width:" + styling['memberaddress']['width'] + "px;font-size:" + styling['memberaddress']['fontSize'] + "px;'>" + station_name + "</div>";
				printhtml = printhtml + "<div style='" + stationnamevisible + stationnameBold + "position:absolute;top:" + styling['stationname']['top'] + "px; left:" + styling['stationname']['left'] + "px;font-size:" + styling['stationname']['fontSize'] + "px;'>" + station_id + "</div>";
				printhtml = printhtml + "<div style='" + stationaddressvisible + stationaddressBold + "position:absolute;top:" + styling['stationaddress']['top'] + "px; left:" + styling['stationaddress']['left'] + "px;width:" + styling['stationaddress']['width'] + "px;font-size:" + styling['stationaddress']['fontSize'] + "px;'>" + station_address + "</div>";
				printhtml = printhtml + "<table id='itemscon'  style='width:90%;position:absolute;top:" + styling['itemtable']['top'] + "px;left:" + styling['itemtable']['left'] + "px;font-size:" + styling['itemtable']['fontSize'] + "px;'> ";
				var countallitem = $('#cart > tbody > tr').length;
				var drlimit = localStorage['dr_limit'];
				var lamankadadr = [];
				var pagectr = 1;
				var rowctr = 1;
				var pagesubtotal = 0;
				var pagetax = 0;
				var pagegrandtotal = 0;
				var pageorigtotal = 0;
				var pagetotaldiscount = 0;

				var vat = 1.12;
				drlimit = parseInt(drlimit) + 1;

				var testdata = data.item_list;
				var same_discount = data.same_discount;
				var group_discount_type = [];
				var price_group_id = 0;
				for(var i in testdata) {
					var itemcode = testdata[i].item_code;
					var description = testdata[i].description;
					var b = testdata[i].barcode;
					var unit_name = testdata[i].unit_name;
					unit_name = (unit_name) ? unit_name : '';
					var qty = testdata[i].qty + "<td style='width:60px;'>"+unit_name+"</td>";
					var price = testdata[i].price;
					var discount = testdata[i].discount;
					var total = testdata[i].total;
					var origtotal = total;
					var discount_type = testdata[i].discount_type;

					var dicount_percentage = "";
					var tmp_discount_label = "0";

					qty = replaceAll(qty,",","");
					if(discount && testdata[i].qty){
						var ind_discount = (discount / testdata[i].qty);
						dicount_percentage = (ind_discount / testdata[i].original_price) * 100;
						dicount_percentage = number_format(dicount_percentage,2,".","");
						if(testdata[i].discount_type){
							tmp_discount_label = testdata[i].discount_type.join();

						}
						if(testdata[i].hide_discount == 1){


							testdata[i].original_total = parseFloat(testdata[i].original_total) + parseFloat(discount);
							testdata[i].original_price = parseFloat(testdata[i].original_price) + parseFloat(ind_discount);
							discount = 0;
						}
					}

					if(itemcode && qty){
						tmp_discount_label = (tmp_discount_label) ? tmp_discount_label : "0";
						price_group_id = testdata[i].price_group_id;
						var todis = {
							item_code  : itemcode,
							description  : description,
							qty  : qty,
							discount  : discount,
							price_group_id  : testdata[i].price_group_id,
							parent_category_id  : testdata[i].parent_category_id,
							price  : testdata[i].original_price,
							total  : testdata[i].original_total,
							tmp_discount_label: tmp_discount_label
						};
						group_discount_type.push(todis);
					}



				}

				var prev_checker="";
				var generated_html="";
				var total_group = 0;
				var total_discount = 0;
				var total_overall = 0;

				for(var i in group_discount_type){

					if(prev_checker !=="" && prev_checker !== group_discount_type[i].tmp_discount_label && price_group_id != 8){

						generated_html += "<tr>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td><strong>Gross</strong></td>";
						generated_html += "<td style='text-align:right;'>"+number_format(total_group,2)+"</td>";
						generated_html += "</tr>";
						// discount
						if(prev_checker.indexOf(',') > -1){
							var splitted = prev_checker.split(",");
							var tmp = total_group;
							for(var j in splitted){
								var cur_disc = (splitted[j] / 100) * tmp;

									generated_html += "<tr>";
									generated_html += "<td></td>";
									generated_html += "<td></td>";
									generated_html += "<td></td>";
									generated_html += "<td></td>";
									generated_html += "<td ><strong>Disc "+ number_format(splitted[j],0)+"%</strong></td>";
									generated_html += "<td style='text-align:right;'>"+number_format(cur_disc,2)+"</td>";
									generated_html += "</tr>";


							}
						} else {

								generated_html += "<tr>";
								generated_html += "<td></td>";
								generated_html += "<td></td>";
								generated_html += "<td></td>";
								generated_html += "<td></td>";
								generated_html += "<td ><strong>Disc "+ number_format(prev_checker,0)+"%</strong></td>";
								generated_html += "<td style='text-align:right;'>"+number_format(total_discount,2)+"</td>";
								generated_html += "</tr>";

						}

						// end discount
						generated_html += "<tr>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td ><strong>Net</strong></td>";
						generated_html += "<td style='text-align:right;'>"+number_format((parseFloat(total_group)+ parseFloat(total_discount)),2)+"</td>";
						generated_html += "</tr>";

						total_group= 0;
						total_discount= 0;
					}
					var cur_price = group_discount_type[i].price;
					var cur_total = group_discount_type[i].total;

					if(cur_price == 0){
						if(parseFloat(group_discount_type[i].discount) > 0){
							cur_price = group_discount_type[i].discount;
							cur_total = parseFloat(cur_price) * parseFloat(group_discount_type[i].qty);
							group_discount_type[i].discount = 0;

						}
					} else if (parseFloat(group_discount_type[i].discount) > 0 || group_discount_type[i].price_group_id == 8){
						var ind_discount = parseFloat(group_discount_type[i].discount)  / parseFloat(group_discount_type[i].qty);
						cur_price = parseFloat(cur_price) + parseFloat(ind_discount);
						cur_total = parseFloat(cur_price) * parseFloat(group_discount_type[i].qty);
						group_discount_type[i].discount = 0;

					} else if (parseFloat(group_discount_type[i].discount) < 0 ){
						var ind_discount = parseFloat(group_discount_type[i].discount)  / parseFloat(group_discount_type[i].qty);

						if(parseFloat(cur_price) +  parseFloat(ind_discount) == 0){
							cur_price = 0;
							cur_total = 0;
							group_discount_type[i].discount = 0;
						}



					}
					generated_html += "<tr>";
					generated_html += "<td>"+group_discount_type[i].qty+"</td>";
					generated_html += "<td></td>";
					generated_html += "<td>"+group_discount_type[i].description+"</td>";
					generated_html += "<td style='text-align:right;'>"+ number_format(cur_price,2)+"</td>";
					generated_html += "<td style='text-align:right;'>"+number_format(cur_total,2)+"</td>";
					generated_html += "</tr>";

					total_group = parseFloat(cur_total) + parseFloat(total_group);
					total_discount = parseFloat(group_discount_type[i].discount) + parseFloat(total_discount);

					prev_checker = group_discount_type[i].tmp_discount_label;
					total_overall = (parseFloat(cur_total) + parseFloat(group_discount_type[i].discount)) + parseFloat(total_overall);

				}

				if(total_group && price_group_id != 8){

					generated_html += "<tr>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td ><strong>Gross</strong></td>";
					generated_html += "<td style='text-align:right;'>"+number_format(total_group,2)+"</td>";
					generated_html += "</tr>";
					if(prev_checker.indexOf(',') > -1){
						var splitted = prev_checker.split(",");
						var tmp = total_group;
						for(var j in splitted){
							var cur_disc = (splitted[j] / 100) * tmp;
							tmp = tmp - number_format(cur_disc,2,".","");

								generated_html += "<tr>";
								generated_html += "<td></td>";
								generated_html += "<td></td>";
								generated_html += "<td></td>";
								generated_html += "<td></td>";
								generated_html += "<td ><strong>Disc "+ number_format(splitted[j],0)+"%</strong></td>";
								generated_html += "<td style='text-align:right;'>"+number_format(cur_disc,2)+"</td>";
								generated_html += "</tr>";

						}
					} else {

						generated_html += "<tr>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td></td>";
						generated_html += "<td ><strong>Disc "+ number_format(prev_checker,0)+"%</strong></td>";
						generated_html += "<td style='text-align:right;'>"+ number_format(total_discount,2)+"</td>";
						generated_html += "</tr>";

					}
					generated_html += "<tr>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td></td>";
					generated_html += "<td><strong>Net</strong></td>";
					generated_html += "<td style='text-align:right;'>"+number_format((parseFloat(total_group)+ parseFloat(total_discount)),2)+"</td>";
					generated_html += "</tr>";

					total_group= 0;
					total_discount= 0;
				}
			generated_html += "<tr><td></td><td></td><td></td><td></td><td></td><td>&nbsp;</td></tr>";
			generated_html += "<tr><td></td><td></td><td></td><td></td><td>Grand Total</td><td style='text-align:right;'>"+number_format(total_overall,2)+"</td></tr>";

				var printhtmlend = "";
				var reservedbyname = '';
				reservedbyname = data.sales_type + " " + reservedbyname;
				var company_id = localStorage['company_id'];
				var agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'];
				if(company_id == 14){
					agent_user_name = localStorage['current_lastname'] + ", " + localStorage['current_firstname'] +"<br>"+cashier_name;
				}
				remarksvisible += "width:750px;width:600px;overflow-wrap: break-word; word-wrap: break-word; -ms-word-break: break-all;  word-break: break-all; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;";

				printhtmlend = printhtmlend + "<div style='" + cashiervisible + cashierBold + "position:absolute;left:" + styling['cashier']['left'] + "px;top:" + styling['cashier']['top'] + "px;font-size:" + styling['cashier']['fontSize'] + "px;'>" + agent_user_name + " &nbsp;&nbsp;&nbsp;&nbsp;</div>";
				printhtmlend = printhtmlend + "<div style='" + remarksvisible + remarksBold + "position:absolute;left:" + styling['remarks']['left'] + "px;top:" + styling['remarks']['top'] + "px;font-size:" + styling['remarks']['fontSize'] + "px;'>" + remarks + "<br>"+local_datetime+"</div>";
				printhtmlend = printhtmlend + "<div style='" + reservedvisible + reservedBold + "position:absolute;left:" + styling['reserved']['left'] + "px;top:" + styling['reserved']['top'] + "px;font-size:" + styling['reserved']['fontSize'] + "px;'>" + reservedbyname + "</div>";
				printhtmlend = printhtmlend + "<div style='position:absolute;left:280px;top:860px;'>Received By:<br>_________________________</div>";
				//additional
				//additional
				var cdr = $('#custom_dr').val();
				var cpr = $('#custom_pr').val();
				var csv = $('#custom_sv').val();
				var inv = $('#custom_invoice').val();
				var nextdr = parseInt(localStorage['dr']) + 1;
				var nextir = parseInt(localStorage['ir']) + 1;
				var nextsv = parseInt(localStorage['sv']) + 1;
				var nextinv = parseInt(localStorage['invoice']) + 1;
				var control_num = '';
				if(newsprint_type == 1){
					var drnumctr =  nextdr;
					drnumctr = (cdr) ? cdr : nextdr;
					if(data.dr && data.dr != "" && data.dr != "0" ){
						drnumctr = data.dr;
					}

					var pref_dr = (localStorage['pref_dr']) ? localStorage['pref_dr'] : '';
					drnumctr = this.str_pad('000000',drnumctr,true);
					drnumctr = pref_dr + drnumctr;
					control_num = DR_LABEL + " " + drnumctr;
				} else if(newsprint_type == 2){

					var irctrnum =  nextir;
					irctrnum = (cpr) ? cpr : nextir;
					if(data.pr && data.pr != "" && data.pr != "0"){
						irctrnum = data.pr;
					}

					var pref_ir = (localStorage['pref_ir']) ? localStorage['pref_ir'] : '';
					irctrnum = this.str_pad('000000',irctrnum,true);
					irctrnum = pref_ir + irctrnum;
					control_num = PR_LABEL + " " +irctrnum;
				} else if(newsprint_type == 3){

					var svctrnum =  nextsv;
					svctrnum = (csv) ? csv : nextsv;
					if(data.sv && data.sv != "" && data.sv != "0"){
						svctrnum = data.sv;
					}

					var pref_sv = (localStorage['pref_sv']) ? localStorage['pref_sv'] : '';
					svctrnum = this.str_pad('000000',svctrnum,true);
					svctrnum = pref_sv + svctrnum;
					control_num = "OV" + " " +svctrnum;
				} else 	if(newsprint_type == 4){
					var invnumctr =  nextinv;
					invnumctr = (inv) ? inv : nextinv;
					if(data.invoice && data.invoice != "" && data.invoice != "0" ){
						invnumctr = data.invoice;
					}

					var pref_inv = (localStorage['pref_invoice']) ? localStorage['pref_invoice'] : '';
					invnumctr = this.str_pad('000000',invnumctr,true);
					invnumctr = pref_inv + invnumctr;
					control_num = INVOICE_LABEL + " " + invnumctr;
				}

				control_num += "<span style='display:block;'>Order ID: " +  data.order_id+"</span>";

				var termstxt = data.terms + "-" + due_date;

				var ponumtxt = data.client_po;
				var tintxt = data.tin_no;

				var termsvisible = (styling['terms']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumvisible = (styling['drnum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var ponumvisible = (styling['ponum']['visible']) ? 'display:inline-block;' : 'display:none;';
				var tinvisible = (styling['tin']['visible']) ? 'display:inline-block;' : 'display:none;';
				var drnumbold = (styling['drnum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var termsbold = (styling['terms']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var ponumbold = (styling['ponum']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
				var tinbold = (styling['tin']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';

				printhtmlend = printhtmlend + "<div style='" + drnumvisible + drnumbold + "position:absolute;left:" + styling['drnum']['left'] + "px;top:" + styling['drnum']['top'] + "px;font-size:" + styling['drnum']['fontSize'] + "px;'>" + control_num + "</div>";
				printhtmlend = printhtmlend + "<div style='" + termsvisible + termsbold + "position:absolute;left:" + styling['terms']['left'] + "px;top:" + styling['terms']['top'] + "px;font-size:" + styling['terms']['fontSize'] + "px;'>" + termstxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + ponumvisible + ponumbold + "position:absolute;left:" + styling['ponum']['left'] + "px;top:" + styling['ponum']['top'] + "px;font-size:" + styling['ponum']['fontSize'] + "px;'>" + ponumtxt + "</div>";
				printhtmlend = printhtmlend + "<div style='" + tinvisible + tinbold + "position:absolute;left:" + styling['tin']['left'] + "px;top:" + styling['tin']['top'] + "px;font-size:" + styling['tin']['fontSize'] + "px;'>" + tintxt + "</div>";


				var is_charge = data.is_charge;
				var charge_label = "";

				if(this.charge_label == 1) {
					if(is_charge == 1) {
						charge_label = "=====Cash On Delivery======";
					} else if(is_charge == 2) {
						charge_label = "======CHARGE======";
					} else if(is_charge == 3) {
						charge_label = "======PDC======";
					}
					if(styling['lbl']) {
						var lblvisible = (styling['lbl']['visible']) ? 'display:inline-block;' : 'display:none;';
						var lblbold = (styling['lbl']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
						printhtmlend = printhtmlend + "<div style='" + lblvisible + lblbold + "position:absolute;left:" + styling['lbl']['left'] + "px;top:" + styling['lbl']['top'] + "px;font-size:" + styling['lbl']['fontSize'] + "px;'>" + charge_label + "</div>";
					}
					var unit_group_total = data.item_list_sum;
					if(styling['lbl2']){
						var lbl2visible = (styling['lbl2']['visible']) ? 'display:inline-block;' : 'display:none;';
						var lbl2bold = (styling['lbl2']['bold']) ? 'font-weight:bold;' : 'font-weight:normal;';
						printhtmlend = printhtmlend + "<div style='" + lbl2visible + lbl2bold + "position:absolute;left:" + styling['lbl2']['left'] + "px;top:" + styling['lbl2']['top'] + "px;font-size:" + styling['lbl2']['fontSize'] + "px;'>" + unit_group_total + "</div>";
					}
				}
				printhtmlend = printhtmlend + "</div>";
				var finalprint = "";

				finalprint =   printhtml + generated_html + printhtmlend;

				finalprint = replaceAll(finalprint, 'undefined', '');
				combinePage += "<div>" + finalprint + "</div>";

			this.popUpPrint(combinePage);
		}, printBackload: function(){

			var date_obj = new Date();
			var curDate = (parseInt(date_obj.getMonth()) + parseInt(1)) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
			var cur_order_det = this.current_order_det;
			var page = "<div class='perpage' style='page-break-after:always;' >";
			var ctrnum = '';
			if(cur_order_det.dr != 0) {
				ctrnum = cur_order_det.dr;
			} else if(cur_order_det.pr != 0) {
				ctrnum = cur_order_det.pr;
			}
			var cur_order = this.backload_data;

			page += "<h3 class='text-center'>" + localStorage['company_name'] + "</h3>";
			page += "<p class='text-center text-muted'>BACKLOAD FORM</p>";
			page += "<p style='font-size:10px;' class='text-right'>ORDER ID# <span style='width:80px;display:inline-block;margin-left:5px;' class='text-left'>" + cur_order_det.id + "</span></p>";
			page += "<div style='font-size:10px;'  class=''>";
			page += "<div class='pull-right'>";
			page += "<p>Date: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + curDate + "</span></p>";
			page += "</div>";
			page += "<p>Branch: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + cur_order_det.branch_name + "</span></p>";
			page += "<div class='pull-right'>";
			page += "<p>DR: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + (ctrnum) + "</span></p>";
			page += "</div>";
			page += "<p>Client: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + cur_order_det.fullname + "</span></p>";
			page += "</div>";
			page += "<hr>";
			page += "<table  style='font-size:10px;' class='table table-bordered table-condensed'>";
			page += "<tr><th>Qty</th><th>Item Code</th><th>Description</th><th>Remarks</th><th>Price</th><th>Total</th></tr>";
			var ctr = 0;
			var total_grand = 0;

			for(var i in cur_order) {
				var item_code = cur_order[i].item_code;
				var description = cur_order[i].description;
				var backload_qty = cur_order[i].backload_qty;
				var remarks = cur_order[i].backload_remarks;
				var price = cur_order[i].price;
				var price_adjustment = cur_order[i].price_adjustment;
				var total = ((parseFloat(price) + parseFloat(price_adjustment)) * cur_order[i].qty) + parseFloat(cur_order[i].member_adjustment);
				var ind_price = total / cur_order[i].qty;
				var total_adjusted = ind_price * cur_order[i].backload_qty;
				total_grand = parseFloat(total_grand) + parseFloat(total_adjusted);
				if(!parseInt(backload_qty)){
					continue;
				}
				page += "<tr><td>"+backload_qty+"</td><td>"+item_code+"</td><td>"+description+"</td><td>"+remarks+"</td><td>"+ number_format(ind_price,2)+"</td><td>"+ number_format(total_adjusted,2)+"</td></tr>";
				ctr++;
			}

			page += "<tr><td></td><td></td><td></td><td></td><td>Total</td><td>"+ number_format(total_grand,2)+"</td></tr>";

			page += '</table>';
			page += "<p>Returned By:<span style='width:130px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;margin-right:5px;'></span> Received By:<span style='width:130px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;margin-right:5px;'></span> Approved By: <span style='width:130px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;margin-right:5px;'></span></p>";


			this.popUpPrintWithStyle(page);


		}, printRackLocation: function() {
			var cur_order = this.current_order_obj;
			var cur_order_det = this.current_order_det;

			var date_obj = new Date();
			var curDate = (parseInt(date_obj.getMonth()) + parseInt(1)) + "/" + date_obj.getDate() + "/" + date_obj.getFullYear();
			var ctrnum = '';

			if(cur_order_det.dr != 0) {
				ctrnum = cur_order_det.dr;
			} else if(cur_order_det.pr != 0) {
				ctrnum = cur_order_det.pr;
			}
			var page = "<div class='perpage' style='page-break-after:always;' >";
			page += "<h3 class='text-center'>" + localStorage['company_name'] + "</h3>";
			page += "<p class='text-center text-muted'></p>";
			page += "<p style='font-size:10px;' class='text-right'>ORDER ID# <span style='width:80px;display:inline-block;margin-left:5px;' class='text-left'>" + this.current_order + "</span></p>";
			page += "<div style='font-size:10px;'  class=''>";
			page += "<div class='pull-right'>";
			page += "<p>Date: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + curDate + "</span></p>";
			page += "</div>";
			page += "<p>Branch: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + cur_order_det.branch_name + "</span></p>";
			page += "<div class='pull-right'>";
			page += "<p>DR: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + (ctrnum) + "</span></p>";
			page += "</div>";
			page += "<p>Client: <span style='width:270px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + cur_order_det.fullname + "</span></p>";
			page += "</div>";
			page += "<p>Remarks: <span style='width:650px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + cur_order_det.remarks + "</span></p>";
			page += "<p>Date Approved: <span style='width:610px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'>" + cur_order_det.approved_date + "</span></p>";
			page += "<hr>";
			page += "<table  style='font-size:10px;' class='table table-bordered'>";
			page += "<tr><th>Item</th><th>Quantity</th><th>Racking</th></tr>";

			var pageitem = [];
			var ctr = 1;
			var strholder = '';
			var arrStockman = [];
			var finalarr = [];
			for(var i in cur_order) {
				var serial_count = cur_order[i].has_serial;
				var item_code = cur_order[i].item_code;
				var description = cur_order[i].description;
				var qty = cur_order[i].qty;
				var racking = JSON.parse(cur_order[i].racking);

				//strholder += "<tr style='min-height:50px;'><td style='width:250px;'>" +item_code+"<br><small class='text-danger'>" + description + "</small></td><td>"+qty+"</td><td style='width:400px;'>";
				if(cur_order[i].is_bundle == 1) {
					try {
						var bundledet = JSON.parse(cur_order[i].bundles);

						for(var j in bundledet) {
							var bundlename = bundledet[j].description + " - Needed: " + (parseFloat(bundledet[j].child_qty) * parseFloat(cur_order[i].qty));
							var bund_qty = (parseFloat(bundledet[j].child_qty) * parseFloat(cur_order[i].qty));
							var rackbund = bundledet[j].rackhtml;
							try {
								var rackjson = JSON.parse(bundledet[j].rackjson);
								for(var rj in rackjson) {
									finalarr.push({
										stock_man: rackjson[rj].stock_man,
										rack: rackjson[rj].rack,
										qty: rackjson[rj].qty,
										item_code: bundledet[j].item_code,
										description: bundledet[j].description +"<br><small style='color:#000000;'>For: "+ item_code +"</small>",
										serial_count: serial_count
									});
								}
							} catch(e) {
								console.log("Error");
							}
							//			strholder += "<div><p><small>"+bundlename+"</small></p><p>"+rackbund+"</p></div>";
						}
					} catch(e) {

					}

				} else {

					for(var r in racking) {
						if(racking[r].stock_man) {
							if($.inArray(racking[r].stock_man, arrStockman) != -1) {
								arrStockman.push(racking[r].stock_man);
							}
						}
						finalarr.push({
							stock_man: racking[r].stock_man,
							rack: racking[r].rack,
							qty: racking[r].qty,
							item_code: item_code,
							description: description,
							serial_count: serial_count
						});
						//		strholder += "<div>"+racking[r].rack+" : "+racking[r].qty+"<br>In charge: "+racking[r].stock_man+"</div>";
					}
				}

				//strholder += "</td></tr>";
				if(ctr % 12 == 0) {
					//	pageitem.push(strholder);
					strholder = '';
				}
				ctr += 1;
			}
			if(this.different_unit == 0){
				finalarr.sort(this.sortByStockman);
			}

			var ctr2 = 1;
			for(var j in finalarr) {
				strholder += "<tr><td style='width:250px;'>" + finalarr[j].item_code + "<br><small class='text-danger'>" + finalarr[j].description + "</small></td><td>" + finalarr[j].qty + "</td><td style='width:400px;'>";
				strholder += "<div>" + finalarr[j].stock_man + " : " + finalarr[j].rack + "</div>";
				strholder += "</td></tr>";
				var has_serial = parseInt(finalarr[j].serial_count);
				if(has_serial == 1 && cur_order_det.member_id != 0) {
					var ctrser = parseInt(finalarr[j].qty);
					ctr2 += 1;
					for(var ind = 1; ind <= ctrser; ind++) {
						strholder += "<tr><td colspan='3'>Serial " + ind + ": </td></tr>";

						if(ctr2 % 12 == 0) {
							pageitem.push(strholder);
							strholder = '';
						}
						ctr2 += 1;
					}
				} else {
					if(ctr2 % 12 == 0) {
						pageitem.push(strholder);
						strholder = '';
					}
					ctr2 += 1;
				}
			}
			var num = Math.ceil((ctr / 12) * 12);
			if(ctr < 12) {
				while(ctr != num + 1) {
					strholder += "<tr style='height:25px;'><td></td><td></td><td></td></tr>";
					ctr += 1;
				}
				pageitem.push(strholder);
				strholder = '';
			} else {
				while(ctr != num + 1) {
					strholder += "<tr style='height:25px;'><td></td><td></td><td></td></tr>";
					ctr += 1;
				}
				pageitem.push(strholder);
				strholder = '';
			}
			var endtable = '</table>';
			var pageend = "";
			pageend += "</div>";
			var countpages = pageitem.length;
			var pageof = 1;
			var finalhtml = "";
			for(var j in pageitem) {
				finalhtml += page;
				finalhtml += pageitem[j];
				finalhtml += endtable;
				finalhtml += "<br><br><div><div class='pull-right'><p  style='font-size:10px;'>Checked By: <span style='width:200px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:13px;'></span></p></div><p  style='font-size:10px;'>Released By: <span style='width:200px;display:inline-block;border-bottom: 1px solid #ccc;margin-left:5px;'></span></p></div>";
				finalhtml += "<p class='text-center' style='color:#ccc;font-size:0.8em;'>Page " + pageof + " of " + countpages + "</p>";
				pageof += 1;
				finalhtml += pageend;

			}

			this.popUpPrintWithStyle(finalhtml);
		}, sortByStockman: function(a, b) {
			var aName = a.stock_man.toLowerCase();
			var bName = b.stock_man.toLowerCase();
			return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
		}, appendItem: function() {
			if(this.request.item_id && this.request.qty) {
				var itemCon = $('#item_id');
				var is_bundle = itemCon.select2('data').is_bundle;
				var item_id = itemCon.val();
				var item_code = (itemCon.select2('data').text).split(':');
				var price = item_code[3]; // not the adjusted price
				var request = this.request;
				var items = this.items;
				var vuecon = this;
				var member_id = this.request.member_id;
				var for_pickup = this.request.for_pickup;




				if(this.different_unit == 1)
				{
					request.orig_qty = request.qty;
					request.preferred_unit = $('#dif_qty option:selected').text();
					request.qty = this.dif_qty * this.request.qty;
				}

				var qty = this.request.qty;
				if(this.isAlreadyAdded(item_id)) {
					alertify.alert('Already in cart');
					request.item_id = '';
					request.qty = '';
					itemCon.select2('val', null);
					return;
				}
				$('#btnAdd').attr('disabled', true);
				$('#btnAdd').html('Loading...');
				$.ajax({
					url: '../ajax/ajax_query2.php',
					type: 'POST',
					data: {
						functionName: 'getAdjustmentPrice',
						branch_id: request.branch_id,
						branch_id_to: request.branch_id_to,
						shipping_company_id: request.shipping_company_id,
						item_id: item_id,
						member_id: member_id,
						qty: qty,
						price_group_id:request.price_group_id,
						for_pickup:for_pickup,
					},
					success: function(data) {
						$('#btnAdd').html('Add Item');
						var dt = JSON.parse(data);
						var data = dt.data;
						var splitted = data.split('||');

						//console.log(parseInt(splitted[3]));
						//console.log(parseInt(vuecon.order_limit) + "<" + parseInt(splitted[3]) + parseInt(vuecon.cart_item_ctr));
						if(vuecon.request.member_id && parseInt(vuecon.order_limit) < parseInt(splitted[3]) + parseInt(vuecon.cart_item_ctr)) {
							showToast('error', '<p>You already reached the order limit. You can only order 15 items in a single request.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
							itemCon.select2('val', null);
							vuecon.disableMemberAndBranch();
							request.item_id = '';
							request.qty = '';
							return;
						}


						if(splitted[2] == 0) {
							if(splitted[5]){
								alertify.alert("<div style='height:250px;overflow-y:auto;'>" + splitted[5] + "</div>");
							} else {
								showToast('error', '<p>Invalid quantity. <br>Remaining stocks: <h4>'+splitted[4]+'</h4></p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
							}

							itemCon.select2('val', null);
							vuecon.disableMemberAndBranch();
							request.item_id = '';
							request.qty = '';
						} else {
							vuecon.cart_item_ctr = parseInt(splitted[3]) + parseInt(vuecon.cart_item_ctr);
							price = parseFloat(price) + parseFloat(splitted[0]);
							var adjustmentmem = splitted[1];
							item_code = "<p>" + item_code[2] + "<small style='display:block' class='text-danger'>" + item_code[1] + "</small></p>";
							var addtl_price =  (request.addtl_disc) ? request.addtl_disc : 0;
							request.addtl_disc = (request.addtl_disc) ? request.addtl_disc : '';
							if(request.addtl_disc.indexOf("%") > 0){
								var ind_discount = 0;
								if(parseFloat(adjustmentmem)){
									ind_discount = adjustmentmem / request.qty;
								}
								addtl_price = replaceAll(addtl_price,"%",'');
								addtl_price = addtl_price / 100;
								addtl_price = (parseFloat(price) + parseFloat(ind_discount)) * addtl_price;
								addtl_price = addtl_price * request.qty;
							}

							if(isNaN(addtl_price))  addtl_price = 0;

							var total = parseFloat(price) * parseFloat(request.qty);

							adjustmentmem = parseFloat(adjustmentmem) - addtl_price;



							total = parseFloat(total) + parseFloat(adjustmentmem);
							total = number_format(total, 2);

						 // add at the beginning
						 // group_adjustment_list
							if(dt.freebies.length > 0){
								for(var j in dt.freebies){
									var free_price = parseFloat(dt.freebies[j].price);
									var free_total = parseFloat(dt.freebies[j].total);
									items.unshift({
										item_id: dt.freebies[j].item_id,
										qty: dt.freebies[j].qty,
										spec_station_id: 0,
										spec_sales_type: 0,
										item_code: dt.freebies[j].item_code + " --  (FREE)",
										price: free_price.toFixed(2),
										total: free_total.toFixed(2),
										adjustmentmem: (-1 * free_total.toFixed(2)),
										is_bundle: 0,
										item_count: 0,
										remaining: dt.freebies[j].inv_qty,
										orig_qty: dt.freebies[j].qty,
										preferred_unit:'',
										freebies: 1
									});
								}
							}
							var group_adjustment_arr = [];
							if(dt.group_adjustment.length > 0){

								for(var ga in dt.group_adjustment){

									group_adjustment_arr.push(
										{
											name: dt.group_adjustment[ga]['name'],
											adjustment: dt.group_adjustment[ga]['adjustment'],
										}
									);
								}

							}


							items.unshift({
								item_id: item_id,
								qty: request.qty,
								spec_station_id: request.spec_station_id,
								spec_sales_type: request.spec_sales_type,
								item_code: item_code,
								price: price.toFixed(2),
								total: total,
								orig_total: number_format(total,2,".",""),
								adjustmentmem: adjustmentmem,
								is_bundle: is_bundle,
								item_count: splitted[3],
								remaining: splitted[4],
								orig_qty: request.orig_qty,
								preferred_unit: request.preferred_unit,
								freebies: 0,
								is_surplus: request.is_surplus,
								group_adjustment:group_adjustment_arr,
								group_adjustment_selected:0,
								adjustment_remarks: dt.adjustment_remarks
							});



							request.spec_station_id = 0;
							request.spec_sales_type = 0;
							request.item_id = '';
							request.qty = '';
							request.addtl_disc = '';
							request.orig_qty ='';
							request.is_surplus ='0';
							request.prepared_qty ='';
							itemCon.select2('val', null);
							vuecon.disableMemberAndBranch();
							vuecon.success = true;
							setTimeout(function() {
								vuecon.success = false;
							}, 3000);
							var backup = [];

							localStorage['wh_backup_items'] = JSON.stringify(items);
							localStorage['wh_backup_request'] = JSON.stringify(request);
						}

					},
					error: function() {
						alert('It seems like you have a very slow internet connection.');
						$('#btnAdd').html('Add Item');
						vuecon.disableMemberAndBranch();
					}
				});
			}
		}, fetchedOrderPickup: function() {
			var vuecon = this;
			var from = vuecon.log_from_pickup;
			var to = vuecon.log_to_pickup;
			var search = vuecon.log_search_pickup;
			var pickup_filter_type = vuecon.pickup_filter_type;

			vuecon.showLoading =true;
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'getWhOrderPickup',search:search, from: from, to: to, pickup_filter_type: pickup_filter_type},
				success: function(data) {
					vuecon.$set('orders_pickup', data);
					vuecon.showLoading =false;
				},
				error: function() {
					console.log('Fetched Item');
					vuecon.showLoading =false;
				}
			});
		}, fetchedOrderService: function() {
			var vuecon = this;
			vuecon.showLoading =true;
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'getWhOrderService'},
				success: function(data) {
					vuecon.$set('orders_service', data);
					vuecon.showLoading =false;
				},
				error: function() {
					console.log('Fetched Item');
					vuecon.showLoading =false;
				}
			});
		}, removeItem: function(item) {
			this.cart_item_ctr = parseInt(this.cart_item_ctr) - parseInt(item.item_count);
			this.items.$remove(item);
			this.disableMemberAndBranch();
		}, showRequestForm: function() {
			this.hideAllView();
			this.nav = {request: true, approve:false,warehouse:false,shipping:false,del: false,pickup:false , service:false };
			this.container.requestView = true;
		}, showApproval: function() {
			this.current_page = 1;
			this.fetchedOrder(1); // addstatus
			this.hideAllView();
			this.nav = {request: false, approve:true,warehouse:false,shipping:false,del: false,pickup:false , service:false };
			this.container.approvalView = true;
		}, showApproved: function() {
			this.current_page = 1;
			this.fetchedOrder(3);
			this.hideAllView();
			this.nav = {request: false, approve:false,warehouse:true,shipping:false,del: false,pickup:false, service:false  };
			this.container.showApproved = true;
		}, showShipping: function() {
			this.current_page = 1;
			this.fetchedOrder(2);
			this.hideAllView();
			this.nav = {request: false, approve:false,warehouse:false,shipping:true,del: false,pickup:false, service:false};
			this.container.showShipping = true;
		}, showLog: function() {
			this.hideAllView();
			this.container.showLog = true;
			this.nav = {request: false, approve:false,warehouse:false,shipping:false,del: true,pickup:false , service:false };
			this.fetchedOrderLog();
		}, showPickup: function() {
			this.hideAllView();
			this.container.showPickup = true;
			this.nav = {request: false, approve:false,warehouse:false,shipping:false,del: false,pickup:true, service:false };
			this.fetchedOrderPickup();
		}, showService: function() {
			this.hideAllView();
			this.container.showService = true;
			this.nav = {request: false, approve:false,warehouse:false,shipping:false,del: false,pickup:false, service:true };
			this.fetchedOrderService();
		}, hideAllView: function() {
			this.container.requestView = false;
			this.container.approvalView = false;
			this.container.showApproved = false;
			this.container.showShipping = false;
			this.container.showPickup = false;
			this.container.showLog = false;
			this.container.showService = false;
		}, disableMemberAndBranch: function() {
			if(this.items.length > 0) {
				$('#member_id').select2('disable', true);
				$('#branch_id').attr('disabled', true);
			} else {
				if(this.is_member != 1) {
					$('#member_id').select2('enable', true);
				}
				$('#branch_id').attr('disabled', false);
			}
		}, submitItem: function() {
			var items = this.items;
			var request = this.request;
			var has_attachment = $('#order_has_attachment').val();
			var vm = this;
			alertify.confirm("Are you sure you want to submit this request?",function(e){
				if(e){
					var is_service_item = $('#chkFromService').is(':checked');
					var service_notification = $('#chkFromNotif').is(':checked');

					var is_service = 0;
					if(is_service_item){
						is_service = 1;
					}
					var is_service_notification = 0;
					if(service_notification){
						is_service_notification = 1;
					}


					$('.loading').show();
					var fd = new FormData();

					if(has_attachment == 1){
						var file_data = $('input[name=requestAttachment]')[0].files[0];
						if(!fd){
							alertify.alert("Add Attachment First");
							return;
						}
						fd.append('file',file_data);
					}

					fd.append('functionName','submitWhOrder');
					fd.append('items',JSON.stringify(items));
					fd.append('request',JSON.stringify(request));
					fd.append('is_service',is_service);
					fd.append('is_service_notification',is_service_notification);


					$.ajax({
						url: '../ajax/ajax_query2.php',
						type: 'POST',
						contentType: false,
						processData: false,
						data: fd,
						dataType: 'json',
						success: function(data) {
							if(data.success) {
								vm.orderCount();


								if(is_service_notification){
									var msg = "FOR SERVICE PROCESSING<br>Click Pending at Service on the upper right corner of this page to monitor your request!"
									tempToast('info', '<p>'+msg+'</p>', "<h4>Information!</h4>",{"closeButton": true,"timeOut":15000});
								}

								tempToast('info', '<p>Order was successfully requested</p>', "<h4>Information!</h4>");

								if(vm.is_member != 1) {
									$('#member_id').select2('val', null);
								}
								$('#branch_id').select2('val', null);
								if(vm.is_member != 1) {

									vm.request = {
										spec_station_id: 0,
										spec_sales_type: 0,
										member_id: '',
										branch_id: '',
										branch_id_to: '',
										shipping_company_id: '',
										remarks: '',
										item_id: '',
										qty: '',
										client_po: '',
										for_pickup: 0,
										is_reserve: 0,
										gen_sales_type: 0
									};
								} else {
									vm.request = {
										spec_station_id: 0,
										spec_sales_type: 0,
										member_id: request.member_id,
										branch_id: '',
										branch_id_to: '',
										shipping_company_id: '',
										remarks: '',
										item_id: '',
										qty: '',
										client_po: '',
										is_reserve: 0,
										gen_sales_type: 0
									};
								}



								$('#chkFromService').prop('checked', false);
								$('#chkFromNotif').prop('checked', false);
								vm.cart_item_ctr =0;
								vm.items = [];
								vm.current_credit_list = [];
								vm.disableMemberAndBranch();
							} else {

								tempToast('error', '<p>Request failed. Please try again.</p>', "<h4>Error!</h4>");
								if(data.message){
									tempToast('error', data.message, "<h4>Error!</h4>");
								}
							}
							localStorage.removeItem('wh_backup_items');
							localStorage.removeItem('wh_backup_request');
							$('.loading').hide();

						},
						error: function() {
							console.log('Submit item');
						}
					})
				}
			})

		}, removeAll: function() {
			var vuecon = this;
			alertify.confirm('Are you sure you want to remove all item(s)?', function(e) {
				if(e) {
					vuecon.items = [];
					vuecon.cart_item_ctr = 0;
				} else {

				}
			});
		}, isAlreadyAdded: function(item_id) {

			for(var i in this.items) {
				if(this.items[i].item_id == item_id) {
					return true;
				}
			}

			return false;

		}, fetchedOrder: function(stat, p) {

			var vuecon = this;
			//this.branch_id_filter.value != 0 || this.salestype_filter.value != 0 || this.for_pickup_filter.value || this.assemble_filter.value
			if(p){
				vuecon.current_page = 1;
			}
			var page = (vuecon.current_page) ? vuecon.current_page : 0;
			vuecon.current_status_order = stat;
			var dt1 = vuecon.warehouse_dt1;
			var dt2 = vuecon.warehouse_dt2;
			var show_all = (vuecon.warehouse_showall) ?  1: 0;

			if(stat == 3){
				var con = $('#btnWarehouseSearchRecord');
				button_action.start_loading(con);
				if(vuecon.pending_counts.warehouse < 500){
					show_all = 1;
				}
			}

			if(vuecon.showLoading == true){
				return;
			}
			vuecon.showLoading =true;


				$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'getWhOrders', stat: stat,dt1:dt1,
					dt2:dt2,show_all:show_all,page:page,search: vuecon.search_text,
					branch_id:vuecon.branch_id_filter,salestype:vuecon.salestype_filter,for_pickup:vuecon.for_pickup_filter,assemble:vuecon.assemble_filter},
				success: function(data) {

					vuecon.$set('orders', data.items);
					$('.nav_order').html(data.nav)

					if(stat == 3){

						button_action.end_loading(con);

					}
					vuecon.showLoading =false;

				},
				error: function() {
					console.log('Fetched Item');
					vuecon.showLoading =false;
				}
			});
		}, fetchedOrderCur: function() {
			var vuecon = this;
			var orders = vuecon.orders;
			vuecon.$set('orders', orders);
		}, fetchedOrderLog: function() {
			var vuecon = this;
			var from = vuecon.log_from;
			var to = vuecon.log_to;
			var search = vuecon.log_search;
			var truck_id = vuecon.log_truck_id;
			var order_type = vuecon.del_filter_type;
			vuecon.showLoading =true;
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'getWhOrderLog',search:search, from: from, to: to, order_type: order_type,truck_id:truck_id},
				success: function(data) {
					vuecon.$set('orders_log', data);
					vuecon.showLoading =false;
				},
				error: function() {
					console.log('Fetched Item');
					vuecon.showLoading =false;
				}
			});
		}, getStocks: function() {
			var vuecon = this;
			var id = vuecon.current_order;
			var btncon = $('#btnGetStock');
			var btnoldval = btncon.html();
			btncon.attr('disabled', true);
			btncon.html('Loading...');
			if(vuecon.ajaxRequest) {
				return;
			}
			vuecon.ajaxRequest = true;

			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'getStockWarehouse', order_id: id},
				success: function(data) {

					tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
					$('#myModal').modal('hide');
					btncon.attr('disabled', false);
					btncon.html(btnoldval);
					vuecon.ajaxRequest = false;
					vuecon.fetchedOrder(3); // warehouse
				},
				error: function() {
					vuecon.ajaxRequest = false;
					tempToast('error', "<p>Error occur. Please try again.</p>", "<h4>Error!</h4>");

				}
			});
		}, assembleItem: function() {
			var vuecon = this;
			var id = vuecon.current_order;
			var btncon = $('#btnAssembleItem');
			var btnoldval = btncon.html();
			btncon.attr('disabled', true);
			btncon.html('Loading...');
			localStorage['get_order_id_assemble'] = id;
			location.href = 'assemble-composite-item.php';
		}, processToShipping: function() {
			var vuecon = this;
			var id = vuecon.current_order;
			var btncon = $('#btnProcessToShipping');
			var btnoldval = btncon.html();
			btncon.attr('disabled', true);
			btncon.html('Loading...');
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'processToShipping', order_id: id},
				success: function(data) {

					tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
					$('#myModal').modal('hide');
					btncon.attr('disabled', false);
					btncon.html(btnoldval);
					vuecon.fetchedOrder(3);
					vuecon.orderCount();
				},
				error: function() {
					tempToast('error', "<p>Error occur. Please try again.</p>", "<h4>Error!</h4>");
				}
			});
		}, returnStocks: function() {
			var vuecon = this;
			var id = vuecon.current_order;
			var curorder = vuecon.current_order_det;
			var btncon = $('#btnReturnStocks');
			var btnoldval = btncon.html();
			btncon.attr('disabled', true);
			btncon.html('Loading...');
			alertify.confirm("Are you sure you want to return this stocks?", function(e) {
				if(e) {
					$.ajax({
						url: '../ajax/ajax_query2.php',
						type: 'POST',
						data: {functionName: 'returnStocks', order_id: id},
						success: function(data) {
							tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
							$('#myModal').modal('hide');
							btncon.attr('disabled', false);
							btncon.html(btnoldval);
							vuecon.fetchedOrder(3);
						},
						error: function() {
							tempToast('error', "<p>Error occur. Please try again.</p>", "<h4>Error!</h4>");
						}
					});
				} else {
					btncon.attr('disabled', false);
					btncon.html(btnoldval);
				}
			})

		}, getOrderDetails: function(order) {
			var vuecon = this;
			vuecon.current_order_det = order;
			vuecon.order_updating = 0;
			vuecon.schedule_date = '';
			vuecon.current_order = order.id;
			vuecon.current_status = order.status;
			vuecon.current_isScheduled = order.is_scheduled;
			vuecon.current_stock_out = order.stock_out;
			vuecon.details_ready = false;
			vuecon.rackings = [];
			$('#myModal').modal('show');
			vuecon.getDetailscomp(order.id, order.status);
		}, getDetailscomp: function(id, status) {
			var vuecon = this;
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'getWhOrdersDetails', order_id: id, order_status: status},
				success: function(data) {


					var orders = JSON.parse(data.order);
					vuecon.current_order_obj = orders;
					vuecon.$set('orderDetails', orders);
					vuecon.insufficient = data.ins;

					try {

						for(var i in orders) {
							if(orders[i].bundles == "[]") {
								vuecon.rackings[orders[i].item_id] = JSON.parse(orders[i].racking);
							}
						}

					} catch(e) {

					}

					try {
						for(var i in orders) {
							vuecon.bundles[orders[i].item_id] = JSON.parse(orders[i].bundles);
						}
					} catch(e) {

					}

					vuecon.details_ready = true;

				},
				error: function() {

				}
			});
		}, showSerialForm: function(order) {
			var vuecon = this;
			var cur = vuecon.current_order_det;

			var qty = parseInt(order.qty);
			$.ajax({
				url: '../ajax/ajax_product.php',
				type: 'POST',
				data: {functionName: 'selectSerials', payment_id: cur.payment_id, qty: qty, item_id: order.item_id},
				dataType: 'json',
				success: function(data) {
					vuecon.serials = data;
					var cnt = 0;
					for(var i in data){
						if(data[i].id == '0'){
							cnt += 1;
						}
					}
					vuecon.bc_scan.serial.item_id = order.item_id;
					vuecon.bc_scan.serial.cnt = cnt;
					vuecon.bc_scan.serial.qty = qty;
				},
				error: function() {

				}
			});
			$('#myModalSerial').modal('show');
		}, saveSerials: function() {
			var vuecon = this;
			var cur = vuecon.current_order_det;
			$.ajax({
				url: '../ajax/ajax_product.php',
				type: 'POST',
				data: {
					functionName: 'saveSerials',
					payment_id: cur.payment_id,
					details: JSON.stringify(vuecon.serials)
				},
				success: function(data) {
					alertify.alert(data);
					vuecon.serials = [];
					$('#myModalSerial').modal('hide');
					vuecon.getDetailscomp(cur.id, cur.status);
				},
				error: function() {

				}
			})
		}, getOrderDates: function(order) {
			var vuecon = this;
			vuecon.current_order = order.id;
			vuecon.current_status = order.status;
			vuecon.current_isScheduled = order.is_scheduled;
			vuecon.current_stock_out = order.stock_out;
			vuecon.current_canBeResched = order.canBeResched;
			vuecon.current_truck_id = order.truck_id;
			vuecon.re_truck_id = order.truck_id;
			vuecon.re_driver_id = order.driverval;
			vuecon.re_for_pick_up = order.pickup_status;
			vuecon.re_helper_id = order.helperval;
			vuecon.dates_ready = false;
			vuecon.rackings = [];
			vuecon.re_schedule_date = '';
			$('#myModalDates').modal('show');
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				dataType: 'json',
				data: {functionName: 'getWhOrderDates', order_id: order.id, order_status: order.status},
				success: function(data) {
					vuecon.$set('scheduleDates', data);
					vuecon.dates_ready = true;
				},
				error: function() {

				}
			});
		},

		approveOrder: function() {
			var vuecon = this;
			var order_id = vuecon.current_order;

			$('.loading').show();
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'approveWhOrder', order_id: order_id},
				dataType: 'json',
				success: function(data) {
					if(data.success) {
						tempToast('info', "<p>Request approved successfully.</p>", "<h4>Information!</h4>");
						vuecon.fetchedOrder(1);
						vuecon.orderCount();
						$('#myModal').modal('hide');

					} else {
						tempToast('error', "<p>"+data.message+"</p>", "<h4>Information!</h4>");
					}
					$('.loading').hide();

				},
				error: function() {
					console.log('approveOrder');
				}
			})
		},
		shipOrder: function() {
			var vuecon = this;
			var order_id = vuecon.current_order;

			$('.loading').show();
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'shipOrder', order_id: order_id},
				dataType: 'json',
				success: function(data) {
					if(data.success) {
						tempToast('info', "<p>Request shipped successfully.</p>", "<h4>Information!</h4>");
						vuecon.fetchedOrder(2);
						vuecon.orderCount();
						$('#myModal').modal('hide');

					} else {
						tempToast('error', "<p>"+data.message+"</p>", "<h4>Information!</h4>");
					}
					$('.loading').hide();

				},
				error: function() {
					console.log('shipOrder');
				}
			})
		},
		declineOrder: function() {
			var vuecon = this;
			var order_id = vuecon.current_order;
			$('.loading').show();
			$('#myModal').modal('hide');
			var config_company_name = $('#config_company_name').val();

			alertify.prompt('Reason for cancelling: ', function (e, str) {
				if (e) {
					if(config_company_name == 'cebuhiq'){
						if(!str){
							alert("Please enter a reason.");
							return;
						}
					}
					$.ajax({
						url: '../ajax/ajax_query2.php',
						type: 'POST',
						data: {functionName: 'declineWhOrder', order_id: order_id,remarks:str},
						dataType: 'json',
						success: function(data) {
							if(data.success) {
								tempToast('info', "<p>Request declined successfully.</p>", "<h4>Information!</h4>");
								vuecon.fetchedOrder(1);
								vuecon.orderCount();
								$('#myModal').modal('hide');

							} else {
								tempToast('error', "<p>Error updating the request. Please refresh and try again.</p>", "<h4>Information!</h4>");
							}
							$('.loading').hide();

						},
						error: function() {
							console.log('approveOrder');
						}
					});
				} else {
					$('.loading').hide();
				}
			}, '');

		},approveReserveOrder: function(){
			var vuecon = this;
			var order_id = vuecon.current_order;
			$('.loading').show();
			$.ajax({
				url: '../ajax/ajax_wh_order.php',
				type: 'POST',
				data: {functionName: 'approveReserveOrder', order_id: order_id},
				dataType: 'json',
				success: function(data) {
					if(data.success) {
						tempToast('info', "<p>Processed successfully.</p>", "<h4>Information!</h4>");
						vuecon.fetchedOrder(1);
						$('#myModal').modal('hide');

					} else {
						tempToast('error', "<p>Error updating the request. Please refresh and try again.</p>", "<h4>Information!</h4>");
					}
					$('.loading').hide();

				},
				error: function() {
					console.log('approveOrder');
				}
			})
		},approveWalkInOrder: function(){
			var vuecon = this;
			var order_id = vuecon.current_order;
			$('.loading').show();
			$.ajax({
				url: '../ajax/ajax_wh_order.php',
				type: 'POST',
				data: {functionName: 'approveWalkInOrder', order_id: order_id},
				dataType: 'json',
				success: function(data) {
					if(data.success) {
						tempToast('info', "<p>Processed successfully.</p>", "<h4>Information!</h4>");
						vuecon.fetchedOrder(1);
						$('#myModal').modal('hide');

					} else {
						tempToast('error', "<p>Error updating the request. Please refresh and try again.</p>", "<h4>Information!</h4>");
					}
					$('.loading').hide();

				},
				error: function() {
					console.log('approveOrder');
				}
			})
		}, toggleReserve: function() {
			var vuecon = this;
			var order_id = vuecon.current_order;
			$('.loading').show();
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'toggleReserveWhOrder', order_id: order_id},
				dataType: 'json',
				success: function(data) {
					if(data.success) {
						tempToast('info', "<p>Request sent successfully.</p>", "<h4>Information!</h4>");
						vuecon.fetchedOrder(1);
						$('#myModal').modal('hide');

					} else {
						tempToast('error', "<p>Error updating the request. Please refresh and try again.</p>", "<h4>Information!</h4>");
					}
					$('.loading').hide();

				},
				error: function() {
					console.log('approveOrder');
				}
			})
		}, togglePriorityOrder: function() {
			var vuecon = this;
			var order_id = vuecon.current_order;

			$('.loading').show();
			$.ajax({
				url: '../ajax/ajax_query2.php',
				type: 'POST',
				data: {functionName: 'togglePriorityOrder', order_id: order_id},
				dataType: 'json',
				success: function(data) {
					if(data.success) {
						tempToast('info', "<p>Request updated successfully.</p>", "<h4>Information!</h4>");
						vuecon.fetchedOrder(1);
						$('#myModal').modal('hide');

					} else {
						tempToast('error', "<p>Error updating the request. Please refresh and try again.</p>", "<h4>Information!</h4>");
					}
					$('.loading').hide();

				},
				error: function() {
					console.log('approveOrder');
				}
			})
		}
	}
});


// watching branch and member select2
vm.$watch('request.for_pickup', function(val) {
	if($('#config_company_name').val() == 'cebuhiq'){
		if(val == 1){

			if(vm['request'].price_group_id !=  8){
				vm['request'].price_group_id = '0';
				tempToast('error','Chosen price group is Invalid for order type "For Pick up".','Price Group was RESET');
			}

		} else {

		}
	}

});
$('#branch_id').on('change', function() {
	vm['request'].branch_id = $(this).val();
	vm.checkPendingOrder();
});

vm.$watch('request.branch_id', function(val) {
	$('#branch_id').select2({
		'placeholder': 'From', allowClear: true
	});
});
// watching branch and member select2
$('#branch_id_to').on('change', function() {
	var v = $(this).val()
	vm['request'].branch_id_to = v;
	$.ajax({
	    url:'../ajax/ajax_wh_order.php',
	    type:'POST',
	    dataType:'json',
	    data: {functionName:'branchHasMember',branch_id:v},
	    success: function(data){

			if(data.member.id){
				vm['request'].member_id = data.member.id;
				$('#member_id').select2('data',{id:data.member.id,text:data.member.name});
			} else {
				//	vm['request'].member_id = 0;
				//	$('#member_id').select2('val',null);
			}
		    if(data.branches.length > 0){
			    vm['request'].branch_id = 0;
			    $('#branch_id').select2('val','null');
			    var ret = "<option value=''></option>";
			    for(var i in data.branches){
				    ret += "<option value='"+data.branches[i].branch_id+"'>"+data.branches[i].branch_name+"</option>";
			    }
			    $('#branch_id').html(ret);
		    }
	    },
	    error:function(){

	    }
	});
});

vm.$watch('request.branch_id_to', function(val) {
	$('#branch_id_to').select2({
		'placeholder': 'To', allowClear: true
	});
});
$('#shipping_company_id').on('change', function() {
	vm['request'].shipping_company_id = $(this).val();
});

vm.$watch('request.shipping_company_id', function(val) {
	$('#shipping_company_id').select2({
		'placeholder': 'Shipping Company', allowClear: true
	});
});
$('#update_shipping_company').on('change', function() {
	vm['order_info'].shipping_company_id = $(this).val();
});

vm.$watch('order_info.shipping_company_id', function(val) {
	$('#update_shipping_company').select2({
		'placeholder': 'Shipping Company', allowClear: true
	});
});
vm.$watch('helper_id', function(val) {
	$('#helper_id').select2({
		'placeholder': 'Select Helper', allowClear: true
	});
});
$('#helper_id').on('change', function() {
	vm.helper_id = $(this).val();
});
$('#member_id').on('change', function() {
	var v = $(this).val();
	vm['request'].member_id = v;
	// ajax call if member has branch
	$.ajax({
	    url:'../ajax/ajax_wh_order.php',
	    type:'POST',
	    dataType:'json',
	    data: {functionName:'getOwnedBranch',member_id:v},
	    success: function(data){
		    var my_branch = data.branches;
		    var my_station = data.stations;
		    var my_credits = data.credits;

		   vm.current_credit_list = my_credits;
		    vm.request.price_group_id =data.price_group_id;
		    vm.is_hold = data.is_hold;
		    vm.member_info.contact_number = data.contact_number;
		    vm.member_info.personal_address = data.personal_address;
		    vm.member_info.region = data.region;
		    vm.member_info.terms = data.terms;
		    vm.member_info.credit_limit = data.credit_limit;

		    var config_company_name = $('#config_company_name').val();
			console.log(config_company_name);
	        if(my_branch.length > 0){
		        var ret = "<option value=''></option>";

		        for(var i in my_branch){
			        var selected = '';

			        ret += "<option value='"+my_branch[i].id+"' >"+my_branch[i].name+"</option>";
		        }
		        $('#branch_id_to').html(ret);


	        }

		    if(config_company_name == 'avision'){
			   $('#branch_id').select2('val',4);
			    vm.request.branch_id = 4;
		    }

		    if(my_station.length > 0){
		        var ret = "<option value='0'>Choose Station</option>";
		        for(var i in my_station){
			        ret += "<option value='"+my_station[i].id+"'>"+my_station[i].name+"</option>";
		        }
		        $('#station_id').html(ret);
		        $('#spec_station_id').html(ret);
	        } else {

			    $('#station_id').html("<option value='0'>No Station</option>");
			    $('#spec_station_id').html("<option value='0'>No Station</option>");
		    }

	    },
	    error:function(){

	    }
	});

});

vm.$watch('request.member_id', function(val) {
	/*$('#member_id').select2({
		'placeholder' :'Select Member',
		allowClear: true
	});*/
	/*$("#member_id").select2({
		placeholder: 'Search Member',
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
							text: item.lastname + ", " + item.firstname + " " + item.middlename,
							slug: item.lastname + ", " + item.firstname + " " + item.middlename,
							id: item.id
						}
					})
				};
			}
		}
	});*/
});

vm.$watch('schedule_date', function(val) {
	$('#schedule_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#schedule_date').datepicker('hide');
	});
});
$('#schedule_date').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#schedule_date').datepicker('hide');
	vm['schedule_date'] = $('#schedule_date').val();
});
$('#custom_date').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#custom_date').datepicker('hide');
});
vm.$watch('log_from', function(val) {
	$('#log_from').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#log_from').datepicker('hide');
	});
});
$('#log_from').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#log_from').datepicker('hide');
	vm['log_from'] = $('#log_from').val();
});
vm.$watch('log_to', function(val) {
	$('#log_to').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#log_to').datepicker('hide');
	});
});
$('#log_to').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#log_to').datepicker('hide');
	vm['log_to'] = $('#log_to').val();
});
vm.$watch('re_schedule_date', function(val) {
	$('#re_schedule_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#re_schedule_date').datepicker('hide');
	});
});
$('#re_schedule_date').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#re_schedule_date').datepicker('hide');
	vm['re_schedule_date'] = $('#re_schedule_date').val();
});

vm.$watch('warehouse_dt1', function(val) {
	$('#warehouse_dt1').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#warehouse_dt1').datepicker('hide');
	});
});
$('#warehouse_dt1').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#warehouse_dt1').datepicker('hide');
	vm['warehouse_dt1'] = $('#warehouse_dt1').val();
});
vm.$watch('warehouse_dt2', function(val) {
	$('#warehouse_dt2').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#warehouse_dt2').datepicker('hide');
	});
});
$('#warehouse_dt2').datepicker({
	autoclose: true
}).on('changeDate', function(ev) {
	$('#warehouse_dt2').datepicker('hide');
	vm['warehouse_dt2'] = $('#warehouse_dt2').val();
});

(function(vm) {

	//getMembers(localStorage['company_id']);
	//getMemberOptList();

	var ajaxOnProgress = false;

	$('body').on('click', '.opennewtabimage', function() {

		var src = $(this).attr('src');
		$.swipebox([{href: src, title: ''}]);

	});

	function getMembersInd(company_id, member_id) {
		$("#activaTab").empty();
		$("#con_member").append("<option></option>");
		$("#con_member_freebies").empty();
		$("#con_member_freebies").append("<option></option>");
		$("#member_credit").empty();
		$("#member_credit").append("<option></option>");
		$("#member_deduction").empty();
		$("#member_deduction").append("<option></option>");
		$.ajax({
			url: "../ajax/ajax_get_members.php",
			type: "POST",
			data: {company_id: company_id, member_id: member_id, type: 1},
			success: function(data) {
				if(data != 0) {
					var mems = JSON.parse(data);
					for(var i in mems) {
						var amt = 0;
						var amt_freebies = 0;
						if(mems[i].amt) {
							var check_not_validyet = 0;
							amt = mems[i].amt;
							if(mems[i].camt) check_not_validyet = mems[i].camt;
							amt = amt - check_not_validyet;
							$("#con_member").append("<option data-con='" + amt + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt + ")</option>");
						}
						if(mems[i].freebiesamount) {
							amt_freebies = mems[i].freebiesamount;
						}
						$("#con_member_freebies").append("<option data-con_freebies='" + amt_freebies + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt_freebies + ")</option>");
						$("#member_credit").append("<option value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + "</option>");
						$("#member_deduction").append("<option value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + "</option>");
						$('#consumable_remarks_holder').html( mems[i].consumable_remarks
						)
					}
					$("#con_member_freebies").select2('val', member_id);
					$("#member_credit").select2('val', member_id);
					$("#con_member").select2('val', member_id);
					$("#member_deduction").select2('val', member_id);
					$("#con_member_freebies").attr('disabled', true);
					$("#member_credit").attr('disabled', true);
					$("#con_member").attr('disabled', true);
					$("#member_deduction").attr('disabled', true);
				}
			}
		});
		$.ajax({
			url: '../ajax/ajax_wh_order.php',
			type: 'POST',
			data: {functionName: 'getOverPayment', member_id: member_id},
			success: function(data) {
				$('#over_payment_holder').html(data);
				var over_payment_list = JSON.parse($('#op_member_list').val());
				if(over_payment_list.length > 0) {
					$('#use_user_overpayment').show();
				}
			},
			error: function() {

			}
		});
	}

	function activaTabOpen(tab){
		$('.nav-tabs a[href="#' + tab + '"]').tab('show');
	}

	function getMembers(company_id) {
		$.ajax({
			url: "../ajax/ajax_get_members.php",
			type: "POST",
			data: {company_id: company_id, type: 1},
			success: function(data) {
				if(data != 0) {
					localStorage['members'] = data;
				} else {
					localStorage.removeItem('members');
				}
			}
		});
	}

	function getMemberOptList() {
		if(localStorage['members']) {
			var mems = JSON.parse(localStorage['members']);
			$("#con_member").empty();
			$("#con_member").append("<option></option>");
			$("#con_member_freebies").empty();
			$("#con_member_freebies").append("<option></option>");
			$("#member_credit").empty();
			$("#member_credit").append("<option></option>");
			for(var i in mems) {
				var amt = 0;
				var amt_freebies = 0;

				if(mems[i].amt) {
					var check_not_validyet = 0;
					amt = mems[i].amt;
					if(mems[i].camt) check_not_validyet = mems[i].camt;
					amt = amt - check_not_validyet;
					$("#con_member").append("<option data-con='" + amt + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt + ")</option>");
				}
				if(mems[i].freebiesamount) {
					amt_freebies = mems[i].freebiesamount;
				}
				$("#con_member_freebies").append("<option data-con_freebies='" + amt_freebies + "' value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + " (" + amt_freebies + ")</option>");
				$("#member_credit").append("<option value='" + mems[i].id + "'>" + mems[i].lastname + ", " + mems[i].firstname + " " + mems[i].middlename + "</option>");

			}
		}
	}

	$("#con_member").select2({
		placeholder: 'Choose member name...', allowClear: true
	}).on('select2-open', function() {

	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});

	$("#con_member_freebies").select2({
		placeholder: 'Choose member name...', allowClear: true
	}).on('select2-open', function() {

	}).on("select2-close", function(e) {
		// fired to the original element when the dropdown closes
		setTimeout(function() {
			$('.select2-container-active').removeClass('select2-container-active');
			$(':focus').blur();
		}, 100);
	});


	$("#member_credit").select2({
		placeholder: 'Choose member name...', allowClear: true
	});
	$("#member_deduction").select2({
		placeholder: 'Choose member name...', allowClear: true
	});


	function receiveCash() {
		var cashreceivebtn = $('.cashreceiveok');
		var cashreceiveoldval = cashreceivebtn.html();
		cashreceivebtn.attr('disabled', true);
		cashreceivebtn.html('Loading...');

		var cash = $("#hidcashpayment").val();
		if(!cash) cash = 0;
		var credit = $("#hidcreditpayment").val();
		if(!credit) credit = 0;
		var banktransfer = $("#hidbanktransferpayment").val();
		if(!banktransfer) banktransfer = 0;
		var cheque = $("#hidchequepayment").val();
		if(!cheque) cheque = 0;
		var con_amount = $("#hidconsumablepayment").val();
		if(!con_amount) con_amount = 0;
		var con_amount_freebies = $("#hidconsumablepaymentfreebies").val();
		if(!con_amount_freebies) con_amount_freebies = 0;
		var member_credit_amount = $("#hidmembercredit").val();
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) member_deduction_amount = 0;
		if(!member_credit_amount) member_credit_amount = 0;
		var member_credit_cod = $('#member_credit_cod').is(':checked');
		member_credit_cod = (member_credit_cod) ? 1 : 0;
		var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(member_deduction_amount);
		var grandtotal = parseFloat($("#hidamountdue").val());


		totalpayment= number_format(totalpayment,2,'.','');
		grandtotal= number_format(grandtotal,2,'.','');

		if(parseFloat(totalpayment) < 0 || parseFloat(totalpayment) < parseFloat(grandtotal)) {
			cashHolderComputation(0, 0);
			showToast('error', '<p>Invalid payment s</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			cashreceivebtn.attr('disabled', false);
			cashreceivebtn.html(cashreceiveoldval);
			return;
		} else {
			if(!isValidFormCheque() || !isValidFormCredit() || !isValidFormBankTransfer() || !isValidFormDeduction()) {
				cashreceivebtn.attr('disabled', false);
				cashreceivebtn.html(cashreceiveoldval);
				return;
			}

			var change = parseFloat(totalpayment) - parseFloat(grandtotal);
			cash = parseFloat(cash) - parseFloat(change);
			localStorage['payment_cash'] = cash;
			localStorage['payment_con'] = con_amount;
			localStorage['payment_con_freebies'] = con_amount_freebies;
			localStorage['payment_member_credit'] = member_credit_amount;

			alertify.confirm('Are you sure you want to submit this payment?', function(e) {
				if(e) {
					// ajax request payment
					var payment_credit;
					var payment_bt;
					var payment_cheque;
					var payment_cash;
					var payment_con_freebies;
					var payment_con;
					var payment_member_credit;
					var payment_member_deduction;
					var member_deduction_remarks;
					var order_id = $('#payment_order_id').val();
					if(localStorage['payment_cash']) {
						payment_cash = localStorage['payment_cash'];
					}
					if(localStorage['payment_con']) {
						payment_con = localStorage['payment_con'];
					}
					if(localStorage['payment_con_freebies']) {
						payment_con_freebies = localStorage['payment_con_freebies'];
					}
					if(localStorage['payment_member_credit']) {
						payment_member_credit = localStorage['payment_member_credit'];
					}
					if(localStorage['payment_credit']) {
						payment_credit = localStorage['payment_credit'];
					}
					if(localStorage['payment_bt']) {
						payment_bt = localStorage['payment_bt'];
					}
					if(localStorage['payment_cheque']) {
						payment_cheque = localStorage['payment_cheque'];
					}
					if(localStorage['payment_member_deduction']) {
						payment_member_deduction = localStorage['payment_member_deduction'];

					}
					if(ajaxOnProgress) {
						return;
					}
					ajaxOnProgress = true;
					var arr_op_ids = [];
					$('input:checkbox.chk_overpayment').each(function() {
						var op_chk = $(this);
						if(op_chk.is(":checked")) {
							var amount_used = op_chk.prev().val();
							arr_op_ids.push({id: op_chk.val(),amount:amount_used});
						}


					});

					$.ajax({
						url: '../ajax/ajax_query2.php', type: 'POST', data: {
							functionName: 'sendPaymentWh',
							payment_credit: payment_credit,
							payment_bt: payment_bt,
							payment_cheque: payment_cheque,
							payment_cash: payment_cash,
							payment_con: payment_con,
							payment_con_freebies: payment_con_freebies,
							payment_member_credit: payment_member_credit,
							member_credit_cod: member_credit_cod,
							payment_member_deduction: payment_member_deduction,
							order_id: order_id,
							override_payment_date:vm.override_payment_date,
							arr_op_ids: JSON.stringify(arr_op_ids),
							totalpayment: totalpayment,
							terminal_id: localStorage['terminal_id']
						}, success: function(data) {
							ajaxOnProgress = false;
							tempToast('info', "<p>" + data + "</p>", "<h4>Information!</h4>");
							cashreceivebtn.attr('disabled', false);
							cashreceivebtn.html(cashreceiveoldval);
							vm.printGroup(order_id);
							vm.fetchedOrder(1);
							localStorage.removeItem('payment_cheque');
							localStorage.removeItem('payment_credit');
							localStorage.removeItem('payment_bt');
							localStorage.removeItem('payment_cash');
							localStorage.removeItem('payment_con');
							localStorage.removeItem('payment_con_freebies');
							localStorage.removeItem('payment_member_credit');
							localStorage.removeItem('payment_member_deduction');
							$('#hidmembercredit').val(0);
							$('#hidmemberdeduction').val(0);
							$('#hidcashpayment').val(0);
							$('#hidcreditpayment').val(0);
							$('#hidbanktransferpayment').val(0);
							$('#hidchequepayment').val(0);
							$('#hidconsumablepayment').val(0);
							$('#hidconsumablepaymentfreebies').val(0);
							vm.override_payment_date = '';
							$('#chkInvoice').prop('checked',false);
							$('#chkDr').prop('checked',false);
							$('#chkPr').prop('checked',false);
							$('#chkSv').prop('checked',false);
							$('#chkTs').prop('checked',false);
							$('#chkSr').prop('checked',false);
						}, error: function() {
							ajaxOnProgress = false;
							tempToast('error', "<p>Error Occur. Please try again.</p>", "<h4>Error!</h4>");
							cashreceivebtn.attr('disabled', false);
							cashreceivebtn.html(cashreceiveoldval);
							vm.fetchedOrder(1);
							localStorage.removeItem('payment_cheque');
							localStorage.removeItem('payment_credit');
							localStorage.removeItem('payment_bt');
							localStorage.removeItem('payment_cash');
							localStorage.removeItem('payment_con');
							localStorage.removeItem('payment_con_freebies');
							localStorage.removeItem('payment_member_credit');
							localStorage.removeItem('payment_member_deduction');
							$('#hidmembercredit').val(0);
							$('#hidmemberdeduction').val(0);
							$('#hidcashpayment').val(0);
							$('#hidcreditpayment').val(0);
							$('#hidbanktransferpayment').val(0);
							$('#hidchequepayment').val(0);
							$('#hidconsumablepayment').val(0);
							$('#hidconsumablepaymentfreebies').val(0);
						}
					});
					$("#credit_table").find("tr").remove();
					$("#bt_table").find("tr").remove();
					$("#ch_table").find("tr").remove();
					$("#tab_d :input[type='text']").val('');
					$("#tab_c :input[type='text']").val('');
					$("#tab_b :input[type='text']").val('');
					$("#tab_a :input[type='text']").val('');
					$('#getpricemodal').modal("hide");
				} else {
					cashreceivebtn.attr('disabled', false);
					cashreceivebtn.html(cashreceiveoldval);
				}
			});
		}
	}

	$('.cashreceivecancel').click(function() {
		$('#getpricemodal').modal("hide");
		$("#credit_table").find("tr").remove();
		$("#bt_table").find("tr").remove();
		$("#ch_table").find("tr").remove();
		$("#tab_d :input[type='text']").val('');
		$("#tab_c :input[type='text']").val('');
		$("#tab_b :input[type='text']").val('');
		$("#tab_a :input[type='text']").val('');
		localStorage.removeItem('payment_cheque');
		localStorage.removeItem('payment_credit');
		localStorage.removeItem('payment_bt');
		localStorage.removeItem('payment_cash');
		localStorage.removeItem('payment_con');
		localStorage.removeItem('payment_con_freebies');
		localStorage.removeItem('payment_member_credit');
		localStorage.removeItem('payment_member_deduction');
	});

	$('body').on('click', '.cashreceiveok', function() {
		receiveCash();
	});

	$('#ch_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#ch_date').datepicker('hide');
	});

	$('#billing_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#billing_date').datepicker('hide');
	});
	$('#bt_date').datepicker({
		autoclose: true
	}).on('changeDate', function(ev) {
		$('#bt_date').datepicker('hide');
	});
	function cashHolderComputation(cash, change) {
		$('#cashreceiveholder').empty();
		$('#changeholder').empty();
		$('#cashreceiveholder').append(number_format(cash, 2));
		$('#changeholder').append(number_format(change, 2));
	}

	function updateTotalPayment() {
		var cash = $("#cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		var con_amount = $("#con_amount").val();
		if(!con_amount) {
			con_amount = 0;
		}
		var con_amount_freebies = $("#con_amount_freebies").val();
		if(!con_amount_freebies) {
			con_amount_freebies = 0;
		}
		var member_credit_amount = $("#member_credit_amount").val();
		if(!member_credit_amount) {
			member_credit_amount = 0;
		}
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) {
			member_deduction_amount = 0;
		}
		var credit_amount = $("#hidcreditpayment").val();
		if(!credit_amount) {
			credit_amount = 0;
		}
		var bt_amount = $("#hidbanktransferpayment").val();
		if(!bt_amount) {
			bt_amount = 0;
		}
		var ck_amount = $("#hidchequepayment").val();
		if(!ck_amount) {
			ck_amount = 0;
		}
		var gtotal = parseFloat(cash) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount) + parseFloat(member_deduction_amount);
		$("#totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Payment: " + gtotal.toFixed(2) + "</span></strong>");

	}

	function updateCashPayment() {
		var cash = $("#cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		$("#totalcashpayment").html(cash);
		updateTotalPayment();
	}

	function updateConPayment() {
		var con_amount = $("#con_amount").val();
		if(!con_amount) {
			con_amount = 0;
		}
		$("#totalconsumablepayment").html(con_amount);
		updateTotalPayment();
	}

	function updateMemberCredit() {
		var member_credit_amount = $("#member_credit_amount").val();
		if(!member_credit_amount) {
			member_credit_amount = 0;
		}
		$("#totalmembercredit").html(member_credit_amount);
		updateTotalPayment();
	}

	function updateMemberDeduction() {
		var member_deduction_amount=0;
		$('#member_deduction_table > tbody > tr').each(function(i){
			var row = $(this);
			var amount = row.attr('data-amount');
			amount = parseFloat(amount);
			member_deduction_amount = parseFloat(member_deduction_amount) + parseFloat(amount);

		});
		$("#totalmemberdeduction").html(member_deduction_amount);
		$("#hidmemberdeduction").val(member_deduction_amount);
		updateTotalPayment();
	}

	function updateConPaymentFreebies() {
		var con_amount_freebies = $("#con_amount_freebies").val();
		if(!con_amount_freebies) {
			con_amount_freebies = 0;
		}

		$("#totalconsumablepaymentfreebies").html(con_amount_freebies);
		updateTotalPayment();
	}

	function updateCreditPayment() {
		var total = 0;
		if($("#credit_table tr").children().length) {
			$("#credit_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#totalcreditpayment").html(total);
		$("#hidcreditpayment").val(total);
		updateTotalPayment();
	}

	function updateBankTransferPayment() {
		var total = 0;
		if($("#bt_table tr").children().length) {
			$("#bt_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#totalbanktransferpayment").html(total);
		$("#hidbanktransferpayment").val(total);
		updateTotalPayment();
	}

	function updateChequePayment() {
		var total = 0;
		if($("#ch_table tr").children().length) {
			$("#ch_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(2).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#totalchequepayment").html(total);
		$("#hidchequepayment").val(total);
		updateTotalPayment();
	}

	function hasItemCreditValidation(elem) {
		if(!$("#credit_table tr").children().length) {
			showToast('error', '<p>Please Add Credit Card First. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			elem.val('');
		}
	}

	$("#billing_firstname, #billing_middlename, #billing_lastname, #billing_company, #billing_address, #billing_postal,#billing_phone,#billing_email,#billing_remarks").keyup(function() {
		hasItemCreditValidation($(this));
	});
	function hasItemBTValidation(elem) {
		if(!$("#bt_table tr").children().length) {
			showToast('error', '<p>Please Add Bank Transfer Data First. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			elem.val('');
		}
	}

	$("#bt_bankto_name, #bt_bankto_account_number, #bt_firstname, #bt_middlename, #bt_lastname, #bt_company,#bt_address,#bt_postal,#bt_phone").keyup(function() {
		hasItemBTValidation($(this));
	});
	function hasItemChequeValidation(elem) {
		if(!$("#ch_table tr").children().length) {
			showToast('error', '<p>Please Add Cheque Data First. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			elem.val('');
		}
	}

	$("#ch_firstname, #ch_middlename, #ch_lastname, #ch_phone").keyup(function() {
		hasItemChequeValidation($(this));
	});

	function isValidAmount(a, addme) {
		var cash = $("#hidcashpayment").val();
		if(!cash) cash = 0;
		var credit = $("#hidcreditpayment").val();
		if(!credit) credit = 0;
		var banktransfer = $("#hidbanktransferpayment").val();
		if(!banktransfer) banktransfer = 0;
		var cheque = $("#hidchequepayment").val();
		if(!cheque) cheque = 0;
		var con_amount = $("#hidconsumablepayment").val();
		if(!con_amount) con_amount = 0;
		var con_amount_freebies = $("#hidconsumablepaymentfreebies").val();
		if(!con_amount_freebies) con_amount_freebies = 0;
		var member_credit_amount = $("#hidmembercredit").val();
		if(!member_credit_amount) member_credit_amount = 0;
		var member_deduction_amount = $("#hidmemberdeduction").val();
		if(!member_deduction_amount) member_deduction_amount = 0;

		var grandtotal = parseFloat($("#hidamountdue").val());

		var currentNotCash = parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque) + parseFloat(con_amount) + parseFloat(con_amount_freebies) + parseFloat(member_credit_amount) + parseFloat(member_deduction_amount);
		if(addme) {
			currentNotCash = parseFloat(currentNotCash) + parseFloat(a);
		}
		if(parseFloat(currentNotCash).toFixed(2) > parseFloat(grandtotal)) {
			return true;
		} else {
			return false;
		}
	}
	function isValidFormDeduction(){
		if($("#member_deduction_table > tbody > tr").children().length ){
			var deductionArray = new Array();
			var member_id = $('#member_deduction').val();

			$("#member_deduction_table > tbody > tr").each(function(index){
				var row = $(this);
				var amount = row.attr('data-amount');
				var remarks = row.children().eq(2).text();
				var addtl_remarks = row.children().eq(3).text();
				var is_approved = row.children().eq(4).text().trim();

				deductionArray[index] = {
					member_id : member_id,
					amount : amount,
					remarks : remarks,
					addtl_remarks : addtl_remarks,
					is_approved : is_approved,
				}
			});

			console.log(deductionArray);
			localStorage['payment_member_deduction'] = JSON.stringify(deductionArray);
			return true;
		}

		return true;
	}
	function isValidFormCheque() {
		if($("#ch_table tr").children().length) {
			var chequeArray = new Array();
			var fn = $("#ch_firstname").val();
			var mn = $("#ch_middlename").val();
			var ln = $("#ch_lastname").val();
			var phone = $("#ch_phone").val();

			if(fn && !isAlphaNumeric(fn)) {
				showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(mn && !isAlphaNumeric(mn)) {
				showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(ln && !isAlphaNumeric(ln)) {
				showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(phone && !isAlphaNumeric(phone)) {
				showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			$("#ch_table tr").each(function(index) {
				var row = $(this);
				chequeArray[index] = {
					date: row.children().eq(0).text(),
					cheque_number: row.children().eq(1).text(),
					amount: row.children().eq(2).text(),
					bank_name: row.children().eq(3).text(),
					firstname: fn,
					lastname: ln,
					middlename: mn,
					phone: phone
				}
			});
			localStorage['payment_cheque'] = JSON.stringify(chequeArray);
			return true;
		}

		return true;
	}

	function isValidFormCredit() {
		if($("#credit_table tr").children().length) {
			var creditArray = new Array();
			var fn = $("#billing_firstname").val();
			var mn = $("#billing_middlename").val();
			var ln = $("#billing_lastname").val();
			var comp = $("#billing_company").val();
			var add = $("#billing_address").val();
			var postal = $("#billing_postal").val();
			var phone = $("#billing_phone").val();
			var email = $("#billing_email").val();
			var rem = $("#billing_remarks").val();
			// required
			/*var card_type = $("#billing_card_type").val();
			var trace_number = $("#billing_trace_number").val();
			var approval_code = $("#billing_approval_code").val();
			var date = $("#billing_date").val();*/

			if(false) {
				showToast('error', '<p>Please Complete Credit Card billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn && !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Postal should be numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(email && !isEmail(email)) {
					showToast('error', '<p>Email should be valid email address</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(rem && !isAlphaNumeric(rem)) {
					showToast('error', '<p>Remarks should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				$("#credit_table tr").each(function(index){
					var row = $(this);
					creditArray[index] = {
						credit_number : row.children().eq(0).text(),
						amount:  row.children().eq(1).text(),
						bank_name:  row.children().eq(2).text(),
						firstname : fn,
						lastname: ln,
						middlename : mn,
						phone: phone,
						comp: comp,
						add: add,
						postal:postal,
						email:email,
						remarks:rem,
						card_type:row.attr('data-card_type'),
						trace_number:row.attr('data-trace_number'),
						approval_code:row.attr('data-approval_code'),
						date:row.attr('data-date')
					}
				});
				localStorage['payment_credit'] = JSON.stringify(creditArray);
				return true;
			}

		}
		return true;
	}

	function isValidFormBankTransfer() {
		if($("#bt_table tr").children().length) {
			var bankTransferArray = new Array();
			var bt_bankto_name = $("#bt_bankto_name").val();
			var bt_bankto_account_number = $("#bt_bankto_account_number").val();
			var fn = $("#bt_firstname").val();
			var mn = $("#bt_middlename").val();
			var ln = $("#bt_lastname").val();
			var comp = $("#bt_company").val();
			var add = $("#bt_address").val();
			var postal = $("#bt_postal").val();
			var phone = $("#bt_phone").val();
			var date = $("#bt_date").val();

			if(!date) {
				showToast('error', '<p>Please Complete Bank Transfer  billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(bt_bankto_name && !isAlphaNumeric(bt_bankto_name)) {
					showToast('error', '<p>Bank name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(bt_bankto_account_number && !isAlphaNumeric(bt_bankto_account_number)) {
					showToast('error', '<p>Bank account number should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn & !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				$("#bt_table tr").each(function(index) {
					var row = $(this);
					bankTransferArray[index] = {
						credit_number: row.children().eq(0).text(),
						amount: row.children().eq(1).text(),
						bank_name: row.children().eq(2).text(),
						bt_bankto_name: bt_bankto_name,
						bt_bankto_account_number: bt_bankto_account_number,
						firstname: fn,
						lastname: ln,
						middlename: mn,
						phone: phone,
						comp: comp,
						add: add,
						postal: postal,
						date: date
					}
				});
				localStorage['payment_bt'] = JSON.stringify(bankTransferArray);
				return true;
			}
		}
		return true;
	}

	function isAlphaNumeric(str) {
		var rexp = /^[\w\-\s\.,??]+$/
		if(rexp.test(str)) {
			return true;
		} else {
			return false;
		}
	}

	function validateDate(testdate) {
		var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/
		return date_regex.test(testdate);
	}

	function isNumeric(str) {
		var rexp = /^[0-9]+$/
		if(rexp.test(str)) {
			return true;
		} else {
			return false;
		}
	}

	function isEmail(str) {
		var rexp = /^[\w\.-_\+]+@[\w-]+(\.\w{2,3})+$/
		if(rexp.test(str)) {
			return true;
		} else {
			return false;
		}
	}

	function showpricemodal(totalforfreebies, grandtotal) {

		if(!totalforfreebies) {
			localStorage['totalforfreebies'] = 0;
		} else {
			localStorage['totalforfreebies'] = totalforfreebies;
		}
		localStorage.removeItem('payment_cheque');
		localStorage.removeItem('payment_credit');
		localStorage.removeItem('payment_bt');
		localStorage.removeItem('payment_cash');
		localStorage.removeItem('payment_con');
		localStorage.removeItem('payment_con_freebies');
		localStorage.removeItem('payment_member_credit');
		localStorage.removeItem('payment_member_deduction');

		$("#credit_table tbody").html('');
		$("#member_deduction_table tbody").html('');
		$("#credit_table tbody").html('');
		$("#bt_table tbody").html('');
		$("#ch_table tbody").html('');

		$("#cashreceiveholder").text(0);
		$("#changeholder").text(0);
		$("#con_amount_freebies").val('');
		$("#con_amount").val('');
		$("#member_credit_amount").val('');

		$('#hidcashpayment').val(0);
		$('#hidcreditpayment').val(0);
		$('#hidbanktransferpayment').val(0);
		$('#hidchequepayment').val(0);
		$('#hidconsumablepayment').val(0);
		$('#hidconsumablepaymentfreebies').val(0);
		$('#hidmembercredit').val(0);
		$('#hidmemberdeduction').val(0);
		$('#hidTotalOfAllPayment').val(0);
		$('#cashreceivetext').val('');

		updateCreditPayment();
		updateCashPayment();
		updateBankTransferPayment();
		updateChequePayment();
		updateConPayment();
		updateConPaymentFreebies();
		updateMemberCredit();
		updateMemberDeduction();
		$("#amountdue").html("<span style='font-size:1.2em;' class='text-info'><strong> Amount Due: " + grandtotal + "</strong></span>");
		$("#hidamountdue").val(replaceAll(grandtotal, ',', ''));
		$("#getpricemodal").modal("show");

		setTimeout(function() {
			$('#cashreceivetext').focus()
		}, 500);
	}

	$('#cashreceivetext').keypress(function(e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			receiveCash();
			$('#cashreceivetext').val('');
			$('#getpricemodal').modal("hide");
		}

	});

	$('#cashreceivetext').keyup(function(e) {
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidcashpayment").val($(this).val());
		updateCashPayment();
	});
	$('#addcreditcard').click(function() {
		var bl_cardnumber = $('#billing_cardnumber').val();
		var bl_bank = $('#billing_bankname').val();
		var bl_amount = $('#billing_amount').val();
		var bl_card_type = $('#billing_card_type').val();
		var bl_trace_number = $('#billing_trace_number').val();
		var bl_approval_code = $('#billing_approval_code').val();
		var bl_date = $('#billing_date').val();
		var others = "<p>Card Type: "+bl_card_type+"</p>";
		others += "<p>Trace Number: "+bl_trace_number+"</p>";
		others += "<p>Approval Code: "+bl_approval_code+"</p>";
		others += "<p>Date: "+bl_date+"</p>";

		if(!bl_cardnumber){
			bl_cardnumber ='N/A';
		}
		if(!bl_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bl_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bl_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bl_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#credit_table").append("<tr data-date='"+bl_date+"' data-card_type='"+bl_card_type+"' data-trace_number='"+bl_trace_number+"' data-approval_code='"+bl_approval_code+"'><td>"+bl_cardnumber+"</td><td>"+bl_amount+"</td><td>"+bl_bank+"</td><td>"+others+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#billing_cardnumber').val('');
		$('#billing_bankname').val('');
		$('#billing_amount').val('');
		$('#billing_card_type').val('');
		$('#billing_trace_number').val('');
		$('#billing_approval_code').val('');
		$('#billing_date').val('');
		updateCreditPayment();
	});

	$('body').on('click', '.removeItem', function() {
		var trid = $(this).parents('tr').remove();
		updateCreditPayment();
		updateCashPayment();
		updateConPayment();
		updateBankTransferPayment();
		updateChequePayment();
	});
	$('#addbanktransfer').click(function() {
		var bt_cardnumber = $('#bankfrom_account_number').val();
		var bt_bank = $('#bankfrom_name').val();
		var bt_amount = $('#bt_amount').val();
		if(!bt_cardnumber) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bt_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(bt_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bt_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#bt_table").append("<tr><td>" + bt_cardnumber + "</td><td>" + bt_amount + "</td><td>" + bt_bank + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#bankfrom_account_number').val('');
		$('#bankfrom_name').val('');
		$('#bt_amount').val('');
		updateBankTransferPayment();
	});
	$('#addcheque').click(function() {
		var ch_date = $('#ch_date').val();
		var ch_number = $('#ch_number').val();
		var ch_amount = $('#ch_amount').val();
		var ch_bankname = $('#ch_bankname').val();
		if(!ch_date) {
			showToast('error', '<p>Please indicate date</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_number) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!validateDate(ch_date)) {
			showToast('error', '<p>Invalid Date Format. It should be mm/dd/yyyy (Ex. 01/01/2014) </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(ch_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(ch_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_bankname) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(ch_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#ch_table").append("<tr><td>" + ch_date + "</td><td>" + ch_number + "</td><td>" + ch_amount + "</td><td>" + ch_bankname + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#ch_date').val('');
		$('#ch_number').val('');
		$('#ch_amount').val('');
		$('#ch_bankname').val('');
		updateChequePayment();
	});
	$('#con_amount_freebies').keyup(function(e) {

		if(!($('#con_member_freebies').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		var validamt = $('#con_member_freebies option:selected').attr('data-con_freebies');
		var cartfreebies = parseFloat(localStorage['totalforfreebies']);
		cartfreebies = 10000000;
		if(parseFloat($(this).val()) > cartfreebies) {
			showToast('error', '<p>Invalid freebies amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}

		if(parseFloat(validamt) < parseFloat($(this).val())) {
			showToast('error', '<p>Invalid freebies amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidconsumablepaymentfreebies").val($(this).val());
		if(isValidAmount($(this).val(), false)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
		}
		$("#hidconsumablepaymentfreebies").val($(this).val());
		updateConPaymentFreebies();
	});
	$('#member_credit_amount').keyup(function(e) {

		if(!($('#member_credit').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			return;
		}


		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidmembercredit").val($(this).val());
		if(isValidAmount($(this).val(), false)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
		}
		$("#hidmembercredit").val($(this).val());
		updateMemberCredit();
	});
	$('#member_deduction_amount').keyup(function(e) {

		if(!($('#member_deduction').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			return;
		}

		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}

		updateMemberDeduction();
	});
	$('#con_amount').keyup(function(e) {

		if(!($('#con_member').val())) {
			showToast('error', '<p>Please Choose member first</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(localStorage['hasType2'] == 1) {
			//current
			var name = $("#con_member option:selected").text();
			var memId = $("#con_member").val();
			removeMemberDetails();
			$("#membersIdHelper").append('Member Id: ');
			$("#memberId").append(memId);
			$("#membersnameHelper").append('Name: ');
			$("#membersname").append(name);
			localStorage.removeItem("temp_item_holder");
		}
		var validamt = $('#con_member option:selected').attr('data-con');
		if(parseFloat(validamt) < parseFloat($(this).val())) {
			showToast('error', '<p>Invalid consumable amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).focus();
			$(this).val('');
		}
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#hidconsumablepayment").val($(this).val());
		if(isValidAmount($(this).val(), false)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
		}
		$("#hidconsumablepayment").val($(this).val());
		updateConPayment();
	});
	$('body').on('click', '.btnPayment', function(e) {
		e.preventDefault();
		var row = $(this).parents('tr');
		var total = row.attr('data-total');
		var order_id = row.attr('data-id');
		var member_id = $(this).attr('data-member_id');
		var for_pickup = $(this).attr('data-for_pick_up');

		getMembersInd(localStorage['company_id'], member_id);

		$('#payment_order_id').val(order_id);
		if($('#AUTO_MEMBER_CREDIT').val() == 1){
			activaTabOpen('tab_g');
		}
		if($('#config_company_name').val() == 'cebuhiq'){
			if(for_pickup == 0){

				$('.tab_nav_a ').hide(1);
				$('.tab_nav_b ').hide(2);
				$('.tab_nav_c ').hide(3);
				$('.tab_nav_e ').hide(4);
				$('.tab_nav_f ').hide(5);

			} else if (for_pickup == 1){

				$('.tab_nav_b ').hide(1);
				$('.tab_nav_c ').hide(2);
				$('.tab_nav_e ').hide(3);
				$('.tab_nav_f ').hide(4);

			}

		}

		showpricemodal('0', total.toString());
	});

	/************* OVER PAYMENT ******************/
	$('body').on('click', '#btnOverPayment', function() {
		localStorage.removeItem('op_payment_cheque');
		localStorage.removeItem('op_payment_credit');
		localStorage.removeItem('op_payment_bt');
		localStorage.removeItem('op_payment_cash');

		op_updateCreditPayment();
		op_updateCashPayment();
		op_updateBankTransferPayment();
		op_updateChequePayment();
		$('#op_label_holder').hide();
		$('#modalOverPayment').modal('show');

	});

	/************************************ over price **********************************/
	function op_updateTotalPayment() {
		var cash = $("#op_cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		var credit_amount = $("#op_hidcreditpayment").val();
		if(!credit_amount) {
			credit_amount = 0;
		}
		var bt_amount = $("#op_hidbanktransferpayment").val();
		if(!bt_amount) {
			bt_amount = 0;
		}
		var ck_amount = $("#op_hidchequepayment").val();
		if(!ck_amount) {
			ck_amount = 0;
		}
		var gtotal = parseFloat(cash) + parseFloat(credit_amount) + parseFloat(bt_amount) + parseFloat(ck_amount);
		$("#op_totalOfAllPayment").html("<strong><span style='font-size:1.2em;' class='text-info' >Total Over Payment: " + gtotal.toFixed(2) + "</span></strong>");

	}

	function op_updateCashPayment() {
		var cash = $("#op_cashreceivetext").val();
		if(!cash) {
			cash = 0;
		}
		$("#op_totalcashpayment").html(cash);
		op_updateTotalPayment();
	}

	function op_updateCreditPayment() {
		var total = 0;
		if($("#op_credit_table tr").children().length) {
			$("#op_credit_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalcreditpayment").html(total);
		$("#op_hidcreditpayment").val(total);
		op_updateTotalPayment();
	}

	function op_updateBankTransferPayment() {
		var total = 0;
		if($("#op_bt_table tr").children().length) {
			$("#op_bt_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(1).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalbanktransferpayment").html(total);
		$("#op_hidbanktransferpayment").val(total);
		op_updateTotalPayment();
	}

	function op_updateChequePayment() {
		var total = 0;
		if($("#op_ch_table tr").children().length) {
			$("#op_ch_table tr").each(function(index) {
				var row = $(this);
				var amount = row.children().eq(2).text();
				total = parseFloat(total) + parseFloat(amount);
			});
		}
		$("#op_totalchequepayment").html(total);
		$("#op_hidchequepayment").val(total);
		op_updateTotalPayment();
	}

	$('#op_cashreceivetext').keyup(function(e) {
		if(isNaN($(this).val())) {
			showToast('error', '<p>Please Enter Valid Amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			$(this).val('');
			$(this).focus();
		}
		$("#op_hidcashpayment").val($(this).val());
		op_updateCashPayment();
	});
	$('#op_addcreditcard').click(function() {
		var bl_cardnumber = $('#op_billing_cardnumber').val();
		var bl_bank = $('#op_billing_bankname').val();
		var bl_amount = $('#op_billing_amount').val();
		if(!bl_cardnumber) {
			bl_cardnumber = 'N/A';
		}
		if(!bl_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bl_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bl_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bl_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#op_credit_table").append("<tr><td>" + bl_cardnumber + "</td><td>" + bl_amount + "</td><td>" + bl_bank + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_billing_cardnumber').val('');
		$('#op_billing_bankname').val('');
		$('#op_billing_amount').val('');
		op_updateCreditPayment();
	});
	$('#op_addbanktransfer').click(function() {
		var bt_cardnumber = $('#op_bankfrom_account_number').val();
		var bt_bank = $('#op_bankfrom_name').val();
		var bt_amount = $('#op_bt_amount').val();
		if(!bt_cardnumber) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(bt_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(bt_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!bt_bank) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(bt_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#op_bt_table").append("<tr><td>" + bt_cardnumber + "</td><td>" + bt_amount + "</td><td>" + bt_bank + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_bankfrom_account_number').val('');
		$('#op_bankfrom_name').val('');
		$('#op_bt_amount').val('');
		op_updateBankTransferPayment();
	});
	$('#op_addcheque').click(function() {
		var ch_date = $('#op_ch_date').val();
		var ch_number = $('#op_ch_number').val();
		var ch_amount = $('#op_ch_amount').val();
		var ch_bankname = $('#op_ch_bankname').val();
		if(!ch_date) {
			showToast('error', '<p>Please indicate date</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_number) {
			showToast('error', '<p>Please indicate card number</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_amount) {
			showToast('error', '<p>Please indicate amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!validateDate(ch_date)) {
			showToast('error', '<p>Invalid Date Format. It should be mm/dd/yyyy (Ex. 01/01/2014) </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isNaN(ch_amount)) {
			showToast('error', '<p>Please indicate a valid amount</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(parseFloat(ch_amount) < 1) {
			showToast('error', '<p>Amount should be greater than Zero</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(!ch_bankname) {
			showToast('error', '<p>Please indicate bank name</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		if(isValidAmount(ch_amount, true)) {
			showToast('error', '<p>Your payment exceeds to amount due.</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
			return;
		}
		$("#op_ch_table").append("<tr><td>" + ch_date + "</td><td>" + ch_number + "</td><td>" + ch_amount + "</td><td>" + ch_bankname + "</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");
		$('#op_ch_date').val('');
		$('#op_ch_number').val('');
		$('#op_ch_amount').val('');
		$('#op_ch_bankname').val('');
		op_updateChequePayment();
	});
	$('.op_cashreceivecancel').click(function() {
		$('#modalOverPayment').modal("hide");
		$("#op_credit_table").find("tr").remove();
		$("#op_bt_table").find("tr").remove();
		$("#op_ch_table").find("tr").remove();
		$("#over_payment_a :input[type='text']").val('');
		$("#over_payment_b :input[type='text']").val('');
		$("#over_payment_c :input[type='text']").val('');
		$("#over_payment_d :input[type='text']").val('');
		localStorage.removeItem('payment_cheque');
		localStorage.removeItem('payment_credit');
		localStorage.removeItem('payment_bt');
		localStorage.removeItem('payment_cash');
		localStorage.removeItem('payment_con');
		localStorage.removeItem('payment_con_freebies');
		localStorage.removeItem('payment_member_credit');
		localStorage.removeItem('payment_member_deduction');
		localStorage.removeItem('op_payment_cheque');
		localStorage.removeItem('op_payment_credit');
		localStorage.removeItem('op_payment_bt');
		localStorage.removeItem('op_payment_cash');
	});
	$('body').on('click', '.op_cashreceiveok', function() {
		var member_id = $('#op_member_id').val();
		var remarks = $('#op_remarks').val();
		if(member_id) {
			op_receiveCash();
		} else {
			tempToast('error', "<p>Please enter a client</p>", "<h4>Information!</h4>");
		}

	});
	function op_isValidFormCheque() {
		if($("#op_ch_table tr").children().length) {
			var chequeArray = new Array();
			var fn = $("#op_ch_firstname").val();
			var mn = $("#op_ch_middlename").val();
			var ln = $("#op_ch_lastname").val();
			var phone = $("#op_ch_phone").val();

			if(fn && !isAlphaNumeric(fn)) {
				showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(mn && !isAlphaNumeric(mn)) {
				showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(ln && !isAlphaNumeric(ln)) {
				showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			if(phone && !isAlphaNumeric(phone)) {
				showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			}
			$("#op_ch_table tr").each(function(index) {
				var row = $(this);
				chequeArray[index] = {
					date: row.children().eq(0).text(),
					cheque_number: row.children().eq(1).text(),
					amount: row.children().eq(2).text(),
					bank_name: row.children().eq(3).text(),
					firstname: fn,
					lastname: ln,
					middlename: mn,
					phone: phone
				}
			});
			localStorage['op_payment_cheque'] = JSON.stringify(chequeArray);
			return true;
		}

		return true;
	}

	function op_isValidFormCredit() {
		if($("#op_credit_table tr").children().length) {
			var creditArray = new Array();
			var fn = $("#op_billing_firstname").val();
			var mn = $("#op_billing_middlename").val();
			var ln = $("#op_billing_lastname").val();
			var comp = $("#op_billing_company").val();
			var add = $("#op_billing_address").val();
			var postal = $("#op_billing_postal").val();
			var phone = $("#op_billing_phone").val();
			var email = $("#op_billing_email").val();
			var rem = $("#op_billing_remarks").val();
			// required
			var card_type = $("#op_billing_card_type").val();
			var trace_number = $("#op_billing_trace_number").val();
			var approval_code = $("#op_billing_approval_code").val();
			var date = $("#op_billing_date").val();

			if(!card_type || !trace_number || !approval_code || !date) {
				showToast('error', '<p>Please Complete Credit Card billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn && !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Postal should be numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(email && !isEmail(email)) {
					showToast('error', '<p>Email should be valid email address</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(rem && !isAlphaNumeric(rem)) {
					showToast('error', '<p>Remarks should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				$("#op_credit_table tr").each(function(index) {
					var row = $(this);
					creditArray[index] = {
						credit_number: row.children().eq(0).text(),
						amount: row.children().eq(1).text(),
						bank_name: row.children().eq(2).text(),
						firstname: fn,
						lastname: ln,
						middlename: mn,
						phone: phone,
						comp: comp,
						add: add,
						postal: postal,
						email: email,
						remarks: rem,
						card_type: card_type,
						trace_number: trace_number,
						approval_code: approval_code,
						date: date
					}
				});
				localStorage['op_payment_credit'] = JSON.stringify(creditArray);
				return true;
			}

		}
		return true;
	}

	function op_isValidFormBankTransfer() {
		if($("#op_bt_table tr").children().length) {
			var bankTransferArray = new Array();
			var bt_bankto_name = $("#op_bt_bankto_name").val();
			var bt_bankto_account_number = $("#op_bt_bankto_account_number").val();
			var fn = $("#op_bt_firstname").val();
			var mn = $("#op_bt_middlename").val();
			var ln = $("#op_bt_lastname").val();
			var comp = $("#op_bt_company").val();
			var add = $("#op_bt_address").val();
			var postal = $("#op_bt_postal").val();
			var phone = $("#op_bt_phone").val();
			var date = $("#op_bt_date").val();

			if(!date) {
				showToast('error', '<p>Please Bank Transfer  billing form. </p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
				return false;
			} else {
				if(bt_bankto_name && !isAlphaNumeric(bt_bankto_name)) {
					showToast('error', '<p>Bank name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(bt_bankto_account_number && !isAlphaNumeric(bt_bankto_account_number)) {
					showToast('error', '<p>Bank account number should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(fn && !isAlphaNumeric(fn)) {
					showToast('error', '<p>First name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(mn & !isAlphaNumeric(mn)) {
					showToast('error', '<p>Middle name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(ln && !isAlphaNumeric(ln)) {
					showToast('error', '<p>Last name should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(comp && !isAlphaNumeric(comp)) {
					showToast('error', '<p>Company should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(add && !isAlphaNumeric(add)) {
					showToast('error', '<p>Address should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(postal && !isNumeric(postal)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				if(phone && !isAlphaNumeric(phone)) {
					showToast('error', '<p>Phone should have letters and numbers only</p>', '<h3>WARNING!</h3>', 'toast-bottom-right');
					return false;
				}
				$("#op_bt_table tr").each(function(index) {
					var row = $(this);
					bankTransferArray[index] = {
						credit_number: row.children().eq(0).text(),
						amount: row.children().eq(1).text(),
						bank_name: row.children().eq(2).text(),
						bt_bankto_name: bt_bankto_name,
						bt_bankto_account_number: bt_bankto_account_number,
						firstname: fn,
						lastname: ln,
						middlename: mn,
						phone: phone,
						comp: comp,
						add: add,
						postal: postal,
						date: date
					}
				});
				localStorage['op_payment_bt'] = JSON.stringify(bankTransferArray);
				return true;
			}
		}
		return true;
	}

	function op_receiveCash() {
		var cash = $("#op_hidcashpayment").val();
		if(!cash) cash = 0;
		var credit = $("#op_hidcreditpayment").val();
		if(!credit) credit = 0;
		var banktransfer = $("#op_hidbanktransferpayment").val();
		if(!banktransfer) banktransfer = 0;
		var cheque = $("#op_hidchequepayment").val();
		if(!cheque) cheque = 0;
		var totalpayment = parseFloat(cash) + parseFloat(credit) + parseFloat(banktransfer) + parseFloat(cheque);
		if(totalpayment) {
			if(!op_isValidFormCheque() || !op_isValidFormCredit() || !op_isValidFormBankTransfer()) {
				return;
			}

			var change = 0;
			cash = parseFloat(cash) - parseFloat(change);
			localStorage['op_payment_cash'] = cash;


			var payment_credit;
			var payment_bt;
			var payment_cheque;
			var payment_cash;


			if(localStorage['op_payment_cash']) {
				payment_cash = localStorage['op_payment_cash'];
			}
			if(localStorage['op_payment_credit']) {
				payment_credit = localStorage['op_payment_credit'];
			}
			if(localStorage['op_payment_bt']) {
				payment_bt = localStorage['op_payment_bt'];
			}
			if(localStorage['op_payment_cheque']) {
				payment_cheque = localStorage['op_payment_cheque'];
			}
			if(payment_cash || payment_credit || payment_bt || payment_cheque) {
				$("#op_credit_table").find("tr").remove();
				$("#op_bt_table").find("tr").remove();
				$("#op_ch_table").find("tr").remove();
				$("#over_payment_b :input[type='text']").val('');
				$("#over_payment_c :input[type='text']").val('');
				$("#over_payment_d :input[type='text']").val('');
				$("#over_payment_a :input[type='text']").val('');
				$('#op_label_holder').show();
				$('#op_grandtotalholder').html(number_format(totalpayment, 2));
				$('#modalOverPayment').modal("hide");
				// ajax call
				$.ajax({
					url: '../ajax/ajax_wh_order.php', type: 'POST', data: {
						functionName: 'saveOverPaymentOrder',
						op_payment_credit: payment_credit,
						op_payment_bt: payment_bt,
						op_payment_cheque: payment_cheque,
						op_payment_cash: payment_cash,
						credit: credit,
						banktransfer: banktransfer,
						cheque: cheque,
						terminal_id: localStorage['terminal_id'],
						member_id: $('#op_member_id').val(),
						remarks: $('#op_remarks').val(),
					}, success: function(data) {

						alertify.alert("Payment received", function() {
							printDepositSlip(data,totalpayment,$('#op_remarks').val());

							location.href = 'wh-order.php';
						});
					}, error: function() {

					}
				});
				// refresh
			} else {
				tempToast('error', "<p>No payment receive.</p>", "<h4>Error!</h4>");
			}
		}
	}

	// American Numbering System
	var th = ['','thousand','million','billion','trillion'];
	// uncomment this line for English Number System
	// var th = ['','thousand','million','milliard','billion'];

	var dg = ['zero','one','two','three','four','five','six','seven','eight','nine']; var tn = ['ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen']; var tw = ['twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];
	function toWords(s){s = s.replace(/[\, ]/g,''); if (s != parseFloat(s)) return 'not a number'; var x = s.indexOf('.'); if (x == -1) x = s.length; if (x > 15) return 'too big'; var n = s.split(''); var str = ''; var sk = 0; for (var i=0; i < x; i++) {if ((x-i)%3==2) {if (n[i] == '1') {str += tn[Number(n[i+1])] + ' '; i++; sk=1;} else if (n[i]!=0) {str += tw[n[i]-2] + ' ';sk=1;}} else if (n[i]!=0) {str += dg[n[i]] +' '; if ((x-i)%3==0) str += 'hundred ';sk=1;} if ((x-i)%3==1) {if (sk) str += th[(x-i-1)/3] + ' ';sk=0;}} if (x != s.length) {var y = s.length; str += 'point '; for (var i=x+1; i<y; i++) str += dg[n[i]] +' ';} return str.replace(/\s+/g,' ');}

	function printDepositSlip(d,total,rem){
		var data = JSON.parse(d);

		var company_name =data.company_name;
		var company_address = data.company_address;
		var member_name = data.member_name;
		var id = data.id;
		//var member_address = "";
		var amount = total;
		var amount_word = toWords(number_format(amount,0,'.',''));

		var remarks = rem;
		var rethtml = "<div style='border:1px solid #ccc;padding-left:20px;padding-right:20px;'>";
		var img = "";
		var contact = data.company_contact_number + " " + data.company_email;

		rethtml += "<h1 style='text-align: center;'>"+ img + " " +(company_name)+"</h1>";
		rethtml += "<p style='position:relative;top:-20px;text-align: center;'>"+company_address+"</p>";
		rethtml += "<p style='position:relative;top:-30px;text-align: center;'>"+contact+"</p>";
		rethtml += "<h4 style='position:relative;top:-10px;text-align: center;'><strong>ACKNOWLEDGEMENT RECEIPT</strong></h4>";
		rethtml += "<p style='position:relative;top:-10px;text-align: center;'>ID #"+id+"</p>";
		rethtml += "<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is to acknowledge the receipt of payment from <strong>"+(member_name)+"</strong> the amount of <strong>"+amount_word+" Pesos Only</strong> (Php. "+amount+") as payment of the following: </p>";
		rethtml += "<p style='height:10px;width:200px;border-bottom: 1px solid #000;'>&nbsp;</p>";
		rethtml += "<p style='height:10px;width:200px;border-bottom: 1px solid #000;'><span style='position:relative;top:-10px;'>"+remarks+"</span></p>";
		rethtml += "<p style='height:10px;width:200px;border-bottom: 1px solid #000;'>&nbsp;</p>";
		rethtml += "<p style='height:10px;text-align: right;'>Receive By: <span style='display:inline-block;width:200px;border-bottom: 1px solid #000;'></span></p>";
		rethtml += "<p style='height:10px;text-align: right;'>Date: <span style='display:inline-block;width:200px;border-bottom: 1px solid #000;'></span></p>";

		rethtml += "</div>";
		vm.popUpPrint(rethtml);


	}

	$('body').on('click', '#use_user_overpayment', function() {
		var over_payment_list = JSON.parse($('#op_member_list').val());
		if(over_payment_list.length > 0) {
			$('#use_user_overpayment').show();
		}
		var ret_html = "";
		for(var op in over_payment_list) {

			var remarks = (over_payment_list[op].remarks) ? over_payment_list[op].remarks : 'None';
			if(over_payment_list[op].status == 1) { // cash
				var total_cash = over_payment_list[op].json_data;
				var total_used_cash = over_payment_list[op].used_total;
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Cash</p>";
				ret_html += "<p>Used: "+(total_used_cash)+"</p>";
				ret_html += "<p>Total: " + (total_cash -total_used_cash) + "</p>";
				ret_html += "<p><input type='text' class='form-control' value='"+ (total_cash -total_used_cash) +"'><input data-status='1' data-id='" + over_payment_list[op].id + "' value='" + over_payment_list[op].id + "' data-total='" + over_payment_list[op].json_data + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			} else if(over_payment_list[op].status == 2) { // credit
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Credit Card</p>";
				var credit_data = JSON.parse(over_payment_list[op].json_data);
				var total_used_credit = over_payment_list[op].used_total;
				var total_credit = 0;
				for(var cd in credit_data) {
					ret_html += "<p>Card: " + credit_data[cd].card_type + "</p>";
					ret_html += "<p>Trance Number: " + credit_data[cd].trace_number + "</p>";
					ret_html += "<p>Date: " + credit_data[cd].date + "</p>";
					ret_html += "<p>Amount: " + credit_data[cd].amount + "</p>";
					ret_html += "<hr>";
					total_credit += parseFloat(total_credit) + parseFloat(credit_data[cd].amount);
				}
				ret_html += "<p>Used: " + total_used_credit + "</p>";
				ret_html += "<p>Total: " + (total_credit - total_used_credit) + "</p>";
				ret_html += "<p><input class='form-control' type='text' value='"+ (total_credit -total_used_credit) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "' value='" + over_payment_list[op].id + "' data-status='2' data-id='" + over_payment_list[op].id + "' data-total='" + total_credit + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			} else if(over_payment_list[op].status == 3) { // cheque
				ret_html += "<div class='panel panel-default'>";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Check</p>";
				var cheque_data = JSON.parse(over_payment_list[op].json_data);
				var total_used_checked = over_payment_list[op].used_total;
				var total_cheque = 0;

				for(var cd in cheque_data) {
					ret_html += "<p>Ctrl#: " + cheque_data[cd].cheque_number + "</p>";
					ret_html += "<p>Date: " + cheque_data[cd].date + "</p>";
					ret_html += "<p>Amount: " + cheque_data[cd].amount + "</p>";
					ret_html += "<hr>";
					total_cheque = parseFloat(total_cheque) + parseFloat(cheque_data[cd].amount);
				}
				ret_html += "<p>Used: " + total_used_checked + "</p>";
				ret_html += "<p>Total: " + total_cheque + "</p>";
				ret_html += "<p><input class='form-control' type='text' value='"+ (total_cheque - total_used_checked) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "' value='" + over_payment_list[op].id + "'  data-status='3' data-id='" + over_payment_list[op].id + "' data-total='" + total_cheque + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			} else if(over_payment_list[op].status == 4) { // bt
				ret_html += "<div class='panel panel-default' >";
				ret_html += "<div class='panel-body'>";
				ret_html += "<p>Remarks: " + remarks + "</p>";
				ret_html += "<p>Type: Bank Transfer</p>";
				var bt_data = JSON.parse(over_payment_list[op].json_data);
				var total_bt_used = over_payment_list[op].used_total;
				var total_bt = 0;
				for(var cd in bt_data) {
					ret_html += "<p>Date: " + bt_data[cd].date + "</p>";
					ret_html += "<p>Amount: " + bt_data[cd].amount + "</p>";

					total_bt = parseFloat(total_bt) + parseFloat( bt_data[cd].amount);
				}

				ret_html += "<p>Used: "+total_bt_used+"</p>";
				ret_html += "<p>Total: "+(total_bt -total_bt_used) +"</p>";

				ret_html += "<p> <input class='form-control' type='text' value='"+ (total_bt -total_bt_used) +"'> <input data-json='" + JSON.stringify(over_payment_list[op]) + "'  data-status='4' data-id='" + over_payment_list[op].id + "' value='" + over_payment_list[op].id + "' data-total='" + total_bt + "' type='checkbox' class='chk_overpayment' > Use Payment</p>";
				ret_html += "</div>";
				ret_html += "</div>";
			}
		}

		$('#right-pane-container').html(ret_html);
		$('.right-panel-pane').fadeIn(100);
	});
	$('body').on('click', '.chk_overpayment', function() {

		var con = $(this);
		var status = con.attr('data-status');
		var total = con.attr('data-total');

		var v = con.is(':checked');
		if(status == 1) { // cash
			var txtcon = $('#cashreceivetext');
			var cur_cash = txtcon.val();
			cur_cash = (cur_cash) ? cur_cash : 0;
			var total_use = con.prev().val();
			var cash_id = con.attr('data-id');
			if(v) {
				txtcon.val(parseFloat(cur_cash) + parseFloat(total_use));
				$("#hidcashpayment").val(parseFloat(cur_cash) + parseFloat(total_use));
			} else {
				txtcon.val(parseFloat(cur_cash) - parseFloat(total_use));
				$("#hidcashpayment").val(parseFloat(cur_cash) - parseFloat(total_use));
			}
			updateCashPayment();

		} else if(status == 2) { // credit
			var json = JSON.parse(con.attr('data-json'));
			var credit_data = JSON.parse(json.json_data);
			var billing_card_type = $('#billing_card_type');
			var billing_trace_number = $('#billing_trace_number');
			var billing_approval_code = $('#billing_approval_code');
			var billing_date = $('#billing_date');
			var chk_id = con.attr('data-id');
			var total_use = con.prev().val();
			var tax = parseFloat(total_use) * 0.035;
			var tax = number_format(tax,2,'.','');
			var rem = total_use - tax;
			rem = number_format(rem,2,'.','');
			$('#member_deduction_amount').val(tax);
			for(var i in credit_data) {
				if(v) {

					var others = "<p>Card Type: "+credit_data[i].card_type+"</p>";
					others += "<p>Trace Number: "+credit_data[i].trace_number+"</p>";
					others += "<p>Approval Code: "+credit_data[i].approval_code+"</p>";
					others += "<p>Date: "+credit_data[i].date+"</p>";

					$("#credit_table").append("<tr id='from_user_credit_credit" +chk_id + "' data-date='"+credit_data[i].date+"' data-card_type='"+credit_data[i].card_type+"' data-trace_number='"+credit_data[i].trace_number+"' data-approval_code='"+credit_data[i].trace_number+"'><td>"+credit_data[i].credit_number +"</td><td>"+rem+"</td><td>"+credit_data[i].bank_name+"</td><td>"+others+"</td><td><span  class='glyphicon glyphicon-remove-sign removeItem'></span></td></tr>");


				} else {
					$('#from_user_credit_credit' + chk_id).remove();
				}
			}
			updateCreditPayment();
		} else if(status == 3) { // check
			var json = JSON.parse(con.attr('data-json'));
			var check_data = JSON.parse(json.json_data);
			var ch_firstname = $('#ch_firstname');
			var ch_middlename = $('#ch_middlename');
			var ch_lastname = $('#ch_lastname');
			var ch_phone = $('#ch_phone');
			var total_use = con.prev().val();
			var chk_id = con.attr('data-id');
			for(var i in check_data) {
				if(v) {
					$('#ch_table').append("<tr id='from_user_credit_check" + chk_id + "' ><td>" + check_data[i].date + "</td><td>" + check_data[i].cheque_number + "</td><td>" + total_use + "</td><td>" + check_data[i].bank_name + "</td><td></td></tr>");
					ch_firstname.val(check_data[i].firstname);
					ch_middlename.val(check_data[i].middlename);
					ch_lastname.val(check_data[i].lastname);
					ch_phone.val(check_data[i].phone);
				} else {
					$('#from_user_credit_check' +chk_id).remove();
				}
			}
			updateChequePayment();
		} else if(status == 4) { // check
			var json = JSON.parse(con.attr('data-json'));
			var bt_id = con.attr('data-id');
			var check_data = JSON.parse(json.json_data);
			var ch_firstname = $('#bt_firstname');
			var ch_middlename = $('#bt_middlename');
			var ch_lastname = $('#bt_lastname');
			var ch_date= $('#bt_date');
			var total_use = con.prev().val();

			for(var i in check_data) {
				if(v) {

					ch_firstname.val(check_data[i].firstname);
					ch_middlename.val(check_data[i].middlename);
					ch_lastname.val(check_data[i].lastname);
					ch_date.val(check_data[i].date);

					$('#bt_table').append("<tr id='from_user_credit_check" + bt_id + "' ><td>" + check_data[i].credit_number + "</td><td>" + total_use + "</td><td>" + check_data[i].bank_name + "</td><td></td></tr>");


				} else {
					$('#from_user_credit_check' + bt_id).remove();
				}
			}
			updateBankTransferPayment();
		}

	});
	/************* END OVER PAYMENT ******************/

	$('body').on('click','#btnAddMoreMemberDeduction',function(){
		var member = $('#member_deduction');
		var member_id = member.val();
		var member_name = member.select2('data').text;
		var member_deduction_amount = $('#member_deduction_amount').val();
		var member_deduction_remarks = $('#member_deduction_remarks').val();
		var member_deduction_addtl_remarks = $('#member_deduction_addtl_remarks').val();
		var member_deduction_approved = "";
		if($('#member_deduction_is_approved').is(':checked')){
			member_deduction_approved = "For Approval";
		}

		$('#member_deduction_is_approved').prop("checked",false);
		$('#member_deduction_amount').val('');
		$('#member_deduction_remarks').val('');
		$('#member_deduction_addtl_remarks').val('');
		var ret = "<tr data-amount='"+member_deduction_amount+"' ><td>"+member_name+"</td><td>"+member_deduction_amount+"</td><td>"+member_deduction_remarks+"</td><td>"+member_deduction_addtl_remarks+"</td><td>"+member_deduction_approved+"</td><td><span class='glyphicon glyphicon-remove removeItem'></span></td></tr>";
		if(!isValidAmount(member_deduction_amount,true)){
			$('#member_deduction_table > tbody').append(ret);
			updateMemberDeduction();
		} else {
			showToast('error','<p>Invalid payment</p>','<h3>WARNING!</h3>','toast-bottom-right');
		}

	});
})(vm);