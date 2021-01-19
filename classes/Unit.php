<?php
	class Unit extends Crud{
		protected $_table = 'units';
		public function __construct($unit=null){
			parent::__construct($unit);
		}
	}
?>