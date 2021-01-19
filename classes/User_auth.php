<?php
	/*
	   If you’re reading this,
	   that means you have been put in charge of my previous project.
	   I am so, so sorry for you.
	   This code sucks, you know it and I know it. Move on and call me an idiot later
	   God speed.
	*/

	class User_auth extends Crud{
		protected $_table = 'user_auth';
		public function __construct($u=null){
			parent::__construct($u);
		}
	}
?>