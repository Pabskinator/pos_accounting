<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	ini_set('memory_limit', '-1');
	require_once '../includes/admin/page_head2.php';
	/**
	 * easy image resize function
	 * @param  $file - file name to resize
	 * @param  $string - The image data, as a string
	 * @param  $width - new image width
	 * @param  $height - new image height
	 * @param  $proportional - keep image proportional, default is no
	 * @param  $output - name of the new file (include path if needed)
	 * @param  $delete_original - if true the original image will be deleted
	 * @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
	 * @param  $quality - enter 1-100 (100 is best quality) default is 100
	 * @return boolean|resource
	 */
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

	if(!Input::get('r') || !Input::get('id') || !Input::get('p')){
		Redirect::to(1);
	}


	$appendurl = "id=".Input::get('id')."&r=".Input::get('r') . "&p=".Input::get('p');
	$allowedreftable = array('members','items','stations'); // allowed to use this page
	$ref_table = Encryption::encrypt_decrypt('decrypt', Input::get('r'));
	$ref_id = Encryption::encrypt_decrypt('decrypt', Input::get('id'));

	if(!$ref_id) Redirect::to(1);
	if(!in_array($ref_table,$allowedreftable))Redirect::to(1);

	$prevpage = Encryption::encrypt_decrypt('decrypt', Input::get('p'));

	$cls = new Upload();
	//$uploadedImages = $cls->getAllImage($user->data()->company_id,$ref_table,$ref_id);
	$alltags = $cls->getAllTags($user->data()->company_id,$ref_table);
	$arrageTags = [];
	foreach($alltags as $itags){
		if(!$itags->tags) continue;

		$t = str_replace('    ','_', $itags->tags);
		$t = str_replace('   ','_', $itags->tags);
		$t = str_replace('  ','_', $itags->tags);
		$t = str_replace(' ','_', $itags->tags);
		$t = str_replace("'",'', $itags->tags);
		$t = str_replace('"','', $itags->tags);
		$t = str_replace('&','', $itags->tags);
		$t = str_replace('/','', $itags->tags);
		$t = str_replace('\\','', $itags->tags);
		$t = trim($t);

		$arrageTags[] = $t;
	}

?>
<?php
	if (isset($_POST['submit'])) {

		$j = 0;     // Variable for indexing uploaded image.
		$target_path = "../uploads/";     // Declaring Path for uploaded images.
		$upcls = new Upload();
		$now = time();
		$retmsg = "";
		for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
			// Loop to get individual element from the array
			$validextensions = array("jpeg", "jpg", "png","bmp");      // Extensions which are allowed.
			$ext = explode('.', basename($_FILES['file']['name'][$i]));   // Explode file name from dot(.)
			$file_extension = end($ext); // Store extensions in the variable.
			$filename = $ref_table ."-$ref_id-$j" .uniqid(). ".".$ext[count($ext) - 1];
			$path = $target_path .$filename ;     // Set the target path with a new name of image.
			$j = $j + 1;      // Increment the number of uploaded images according to the files in array.
			if (($_FILES["file"]["size"][$i] < 10000000)
				&& in_array($file_extension, $validextensions)) {
				if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $path)) {
					// If file moved to uploads folder.
					$fileToThumb = $path;
					//indicate the path and name for the new resized file
					$resizedFile = $target_path ."th_" . $filename;
					$saveResizeFilename = "th_" . $filename;
					smart_resize_image($fileToThumb , null, 500 , 500 , true , $resizedFile , false , false ,85 );

					$tag_name = Input::get('tag_name');
					$file_title = $_POST['file_title'][$i];
					$file_description = $_POST['file_description'][$i];

					if(!$file_description) $file_description ='';
					if(!$file_title) $file_title='';

					if(!$tag_name) $tag_name = "Uncategorized";
					$tag_name = trim(strtolower($tag_name));
					$upcls->create(array(
						'filename' =>$filename,
						'thumbnail' =>$saveResizeFilename,
						'ref_table' => $ref_table,
						'ref_id' => $ref_id,
						'company_id' => $user->data()->company_id,
						'is_active' => 1,
						'created' => $now,
						'modified' => $now,
						'tags' => $tag_name,
						'title' => $file_title,
						'description' => $file_description
					));
					$retmsg .= "<div class='alert alert-info'" . $j. " ".'<span id="noerror">Image uploaded successfully!.</span></div>';
				} else {     //  If File Was Not Moved.
					$retmsg .=  "<div class='alert alert-danger'>" . $j. " ".'<span  id="noerror">please try again!.</span></div>';
				}
			} else {     //   If File Size And File Type Was Incorrect.
				$retmsg .= "<div class='alert alert-danger'>" . $j. " ".'<span  id="noerror">Invalid  File/File Size/Type</span></div>';
			}
		}
		Session::flash('flash',$retmsg);
		Redirect::to('upload.php?'.$appendurl);
	}
	if(isset($_POST['btnDelete'])){

		$delcls = new Upload();
		$delfile = Input::get('delfilename');
		$delid =  Input::get('delid');
		$isdel = $delcls->deleteFile($delid);

		if($isdel){
			unlink("../uploads/" .$delfile);
			$retmsg = "<div class='alert alert-info'>File deleted successfully</div>";
		} else {
			$retmsg = "<div class='alert alert-danger'>Failed to delete the file</div>";
		}

		Session::flash('flash',$retmsg);
		Redirect::to('upload.php?'.$appendurl);

	}
?>

	<link rel="stylesheet" href="../css/dropzone2.css">
	<link rel="stylesheet" href="../css/swipebox.css">
	<link rel="stylesheet" href="../css/viewer.min.css">



	<!-- Page content -->
	<div id="page-content-wrapper">
		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<div class="col-md-6">
					<h1>
						<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
						Images
					</h1>
				</div>

			</div>
			<br>
			<?php
				if(Configuration::thisCompany('calayan')){
					?>
					<div class="alert alert-danger">
						<i class='fa fa-warning'></i> Please avoid uploading sensitive images. If it cannot be avoided, crop the image or blur the face. Thank you.
					</div>
					<br>
			<?php
				}
			?>


			<div class="container-fluid">
				<input type="hidden" value='<?php echo $ref_table; ?>' id='hid_ref_table'>
				<input type="hidden" value='<?php echo $ref_id; ?>' id='hid_member_id'>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" id="upload_tags" class='form-control' placeholder='Enter Tags'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" id="upload_title" class='form-control' placeholder='Enter Title'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" id="upload_description" class='form-control' placeholder='Enter Description'>
						</div>
					</div>
					</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<input type="file" class='form-control' name='requestAttachment' id='requestAttachment'>
							<span class='help-block'>Upload image</span>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<button class='btn btn-success' id='btnUpload'><span>Upload</span></button>
						</div>
					</div>
				</div>


				<!-- hide carousel	-->
				<?php
					// get flash message if add or edited successfully
					if(Session::exists('flash')) {
						echo Session::flash('flash');
					}
				?>
				<?php
					//		dump(Input::get('tag_name'));
					/*
					<div class="panel panel-default">
						<div class="panel-body">
							<?php

								if($uploadedImages){
									?>

									<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
										<!--Indicators -->

										<ol class="carousel-indicators">
											<?php
												$isfirst = true;
												$io = 0;
												foreach($uploadedImages as $indimg){
													$active ='';
													if($isfirst){
														$active = 'active';
														$isfirst= false;
													}
													?>
													<li data-target="#carousel-example-generic" data-slide-to="<?php echo $io ?>" class="<?php echo $active; ?>">
													</li>
													<?php
													$io += 1;
												}
											?>
										</ol>
										<!-- Wrapper for slides -->
										<div class="carousel-inner" role="listbox">
											<?php

												$isfirst = true;
												foreach($uploadedImages as $indimg){
													$active ='';
													if($isfirst){
														$active = 'active';
														$isfirst= false;
													}
													?>
													<div  style="height: 400px;"  class="item <?php echo $active; ?>">

														<img src="../uploads/<?php echo $indimg->filename ?>"  style="height:100%;"  alt="<?php echo $indimg->filename ?>">
														<div class="carousel-caption">
															<?php echo $indimg->filename ?>
														</div>
													</div>
												<?php
												}
											?>

										</div>

										<!-- Controls -->
										<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
											<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
											<span class="sr-only">Previous</span>
										</a>
										<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
											<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
											<span class="sr-only">Next</span>
										</a>
									</div>
								<?php
								} else {
									?>
									<div class="alert alert-info">No image yet</div>
								<?php
								}
							?>
						</div>
					</div>
				 */
				?>

				<div class="panel panel-default">
					<div class="panel-body" id='img-container'>

					</div>
				</div>


				<div id="maindiv" style='display:none;'>
					<div id="formdiv" >
						<h3>Upload Images</h3>
						<form enctype="multipart/form-data" action="" method="post">
							<div class="form-group">
								<input type="hidden" class='form-control' placeholder='Category' name='tag_name' id='tag_name'>
								<span class='help-block'>Select Existing Category or type a new one.</span>
							</div>
							<div id="filediv" style='margin-bottom: 5px;'>
								<input class='form-control'   name="file[]" type="file" id="file"/>
								<input type="text" class='form-control' name="file_title[]"  placeholder="Title" id="file_title" style='margin-top:10px;'/>
								<input type="text" class='form-control'  name="file_description[]"  placeholder="Description"  id="file_description" style='margin-top:10px;'/>
							</div>
							<input type="button"  id="add_more" class="btn btn-default upload" value="Add More Files"/>
							<input type="submit"  value="Upload File" name="submit" id="upload" class="btn btn-default upload"/>


						</form>
						<br><br>
					</div>
				</div>
			</div>
		</div>

	</div> <!-- end page content wrapper-->

	<script src='../js/viewer.min.js'></script>
	<script src='../js/dropzone2.js'></script>
	<script src='../js/img-compress.js'></script>
	<script>
		$(document).ready(function() {
			$(document).bind("contextmenu",function(e){
				return false;
			});
			$(window).on('keydown',function(event)
			{
				if(event.keyCode==123)
				{
					alert('Entered F12');
					return false;
				}
				else if(event.ctrlKey && event.shiftKey && event.keyCode==73)
				{
					alert('Entered ctrl+shift+i');
					return false;  //Prevent from ctrl+shift+i
				}
				else if(event.ctrlKey && event.keyCode==73)
				{
					alert('Entered ctrl+shift+i');
					return false;  //Prevent from ctrl+shift+i
				}
			});

		/*
			var myDropzone = new Dropzone("#dropzone-form", {
					url: "../ajax/ajax_upload.php?functionName=upload",
					acceptedFiles: "image/*"
				}
			);
		*/

			$('body').on('keyup','#upload_tags,#upload_title,#upload_description',function(){

			});

			$('body').on('click','.gotoslide',function(e){
				e.preventDefault();
				var s = $(this).attr('data-slide_no');
				goToSlide(s);
			});
			$('body').on('click','#btnUpload',function(e){
				e.preventDefault();
				var fd = new FormData();
				var tags = $('#upload_tags').val();
				var title = $('#upload_title').val();
				var desc = $('#upload_description').val();
				tags = (tags) ? tags : 'No tag';
				title = (title) ? title : 'No title';
				desc = (desc) ? desc : 'No description';
				var con = $(this);
				button_action.start_loading(con);

				var file_data = $('input[name=requestAttachment]')[0].files[0];
				/*test start*/


					new ImageCompressor(file_data, {
						quality: .8,
						maxHeight:1000,
						success(result) {
							try{
								fd.append('file',result,result.name);

								fd.append('functionName','upload');
								fd.append('member_id', $('#hid_member_id').val());
								fd.append('tbl', $('#hid_ref_table').val());
								fd.append('title',title);
								fd.append('tags', tags);
								fd.append('description',desc);
								$.ajax({
									url: '../ajax/ajax_upload.php',
									type: 'POST',
									contentType: false,
									processData: false,
									data: fd,
									success: function(data) {
										getImages();
										$('#requestAttachment').val('');
										button_action.end_loading(con);
									},
									error: function() {
										console.log('Submit item');
									}
								})
							} catch(e){
								console.log("Error compressing image")
							}



					},
					error(e) {
						console.log(e.message);
					},
				});
				/* test end */



			});

			function goToSlide(number) {
				$("#carousel-example-generic").carousel(number);
			}
			$('body').on('click','#showUpload',function(){
				$('#right-pane-container').html($('#maindiv').html());
				$('.right-panel-pane').fadeIn(100,function(){
					$("#tag_name").select2({
						tags: tags,
						maximumSelectionSize : 1
					});
				});
			});

			getImages();

			function getImages(){
				var member_id = $('#hid_member_id').val();
				var tbl =  $('#hid_ref_table').val();
				$.ajax({
				    url:'../ajax/ajax_upload.php',
				    type:'POST',
				    data: {functionName:'getImages',member_id:member_id,tbl:tbl},
				    success: function(data){
				        $('#img-container').html(data);
					    var tags = $('#hid_tags').val();

					    try{
						    tags = JSON.parse(tags);
						    console.log(tags);
						    for(var i in tags){
							    $('.'+tags[i]).viewer({url: 'data-orig-src'});
						    }
					    } catch(e){
						    tags =[];
						    console.log("Error in viewing image.")
					    }
				    },
				    error:function(){
				        
				    }
				})
			}
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>