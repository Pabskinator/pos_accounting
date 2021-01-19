<?php
	include 'ajax_connection.php';
	$functionName = Input::get("functionName");
	$functionName();
	function getAffiliates(){
		$search = Input::get('search');
		$user = new User();
		$aff = new Affiliate();
		$cid = $user->data()->company_id

		?>

			<?php
				//$targetpage = "paging.php";
				$limit = 50;
				$countRecord = $aff->countRecord($cid,$search);


				$total_pages = $countRecord->cnt;

				$stages = 3;
				$page = Input::get('p');
				$page = (int)$page;
				if($page) {
					$start = ($page - 1) * $limit;
				} else {
					$start = 0;
				}

				$data = $aff->get_record($cid, $start, $limit,$search);

				getpagenavigation($page, $total_pages, $limit, $stages);

				if($data) {
					?>
					<div id="no-more-tables">


					<table class='table' id='tblAffiliates'>
					<thead>
					<tr>
						<th>Name</th>
						<th>Security Code</th>
						<th>Wallet</th>
						<TH>Description</TH>
						<TH>Street #/Lot #</TH>
						<TH>Brgy</TH>
						<TH>City</TH>
						<TH>Province</TH>
						<TH>Region</TH>
						<TH>Lat Long</TH>
						<th></th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($data as $d) {
						?>
						<tr>
							<td class='border=' data-title='Name'>
								<strong><?php echo escape($d->name); ?></strong>

							</td>
							<td data-title='Security Code'>
								<strong class='text-danger'><?php echo escape($d->security_code); ?></strong>
							</td>
							<td data-title='Wallet'>
								<?php echo number_format($d->current_wallet,3); ?>
							</td>
							<td data-title='Description'>
								<?php echo escape($d->description); ?>
								<?php echo "<a target='_blank' href='http://" . $_SERVER['HTTP_HOST'] . "/secure/affiliate.php?affiliate=" . Encryption::encrypt_decrypt('encrypt',$d->id)."'><small class='text-danger span-block'>http://" . $_SERVER['HTTP_HOST'] . "/secure/affiliate.php?affiliate=" . Encryption::encrypt_decrypt('encrypt',$d->id) . "</small></a>"; ?>
							</td>
							<td data-title='Street'>
								<?php echo escape($d->street_no); ?>
							</td>
							<td data-title='Brgy'>
								<?php echo escape($d->brgy); ?>
							</td>
							<td data-title='City'>
								<?php echo escape($d->city); ?>
							</td>
							<td data-title='Province'>
								<?php echo escape($d->province); ?>
							</td>
							<td data-title='Region'>
								<?php echo escape($d->region); ?>
							</td>
							<td data-title='Lat Long'>
								<?php echo escape($d->lat_long); ?>
							</td>
							<td data-title='Active'>
								<a class='btn btn-primary btn-sm btn-margin' href='addaffiliate.php?edit=<?php echo Encryption::encrypt_decrypt('encrypt', $d->id); ?>' title='Edit Affiliate'><span class='glyphicon glyphicon-pencil'></span></a>
								<a href='#' class='btn btn-primary btn-sm deleteAffiliate btn-margin' id="<?php echo Encryption::encrypt_decrypt('encrypt', $d->id); ?>" title='Delete Affiliate'><span class='glyphicon glyphicon-remove'></span></a>
								<a href='#' class='btn btn-primary btn-sm generateNewCode btn-margin' id="<?php echo Encryption::encrypt_decrypt('encrypt', $d->id); ?>" title='Generate New Code'><i class='fa fa-lock'></i></a>

							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
					</table>
					<?php
				} else {
					?>
				<div class='alert alert-info'>No data found</div>
					<?php
				}
			?>

		<?php
	}
	function generateNewCode(){
		$arr = [];
		$id = Input::get('id');
		$id = Encryption::encrypt_decrypt('decrypt',$id);
		$aff = new Affiliate();
		$digits = 4;
		$auto_generated_security_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
		$arr['title'] = $auto_generated_security_code;
		$arr['message'] = "New Code";
		$aff->update(['security_code' => $auto_generated_security_code],$id);
		mail("iloveprogramming17@gmail.com","New security codes","Your new security codes is " . $auto_generated_security_code);
		echo json_encode($arr);
	}