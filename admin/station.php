<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('station')) {
		// redirect to denied page
		Redirect::to(1);
	}



	$cf = new Custom_field();
	$cfd = new Custom_field_details();

	$getstationdet = $cf->getcustomform('stations',$user->data()->company_id);

	$label_name = isset($getstationdet->label_name)? ($getstationdet->label_name):'Station';
	$label_name = ucwords(strtolower($label_name));
	$description = $cfd->getIndData('description',$user->data()->company_id);
	$region = $cfd->getIndData('region',$user->data()->company_id);
	$brand = $cfd->getIndData('brand',$user->data()->company_id);
	$package = $cfd->getIndData('package',$user->data()->company_id);
	$otherfield = isset($getstationdet->other_field)?$getstationdet->other_field:'';

	if(($description)){
		if($description->field_label){
			$desc_label = $description->field_label;
		}
		if($description->is_visible == 0){
			$desc_visible = 'display:none;';
		}
	}
	if(!$desc_label){
		$desc_label = 'Address';
	}
	if(!$desc_visible){
		$desc_visible = '';
	}

	if(($region)){
		if($region->field_label){
			$region_label = $region->field_label;
		}
		if($region->is_visible == 0){
			$region_visible = 'display:none;';
		}
	}
	if(!$region_label){
		$region_label = 'Region';
	}
	if(!$region_visible){
		$region_visible = '';
	}

	if(($brand)){
		if($brand->field_label){
			$brand_label = $brand->field_label;
		}
		if($brand->is_visible == 0){
			$brand_visible = 'display:none;';
		}else {

		}
	}
	if(!$brand_label){
		$brand_label = 'Brand';
	}
	if(!$brand_visible){
		$brand_visible = '';
	}

	if(($package)){
		if($package->field_label){
			$package_label = $package->field_label;
		}
		if($package->is_visible == 0) {
			$package_visible = 'display:none;';
		}
	}
	if(!$package_label){
		$package_label = 'Package';
	}
	if(!$package_visible){
		$package_visible = '';
	}
?>



	<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Manage <?php echo $label_name; ?> </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('stationflash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('stationflash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<?php include 'includes/station_nav.php'; ?>


				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><?php echo $label_name; ?></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<input type="text" class='form-control' placeholder='SEARCH...' id='searchStation'>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			getPage(0,'');
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var v = $('#searchStation').val();
				getPage(page,v);
			});
			$('#searchStation').keyup(function(){
				var v = $('#searchStation').val();
				getPage(0,v);
			});
			function getPage(p,s){
				$('.loading').show();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,s:s,functionName:'stationList',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='station.php';
						$('.loading').hide();
					}
				});
			}
			$(".deleteStation").click(function() {
				if(confirm("Are you sure you want to delete this record?")) {
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php', {id: id, table: 'stations'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});
				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>