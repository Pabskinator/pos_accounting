
<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Display Location
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<?php include 'includes/product_nav.php'; ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Display Location</div>
					<div class="panel-body">
						<?php if($display) { ?>
						<div id="no-more-tables">
							<table class='table'>
								<thead>
								<tr>
									<TH>Display Location</TH>
									<TH>Description</TH>
									<TH>Created</TH>
									<?php if($user->hasPermission('display_location_m')){ ?>
										<TH>Actions</TH>
									<?php } ?>
								</tr>
								</thead>
								<tbody>
								<?php

									foreach($display as $d) {
										?>
										<tr>

											<td data-title='Name'><?php echo escape($d->name); ?></td>
											<td data-title='Description'><?php echo escape($d->description); ?></td>
											<td data-title='Created'><?php echo escape(date('m/d/Y H:i:s A', $d->created)); ?></td>
											<?php if($user->hasPermission('display_location_m')){ ?>
												<td>
													<a class='btn btn-primary' href='adddisplay.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $d->id); ?>' title='Edit Display Location'><span class='glyphicon glyphicon-pencil' ></span></a>
													<a href='#' class='btn btn-primary deleteDisplay' id="<?php echo Encryption::encrypt_decrypt('encrypt', $d->id); ?>" title='Delete Display Location'><span class='glyphicon glyphicon-remove'></span></a>
												</td>
											<?php } ?>
										</tr>
										<?php

									}
								?>
								</tbody>
							</table>
						</div>
					</div>
					<?php   }  else { ?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){

		});
	</script>