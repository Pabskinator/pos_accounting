<?php
	class Item_brand extends Crud{
		protected $_table = 'item_brands';
		public function __construct($b=null){
			parent::__construct($b);
		}
	}
?>