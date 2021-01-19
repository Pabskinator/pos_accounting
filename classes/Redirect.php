<?php 
class Redirect{
	public static function to($location=null){

		if($location){
			if(is_numeric($location))
			{
				switch($location){
				case 404:
					header('HTTPS/1.0 404 Not Found');
					include '../includes/errors/404.php';
					exit();
				break;
					case 1:
					include '../includes/errors/denied.php';
					exit();
				}
			}

			}

			header('Location: ' . $location);
			exit();
		}
	}

?>