<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/support/page_head.php';
	if(!$user->hasPermission('dashboard')){
		// redirect to denied page
		//	Redirect::to(1);
	}

?>
	<style>
		.chat_msg{
			height: 80px;
			overflow-y: auto;
			border: 1px solid #ccc;
			background: #fff;
			color: #444;
			padding: 5px;
		}
		#chat_container{
			width: 100%;
			height: 100%;
			position: fixed;
			bottom: 0;
			right: 0;
			overflow-x:auto;
			white-space: nowrap;
			text-align: right;
			background: #ccc;
		}
		.chat_box{
			position: relative;
			margin-top: 20vh;
			height: 80vh;
			width: 280px;
			display: inline-block;
			margin-right: 5px;


		}

		.chat_box div {
			-webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
			-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
			box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
			height:  80vh;
			width: 100%;

			background: #fff;
			padding: 10px;


		}

		.chat_box h4{
			position: absolute;
			top: -20px;
			left: 0;
			text-align: center;
			height: 30px;
			background: #2c3e50;
			width: 280px;
			color: #fff;
			padding: 5px;
		}

		.chat_box ul{
			height: 64vh;
			list-style: none;
			margin: 0;
			padding: 0;
			margin-top: 35px;
			overflow-y: auto;
			white-space: normal;
		}

		.chat_box ul li{
			display:inline-block;
			clear: both;
			padding: 10px;
			border-radius: 30px;
			margin-bottom: 4px;
			font-family: Helvetica, Arial, sans-serif;
		}

		.him{
			background: #eee;
			float: left;
		}

		.me{
			float: right;
			background: #0084ff;
			color: #fff;
		}

		.him + .me{
			border-bottom-right-radius: 5px;
		}

		.me + .me{
			border-top-right-radius: 5px;
			border-bottom-right-radius: 5px;
		}

		.me:last-of-type {
			border-bottom-right-radius: 30px;
		}

		.chat_box input{
			position: absolute;
			bottom: 0px;
			left: 0px;
			width: 280px !important;
			-webkit-border-radius: 0 !important;
			-moz-border-radius: 0 !important;
			border-radius: 0 !important;
		}
		.chat_action{
			position: absolute;
			right: 3px;
			cursor: pointer;
		}

		.chat_box_inactive{
			visibility: hidden;

		}
		.chat_box_inactive input, .chat_box_inactive ul .chat_box_inactive div{
			display: none;
		}
		.chat_box_inactive h4{
			visibility: visible;
			position: absolute;
			bottom: 0 !important;
			top: auto;

		}

		#chat_container_close{
			position: fixed;
			top:70px;
			right: 10px;
		}
	</style>

	<!-- Sidebar -->
<?php include_once '../includes/support/sidebar.php';?>
	<!-- Page content -->
	<div id='chat_app'>
	<div  id="page-content-wrapper" style='padding-top: 30px;'>
		<div class="container">
			<button class='btn btn-primary' v-show="messages.length" @click="chat_container = true"><i class='fa fa-check'></i> Open Chat Container ({{messages.length}})</button>

			<div id='new_msg_container'>
				<h4>Pending Chat Request</h4>
				<div class="row" v-show="pending_chat.length">
					<div class="col-sm-12 col-md-3" v-for="chat in pending_chat">
						<div class="thumbnail chat_new">
							<div class="caption">
								<h5 class='text-center'> {{ chat.client_name }}</h5>
								<p class='text-center'>{{ chat.email }}</p>
								<p  class='text-center'>{{ chat.contact_number }}</p>
								<p class='chat_msg'>{{chat.concern}}</p>
								<p class='text-success'>{{chat.created}}</p>
								<p class='text-center'><a href="#" class="btn btn-primary" role="button" @click="acceptChat(chat)">Accept</a> </p>

							</div>
						</div>
					</div>
				</div>
				<div  v-show="!pending_chat.length">
					<div class="alert alert-info">
						No record found.
					</div>
				</div>
			</div>

			<div id="chat_container" v-show="chat_container">
				<button id='chat_container_close'  class='btn btn-primary' @click="chat_container = false">
					<i class='fa fa-remove'></i> Close Chat Container
				</button>
				<div class="chat_box" v-for="chat in messages" v-show="chat.active" v-bind:class="[chat.active ? '' : 'chat_box_inactive']">
					<h4 class='left-align'>
						{{chat.name}} <span v-show="!chat.alive">(Inactive)</span>
						<span v-show="!chat.active" class='label label-badge'>
							{{ getUnread(chat.conversations) }}
						</span>
						<span class='chat_action'>
						<!--	<i class='fa fa-minus' @click="toggleActive(chat)" v-show="chat.active"></i> -->
							<i class='fa fa-square-o'  @click="toggleActive(chat)" v-show="!chat.active"></i>
							<i class='fa fa-close' @click="closeChat(chat)"></i>
						</span>
					</h4>
					<div>

						<ul class='scrollBotUl' v-bind:id="chat.id">
							<li class='text-danger pull-left'>Concern: {{chat.concern}}</li>
							<li v-bind:class="[msg.me ? 'me' : 'him']" v-for="msg in chat.conversations">
								{{ msg.msg }}
							</li>
						</ul>
						<input @keyup="appendMessage($event,chat)" v-model="chat.new_msg" type="text" class='form-control' placeholder='Enter message'>
					</div>
				</div>

			</div>
		</div>
	</div>
	</div>
	<script src='../js/vue3.js'></script>
	<script type="text/javascript">

		var vm = new Vue({
			el:'#chat_app',
			data: function(){
				return {
					chat_container: false,
					new_msg_txt:[],
					pending_chat: [

					],
					messages:[]
				}
			},
			mounted: function(){
				var vm = this;
				vm.scrollUl();
				vm.getPending();
				vm.getMyClient();
				setInterval(function(){
					vm.getPending()
				},10000);
				setInterval(function(){
					vm.getMyClient()
				},5000);

			},
			computed:{

			},
			methods: {
				closeChat: function(c){
					if(confirm("Are you sure you want to close this chat session?")){
						var vm = this;
						c.active = false;
						$.ajax({
							url:'ajax_chat.php',
							type:'POST',
							dataType:'json',
							data: {functionName:'closeChat', id: c.cid},
							success: function(data){
								vm.getMyClient();
								vm.getPending();
							},
							error:function(){

							}
						});
					}

				},
				insertMessage: function(msg,id){

					$.ajax({
						url:'ajax_chat.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'insertMessage',msg: msg, id: id},
						success: function(data){

						},
						error:function(){

						}
					});
				},
				getPending: function(){
					var vm = this;
					$.ajax({
					    url:'ajax_chat.php',
					    type:'POST',
						dataType:'json',
					    data: {functionName:'getPendingReq'},
					    success: function(data){
						    if(vm.pending_chat.length < data.length){
							    vm.notifyMe();
						    }
							vm.pending_chat = data;
					    },
					    error:function(){

					    }
					});
				},

				getMyClient: function(){
					var vm = this;
					$.ajax({
						url:'ajax_chat.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getMyClient'},
						success: function(data){

							for(var i in vm.messages){
								if(vm.messages[i].new_msg){
									for(var j in data){
										if(data[j].id == vm.messages[i].id){
											data[j].new_msg = vm.messages[i].new_msg;
										}
									}
								}
							}

							vm.messages = data;
							if(data.length == 0){
								vm.chat_container = false;
							}
							$('.scrollBotUl').each(function(){
								var div = $(this);
								if (div[0].scrollHeight - div.scrollTop() == div.height()) //scrollTop is 0 based
								{

									$('#' + div.attr('id')).animate({ scrollTop: $('#' + div.attr('id')).prop("scrollHeight")}, 300);
								}
							});


						},
						error:function(){

						}
					});
				},
				acceptChat: function(c){
					var vm = this;
					vm.chat_container = true;
					$.ajax({
						url:'ajax_chat.php',
						type:'POST',
						data: {functionName:'acceptChat',chat_id : c.id},
						success: function(data){
							vm.getMyClient();
							vm.getPending();
						},
						error:function(){

						}
					});

				},
				getUnread: function(conversations){
					var count = 0;
					for(var i in conversations){
						if(conversations[i].read != true){
							count +=1;
						}
					}
					return count;
				},
				scrollUl: function(){
					$('#chat_container ul').each(function(){
						var d = $(this);
						d.scrollTop(d.prop("scrollHeight"));
					});
				},
				appendMessage: function(e,chat){
					console.log(e.key);
					if (e.key == 'Enter') {
						if (chat.new_msg) {
							chat.conversations.push({me:true,msg: chat.new_msg, read: true});

							this.insertMessage(chat.new_msg,chat.cid);
							$("#"+chat.id).animate({ scrollTop: $('#'+chat.id).prop("scrollHeight")}, 300);
							chat.new_msg = '';
						}
					}
				},
				toggleActive: function(chat){
					chat.active = !chat.active;
				},
				notifyMe: function() {
				// Let's check if the browser supports notifications
				if (!("Notification" in window)) {
					console.log("This browser does not support desktop notification");
				}

				// Let's check whether notification permissions have already been granted
				else if (Notification.permission === "granted") {
					// If it's okay let's create a notification
					var notification = new Notification("Notification",{
						body: 'You have new message(s).',
						icon: '../css/img/logo.jpg' // optional

					});
				}

				// Otherwise, we need to ask the user for permission
				else if (Notification.permission !== "denied") {
					Notification.requestPermission(function (permission) {
						// If the user accepts, let's create a notification
						if (permission === "granted") {
							var notification = new Notification("Notification",{
								body: 'You have new message(s).',
								icon: '../css/img/logo.jpg' // optional

							});
						}
					});
				}

				// At last, if the user has denied notifications, and you
				// want to be respectful there is no need to bother them any more.
			}
			}

		})
	</script>


<?php require_once '../includes/support/page_tail.php'; ?>