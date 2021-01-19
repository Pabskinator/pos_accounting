export default {
	data: {
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
	}
}