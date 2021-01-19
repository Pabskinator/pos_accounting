<?php
	include 'ajax_connection.php';
	$curData = new Data();
	$data_mon = Input::get('mon_id');
	$moncls = new Monitoring($data_mon);
	$data_items = $curData->getData($data_mon);
	$datalabels = $curData->getLabels($moncls->data()->process_id);

	$userlabels = [];
	$user = new User();
?>
<h3>Details</h3>
<div class="panel panel-success">
		  <!-- Default panel contents -->
		  <div class="panel-heading">Details</div>
	<?php if($moncls->data()->current_step == -1){ ?>
			<form id='formReturn' action="" method='POST'>
				<input type="hidden" value='<?php echo $data_mon; ?>' name='hid_id'>
				<input type="hidden" value='<?php echo $moncls->data()->from_step; ?>' name='from_step'>
		<?php } ?>
	<table class='table'>


		<?php
			// get DATA
			$foResubmit = false;

			if($moncls->data()->current_step == -1 && $moncls->data()->from_cancel == 0 && $user->data()->id == $moncls->data()->who_request ){
				$foResubmit = true;
			}
			foreach($data_items as $di){
					$content = '';

				if($foResubmit){
					if($di->element_name == 'text'){
						$content = "<input class='form-control'  name='$di->id' value='$di->content' />";
					} else if($di->element_name ==  'textarea'){
						$content = "<textarea class='form-control'  name='$di->id' >$di->content</textarea>";
					} else if($di->element_name== 'radio'){
						$exploded = explode(',',$di->choices);

						foreach($exploded as $rd){
							$selected = "";
							if($rd == $di->content){
								$selected = 'checked';
							}
							$content.= " <input $selected type='radio'  name='$di->id' value='$rd'>" .$rd;
						}
					} else if($di->element_name== 'select'){
						$selectelem = "<select class='form-control' name ='$di->id'>";
						$exploded= explode(',',$di->choices);
						foreach($exploded as $rd){
							$selected = "";
							if($rd == $di->content){
								$selected = 'selected';
							}
							$selectelem .= "<option value='$rd' $selected>$rd</option>";
						}
						$selectelem .= "</select>";
						$content = $selectelem;
					}
				} else {
					$content = $di->content;
				}
				if($datalabels){
					foreach($datalabels as $label){
						if(in_array($label->label,$userlabels)) continue;
						if($label->order < $di->order ){
							$userlabels[] = $label->label;
							?>
							<tr><td class='text-danger'><strong><?php echo $label->label; ?></strong></td><td></td></tr>

							<?php
						}
					}
				}

				?>
				<tr><td class=''><strong><?php echo $di->label; ?></strong></td><td ><?php echo $content; ?></td></tr>
			<?php
			}
		?>
	</table>
	<?php if($foResubmit){
		?>
		<hr>
		<div class='text-right'>
			<button class='btn btn-default' id='btnReSubmit'>Re-Submit</button>
		</div>
		</form>
		<?php
	}?>
	</div>
	</div>
<h3>Approval Log</h3>
<div class="panel panel-success">
		  <!-- Default panel contents -->
		  <div class="panel-heading">Details</div>

		  <!-- Table -->
		  <table class="table">
			<?php 
				// get user process log

				$user_app = new User_approval();
				$app_log = $user_app->getLog($data_mon);
				if($app_log){
					?>
					<tr><th>Step</th><th>Processed by</th><th>Date</th><th>Remarks</th></tr>
					<?php
					foreach ($app_log as $value) {
						$pdate = date('m/d/Y H:i:s A',$value->created);
						?>
						<tr>
							<td><?php echo $value->name ?></td>
							<td><?php echo ucwords($value->lastname . ', ' . $value->firstname . ' ' . $value->middlename); ?></td>
							<td><?php echo $pdate ?></td>
							<td><?php echo $value->remarks ?></td>
						</tr>
						<?php
					}
					?>
					
					<?php
				} else {
					?>
					<tr><td><p>No Approval Log</p></td>	</tr>
					<?php
				}
			?>
			   
		  </table>

		</div>
	</div>