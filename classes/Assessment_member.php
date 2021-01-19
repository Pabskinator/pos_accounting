<?php
	/**
	 * Created by PhpStorm.
	 * User: temp
	 * Date: 4/11/2017
	 * Time: 2:18 PM
	 */

	class Assessment_member extends Crud{
		protected $_table='assessment_members';
		public function __construct($o=null){
			parent::__construct($o);
		}

	}
