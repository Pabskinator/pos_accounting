<?php
	require_once '../core/admininit.php';
	$functionName = Input::get("functionName");
	$functionName();
	function acceptChat(){
		$chat_id = Input::get('chat_id');

		$user = new User();
		if($chat_id){
			$chat = new Chat($chat_id);
			$chat->update(['assisted_by'=> $user->data()->id],$chat_id);
			$name = ucwords($user->data()->firstname);
			$chat->insertChat($chat_id,"Hi! Thank you for waiting. I'm $name. How may I help you today?",1);

		}
	}
	function closeChat(){
		$chat_id = Input::get('id');
		$chat = new Chat();
		$user = new User();
		if($chat_id){
			$chat->update(['status'=> 1],$chat_id);
			$chat->insertChat($chat_id,"Thank you for contacting us. Have a nice day.",1);
		}
	}
	function getPendingReq(){
		$chat = new Chat();
		$chats = $chat->getPendingMessage(0);
		$arr = [];

		if($chats){
			foreach($chats as $c){
				$c->created = date('F d, Y H:i:s A',$c->created);
				$arr[]= $c;
			}
		}
		echo json_encode($arr);
	}

	function getMyClient(){
		$chat = new Chat();
		$user = new User();
		$chats = $chat->getMyClient($user->data()->id);
		$arr = [];
		$arr_all = [];
		if($chats){
			$main = [];
			$arrmsg = [];
			$arrlastupdate = [];
			$arrconcern = [];
			foreach($chats as $c){
				$main[$c->chat_id] = $c->client_name;
				$arrlastupdate[$c->chat_id] = $c->last_updated;
				$arrconcern[$c->chat_id] = $c->concern;
				$me = ($c->me) ? true : false;
				$arrmsg[$c->chat_id][] = ['me' => $me, 'msg' => $c->msg, 'created' => date('m/d/Y H:s:i A',$c->created),'read' => true];
			}
			$now = time();
			foreach($main as $m => $i){
				$arr['id'] = 'ch'. $m;
				$arr['cid'] = $m;
				$arr['name'] = $i;
				$arr['active'] = true;
				$arr['alive'] = true;
				$lastupdate = isset($arrlastupdate[$m]) ? $arrlastupdate[$m] : 0;
				$arrconcern = isset($arrconcern[$m]) ? $arrconcern[$m] : '';
				$lastupdate = (int) $lastupdate;
				$diff = $now - $lastupdate;
				if($diff > 60){
					$arr['alive'] = false;
				}
				$arr['concern'] = $arrconcern;
				$arr['conversations'] = (isset($arrmsg[$m]) && $arrmsg[$m]) ? $arrmsg[$m] : [];
				$arr_all[] = $arr;
			}
		}
		echo json_encode($arr_all);
	}
	function insertMessage(){
		$msg = Input::get('msg');
		$id = Input::get('id');
		$chat = new Chat();
		if($msg && $id){
			$chat->insertChat($id,$msg,1);
		}
		echo "[]";
	}
	function getChatHistory(){
		$chat = new Chat();
		$chats = $chat->getHistory(1);
		$arr = [];

		if($chats){
			foreach($chats as $c){
				$c->created = date('F d, Y H:i:s A',$c->created);
				$arr[]= $c;
			}
		}
		echo json_encode($arr);
	}
	function getConversation(){
		$chat = new Chat();
		$id = Input::get('id');
		$chats = $chat->getConversation($id);

		$arrmsg = [];
		$cname = "";
		$assistedby="";
		if($chats){

			foreach($chats as $c){
				$cname = $c->client_name;
				$assistedby = ucwords($c->ufn . " " . $c->uln);
				$me = ($c->me) ? true : false;
				$arrmsg[] = ['me' => $me, 'msg' => $c->msg, 'created' => date('m/d/Y H:s:i A',$c->created),'read' => true];
			}


		}
		echo json_encode(['msgs' => $arrmsg,'cname' => $cname,'assisted_by' => $assistedby]);
	}