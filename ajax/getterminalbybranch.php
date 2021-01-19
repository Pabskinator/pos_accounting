<?php
	include 'ajax_connection.php';
	$tbid = $_POST['branch_id'];

	$edit = $_POST['editmoko'];
	if(strpos($edit,",")){
	$termlist = explode(',',$edit);
	} else {
		$termlist=$edit;
	}
	$productTerminal = new Terminal();

	$prodTerminals = $productTerminal->get_active('terminals', array('branch_id', '=', $tbid));

	if($prodTerminals){
		foreach($prodTerminals as $pt):
			?>
			<div class="col-md-3">
				<label class="checkbox-inline" for="t<?php echo $pt->id; ?>">
					<input class='termcheckbox' name="terms[]" id="t<?php echo $pt->id; ?>" value="t<?php echo $pt->id; ?>" type="checkbox"
						<?php
						//selected checking here
							if(isset($edit) && !empty($edit)){
							if(is_array($termlist)) {
								if(in_array($pt->id, $termlist)) {
									echo ' checked ';
								}
							} else {
								if($termlist == $pt->id){
									echo ' checked ';
								}
							}
							}
						?>
						>
					<span><?php echo $pt->name; ?></span>
				</label>
			</div>
		<?php
		endforeach;
	} else {
		?>
		<div class="alert alert-info">No Terminals Yet</div>
	<?php
	}
?>