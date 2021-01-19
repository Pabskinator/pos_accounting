<script>
	$(function(){
		$('body').on('click','#btnSignOut', function(){
			$.ajax({
				    url:'service/service.php',
				    type:'POST',
				    dataType:'json',
				    beforeSend:function(){
				    	
				    },
				    data: {functionName:'logout'},
				    success: function(data){
				    
				    	if(data == '1') location.href='login.php';
 					},
				    error:function(){

				    }
				});
		});
	})
</script>
</body>
</html>