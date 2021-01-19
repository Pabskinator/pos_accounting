<?php 
	require 'db.php';
	
	$func = $_POST['functionName'];
	if(function_exists($func)){
		$func($conn);
	}
	
	function smart_resize_image($file,
	                            $string             = null,
	                            $width              = 0,
	                            $height             = 0,
	                            $proportional       = false,
	                            $output             = 'file',
	                            $delete_original    = true,
	                            $use_linux_commands = false,
	                            $quality = 100
	) {

		if ( $height <= 0 && $width <= 0 ) return false;
		if ( $file === null && $string === null ) return false;
		# Setting defaults and meta
		$info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
		$image                        = '';
		$final_width                  = 0;
		$final_height                 = 0;
		list($width_old, $height_old) = $info;
		$cropHeight = $cropWidth = 0;
		# Calculating proportionality
		if ($proportional) {
			if      ($width  == 0)  $factor = $height/$height_old;
			elseif  ($height == 0)  $factor = $width/$width_old;
			else                    $factor = min( $width / $width_old, $height / $height_old );
			$final_width  = round( $width_old * $factor );
			$final_height = round( $height_old * $factor );
		}
		else {
			$final_width = ( $width <= 0 ) ? $width_old : $width;
			$final_height = ( $height <= 0 ) ? $height_old : $height;
			$widthX = $width_old / $width;
			$heightX = $height_old / $height;

			$x = min($widthX, $heightX);
			$cropWidth = ($width_old - $width * $x) / 2;
			$cropHeight = ($height_old - $height * $x) / 2;
		}
		# Loading image to memory according to type
		switch ( $info[2] ) {
			case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
			case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
			case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
			default: return false;
		}


		# This is the resizing/resampling/transparency-preserving magic
		$image_resized = imagecreatetruecolor( $final_width, $final_height );
		if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
			$transparency = imagecolortransparent($image);
			$palletsize = imagecolorstotal($image);
			if ($transparency >= 0 && $transparency < $palletsize) {
				$transparent_color  = imagecolorsforindex($image, $transparency);
				$transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagefill($image_resized, 0, 0, $transparency);
				imagecolortransparent($image_resized, $transparency);
			}
			elseif ($info[2] == IMAGETYPE_PNG) {
				imagealphablending($image_resized, false);
				$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
				imagefill($image_resized, 0, 0, $color);
				imagesavealpha($image_resized, true);
			}
		}

		imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


		# Taking care of original, if needed
		if ( $delete_original ) {
			if ( $use_linux_commands ) exec('rm '.$file);
			else @unlink($file);
		}
		# Preparing a method of providing result
		switch ( strtolower($output) ) {
			case 'browser':
				$mime = image_type_to_mime_type($info[2]);
				header("Content-type: $mime");
				$output = NULL;
				break;
			case 'file':
				$output = $file;
				break;
			case 'return':
				return $image_resized;
				break;
			default:
				break;
		}

		# Writing image according to type to the output destination and image quality
		switch ( $info[2] ) {
			case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
			case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
			case IMAGETYPE_PNG:
				$quality = 9 - (int)((0.9*$quality)/10.0);
				imagepng($image_resized, $output, $quality);
				break;
			default: return false;
		}
		return true;
	}
	
	function hasTimeIn($code,$conn){
		$cur_date = date('Y-m-d');
		$query = "Select * from attendance where date(from_unixtime(time_in)) = '$cur_date' and emp_id ='$code' ";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		if(isset($row['id']) && $row['id']){
			return ['time_in' => $row['time_in'],'time_out' => $row['time_out']];
		} else {
			return false;
		}
	}
	
	function insertAttendance($hasTimeIn,$code,$url,$conn){
		
		$now = time();

		if($hasTimeIn){
			//$now = $now +25200;
			$query = "update attendance set time_out=$now, time_out_pic='$url' where emp_id='$code' and time_out=0";
			
		} else {
			
			$query = "insert into attendance(emp_id,time_in,time_in_pic) values($code,$now,'$url')";
			
		}
		
		$result = mysqli_query($conn, $query);
		
	}
function upload($conn){
	if (!empty($_FILES)) {
		$tempFile = $_FILES['file']['tmp_name'];          //3
		$targetPath = "imgs/" ;
		$code =  $_POST['code'];
		$userExist = userExist($conn,$code);
		
		if($userExist){
			$name = uniqid();
			$path = $_FILES['file']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$targetFile =  $targetPath.$name . ".".$ext;  //5
			move_uploaded_file($tempFile,$targetFile); //6
			//smart_resize_image($targetFile , null, 0 , 300 , true , $targetFile , true , false ,75 );
			$has_time_in =  hasTimeIn($code,$conn);
			$time_in  = ($has_time_in['time_in']) ? true:false;
			if(!($has_time_in['time_in'] && $has_time_in['time_out'])){
				insertAttendance($time_in,$code,$targetFile,$conn);
			}

		
			if($has_time_in['time_in'] && !$has_time_in['time_out']){
				echo "Good Bye!";
			} else if($has_time_in['time_in'] && $has_time_in['time_out']){
				echo "Record exists";
			} else {
				echo "Welcome!";
			}
		} else {
			echo "Unknown user code.";
		}
		
   }
}

function userExist($conn,$emp_id){

	$query = "Select * from users where  emp_id ='$emp_id'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if(isset($row['id']) && $row['id']){
		return true;
	} else {
		return false;
	}
}

function getAttendance($conn){

	$day = $_POST['day'];

	if($day){
		$cur_date=date('Y-m-d',strtotime(date('Y-m-d') . "-$day day"));
	} else {
		$cur_date=date('Y-m-d');
	}


	$query = "  Select a.*, u.firstname, u.lastname from attendance a
				left join users u on u.emp_id = a.emp_id
				where date(from_unixtime(a.time_in)) = '$cur_date' ";
	$result = mysqli_query($conn, $query);
	$arr = [];
	while($row = mysqli_fetch_assoc($result)){
		$row['dt1'] = date('m/d/Y H:i:s A',$row['time_in']);
		$row['fullname'] = ucwords($row['firstname'] . " " . $row['lastname']);
		if(!$row['time_out']){
			$row['dt2'] = 'N/A';
		} else {
			$row['dt2'] = date('m/d/Y H:i:s A',$row['time_out']);
		}
		
		$arr[] = $row;
	}
	echo json_encode(['list' =>$arr,'dt' => $cur_date]);
	
}

function getSummary($conn){
	$dt1 = $_POST['dt1'];
	$dt2 = $_POST['dt2'];

	if($dt1 && $dt2){
		$dt1 = strtotime($dt1);
		$dt2 = strtotime($dt2. "1 day -1 min");
	} else {
		$dt1 = strtotime(date('F Y'));
		$dt2 = strtotime(date('F Y') . "1 month -1 min");
	}
		

		 $query = "
			Select a.*, u.firstname , u.lastname, u.salary_rate, u.break_hrs, u.attendance_commission
			from attendance a
			left join users u on u.emp_id = a.emp_id
			where a.time_in >= $dt1 and a.time_in <= $dt2
			";
		$result = mysqli_query($conn, $query);
		$arr = [];
		$arr_com = [];
		$arr_data= [];
		while($row = mysqli_fetch_assoc($result)){
			if($row['time_out'] && $row['time_in']){
				$diff = $row['time_out'] - $row['time_in'];
				$hrs = ($diff / 60) / 60; // get hrs

				$break_hrs = $row['break_hrs'];
				$break_hrs = $break_hrs ? $break_hrs : 0;
				$hrs = $hrs - $break_hrs;
				if($hrs >= 8){
					$com = $row['attendance_commission'];
					$com = ($com) ? $com : 0;
					if($com){
						if(isset($arr_com[$row['emp_id']])){
							$arr_com[$row['emp_id']] += $com;
						} else {
							$arr_com[$row['emp_id']] = $com;
						}
					}

				}
				if(isset($arr[$row['emp_id']])){
					$arr[$row['emp_id']] += $hrs;
				} else {
					$arr[$row['emp_id']] = $hrs;
				}


				$fullname = ($row['lastname']) ? $row['firstname']. " " . $row['lastname'] : 'No Name';
				$row['salary_rate'] = ($row['salary_rate']) ? $row['salary_rate'] : 0;
				$arr_data[$row['emp_id']] = ['fullname' => $fullname,'rate' => $row['salary_rate']];

			}
			
		}

		$final =[];

		foreach($arr as $k => $v){

			$i_com = isset($arr_com[$k]) ? $arr_com[$k] : 0;

			$final[] = ['attendance_com' => $i_com, 'emp_id' => $k, 'total' => number_format($v,2),'fullname' => $arr_data[$k]['fullname'],'rate' => $arr_data[$k]['rate']];

		}
		echo json_encode($final);

	

}

function getDetails($conn){

	$emp_id = $_POST['emp_id'];
	$dt1 = $_POST['dt1'];
	$dt2 = $_POST['dt2'];
   if($dt1 && $dt2){
		$dt1 = strtotime($dt1);
		$dt2 = strtotime($dt2. "1 day -1 min");
	} else {
		$dt1 = strtotime(date('F Y'));
		$dt2 = strtotime(date('F Y') . "1 month -1 min");
	}
	   $query = "Select a.*, u.lastname, u.firstname, u.salary_rate from attendance a left join users u on u.emp_id = a.emp_id where a.time_in >= $dt1 and a.time_out <= $dt2 and a.emp_id = '$emp_id' ";
		$result = mysqli_query($conn, $query);
		$arr = [];
		$arr_data= [];
		if(mysqli_num_rows($result)){
			while($row = mysqli_fetch_assoc($result)){
			if($row['time_out'] && $row['time_in']){
				$diff = $row['time_out'] - $row['time_in'];
				$hrs = ($diff / 60) / 60; // get hrs

				$row['time_in'] = date('m/d/Y H:i:s A',$row['time_in']);
				$row['time_out'] = date('m/d/Y H:i:s A',$row['time_out']);
				$row['hrs'] = number_format($hrs,2);
				$arr[] = $row;
			}		
		}
		}
		
		echo json_encode($arr);
}



	function getName($conn){

		$emp_id = $_POST['emp_id'];

		$query = "Select * from users where  emp_id ='$emp_id'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		if(isset($row['id']) && $row['id']){
			echo json_encode(['name' => $row['firstname'] . " " . $row['lastname'], 'emp_id'=>$row['emp_id']]);
		} else {
			echo json_encode(['name' => "Not Found", 'emp_id'=>0]);
		}


	}


?>