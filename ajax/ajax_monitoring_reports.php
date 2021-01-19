<?php
	include 'ajax_connection.php';

	$function = Input::get('functionName');
	
	$function();

	function getSteps(){
		$process_id = Input::get('process_id');
		$mstep = new Steps();
		$steps = $mstep->getMyStep($process_id);
		if($steps){
			?>
			<select id='step_id' class='form-control'>
				<option></option>
				<option value='-2'>Approved</option>
				<option value='-1'>Decline</option>

				<?php 
					foreach ($steps as $value) {
						?>
						<option value='<?php echo $value->id; ?>'><?php echo $value->name; ?></option>
						<?php
					}
				?>
			</select>
			<?php
		} else {
			?>
			<p>No Steps for that process.</p>
			<?php
		}
	}
?>