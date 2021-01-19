<?php
	class Company extends Crud{
		protected $_table = 'companies';
		public function __construct($company=null){
			parent::__construct($company);
		}
	}
?>