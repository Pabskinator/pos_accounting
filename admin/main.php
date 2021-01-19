<?php

	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head.php';

	if(Session::exists('acc_log')){
			Session::put('acc_log', (Session::get('acc_log') + 1));
	}

	//put flag for accounting login
	if(!Session::exists('acc_log')){
      Session::put('acc_log', 1);
  }

?>
	<div id="errorIframe" style='display:none;padding-top:70px;' >
		<div class="container">
			<div class="jumbotron" >
				<h1><span class='glyphicon glyphicon-wrench'></span> You have poor connection.</h1>
				<p>Try refreshing the page.</p>
				<p><a class="btn btn-primary btn-lg" href="#" role="button" id='btnIframeRefresh'><span class='glyphicon glyphicon-refresh'></span> Retry</a></p>
			</div>
		</div>
	</div>


	<div id='iframeHolder' style="width: 100%; height: 100%; overflow: auto;">
		<iframe  id='conPage' style="position: fixed;left:0px;top:50px;width:100%; height:100%;padding-bottom: 50px; border: none;" ></iframe>
	</div>


	<script>

			if(<?php echo Session::get('acc_log'); ?> == 1){

          //get credentials temporarily stored in local storage
          var temp_user = localStorage.getItem("username");
          var temp_pass = localStorage.getItem("password");

          $.ajax({
              url: '../accounting/public/api/login',
              method: 'POST',
              data: {
                  username: temp_user,
                  password: temp_pass
              },

              success: function(data) {

                  //set credentials in local storage to null
                  localStorage.setItem('username', null);
                  localStorage.setItem('password', null);

              },

              error: function(err) {
                  console.log(err);
              }
          });
			}

	</script>

<?php require_once '../includes/admin/page_tail.php'; ?>