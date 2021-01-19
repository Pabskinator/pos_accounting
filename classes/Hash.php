<?php
class Hash {
	public static function make($string){
		return md5($string);
	}
		public static function unique(){
			return self::make(uniqid());
	}
}
?>