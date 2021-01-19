<?php include 'includes/page_head.php' ?>
<div class='container-fluid' >
	<div class="card text-center">
  <div class="card-header">
   Users
  </div>
  <div class="card-body">
    <h5 class="card-title">List of users</h5>
     <p class="card-text"></p>
     <p class='text-right'> <button  class='btn btn-primary' id='btnAddUser'>Add User</button></p>
   
   <div id="con">
      <div class="linePreloader"></div>
        <div id="no-more-tables">
   	    <table class="table"> 
            <thead> 
                <tr>  
                    <th>Emp ID</th>
                    <th>Name</th>
                    <th>Salary/Hr</th>
                </tr>
            </thead>
            <tbody id='details-user'> 
            </tbody>
        </table>
        </div>
   </div>
   
  </div>
  <div class="card-footer text-muted">
      
  </div>
</div>
<br>
</div>
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add Users</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
   
    
          Employee ID
          <input type="text" class='form-control' placeholder='Employee ID' id='emp_id'>
           Full Name
          <input type="text" class='form-control' placeholder='Full Name' id='fullname'>
           Salary Rate
          <input type="text" class='form-control' placeholder='Salary Rate' id='salary_rate'>
      
      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

        <button type="button" class="btn btn-primary" id='btnSaveUser'>Save</button>
        
      </div>
    </div>
  </div>
</div>


<script>
		$(function(){
        getUsers();
        function getUsers(){
 $('#details-user').html('')
            $.ajax({
                url:'process.php',
                type:'POST',
                dataType:'json',
                data: {functionName:'getUsers'},
                success: function(data){
                  
                  for(var i in data){
                      $('#details-user').append("<tr><td data-title='ID'>"+data[i].emp_id+"</td><td data-title='Name'>"+data[i].fullname+"</td><td data-title='Rate'>"+data[i].salary_rate+"</td></tr>");
                  }
                  
                },
                error:function(){
                   
                }
            });

        }
        $('#btnAddUser').click(function(){
            $('#emp_id').val('');
            $('#fullname').val('');
            $('#salary_rate').val('');
           $('#exampleModalCenter').modal('show');
        });
        $('#btnSaveUser').click(function(){
             var emp_id = $('#emp_id').val();
            var fullname = $('#fullname').val();
            var salary_rate = $('#salary_rate').val();

            if(emp_id && fullname && salary_rate){
               $.ajax({
                  url:'process.php',
                  type:'POST',
                  data: {functionName:'addUser',emp_id:emp_id,fullname:fullname,salary_rate:salary_rate},
                  success: function(data){
                   
                     showStackbar(data);
                     if(data != 'User already exists'){
                         $('#exampleModalCenter').modal('hide');
                     }
                   
                     getUsers();
                  },
                  error:function(){
                     
                  }
              });

            }
        });

		});

  </script>

<?php include 'includes/page_tail.php' ?>