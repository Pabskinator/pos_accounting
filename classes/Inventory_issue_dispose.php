<?php
	class Inventory_issue_dispose extends Crud {
		protected $_table = 'inventory_issues_disposed';

		public function __construct($inventory = null) {
			parent::__construct($inventory);
		}
	}