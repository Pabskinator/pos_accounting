
<!-- Page content -->
<div id="page-content-wrapper">


	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Account Titles
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
				<?php
					if(	$user->hasPermission('acc_m')) {
						?>

						<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
							<a href='add-account-title.php' class='btn btn-default' title='Add Account title'>
								<span class='glyphicon glyphicon-plus'></span>
								<span class='hidden-xs'>Add Account Title</span>
							</a></div>
					<?php } ?>

				<?php
					if ($account_titles){
				?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Account Title</div>
					<div class="panel-body">
						<div id="no-more-tables">
							<table class='table' id='tblCategories'>
								<thead>
								<tr>
									<TH>Name</TH>
									<TH class='text-center'>Created</TH>
									<?php
										if($user->hasPermission('acc_m')) {
											?>
											<TH>Actions</TH>
										<?php } ?>
								</tr>
								</thead>
								<tbody>
								<?php
									function objectToArray ($object) {
										if(!is_object($object) && !is_array($object))
											return $object;
										return array_map('objectToArray', (array) $object);
									}
									$ccc = new Account_title();
									$cc = objectToArray($ccc->getAcc($user->data()->company_id));
									$array = array();


									function get_nested($array,$child = FALSE,$iischild=''){
										$user = new User();
										$str = '';
										$iischildOpen ='';
										$iischildClose='';
										if (count($array)){
											if($child == FALSE){
												$iischild = 0;
											} else {

												$iischild += 60;
											}

											$iischildOpen = $child == FALSE ? '<div class="">' : '<div class="" style="margin-left:'.$iischild.'px;"><span class=""></span> ';
											$iischildClose = $child == FALSE ? '</div>' : ' </div>';
											foreach ($array as $item){

												$cate = new Account_title($item['id']);
												if(isset($item['children']) && count($item['children'])){

													$str .= '<tr><td class="text-danger" data-title="Name"><strong> '.$iischildOpen.ucfirst($item['name']).$iischildClose.'</strong></td><td data-title="Date Created" class="text-center">'.date('m/d/Y H:i:s A' ,$cate->data()->created).'</td>';
													if($user->hasPermission('acc_m')) {
														$str .= '<td  data-title="Action">';
														$str .= "<a class='btn btn-primary' href='add-account-title.php?edit=" . Encryption::encrypt_decrypt('encrypt', $item['id']) . "' title='Edit Account Title'><span class='glyphicon glyphicon-pencil' ></span></a>
											<a href='#' class='btn btn-primary deleteAccountTitle' id='" . Encryption::encrypt_decrypt('encrypt', $item['id']) . "' title='Delete Account Title'><span class='glyphicon glyphicon-remove'></span></a>";
														$str .= '</td>';
													}
													$str .= '</tr>';
													$str .= get_nested($item['children'], true, $iischild);
												} else {
													if($child == false) $iischild='';
													$str .= '<tr><td class="text-danger" data-title="Name"><strong>'.$iischildOpen.ucfirst($item['name']).$iischildClose.'</strong></td><td data-title="Date Created" class="text-center">'.date('m/d/Y H:i:s A' ,$cate->data()->created).'</td>';
													if($user->hasPermission('acc_m')) {
														$str .= '<td data-title="Action">';
														$str .= "<a class='btn btn-primary' href='add-account-title.php?edit=" . Encryption::encrypt_decrypt('encrypt', $item['id']) . "' title='Edit Account Title'><span class='glyphicon glyphicon-pencil'></span></a>
											<a href='#' class='btn btn-primary deleteAccountTitle' id='" . Encryption::encrypt_decrypt('encrypt', $item['id']) . "' title='Delete Account Title'><span class='glyphicon glyphicon-remove'></span></a>";
														$str .= '</td>';
													}
													$str .='</tr>';
												}

											}
										}

										return $str;
									}


									function makeRecursive($d, $r = 0, $pk = 'parent_id', $k = 'id', $c = 'children') {
										$m = array();
										foreach ($d as $e) {
											isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
											isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
											$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
										}

										return $m[$r]; // remove [0] if there could be more than one root nodes
									}

								?>
								<?php echo get_nested(makeRecursive($cc)); ?>
								</tbody>
							</table>
						</div>
					</div>
					<?php
						} else {
						?>
						<div class='alert alert-info'>There is no current item at the moment.</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$(".deleteAccountTitle").click(function(){
				if(confirm("Are you sure you want to delete this record?")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'account_titles'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});

		});


	</script>