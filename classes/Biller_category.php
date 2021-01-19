<?php
	class Biller_category extends Crud{
		protected $_table = 'biller_categories';
		public function __construct($b=null){
			parent::__construct($b);
		}
	}