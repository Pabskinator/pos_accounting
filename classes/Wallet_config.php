<?php
	class Wallet_config extends Crud{
		protected $_table = 'wallet_configuration';
		public function __construct($w=null){
			parent::__construct($w);
		}

	}