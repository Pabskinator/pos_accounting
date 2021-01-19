<?php include 'page_head.php' ?>
	<div v-show="nav.home">
		<nav class="navbar navbar-default navbar-inverse">

				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed menu-toggle"  aria-expanded="false">
						<span class="sr-only ">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#"></a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class='menu-toggle'><a href="">Toggle</a></li>
					</ul>

					<ul class="nav navbar-nav navbar-right">
						<li><a href="#" @click="navigateTo('home','request')">Add Sales</a></li>
						<li><a href="#" @click="navigateTo('home','cart')" >List ({{home.cart_items.length}})</a></li>
					</ul>
				</div><!-- /.navbar-collapse -->

		</nav>
		<div class='show-on-mobile' style='margin-bottom: 10px;'>
			<div class="btn-group" role="group" aria-label="...">
				<button type="button" class="btn btn-default" @click="navigateTo('home','request')">Add Sales</button>
				<button type="button" class="btn btn-default" @click="navigateTo('home','cart')">List ({{home.cart_items.length}})</button>
			</div>
		</div>
		<div v-show="container.home.request">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-body">
						<h3 class='text-center'>Enter Sales</h3>
						<br>

						<div class="form-group">
							<input type="text" autocomplete="false" class='form-control' id='date_sold' placeholder='Date' v-model="home.request.date">
							<span class='help-block'>Enter Date</span>
						</div>
						<div class="form-group">
							<input type="text"  autocomplete="false" class='form-control' id='invoice' placeholder='Invoice' v-model="home.request.invoice">
							<span class='help-block'>Enter Invoice</span>
						</div>
						<div class="form-group">
							<input type="text" autocomplete="false" id='item_id' class='form-control selectitem' v-model="home.request.item_id">
							<span class='help-block'>Enter Item</span>
						</div>
						<div class="form-group">
							<input type="text" autocomplete="false" class='form-control' placeholder='Qty' v-model="home.request.qty">
							<span class='help-block'>Enter Qty</span>
						</div>
						<div class="form-group">
							<button class='btn btn-primary' @click="addItem">Add Item</button>
							<button class='btn btn-danger' @click="resetForm">Reset</button>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3"></div>
		</div>

		<div v-show="container.home.cart">
				<div v-show="home.cart_items.length">

				<table class='table table-bordered'>
					<thead>
						<tr>
							<th>Date</th>
							<th>Invoice</th>
							<th>Item</th>
							<th>Qty</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="item in home.cart_items">
							<td>{{ item.date }}</td>
							<td>{{ item.invoice }}</td>
							<td>
								{{ item.item_code }}
								<small class='text-danger span-block'>{{item.description}}</small>
							</td>
							<td>{{ item.qty }}</td>

							<td class='text-center'>
								<button class='btn btn-danger btn-sm' @click="removeItem(item)"><i class='glyphicon glyphicon-remove'></i></button>
							</td>
						</tr>
					</tbody>
				</table>
				<div>
						<button class='btn btn-primary' @click="submitSales()">Submit</button>
						<button class='btn btn-danger' @click="emptyCart">Empty Cart</button>
				</div>
			</div>
			<div v-show="!home.cart_items.length">
				<div class="alert alert-info">
					<i class='glyphicon glyphicon-info-sign'></i> No item found.
				</div>
			</div>

		</div>
	</div>
	<div v-show="nav.history">
		<nav class="navbar navbar-default navbar-inverse">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed menu-toggle"  aria-expanded="false">
					<span class="sr-only ">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"></a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li class='menu-toggle'><a href="">Toggle</a></li>
				</ul>

				<ul class="nav navbar-nav navbar-right">

				</ul>
			</div><!-- /.navbar-collapse -->
		</nav>
		<h1>History</h1>
		<div v-show="!history.details.length">

			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='date_from' v-model='history.request.date_from' placeholder='Date From' class='form-control'>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='date_to'  v-model='history.request.date_to' placeholder='Date To' class='form-control'>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<button class='btn btn-default' id='btnSubmit' @click="getSales" >Submit</button>
					</div>
				</div>
			</div>
			<table class='table table-bordered'>
				<thead>
					<tr>
						<th>Sold Date</th>
						<th>Invoice</th>
						<th>Total</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="l in history.list">
						<td>{{ l.sold_date }}</td>
						<td>{{ l.invoice }}</td>
						<td>{{ l.total }}</td>
						<td class='text-center'><button class='btn btn-default btn-sm' @click="getDetails(l.payment_id)">Details</button></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div v-show="history.details.length">
			<div class="row">
				<div class="col-md-12">
					<button class='btn btn-default' @click="history.details = []">Back</button>
				</div>
			</div>
			<br>
			<table class='table table-bordered'>
				<thead>
					<tr>
						<th>Item</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="det in history.details" >
						<td>{{ det.item_code }}</td>
						<td>{{ det.qty }}</td>
					</tr>
				</tbody>
			</table>
		</div>

	</div>
<?php include 'page_tail.php' ?>