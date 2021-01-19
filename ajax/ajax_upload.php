<?php
	ini_set('memory_limit', '-1');
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();
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

	function upload(){
		$user = new User();
		if(!$user->data()->id){
			die("You don't have access to this information");
		}
		if (!empty($_FILES)) {

			$tempFile = $_FILES['file']['tmp_name'];          //3
			$targetPath = "../uploads/" ;
			$title =  Input::get('title');
			$desc =  Input::get('description');
			$tags =  Input::get('tags');
			$member_id =  Input::get('member_id');
			$table_ref =  Input::get('tbl');

			$name = $table_ref ."-$member_id-" .uniqid() . md5($member_id);
			$path = $_FILES['file']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$targetFile =  $targetPath.$name . ".".$ext;  //5
			move_uploaded_file($tempFile,$targetFile); //6

			$fileToThumb = $targetFile;
			$resizedFile = $targetPath ."th_" . $name . ".".$ext;
			$saveResizeFilename = "th_" . $name. ".".$ext;

			//copy($targetFile,$resizedFile); //6

			//smart_resize_image($fileToThumb , null, 500 , 500 , true , $resizedFile , false , false ,75 );
			//smart_resize_image($targetFile , null, 1420 , 750 , true , $targetFile , true , false ,75 );


			$upcls = new Upload();
			$now = time();


			$upcls->create(array(
				'filename' =>$name. "." . $ext,
				'thumbnail' =>$saveResizeFilename,
				'ref_table' => 'members',
				'ref_id' => $member_id,
				'company_id' => $user->data()->company_id,
				'is_active' => 1,
				'created' => $now,
				'modified' => $now,
				'tags' => $tags,
				'title' => $title,
				'description' => $desc
			));
		}
	}

	function getImages(){
		$user = new User();
		if(!$user->data()->id){
			die("You don't have access to this information");
		}
		$cls = new Upload();
		$ref_id =  Input::get('member_id');
		$ref_table =  Input::get('tbl');
		$uploadedImages = $cls->getAllImage($user->data()->company_id,$ref_table,$ref_id);
							if($uploadedImages){
								$i=0;
								$arrayGroup = [];
								$tags= [];
								foreach($uploadedImages as $im){
									$im->tags = str_replace(' ', '_',$im->tags);
									$im->tags = str_replace('    ','_', $im->tags);
									$im->tags = str_replace('   ','_', $im->tags);
									$im->tags = str_replace('  ','_', $im->tags);
									$im->tags = str_replace(' ','_', $im->tags);
									$im->tags = str_replace("'",'', $im->tags);
									$im->tags = str_replace('"','', $im->tags);
									$im->tags = str_replace('&','', $im->tags);
									$im->tags = str_replace('/','', $im->tags);
									$im->tags = str_replace('\\','', $im->tags);
									$arrayGroup[$im->tags][] = $im;
									if(!in_array($im->tags,$tags)){
										$tags[] =$im->tags;
									}

								}

								foreach($arrayGroup as $each_tags => $contents){
									?>
									<h3><?php echo ucwords(strtolower(str_replace('_',' ',$each_tags))); ?></h3>
									<div class="row">
										<div class='<?php echo $each_tags; ?>'>
											<?php
												foreach($contents as $content){
													if(!trim($content->filename)){
														continue;
													}
													if(!file_exists("../uploads/{$content->filename}")) continue;
													?>
													<div class="col-sm-4 col-md-3">
														<div class="thumbnail"  style="height: 220px;" >
															<a  href="#" class="" title="">
																<?php if(file_exists("../uploads/" . $content->thumbnail)){
																	$thumb = "../uploads/" . $content->thumbnail;
																	} else {
																	$thumb = "../uploads/" . $content->filename;
																}
																?>
																<img class='gotoslide' data-orig-src="../uploads/<?php echo $content->filename ?>" data-slide_no="<?php //echo $i; ?>" src="<?php echo $thumb; ?>" style='height:70%;border:1px solid #ccc;' alt="<?php echo $content->filename ?>"

															</a>
															<div class="caption">
																<p class='text-danger text-center'><strong><?php echo $content->title ?></strong></p>
																<p class='text-muted text-center'><strong><?php echo $content->description ?></strong></p>
																<form style='position:absolute;bottom:25px;right:20px;opacity:0.8;' action="" method="POST" class='text-right'>
																	<input type="hidden" name='delfilename' value='<?php echo $content->filename ?>' />
																	<input type="hidden" name='delid' value='<?php echo $content->id ?>' />
																	<button style='' class='btn btn-danger btn-sm' type='submit' name='btnDelete'><span class='glyphicon glyphicon-remove'></span></button>
																</form>
															</div>
														</div>
													</div>
													<?php
												}
											?>
										</div>
									</div>
									<hr>                                   
									<input type="hidden" id='hid_tags' value='<?php echo json_encode($tags); ?>'>
									
									<?php
								}
							} else {
								echo "<div class='container-fluid'><div class='alert alert-info'>No thumbnail yet...</div></div>";
							}
	}