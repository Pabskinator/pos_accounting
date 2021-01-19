<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$branch_tag = new Branch_tag();
	$branch_tags = $branch_tag->get_active('branch_tags',array('1' ,'=','1'));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Branch Quotas
			</h1>

		</div>
		<?php
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
			<a class='btn btn-default btnCon' data-con='1'  title='Monthly'>
				<span class='glyphicon glyphicon-calendar'></span> <span class='hidden-xs'>Monthly</span></span>
			</a>
			<a class='btn btn-default btnCon' data-con='2'  title='Daily'>
						<span class='fa fa-users'></span> <span class='hidden-xs'>Daily</span></span>
			</a>

		</div>
		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Branch</div>
					<div class="panel-body">


						<div id="con1">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' id='year' placeholder="Enter Year">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnSubmit'>Submit</button>
									</div>
								</div>
							</div>
							<div id="con"></div>
						</div>
						<div id="con2" style='display:none;'>
							<div class="row">
								<div class="col-md-4">
									<button class='btn btn-default btn-sm' id='btnPrev'>Prev</button>
								</div>
								<div class="col-md-4">

								</div>
								<div class="col-md-4 text-right">
									<button  class='btn btn-default btn-sm' id='btnNext'>Next</button>
								</div>
							</div>
							<div id="daily"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	  </div>
	</div> <!-- end page content wrapper-->

	<script>

		$(document).ready(function(){
			var cur_week = 0;
			$('body').on('click','#btnSubmit',function(){
				getForecast();
			});
			$('body').on('click','#btnPrev',function(){
				cur_week--;
				getDailyQuotas();
			});
			$('body').on('click','#btnNext',function(){
				cur_week++;
				getDailyQuotas();

			});

			$('body').on('click','.btnCon',function(){
				var con = $(this);
				var c = con.attr('data-con');
				var con1 = $('#con1');
				var con2 = $('#con2');
				con1.hide();
				con2.hide();
				if(c == 1){
					con1.fadeIn(300);
				} else if ( c == 2){
					con2.fadeIn(300);
					getDailyQuotas();
				}
			});
			getForecast();
			function getForecast(){
				var year = $('#year').val();
				$.ajax({
				    url:'../ajax/ajax_sales_query.php',
				    type:'POST',
				    data: {functionName:'getForecast',year:year},
				    success: function(data){
				        $('#con').html(data);
				    },
				    error:function(){

				    }
				});
			}
			function getDailyQuotas(){

				$.ajax({
				    url:'../ajax/ajax_sales_query.php',
				    type:'POST',
				    data: {functionName:'getDailyQuotas',cur_week:cur_week},
				    success: function(data){
				        $('#daily').html(data);
				    },
				    error:function(){

				    }
				});
			}

		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>