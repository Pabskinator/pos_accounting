<?php
	class Truck extends Crud{
		protected $_table = 'trucks';
		public function __construct($t=null){
			parent::__construct($t);
		}
	}
?>