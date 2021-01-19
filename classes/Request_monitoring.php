<?php
	class Request_monitoring extends Crud{
		protected $_table = 'request_monitoring';
		public function __construct($monitoring=null){
			parent::__construct($monitoring);
		}
		

	}
?>