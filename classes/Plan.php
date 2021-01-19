<?php
	class Plan extends Crud{
		protected $_table = 'plans';
		public function __construct($plan=null){
			parent::__construct($plan);
		}



	}
?>