<?php
	include 'ajax_connection.php';
	/*
	 * get the function name and run it
	 */
	$functionName = Input::get('functionName');
	$params = Input::get('page');
	$cid = Input::get('cid');
	$functionName($params,$cid);

	function monitoringPaginate($args,$cid){
			$user = new User();
			$mon = new Monitoring();
			$process_id = Input::get('process_id');
			$step_id = Input::get('step_id');
		?>

		<table class="table">
					<tr>
						<th>Request Id</th>
						<th>Date Created</th>
						<th>Who Requested</th>
						 <th>Attachment</th> 
						<th>Details</th>
					</tr>
	
		<?php
		$limit = 20;
		$countRecord = $mon->countRecord($cid,$process_id,$step_id);
		$total_pages =$countRecord->cnt;
	
		$stages = 3;
		$page = $args;
		$page = (int) $page;
		if($page){
			$start = ($page - 1) * $limit;
		}else{
			$start = 0;
		}

			$company_mon = $mon->get_mon_record($cid,$start,$limit,$process_id,$step_id);
		getpagenavigation($page,$total_pages,$limit,$stages);
		if($company_mon) {
					
					
						foreach ($company_mon as $value) {
							$request_user = new User($value->who_request);
							?>
							<tr>
								<td><span class='badge'><?php echo $value->id; ?></span></td>
								<td><?php echo date('m/d/Y h:i:s A',$value->created); ?></td>
								<td><?php echo ucwords($request_user->data()->lastname . ", " .$request_user->data()->firstname . " " . $request_user->data()->middlename); ?></td>
								<?php 
									
										
										
								?>
								<td>
								<?php 
								
									$att = new Attachment();
									$req_attach = $att->getAttachments($value->id,$step_id);
									if($req_attach){
								
										foreach ($req_attach as $at) {
											?>
											<a style='margin:3px;' class='btn btn-default' href="attachments/<?php echo $at->filename; ?>" target='_blank'>
												<span class='glyphicon glyphicon-paperclip'></span> 
												<?php echo substr($at->filename,17); ?>
											</a> <br/>
											<?php
										}
									}
									
								?>
								</td>
						
								<td>

									<button type='button' data-mon-id='<?php echo  $value->id; ?>'  class='btn btn-default showData'>
									<span class='glyphicon glyphicon-list-alt'></span> Show Details
									</button>
									
								</td>
							</tr>
						<?php
						}
					} else {
						?>
						<tr> <td colspan='4'>No record found</td></tr>
						<?php
					}
					?>
					</table>
				<?php
	}






	// GETTING PAGE NAVIGATION

		function getpagenavigation($page,$total_pages,$limit,$stages){
		if ($page == 0){$page = 1;}
		$prev = $page - 1;
		$next = $page + 1;
		$lastpage = ceil($total_pages/$limit);
		$LastPagem1 = $lastpage - 1;


		$paginate = '';
		if($lastpage > 1)
		{

			$paginate .= "<ul class='pagination' style='float:right'>";

			if ($page > 1){
				$paginate.= "<li><a href='#'  class='paging' page='$prev' style='padding:5px'>PREVIOUS</a></li>";
			}else{
				$paginate.= "<li class='disabled'><span class='disabled' style='padding:5px'>PREVIOUS</span></li>"; }




			if ($lastpage < 7 + ($stages * 2))
			{
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page){
						$paginate.= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
					}else{
						$paginate.= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";}
				}
			}
			elseif($lastpage > 5 + ($stages * 2))
			{

				if($page < 1 + ($stages * 2))
				{
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
					{
						if ($counter == $page){
							$paginate.= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						}else{
							$paginate.= "<li><a href='#'  class='paging' page='$counter' style='padding:5px'>$counter</a></li>";}
					}
					$paginate.= "<li><a>...</a></li>";
					$paginate.= "<li><a href='#'   class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate.= "<li><a href='#' class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				}

				elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2))
				{
					$paginate.= "<li><a href='#' class='paging' page='1'  style='padding:5px'>1</a></li>";
					$paginate.= "<li><a href='#' class='paging' page='2'  style='padding:5px'>2</a></li>";
					$paginate.= "<li><a>...</a></li>";
					for ($counter = $page - $stages; $counter <= $page + $stages; $counter++)
					{
						if ($counter == $page){
							$paginate.= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						}else{
							$paginate.= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";}
					}
					$paginate.= "<li><a>...</a></li>";
					$paginate.= "<li><a href='#' class='paging' page='$LastPagem1' style='padding:5px'>$LastPagem1</a></li>";
					$paginate.= "<li><a  href='#'  class='paging' page='$lastpage' style='padding:5px'>$lastpage</a></li>";
				}

				else
				{
					$paginate.= "<li><a href='#' class='paging' page='1' style='padding:5px'>1</a></li>";
					$paginate.= "<li><a href='#' class='paging' page='2' style='padding:5px'>2</a></li>";
					$paginate.= "<li><a>...</a></li>";
					for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page){
							$paginate.= "<li class='active'><span class='current' style='padding:5px'>$counter</span></li>";
						}else{
							$paginate.= "<li><a href='#' class='paging' page='$counter'  style='padding:5px'>$counter</a></li>";}
					}
				}
			}


			if ($page < $counter - 1){
				$paginate.= "<li><a href='#' class='paging' page='$next' style='padding:5px'>NEXT</a></li>";
			}else{
				$paginate.= "<li class='disabled'><span class='disabled' style='padding:5px'>NEXT</span></li>";
			}

			$paginate.= "</ul><div style='clear: both;'></div>";


		}
		// echo $total_pages.' Results';
		echo $paginate;
	}