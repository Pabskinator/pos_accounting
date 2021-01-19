<?php
	include 'ajax_connection.php';
	$tcid = $_POST['company_id'];

	$edit = $_POST['editmoko'];
	if(strpos($edit,",")){
		$displaylist = explode(',',$edit);
	} else {
		$displaylist=$edit;
	}
	$displaycls = new Display_location();

	$displaylocations = $displaycls->get_active('display_location', array('company_id', '=', $tcid));

	if($displaylocations){
		foreach($displaylocations as $d):
			?>
			<div class="col-md-3">
				<label class="checkbox-inline" for="dis<?php echo $d->id; ?>">
					<input class='displaycheckbox' name="chkdis[]" id="dis<?php echo $d->id; ?>" value="dis<?php echo $d->id; ?>" type="checkbox"
						<?php
							//selected checking here
							if(isset($edit) && !empty($edit)){
								if(is_array($displaylist)) {
									if(in_array($d->id, $displaylist)) {
										echo ' checked ';
									}
								} else {
									if($displaylist == $d->id){
										echo ' checked ';
									}
								}
							}
						?>
						>
					<span><?php echo $d->name; ?></span>
				</label>
			</div>
		<?php
		endforeach;
	} else {
		?>
		<div class="alert alert-info">No Display Location</div>
	<?php
	}
?>