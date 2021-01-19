<?php
	class Remark extends Crud{
		protected $_table = 'remarks';
		public function __construct($remark=null){
			parent::__construct($remark);
		}
	}
?>