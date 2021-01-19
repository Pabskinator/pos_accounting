<?php
	class Member_char_list extends Crud{
		protected $_table = 'member_characteristics_list';
		public function __construct($char=null){
			parent::__construct($char);
		}
	}
?>