<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	$functionName();
	function addSpec(){
		$item_id = Input::get('item_id');
		$specs = json_decode(Input::get('current_specs'));
		if($item_id && count($specs) > 0 ){
			$user = new User();
			foreach($specs as $spec){
				$spec_cls = new Item_post_spec();
				if($spec->value){
					$spec_cls->create(
						[
							'item_post_id' =>$item_id,
							'spec_id' => $spec->id,
							'spec_value' => $spec->value,
							'is_active' =>1,
							'company_id' =>$user->data()->company_id
						]
					);
				}
			}
			echo "Added successfully.";
		}
	}
	function saveSpecs(){
		$data = json_decode(Input::get('item'));
		if(isset($data->id) && is_numeric($data->id) && $data->id){
			$cls = new Item_post_spec();
			$cls->update(['spec_value' => $data->spec_value],$data->id);
			echo "Updated successfully.";
		} else {
			echo "Invalid request";
		}

	}
	function deleteSpecs(){
		$data = json_decode(Input::get('item'));
		if(isset($data->id) && is_numeric($data->id) && $data->id){
			$cls = new Item_post_spec();
			$cls->deleteSpecs($data->item_post_id);
			echo "Deleted successfully.";
		}
	}
	function getSpecList(){
		$user = new User();
		$cls = new Item_post_spec();
		$list = $cls->getSpecs($user->data()->company_id);
		$prev = '';
		$arr = [];
		foreach($list as $l){
			if($prev != $l->item_code){
				$prev = $l->item_code;
			} else {
				$prev = $l->item_code;
				$l->item_code = '';

			}
			$l->is_editing = false;

			$arr[] = $l;
		}
		echo json_encode($arr);
	}

	function addPostRecord(){
		$post = Input::get('post_request');
		$post = json_decode($post);
		$user = new User();
		if($post->item_id && $post->qty){
			$item_post = new Item_post();
			$now = time();
			$price_adj = (float) $post->price_adjustment;

			$item_post->create([
				'item_id' => $post->item_id,
				'qty' => $post->qty,
				'remarks' => $post->remarks,
				'date_posted' =>$now,
				'created' =>$now,
				'status' =>1,
				'is_active' =>1,
				'company_id' =>$user->data()->company_id,
				'user_id' =>$user->data()->id,
				'price_adjustment' =>$price_adj
			]);

			echo json_encode(['success' => true,'message' => 'Item inserted successfully.']);
		} else {
			echo json_encode(['success' => false,'message' => 'Failed to insert a record. Please try again.']);
		}
	}
	function getAtt(){
		$cls = new Upload();
		$user = new User();
		$ref_table = 'item_post';
		$ref_id = Input::get('id');
		$uploadedImages = $cls->getAllImage($user->data()->company_id,$ref_table,$ref_id);
		if($uploadedImages){
			$arr = [];
			foreach($uploadedImages as $u){
				$arr[] = ['id' => $u->id,'ref_id' => $u->ref_id,'ref_table' => $u->ref_table,'is_main' => $u->is_main,'url' => '../uploads/'. $u->filename,'thumbnail' => '../uploads/'. $u->thumbnail, 'title' => $u->title, 'description' => $u->description];
			}
			echo json_encode($arr);
		} else {
			echo json_encode([]);
		}
	}
	function updateItem(){
		$item = json_decode(Input::get('item'));
		$item_post = new Item_post();

		$item_post->update(
			['qty' => $item->qty, 'remarks' => $item->remarks,'item_id' =>  $item->item_id,'price_adjustment' => $item->price_adjustment]
			,$item->id);

		echo "Updated successfully";

	}
	function deleteItem(){
		$item = json_decode(Input::get('item'));
		$item_post = new Item_post();
		$item_post->update(['is_active' => 0],$item->id);

		echo "Deleted successfully";

	}
	function deletePicture(){
		// update all to zero
		// mark current as one
		$att = json_decode(Input::get('att'));
		$ref_table = $att->ref_table;
		$ref_id = $att->ref_id;
		$id = $att->id;
		$upload = new Upload();
		$upload->deletePicture($id);

		echo "Deleted successfully.";
	}
	function markAsMainPicture(){
		// update all to zero
		// mark current as one
		$att = json_decode(Input::get('att'));
		$ref_table = $att->ref_table;
		$ref_id = $att->ref_id;
		$id = $att->id;
		$upload = new Upload();
		$upload->updateIsMain($ref_table,$ref_id);
		$upload->markAsMain($id);
		echo "Record updated successfully.";
	}

	function upload(){
		if (!empty($_FILES)) {

			$tempFile = $_FILES['file']['tmp_name'];          //3
			$targetPath = "../uploads/" ;
			$name = "item_post_" . uniqid() ;
			$path = $_FILES['file']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$targetFile =  $targetPath.$name . ".".$ext;  //5
			move_uploaded_file($tempFile,$targetFile); //6

			$fileToThumb = $targetFile;
			$resizedFile = $targetPath ."th_" . $name . ".".$ext;
			$saveResizeFilename = "th_" . $name. ".".$ext;

			$resizedFileSM = $targetPath ."sm_" . $name . ".".$ext;
			$saveResizeFilenameSm = "sm_" . $name. ".".$ext;

			smart_resize_image($fileToThumb , null, 500 , 320 , true , $resizedFile , false , false ,75 );
			smart_resize_image($fileToThumb , null, 60 , 40 , true , $resizedFileSM , false , false ,80 );
			smart_resize_image($targetFile , null, 1420 , 750 , true , $targetFile , true , false ,75 );

			$title =  Input::get('title');
			$desc =  Input::get('description');
			$ref_id =  Input::get('request_id');
			$upcls = new Upload();
			$now = time();
			$user = new User();

			$upcls->create(array(
				'filename' =>$name. "." . $ext,
				'thumbnail' =>$saveResizeFilename,
				'sm_thumbnail' => $saveResizeFilenameSm,
				'ref_table' => 'item_post',
				'ref_id' => $ref_id,
				'company_id' => $user->data()->company_id,
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'tags' => '',
				'title' => $title,
				'description' => $desc
			));
		}
	}
	function updateLabel(){
		$id = Input::get('id');
		$msg = Input::get('label');
		if($id && is_numeric($id)){
			$item_posting = new Item_post();
			$item_posting->update([
				'ribbon_label' => $msg
			],$id);
			echo "Updated successfully.";
		}
	}
	function getRecord(){
		$item_post = new Item_post();
		$status = Input::get('status');
		$item_posts = $item_post->getRecord($status);
		if($item_posts){
			$arr = [];
			foreach($item_posts as $item){
				$item->dateStr = date('m/d/Y H:i:s A',$item->created);
				$item->updating = false;
				$arr[] = $item;
			}
			echo json_encode($arr);
		} else {
			echo json_encode([]);
		}
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