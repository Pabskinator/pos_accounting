<?php
	class Brand extends Crud{
		protected $_table = 'brands';
		public function __construct($b=null){
			parent::__construct($b);
		}
	}
?>