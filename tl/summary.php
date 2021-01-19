<?php include 'includes/page_head.php' ?>
<div class='container-fluid' >
	<div class="card text-center">
  <div class="card-header">
   Summary
  </div>
  <div class="card-body">
    <h5 class="card-title"><?php echo date('M Y') ?></h5>
     <p class="card-text">Attendance for this month</p>
    <div class="row">
    	<div class="col-md-4">
    		<div class="form-group">
    			<input type="date" class='form-control' placeholder="From" id='dt1'>
          <span class='help-block'> Start Date</span>
    		</div>
    	</div>
    	<div class="col-md-4">
    		<div class="form-group">
    			<input type="date" class='form-control' placeholder="To" id='dt2'>
           <span class='help-block'> End Date</span>
    		</div>
    	</div>
    	<div class="col-md-4">
    		<div class="form-group">
    			<button class='btn btn-primary' id='btnSubmit'>Submit</button>
    		</div>
    	</div>
    </div>
   <div id="con">
      <div class="linePreloader"></div>
       <div id='no-more-tables'>
   	    <table class="table" id='tblSummary'> 
            <thead> 
                <tr>  
                    <th>Emp ID</th>
                    <th>Name</th>
                    <th>Total Hrs</th>
                    <th>Salary/Hr</th>
                    <th>Total Salary</th>
                </tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
      </div>
   </div>
   
  </div>
  <div class="card-footer text-muted">
    <?php echo date('F d Y') ?>
  </div>
</div>
<br>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id='no-more-tables'>
        <table class='table table-bordered'>
            <thead>
              <tr>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Hrs</th>
              </tr>
            </thead>
            <tbody id='details-body'></tbody>
        </table>
        </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
 
      </div>
    </div>
  </div>
</div>

<script>
		$(function(){
     getSummary();
      $('#btnSubmit').click(function(){
      getSummary();
    });
			function getSummary(){

			var dt1 = $('#dt1').val(); 
			var dt2 = $('#dt2').val();
      $('.linePreloader').show();
					$.ajax({
				    url:'process.php',
				    type:'POST',
					dataType:'json',
				    data: {functionName:'getSummary',dt1:dt1,dt2:dt2},
				    success: function(data){
				        updateTable(data);
                $('.linePreloader').hide();
				    },
				    error:function(){
				        $('.linePreloader').hide();
				    }
			})
    }
          function updateTable(data){
           
                if(data.length){
                $("#tblSummary > tbody").html("");
                var grand_total = 0;
                for(var i in data){
                    var total =  data[i].total * data[i].rate;
                    var attendance_com = data[i].attendance_com;
                    total = parseFloat(total) + parseFloat(attendance_com);
                    total = (total) ? total : 0;
                    grand_total += parseFloat((total).toFixed(2));
                   $('#tblSummary > tbody').append("<tr><td data-title='Id' data-emp_id='"+data[i].emp_id+"' class='getDetails' >"+data[i].emp_id+"</td><td data-title='Name'>"+data[i].fullname+"</td><td data-title='Total'> "+ data[i].total +"</td><td data-title='Rate'>"+data[i].rate+"</td><td data-title='Salary'>"+(total).toFixed(2)+"</td></tr>");
                }

                grand_total = (grand_total) ? grand_total : 0;
                 $('#tblSummary > tbody').append("<tr class='bg-info'><td></td><td></td><td></td><td></td><td class='text-white'>"+(grand_total).toFixed(2)+"</td></tr>");
                
              } else {

                $('#tblSummary > tbody').html("<tr><td colspan='5'>No record found.</td></tr>");
             }
        }



        $('body').on('click','.getDetails',function(){
            var con = $(this);
            var emp_id = con.attr('data-emp_id');
            var dt1 = $('#dt1').val(); 
            var dt2 = $('#dt2').val();

        
              $.ajax({
                url:'process.php',
                type:'POST',
                dataType:'json',
                data: {functionName:'getDetails',dt1:dt1,dt2:dt2,emp_id:emp_id},
                success: function(data){
                  
                    $('#details-body').html('');
                    for(var i in data){
                     $('#details-body').append("<tr><td data-title='Time In'>"+data[i].time_in+"</td><td data-title='Time Out'>"+data[i].time_out+"</td><td data-title='Hrs'>"+data[i].hrs+"</td></tr>");
                    $('#exampleModalCenter').modal('show');
                  }
                },
                error:function(){
                   
                }
            });
      

      });




		});
  </script>

<?php include 'includes/page_tail.php' ?>