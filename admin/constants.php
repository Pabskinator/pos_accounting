<?php
	if($http_host == 'localhost:81'){
		$collection_report_view = [
			[
				'date' => true,
				'order' => 1
			],
			[
				'dr' => true,
				'order' => 2
			],
			[
				'invoice' => true,
				'order' => 3
			],
			[
				'client' => true,
				'order' => 4
			],
			[
				'receipt_amount' => true,
				'order' => 5
			],
			[
				'deduction' => true,
				'order' => 6
			],
			[
				'paid_amount' => true,
				'order' => 7
			],
			[
				'bank' => true,
				'order' => 8
			],
			[
				'check' => true,
				'order' => 9
			],
			[
				'check_date' => true,
				'order' => 10
			],
			[
				'terms' => true,
				'order' => 11
			],
			[
				'com' => true,
				'order' => 12
			],
		];
	}

?>