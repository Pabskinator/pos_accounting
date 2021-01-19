<?php include 'includes/page_head.php' ?>

	<div class='container-fluid text-center' >
	<div class='row'>
	<div class='col-md-6'>


<div class="card text-center">
  <div class="card-header">
    Attendance Monitoring
  </div>
  <div class="card-body">

		<div class='img-con'>
	  <img id="blah" src="noimg.png" alt="your image" />
	  </div>
	<br>
<form enctype="multipart/form-data" action="" method="post">

	<div class='form-group'>
		<input type='text' class='form-control' placeholder='Enter Employee Name' id='employee_name'>
	</div>
	<div class='form-group'>
		<input type='text' class='form-control' placeholder='Enter Employee Number' id='code'>
	</div>
	<div class='form-group'>
		<label class='btn btn-primary' id='captureBtn' for="imgInp"><i> </i> Capture Image </label>
	<input type="file" style='display:none;' accept="image/*" id='imgInp' name='myImg' capture="camera" class='btn btn-primary'>
	</div>
	<div class="linePreloader"></div>
	<div id='btn-action'>
	<div class='row' >
		<div class='col-sm-6'>
			<div class='form-group'>
			<button class='btn btn-primary btn-block' id='btn-submit'>Submit</button>
			</div>
		</div>
		<div class='col-sm-6'>
			<div class='form-group'>
			<button class='btn btn-danger btn-block' id='btn-reset'>Reset</button>
			</div>
		</div>
	</div>
  </div>
  </form>
  </div>
  <div class="card-footer text-muted">
    <?php echo date('F d, Y'); ?>
  </div>
</div>
</div>
<div class='col-md-6'>
 <div class="card">
      <div class="card-body">
	  <div id='no-more-tables'>
		  <div class="row">
			  <div class="col-md-4 text-left">
				  <button id='prev' class='btn btn-secondary'>Prev</button>
			  </div>
			  <div class="col-md-4">
				  <strong id='current_date'></strong>
			  </div>
			  <div class="col-md-4 text-right">
				  <button id='next' style='display:none;' class='btn btn-secondary'>Next</button>
			  </div>

		  </div>
		  <br>

<table class="table table-striped" id='tblList'>
  <thead class="">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Time In</th>
      <th scope="col">Time Out</th>
    </tr>
  </thead>
  <tbody>
   
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	 
			<div class="card" style="width: 18rem;margin:0 auto;">
			 	<div class='img-con'>
					<img class="card-img-top" id='pic1' src="" alt="">
				</div>
			  <div class="card-body">
					Time in
			  </div>
			</div>
			<br>
			<div class="card" style="width: 18rem;margin:0 auto;">
				<div class='img-con'>
				<img class="card-img-top" id='pic2'  src="" alt="">
			  </div>
			  <div class="card-body">
					Time Out
			  </div>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
 
      </div>
    </div>
  </div>
</div>
<script src='compress.js'></script>
<script>
	$(function(){

	var day = 0;

	$('body').on('click','#prev',function(){
		prev();
	});

	$('body').on('click','#next',function(){
		next();
	});

		$('body').on('keyup','#code',function(){
			var emp_id = $(this).val();
			$.ajax({
				url:'process.php',
				type:'POST',
				dataType:'json',
				data: {functionName:'getName',emp_id:emp_id},
				success: function(data){
					if(data.name && data.emp_id){
						$('#employee_name').select2('data',{text:data.name,id:data.emp_id});
					} else {
						$('#employee_name').select2('data',{text:"Not Found",id:0});
					}
				},
				error:function(){

				}
			});
		});
		$('#employee_name').change(function(){
			var emp_id = $('#employee_name').val();
			$('#code').val(emp_id);
		});

		$('#employee_name').select2({
			placeholder: 'Search Agent', allowClear: true, minimumInputLength: 2,

			ajax: {
				url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
					return {
						q: term, functionName: 'users'
					};
				}, results: function(data) {
					return {
						results: $.map(data, function(item) {

							return {
								text: item.firstname + " " + item.lastname,
								slug: item.firstname + ", " + item.lastname,
								id: item.emp_id
							}
						})
					};
				}
			}
		});

	getAttendance();
	function prev(){
		day = parseInt(day) + 1;
		if(day){
			$('#next').show();
		}
		getAttendance();
	}
	function next(){
		day = parseInt(day) - 1;
		if(!day){
			$('#next').hide();
		}
		getAttendance();
	}

		function readURL(input) {

			  if (input.files && input.files[0]) {
			    var reader = new FileReader();
				
			    reader.onload = function(e) {
			      $('#blah').attr('src', e.target.result);
			    }

			    reader.readAsDataURL(input.files[0]);
			  }
		}

		$("#imgInp").change(function() {
			readURL(this);
			$('#btn-action').show();
			$("#imgInp").hide();
			$("#captureBtn").hide();

		});
		$('#btn-reset').click(function(e){
			e.preventDefault();
			resetForm();
		});

		$('#btn-submit').click(function(e){
				e.preventDefault();
				var code = $('#code').val();
				var con = $(this);
				con.attr('disabled',true);
				con.html('Loading...')
				var fd = new FormData();
				var file_data = $('input[name=myImg]')[0].files[0];

				new ImageCompressor(file_data, {
				    quality: .6,
				    height:500,
				    success(result) {
				    if(code && file_data){
					fd.append('file',result,result.name);
					fd.append('functionName','upload');
					fd.append('code',code);
					$('.linePreloader').show();
					$('#btn-action').hide();
					$.ajax({
						url: 'process.php',
						type: 'POST',
						contentType: false,
						processData: false,
						data: fd,
						success: function(data) {
							resetForm();
							getAttendance();
							showStackbar(data);
							
						},
						error: function() {
							$('.linePreloader').hide();
						}
					});
				} else {
					alert("Enter code and image");
					con.attr('disabled',false);
					con.html("Submit")
				}
				     
				    },
				    error(e) {
				      console.log(e.message);
				    },
				  });

				
				
		});

		


		function resetForm(){
			$('#blah').attr('src','noimg.png')
			$('#btn-action').hide();
			$("#captureBtn").show();
			$("#imgInp").val('');
			$("#code").val('');
			$('.linePreloader').hide();
			$('#btn-submit').attr('disabled',false);
			$('#btn-submit').html("Submit")
		}
		function getAttendance(){

			$.ajax({
			    url:'process.php',
			    type:'POST',
				dataType:'json',
			    data: {functionName:'getAttendance',day:day},
			    success: function(data){
			        updateTable(data.list);
				    $('#current_date').html(data.dt);
			    },
			    error:function(){

			    }
			});
		}
		function updateTable(data){
			$('#tblList tbody').html('');
			if(data.length){

				for(var i in data){
					$('#tblList tbody').append("<tr ><td data-pic1='"+data[i].time_in_pic+"' data-pic2='"+data[i].time_out_pic+"' data-title='ID' class='get-img'>"+data[i].emp_id+"</td><td data-title='Name'>"+data[i].fullname+"</td><td data-title='In'>"+data[i].dt1+"</td><td data-title='Out'>"+data[i].dt2+"</td></tr>");
				}
			} else {
				$('#tblList tbody').append("<tr><td colspan='4'> No Record</td></tr>");
			}
			
		}
		$('body').on('click','.get-img',function(){
			var con = $(this);
			var pic1 = con.attr('data-pic1');
			var pic2 = con.attr('data-pic2');
			if(pic1){
				$('#pic1').attr('src',pic1)
			}
			if(pic2){
				$('#pic2').attr('src',pic2)
			}
			$('#exampleModalCenter').modal('show');
			
		});

});
	
</script>

<?php include 'includes/page_tail.php' ?>