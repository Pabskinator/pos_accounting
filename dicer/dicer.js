var vm = new Vue({
	el:'#wrapper',
	data: {
		nav: {
			home:true,
			history:false
		},
		container:{
			home: {
					request: true,
					cart: false
			},
			history: {

			}
		},
		home: {

			request: {
				item_id: '',
				invoice: '',
				qty: '',
				date: '',
				item_code: '',
				description: '',
			},
			cart_items:[],
		},
		history:{
			list:[
				{ total:100, invoice:'asd', dr:'', ir:'', sold_date:'01/01/2018' },
			],
			request: { date_from: '', date_to: '' },
			details: [],
		}
	},
	mounted: function(){
		var self = this;
		$('#main-lbl').html(localStorage['company_name']);
		$(".menu-toggle").click(function(e) {
			e.preventDefault();
			$("#wrapper").toggleClass("toggled");
		});

		$(".selectitem").select2({
			placeholder: 'Item code',
			allowClear: true,
			minimumInputLength: 2,
			formatResult: self.formatItem,
			formatSelection: self.formatItem,
			escapeMarkup: function(m) {
				return m;
			},
			ajax: {
				url: '../ajax/ajax_query.php',
				dataType: 'json',
				type: "POST",
				quietMillis: 50,
				data: function(term) {
					return {
						search: term, functionName: 'searchItemJSON'
					};
				},
				results: function(data) {
					return {
						results: $.map(data, function(item) {
							return {
								text: item.barcode + ":" + self.replaceAll(item.item_code,':','') + ":" + self.replaceAll(item.description,':','') + ":" + item.price,
								slug: item.description,
								is_bundle: item.is_bundle,
								unit_name: item.unit_name,
								id: item.id
							}
						})
					};
				}
			}
		}).on("select2-close", function(e) {
			// fired to the original element when the dropdown closes

		}).on("select2-highlight", function(e) {

		});

		$('#item_id').change(function(){
			var con = $(this);
			self.home.request.item_id = con.val();
			var txt = con.select2('data').text;
			var txt_info = txt.split(':');
			self.home.request.item_code = txt_info[1];
			self.home.request.description =  txt_info[2];
		});


		$('#date_sold').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#date_sold').datepicker('hide');
			self.home.request.date = $('#date_sold').val();
		});

		$('#date_from').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#date_from').datepicker('hide');
			self.history.request.date_from = $('#date_from').val();
		});

		$('#date_to').datepicker({
			autoclose:true
		}).on('changeDate', function(ev){
			$('#date_to').datepicker('hide');
			self.history.request.date_to = $('#date_to').val();
		});

	},
	methods: {
		getDetails: function(id){
			var self =  this;

			$.ajax({
			    url:'service.php',
			    type:'POST',
				dataType:'json',
			    data: {functionName:'getDetails', payment_id:id},
			    success: function(data){
					self.history.details = data;
			    },
			    error:function(){

			    }
			});

		},
		getSales: function(){
			var self = this;
			$.ajax({
			    url:'service.php',
			    type:'POST',
				dataType:'json',
			    data: {functionName:'getSales',date_from:self.history.request.date_from,date_to:self.history.request.date_to},
			    success: function(data){
				    self.history.list = data;
			    },
			    error:function(){

			    }
			});

		},
		emptyCart: function(){
			if(confirm("Are you sure you want to empty your cart?")){
				this.home.cart_items = [];
			}
		},
		submitSales: function(){
			var self = this;
			$.ajax({
			    url:'service.php',
			    type:'POST',
			    data: {functionName:'addSales',items: JSON.stringify(self.home.cart_items)},
			    success: function(data){
			        alert(data);
			    },
			    error:function(){
			        
			    }
			})
		},
		removeItem: function(r){
			this.home.cart_items = this.home.cart_items.filter(function(i){
				return i.item_id != r.item_id;
			});
		},
		resetForm: function(){
			var self = this;
			self.home.request = {
				item_id: '', qty: '', invoice:'',date: '', item_code: '', description: '',
			};
			$(".selectitem").select2('val', null);
			$('#date_sold').val('');
		},
		addItem: function() {
			var self = this;
			self.home.cart_items.push(self.home.request)

			self.home.request = {
				item_id: '', qty: '', invoice: $('#invoice').val(),date: $('#date_sold').val(), item_code: '', description: '',
			};
			tempToast('info','Item added.','Info');
			$(".selectitem").select2('val', null);


		},
		 escapeRegExp: function(string) {
			return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
		},
		 replaceAll: function(string, find, replace) {
			return string.replace(new RegExp(this.escapeRegExp(find), 'g'), replace);
		},
		formatItem: function(o) {

			if(!o.id)
				return o.text; // optgroup
			else {
				var r = o.text.split(':');
				return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
			}
		},
		navigateTo: function(c,to){
				var self = this;
				if(c == 'home'){
					self.hideContainer(c);
					self.container.home[to] = true;
				}
		},
		goTo: function(c){
				var self = this;
				self.nav = {home: false,history:false};
				if( c == 'home'){
					self.nav.home = true;
				} else if( c == 'history'){
					self.nav.history = true;
					self.getSales();
				}
		},
		hideContainer: function(c){
			var self = this;
			if( c == 'home' ){
				self.container.home = { request: false, cart: false };
			} else if ( c == 'history' ){
				//self.history.home = { request: false, cart: false };

			}
		},
	}
});