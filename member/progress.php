<?php
	include 'service/connection.php';
	session_start();

	if(!$_SESSION['member_id']){
		header("Location: login.php");
		exit();
	}
	function experienceTable($mysqli){

		$q = "Select * from experience_table order by points_needed asc";

		$result = $mysqli->query($q);
		$num_rows = $result->num_rows;

		if($result->num_rows > 0){
			$table = "<table id='tblExpiTable' class='stripe'>";
			$table .= "<thead><tr><th>Level</th><th>Points Needed</th></tr></thead>";
			$table .= "<tbody>";
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$table .= "<tr><td>$row[name]</td><td class='red-text right-align'>$row[points_needed]</td></tr>"; 
			}
		
			$table .= "</tbody>";
			$table .= "</table>";

		}
		return $table;
	
		

	}

	function weightComparison($mysqli){
		$q = "Select * from body_measurements where member_id =  " . $_SESSION['member_id'] . " order by created asc limit 1";
		$result = $mysqli->query($q);
		$num_rows = $result->num_rows;
		$before_weight = "No data yet.";
		if($result->num_rows > 0){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$before_weight = number_format($row['weight'],3) . " lbs";
		} 

		$q2 = "Select * from body_measurements where member_id =  " . $_SESSION['member_id'] . " order by created desc limit 1";
		$result2 = $mysqli->query($q2);
		$num_rows2 = $result2->num_rows;
		$after_weight = "No data yet.";
		if($num_rows2 > 0){
			$row2 = $result2->fetch_array(MYSQLI_ASSOC);
			$after_weight = number_format($row2['weight'],3) . " lbs";
		} 
		return ['before' => $before_weight ,'after' => $after_weight];
	}


	function getBodyTransformation($mysqli){
		 $q_before = "Select * from uploads where ref_id = $_SESSION[member_id] and ref_table = 'members' and tags ='wholebody' order by created asc limit 1";
		$result = $mysqli->query($q_before);
		$num_rows = $result->num_rows;
		$arr = [];
		$before_img = '';
		if($num_rows > 0){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$arr['before'] = $row['filename'];
			$before_img = $row['filename'];
		}

		$q_after = "Select * from uploads where ref_id = $_SESSION[member_id] and ref_table = 'members' and tags ='wholebody' order by created desc limit 1";
		$result = $mysqli->query($q_after);
		$num_rows = $result->num_rows;

		if($num_rows > 0){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			if($before_img != $row['filename'])
				$arr['after'] = $row['filename'];
		}
		return $arr;
	}


	$weight_comparison = weightComparison($mysqli);

?>

<?php include_once 'includes/member/page_head.php'; ?>

<?php include_once 'includes/member/sidebar.php'; ?>

<main>
		<div class="main_heading">
					<div class='white-text'>
					<div id='con_title'>
							<h4 id='current_brace'>Orange Brace</h4>
					</div>
					<div id="con_before">
						<p>EXPERIENCE</p>
						<h3 id='txtExpi'>0</h3>
						<p style='color:#eeeeee '>Experience gain at Safehouse</p>
					</div>
					<div id="con_after">
						<p id='next_brace'></p>
						<h3 id='txtExpiNeeded'>0</h3>
						<p style='color:#eeeeee '>Needed experience to level up</p>
					</div>
						</div>
					
			

		</div>
			<div id="main">
		  		 <div class="progress">
	   				   <div id='progressLevel' class="determinate" style="width: 0%"></div>
	 			</div>
	 			<div>
	 				<div class="row">	
						<div class="col m12 center-align">
							<span class='left' id='current_brace_lbl'></span>
							<small  class='grey-text' id='progressLevelLbl'></small>
							<span class='right' id='next_brace_lbl'></span>
						</div>
	 				</div>
	 			</div>
		  		<h3 class='center-align'>Experience</h3>
		  		<div class="row">	
		  		<div class="col m8 s12">
						<h5>Movement</h5>
						<div id="expi-graph" style='height: 400px;width:100%;'></div>
				</div>
				<div class="col m4 s12" >
						<h5>Experience Table</h5>
						<div style='height:400px;overflow-y:scroll;'>	
						<?php 
							echo experienceTable($mysqli);
						?>
						</div>
				</div>
				</div>
			
		<div id='before-after-body-transformation'>
			<div class="container">
			<h3 class='center-align'>Body Transformation</h3>
			<?php 
				$body_trans = getBodyTransformation($mysqli);

				$body_trans_from = "img/default_img.jpg";
				$body_trans_to = "img/default_img.jpg";
				$url_to_use = "";
				if($http_host == 'safehouseacademy.com'){
					$url_to_use = "http://safehouse.apollosystems.ph";
				}
				if(isset($body_trans['before']) && $body_trans['before']){
					$body_trans_from = $url_to_use . "/uploads/".$body_trans['before'];
				}
				if(isset($body_trans['after']) && $body_trans['after']){
					$body_trans_to = $url_to_use ."/uploads/".$body_trans['after'];
				}

			?>
			<div class="row">
				<div class="col m6 s12">
					<!-- card 1 -->
					 <div class="card">
		            <div class="card-image" style='height:300px;'>
		              <img  style='height:100%;width:auto;margin:0 auto;' src="<?php echo $body_trans_from; ?>">
		              <span class="card-title">Before</span>
		            </div>
		     
					        <div class="card-content">
			              <p><strong>Before</strong></p>
			            </div>
		          </div>
					<!-- end car 1 -->
				</div>
				<div class="col m6 s12">
					<!-- card 2 -->
					 <div class="card">
		            <div class="card-image " style='height:300px;width:auto;'>
		              <img style='height:100%;width:auto;margin:0 auto;' src="<?php echo $body_trans_to; ?>">
		              <span class="card-title">After</span>
		            </div>
		             <div class="card-content">
			              <p><strong>After</strong></p>
			            </div>
		           
		          </div>
					<!-- end car 3 -->
				</div>
			</div>
			<div class='center-align'>
					<button class='waves-effect waves-teal btn' id='btnShareTransformation'>Share It</button>
			</div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col m6 s12">
				<h4 class='center-align'>Weight Movement</h4>
				<div id="bar-graph" style='height: 300px;width:100%;'></div>
			</div>
			<div class="col m6 s12">
				<h4 class='center-align'>Arm Size</h4>
				<div id="area-graph" style='height: 300px;width:100%;'></div>	
			</div>
		</div>
		<div class="row">
			<div class="col m6 s12">
					<h4 class='center-align'>Thigh</h4>
					<div id="area2-graph" style='height: 300px;width:100%;'></div>
			</div>
			<div class="col m6 s12">
				<h4 class='center-align'>Calf</h4>
				<div id="line-graph" style='height: 300px;width:100%;'></div>
			</div>
		</div>
		
	</div>
		
	<br>
	<div class="row">
		<div class="col m12">
			<h4 class='center-align'>Services Attended</h4>
				<div id="donut-graph" style='height: 400px;width:100%;'></div>
		</div>
	</div>

	
	<div class="card-panel grey darken-4 white-text">
		
			<div id=""><h3 class='white-text'>Weight</h3></div>

			<div class="row">
				<div class="col m6 s12">
				 <div id="">
				<h6 class='white-text'>Before</h6>
					<h4 class='white-text'><?php echo $weight_comparison['before']?></h4>
				</div>
				</div>
				<div class="col m6 s12">
					<div id="">
					<h6 class='white-text'>After</h6>
						<h4 class='white-text'><?php echo $weight_comparison['after']?></h4>
					</div>
					</div>
				</div>
			</div>
			
		
</main>

<footer>

</footer>


<script src="js/jquery.js"></script>
<script src="js/materialize.min.js"></script>
<script src="js/raphael.js"></script>
<script src="js/morris.js"></script>
<script>
	$(function(){
		$('.button-collapse').sideNav();
		
		


	
		getStats();
		getExpiStats();

		function getExpiStats(){
				$.ajax({
					 url:'service/service.php',
				    type:'POST',
				    dataType:'json',
				    beforeSend:function(){
				    	$('#expi-graph').html('<div class="progress"><div class="indeterminate"></div></div>');
				    },
				    data: {functionName:'getExperience'},
				    success: function(data){
				    	$('#expi-graph').html('');
				    	var d = data.data;
				    	var total = data.total;
				    	$('#txtExpi').html(total);
				    	console.log(data)
				    	Morris.Area({
		 				element: 'expi-graph',
		  				data: d,
		 				 xkey: 'y',
						 ykeys: ['a'],
		 				 labels: ['Experience Gain'],
		 				 parseTime: false,
						behaveLikeLine: true
						});
				    	var cur_brace = "";
				    	var next_brace = "";
				    	var needed_points = "";
				    	var first_hit = false;
				    	var next_pt = 0;
				    	var perc = 0;
						$('#tblExpiTable tbody tr').each(function(){
								var row = $(this);
								var expi = parseInt(row.children().eq(1).html());
								var name= row.children().eq(0).html();
								if(parseInt(total) > expi){
									cur_brace = name;
								} else {
									if(!first_hit){
										first_hit	 = true;
										next_brace	 = name ;
									
										perc  = ((parseInt(total) / parseInt(expi)) * 100).toFixed(2);
										needed_points	= parseInt(expi) - parseInt(total);
									}
								}
						});
						$('#current_brace,#current_brace_lbl').html(cur_brace);
						$('#next_brace,#next_brace_lbl').html(next_brace);
						$('#txtExpiNeeded').html(needed_points);
						$('#progressLevel').css("width",perc+"%");
						$('#progressLevelLbl').html(perc+"%");
					 	
 					},
				    error:function(){

				    }	
				})

		
		}

		function getStats(){

				$.ajax({
				    url:'service/service.php',
				    type:'POST',
				    dataType:'json',
				    beforeSend:function(){
				    	$('#donut-graph,#area2-graph,#area-graph,#line-graph,#bar-graph').html('<div class="progress"><div class="indeterminate"></div></div>');
				    },
				    data: {functionName:'getStats'},
				    success: function(data){
				    	$('#donut-graph,#area2-graph,#area-graph,#line-graph,#bar-graph').html('');
					   weigthStats(data.weight);
					   armStats(data.arm);
					   calfStats(data.calf);
					   thighStats(data.thigh);
					  serviceStats(data.services);

 					},
				    error:function(){

				    }
				});
		}
		function serviceStats(s){
				if(s.length > 0){
					var total_expi =0;
					for(var i in s){
						var v = s[i].value;
						total_expi = parseInt(total_expi) + parseInt(v);
					}
				
				
					Morris.Donut({
						element: 'donut-graph',
						data: s
					});
				} else {
				$('#donut-graph').html("<div style='padding:10px;' class='grey lighten-5 z-depth-1'><h5 class='black-text'>No record yet.</h5></div>'");
					
				}
					
		}
		function thighStats(t){
			if(t.length > 0){
				Morris.Line({
				element: 'area2-graph',
				data: t,
				xkey: 'y',
				ykeys: ['a', 'b'],
				labels: ['Left Thigh', 'Right Thigh'],
				parseTime: false,
				behaveLikeLine: true,
				});
			} else {
				$('#area2-graph').html("<div style='padding:10px;' class='grey lighten-5 z-depth-1'><h5 class='black-text'>No record yet.</h5></div>'");
			}
			
			   
		}

		function calfStats(c){
			if(c.length > 0){
				Morris.Line({
						element: 'line-graph',
						data:c,
						xkey: 'y',
						ykeys: ['a', 'b'],
						labels: ['Left Calf', 'Right Calf'],
						parseTime: false,
				});
			}  else {
				$('#line-graph').html("<div style='padding:10px;' class='grey lighten-5 z-depth-1'><h5 class='black-text'>No record yet.</h5></div>'");
			}
		}

		function armStats(a){
			if(a.length > 0){
				Morris.Line({
						element: 'area-graph',
						data: a,
						xkey: 'y',
						ykeys: ['a', 'b'],
						labels: ['Left Arm', 'Right Arm'],
						parseTime: false,
						behaveLikeLine: true,
					});
			  } else {
			  	$('#area-graph').html("<div style='padding:10px;' class='grey lighten-5 z-depth-1'><h5 class='black-text'>No record yet.</h5></div>'");
			  } 
			
		}
		function weigthStats(w){
			if(w.length){
					 Morris.Line({
						element: 'bar-graph',
						data: w,

						xkey: 'y',
						ykeys: ['a'],
						labels: ['Weight'],
						xLabelAngle: 25,
						padding: 40,
						parseTime: false,
						hoverCallback: function(index, options, content) {
							var data = options.data[index];
							return("<p> "+data.y + "<br><span class='text-danger'>" + data.a +" lbs</span></p>");
						}
					});
				} else {
						$('#bar-graph').html("<div style='padding:10px;' class='grey lighten-5 z-depth-1'><h5 class='black-text'>No record yet.</h5></div>'");
				}
		   
		}

		$('body').on('click','#btnShareTransformation',function(){
			   Materialize.toast("This feature is still on development. It will be available soon.",2000,"green lighten-2");
		});

	});
</script>


<?php include_once 'includes/member/page_tail.php';  ?>
