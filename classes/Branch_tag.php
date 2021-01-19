<?php
	class Branch_tag extends Crud{
		protected $_table = 'branch_tags';
		public function __construct($b=null){
			parent::__construct($b);
		}
	}
