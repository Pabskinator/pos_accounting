<div class="row">
	<div class="col s12">

		<ul class="tabs">
			<li class="tab col s4 m2">
				<a class="active" href="#cash">Cash</a>
			</li>
			<li class="tab col s4 m2"><a href="#cheque">Cheque</a></li>
			<li class="tab col s4 m2"><a href="#credit_card">Credit Card</a></li>
			<li class="tab col s4 m2"><a href="#bank_transfer">Bank Deposit</a></li>
			<li class="tab col s4 m2"><a href="#deduction">Deduction</a></li>
			<li class="tab col s4 m2"><a href="#member_credit">Member Credit</a></li>
		</ul>
		<div id='total-holder' class="card-panel hide-on-med-and-down">
			<div class="col s2 center-align">&#8369; {{ cash_total }}</div>
			<div class="col s2 center-align">&#8369; {{ cheque_total }}</div>
			<div class="col s2 center-align">&#8369; {{ credit_total }}</div>
			<div class="col s2 center-align">&#8369; {{ bt_total }}</div>
			<div class="col s2 center-align ">&#8369; {{ deduction_total }}</div>
			<div class="col s2 center-align">&#8369; {{ member_credit_total }}</div>
		</div>
	</div>
	<div id='p-all' class='white-text'>
	<div id="cash" >
		<div class="row">
			<div class="col s12 m3"></div>
			<div id='cash-container' class='col s12 m6'>
				<div class="card-panel grey darken-4">
					<div class="input-field">
						<input placeholder="Amount"  v-model='cash' @keyup=numberOnly('cash','input-cash') id="input-cash" type="number" >
						<label for="input-cash">Enter Cash Amount</label>
					</div>
					<div class='row noselect'>
						<div class="col s2 cpointer"  @click="addPaymentAmount('cash',20)">20</div>
						<div class="col s2 cpointer"  @click="addPaymentAmount('cash',50)">50</div>
						<div class="col s2 cpointer"  @click="addPaymentAmount('cash',100)">100</div>
						<div class="col s2 cpointer"  @click="addPaymentAmount('cash',500)">500</div>
						<div class="col s2 cpointer"  @click="addPaymentAmount('cash',1000)">1000</div>
						<div class="col s2 cpointer"  @click="addPaymentAmount('cash',5000)">5000</div>
					</div>
					<div class="row noselect">
						<a href="#" @click.prevent="addPaymentAmount('cash',cartTotal)">Exact Amount</a>
					</div>
				</div>
			</div>
			<div class="col s12 m3"></div>
		</div>
	</div>
	<div id="cheque" class="col s12">
		<div class='card-panel grey darken-4'>
			<div class="row">

				<div class="col m2 s6">
					<div class="input-field">
						<input placeholder="Bank" id="input-cheque-bank" v-model='cheque_form.bank'  type="text" >
						<label for="input-cheque-bank">Enter Bank Name</label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<input placeholder="Cheque Number" id="input-cheque-number" v-model='cheque_form.number'  type="text" >
						<label for="input-cheque-number">Enter Cheque Number</label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<input placeholder="Amount" id="input-cheque"  @keyup=numberOnly('cheque_form','input-cheque','amount') v-model='cheque_form.amount' type="number" >
						<label for="input-cheque">Enter amount</label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<input  id="input-maturity-date"  v-model='cheque_form.date' type="date" >
						<label for="input-maturity-date"></label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<button class='btn grey darken-1' @click="addCheque">Submit</button>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div id='check-list' class='card-panel grey darken-4'>
			<table class='bordered' v-show="cheque.length">
				<thead><tr><th>Bank</th><th>Cheque Number</th><th>Amount</th><th>Date</th></tr></thead>
				<tbody><tr v-for="ch in cheque"><td>{{ch.bank}}</td><td>{{ch.number}}</td><td>{{ch.amount}}</td><td>{{ch.date}}</td></tr></tbody>
			</table>
			<div  v-show="!cheque.length">
				<h4>Enter Cheque Details First</h4>
			</div>
		</div>
	</div>
	<div id="credit_card" class="col s12">
		<div class='card-panel grey darken-4'>
			<div class="row">

				<div class="col m2 s6">
					<div class="input-field">
						<input placeholder="Bank" id="input-card-bank" v-model='card_form.bank'   type="text" >
						<label for="input-card-bank">Enter Bank Name</label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<input placeholder="Card Type" id="input-card-type"  v-model='card_form.type'   type="text" >
						<label for="input-card-type">Enter Card Type</label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<input placeholder="Amount" id="input-card"  @keyup=numberOnly('card_form','input-card','amount') v-model='card_form.amount' type="number" >
						<label for="input-card">Enter amount</label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<input placeholder="" id="input-card-date"  v-model='card_form.date' type="date" >
						<label for="input-card-date"></label>
					</div>
				</div>
				<div class="col m2 s6">
					<div class="input-field">
						<button class='btn grey darken-1' @click="addCard">Submit</button>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div id='check-list' class='card-panel grey darken-4'>
			<table class='bordered' v-show="credit_card.length">
				<thead><tr><th>Bank</th><th>Card Type</th><th>Amount</th><th>Date</th></tr></thead>
				<tbody><tr v-for="ch in credit_card"><td>{{ch.bank}}</td><td>{{ch.type}}</td><td>{{ch.amount}}</td><td>{{ch.date}}</td></tr></tbody>
			</table>
			<div  v-show="!credit_card.length">
				<h4>Enter Card Details First</h4>
			</div>
		</div>
	</div>
	<div id="bank_transfer" class="col s12">
		<div class='card-panel grey darken-4'>
			<div class="row">

				<div class="col m3 s6">
					<div class="input-field">
						<input placeholder="Bank" id="input-bt-bank" v-model='bt_form.bank'  type="text" >
						<label for="input-bt-bank">Enter Bank Name</label>
					</div>
				</div>

				<div class="col m3 s6">
					<div class="input-field">
						<input placeholder="Amount" id="input-bt" @keyup=numberOnly('bt_form','input-bt','amount') v-model='bt_form.amount' type="number" >
						<label for="input-bt">Enter amount</label>
					</div>
				</div>
				<div class="col m3 s6">
					<div class="input-field">
						<input  id="input-bt-date"  v-model='bt_form.date' type="date" >
						<label for="input-bt-date"></label>
					</div>
				</div>
					<div class="col m3 s6">
					<div class="input-field">
						<button class='btn grey darken-1' @click="addBt">Submit</button>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div id='check-list' class='card-panel grey darken-4'>
			<table class='bordered' v-show="bank_transfer.length">
				<thead><tr><th>Bank</th><th>Amount</th><th>Date</th></tr></thead>
				<tbody><tr v-for="ch in bank_transfer"><td>{{ch.bank}}</td><td>{{ch.amount}}</td><td>{{ch.date}}</td></tr></tbody>
			</table>
			<div  v-show="!bank_transfer.length">
				<h4>Enter Bank Details First</h4>
			</div>
		</div>
	</div>
	<div id="deduction" class="col s12">
		<div class='card-panel grey darken-4'>
			<div class="row">

				<div class="col m4 s6">
					<div class="input-field">
						<input placeholder="Type" id="input-deduct-type" v-model='deduction_form.type'  type="text" >
						<label for="input-deduct-type">Enter Deduction Type</label>
					</div>
				</div>

				<div class="col m4 s6">
					<div class="input-field">
						<input placeholder="Amount" id="input-deduct"  @keyup=numberOnly('deduction_form','input-deduct','amount') v-model='deduction_form.amount' type="number" >
						<label for="input-deduct">Enter amount</label>
					</div>
				</div>
				<div class="col m4 s6">
					<div class="input-field">
						<button class='btn grey darken-1' @click="addDeduction">Submit</button>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div id='check-list' class='card-panel grey darken-4'>
			<table class='bordered' v-show="deductions.length">
				<thead><tr><th>Type</th><th>Amount</th></tr></thead>
				<tbody><tr v-for="ch in deductions"><td>{{ch.type}}</td><td>{{ch.amount}}</td></tr></tbody>
			</table>
			<div  v-show="!deductions.length">
				<h4>Enter Deduction Details First</h4>
			</div>
		</div>
	</div>
	<div id="member_credit" class="col s12">
		<div class="row">
			<div class="col s12 m3"></div>
			<div id='member-credit-container' class='col s12 m6'>
				<div class="card-panel grey darken-4">
					<div class="input-field">
						<input placeholder="Amount" v-model='member_credit' id="input-member-credit" type="text" >
						<label for="input-member-credit">Enter Member Credit Amount</label>
					</div>
				</div>
			</div>
			<div class="col s12 m3"></div>
		</div>
	</div>

</div>
</div>