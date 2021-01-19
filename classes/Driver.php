<?php

	class Driver extends  Crud {
		protected $_table = 'drivers';
		public function __construct($d=null){
			parent::__construct($d);
		}
	}