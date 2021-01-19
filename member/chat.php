<?php
	include 'service/connection.php';
	session_start();

	if(!$_SESSION['user_id']){
		header("Location: index.php");
		exit();
	}

?>


<?php include_once 'includes/member/page_head.php'; ?>
<?php include_once 'includes/member/sidebar.php'; ?>

<main id='app' >


	<div id="main" >
	<div class="col s12" v-show="openChat">
		<button @click='openChat = false; user_id_to=0;' class='btn btn-flat '>Back</button>
	<button @click='attachFiles' class='btn  btn-flat'>Attachment</button>
	<div v-show="messages.length">
	
    <ol class="chat" id='message-container'>
    <li v-bind:class="[message.from == user_id_1 ? 'self' : 'other']" v-for="message in messages">
        <div class="avatar"><img src="http://i.imgur.com/DY6gND0.png" draggable="false"/></div>
      <div class="msg">
         <p class='grey-text'>
	      <span v-show="message.is_file == 1" v-html="message.msg">
	      
	      </span>
	      <span v-show="message.is_file== 0">
	      	{{
	      		message.msg
	     	 }}
	      </span>
	      
	      </p>
        <time>{{message.created}}</time>
      </div>
    </li>
   
    
    </ol>
    </div>
    <div class="progress" v-show="ajax_loading && !messages.length">
				      <div class="indeterminate"></div>
	 </div>
	<p v-show="!messages.length && !ajax_loading">No message found.</p>
   <input class='textarea'type="text" id='txtMessage' v-model='msg' placeholder='Enter message'>

</div> <!-- End open chat -->

	<input type="hidden" id='myUserId' value='<?php echo $_SESSION['user_id']; ?>'>
	 <div class="fixed-action-btn">
	 <div id='online-list' v-show="showUserList && user_list.length">
	 	<input type="text" id='searchUser' v-model='searchUser' placeholder="Search Member">
	 	<p class='userCon' v-for="user in filteredUsers" @click="showThisUser(user)"> 
	 		<img src="img/no-thumb.jpg" class='chatAvatar left' alt=""> 
	 		<span class='chatName'>{{user.name}}</span>
	 	</p>
	 	
	 </div>

	 <a class="btn-floating btn-large waves-effect waves-light red right" @click.prevent="toggleUserList">
	 	<i class="material-icons" v-show="!showUserList">chat</i>
	 	<i class="material-icons" v-show="showUserList">close</i>
	 </a>

	 </div>
		<div class="row">
	<div class="col s12" v-show="!openChat">
			<div>
				<div id="conversation-container">
					
					<ul class="collection" v-show="conversation_list.length">

					 <li style='min-height:64px;cursor:pointer;' class="collection-item avatar" v-for="cb in conversation_list" @click="showThisUser(cb)">
						      <i class="material-icons circle">person_pin</i>
						      <p style='margin-top:8px;'>
						      	{{ cb.to }}
						      </p>
						    
					</li>
				</ul>
				<p v-show="!conversation_list.length">No conversation found.</p>
				</div>
			</div>	
	</div>
	
		


	</div>
</main>
<footer>
</footer>
  <div id="modal1" class="modal">
    <div class="modal-content">
			
			<form class='dropzone' id="dropzone-form">
				<input type="hidden" id='dropzone_order_id'>
			</form>
      
    </div>
   
  </div>


<script src="js/jquery.js"></script>
<script src="js/materialize.min.js"></script>
<script src="js/dropzone2.js"></script>
<script src="js/vue.js"></script>
<script>
	var vm = new Vue({
		el: '#app',
		data: {
			showUserList: false,
			user_id_1: 0,
			user_id_2 :0,
			user_list:[],
			conversation_list:[],
			msg:'',
			searchUser:'',
			openChat: false,
			ajax_loading: false,
			att_title:'',
			att_description:'',
			messages: [
				
			]
		},
		computed:{
			filteredUsers: function(){
					var self = this
		            return self.user_list.filter(function (item) {
		              return (item.name).toLowerCase().indexOf(self.searchUser.toLowerCase()) !== -1
		            });
			}
		},
		mounted: function(){
			
			var vm = this;
			 $('.modal').modal();
			var myDropzone = new Dropzone("#dropzone-form", {
						url: "service/service.php?functionName=uploadAtt",
						acceptedFiles: "image/jpegimage/jpg,image/bmp,image/png,image/gif"
					}
				);
				myDropzone.on('sending', function(file, xhr, formData){
					formData.append('user_id_1', vm.user_id_1);
					formData.append('user_id_2', vm.user_id_2);
					
				});

			$('#message-container').animate({scrollTop: $('#message-container').prop('scrollHeight')});
			$('#txtMessage').keypress(function(e) {
			    if(e.which == 13) {
			    	if(vm.user_id_2){
			    		vm.messages.push({msg: 'Sending...',is_file:0,from:vm.user_id_1});
				        vm.sendMessage();
				        vm.msg = '';
			    	}
			       
			        $('#message-container').animate({scrollTop: $('#message-container').prop('scrollHeight')});
			    }
			});
			setInterval(function(){
				vm.getMessages();
			},5000);
			vm.getUsers();
			vm.getMessages();
			vm.getConversations();

			vm.user_id_1 = $('#myUserId').val();

		},
		methods:{
			attachFiles: function(){
					Dropzone.forElement("#dropzone-form").removeAllFiles(true);
					$('#modal1').modal('open');
			},
			toggleUserList: function(){
				this.showUserList = !this.showUserList;
			},
			getUsers: function(){

				var vm = this;

				$.ajax({
				    url:'service/service.php',
				    type:'POST',
				    dataType:'json',
				    data: {functionName:'getUsers'},
				    success: function(data){
				   
				       vm.user_list = data; 
				    },
				    error:function(){
				        
				    }
				});


			},
			getConversations: function(){

				var vm = this;

				$.ajax({
				    url:'service/service.php',
				    type:'POST',
				    dataType:'json',
				    data: {functionName:'getConversation'},
				    success: function(data){
				   
				       vm.conversation_list = data; 
				    },
				    error:function(){
				        
				    }
				});


			},
			getMessages: function(){

				var vm = this;
				
				if(vm.user_id_2 && vm.ajax_loading == false){
					vm.ajax_loading = true;
					$.ajax({
					    url:'service/service.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'getMessages',user_id_1:vm.user_id_1,user_id_2:vm.user_id_2},
					    success: function(data){
					       vm.messages = data;
					          vm.ajax_loading  = false;

					    },
					    error:function(){

							vm.ajax_loading  = false;
					        
					    }
					});
				}
				


			},
			sendMessage: function(){
				var vm = this;
				if(vm.user_id_2){
					$.ajax({
					    url:'service/service.php',
					    type:'POST',
					    dataType:'json',
					    data: {functionName:'insertMessage',msg: vm.msg,user_id_1:vm.user_id_1,user_id_2:vm.user_id_2},
					    success: function(data){



					     
					    },
					    error:function(){
					        
					    }
					}); 
				}
			},
			showThisUser: function(u){
				vm.showUserList= false;
				vm.messages = [];
				this.user_id_2 = u.id;
				this.getMessages;
				this.openChat = true;

			},
		}
	});

	$(function(){
		$('.button-collapse').sideNav();

		
	});
</script>


<?php include_once 'includes/member/page_tail.php';  ?>
