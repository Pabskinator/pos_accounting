<?php
	class Package extends Crud{
		protected $_table = 'packages';
		public function __construct($p=null){
			parent::__construct($p);
		}
	}
?>