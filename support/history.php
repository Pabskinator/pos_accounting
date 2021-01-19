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


			overflow-x:auto;
			white-space: nowrap;
			text-align: center;
			
		}
		.chat_box{
			position: relative;
			width: 340px;
			display: inline-block;
			margin: 0 auto;

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
		[v-cloak] {
			display: none;
		}
	</style>

	<!-- Sidebar -->
<?php include_once '../includes/support/sidebar.php';?>
	<!-- Page content -->
	<div id='chat_app'>
		<div  id="page-content-wrapper" style='padding-top: 30px;'>
			<div class="container">



				<div v-show='container.list_view'>
					<h4>Chat History</h4>
					<div v-show="messages.length">
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th>Client Name</th>
								<th>Email</th>
								<th>Concern</th>
								<th>Date</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="message in messages" v-cloak>
								<td>{{message.client_name}}</td>
								<td>{{message.email}}</td>
								<td style='width:350px;'>{{message.concern}}</td>
								<td>{{message.created}}</td>
								<td><button class='btn btn-primary' @click="showDetails(message)">Details</button></td>
							</tr>
						</tbody>
					</table>
					</div>
					<div class='alert alert-info' v-show="!messages.length">No record found</div>
				</div>
				<div v-show='container.detail_view'>
					<button class='pull-left btn btn-default' @click="getMessage">Back</button>
					<h4 class='text-center'>Conversation</h4>
					<div id='chat_container'>
						<p class='text-center'>Client: <span class='text-danger'>{{ chat_cname }}</span> Assisted by: <span class='text-danger'>{{ assisted_by }}</span></p>
						<div class="chat_box">
						<ul class='scrollBotUl'>
							<li v-bind:class="[msg.me ? 'me' : 'him']" v-for="msg in chat_msg"  v-cloak>
								{{ msg.msg }}
							</li>
						</ul>
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
					container : {list_view: true, detail_view: false},
					messages: [],
					chat_msg: [],
					chat_cname: '',
					assisted_by:''
				}
			},
			mounted: function(){
				var vm = this;

				vm.getMessage();
			},
			computed:{

			},
			methods: {
				getMessage: function(){
					var vm = this;
					vm.container = {list_view: true, detail_view: false};
					$.ajax({
						url:'ajax_chat.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getChatHistory'},
						success: function(data){
							vm.messages = data;
						},
						error:function(){

						}
					});
				},
				showDetails: function(msg){
					var vm = this;
					vm.container = {list_view: false, detail_view: true};
					$.ajax({
						url:'ajax_chat.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'getConversation', id:msg.id},
						success: function(data){
							vm.chat_msg = data.msgs;
							vm.chat_cname = data.cname;
							vm.assisted_by = data.assisted_by;
						},
						error:function(){

						}
					});
				}
			}

		});
	</script>


<?php require_once '../includes/support/page_tail.php'; ?>