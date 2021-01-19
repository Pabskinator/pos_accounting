<?php
	include 'ajax_connection.php';
	$oid = Input::get('oid');
	$mon = new Reorder_monitoring();
	$mons = $mon->get_active('reorder_monitoring',array('reorder_id' ,'=',$oid));

	if($mons){
		?>
		<table class="table">
			<tr><th>Date</th><th>Status</th><th>User</th></tr>
			<?php
				foreach($mons as $m){
					$whoapp = new User($m->user_id);
					?>
					<tr>
						<td><?php echo date('m/d/Y H:i:s',$m->date_processed) ?></td>
						<td><?php echo $m->remarks; ?> </td>
						<td><?php echo ucwords($whoapp->data()->lastname . ", " .$whoapp->data()->firstname . " " . $whoapp->data()->middlename) ; ?> </td>
					</tr>
					<?php
				}
			?>
		</table>
	<?php
	} else {
		echo "<div class='alert alert-info'>No Record</div>";
	}
?>