<?php
	class Chat extends Crud {
		protected $_table = 'chats';

		public function __construct($c = null) {
			parent::__construct($c);
		}

		public function getPendingMessage($status = 0) {
			$parameters = array();

			$parameters[] = $status;

			$q = "Select * from chats where status = ? and  assisted_by = 0";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function getHistory($status = 0) {
			$parameters = array();

			$parameters[] = $status;

			$q = "Select * from chats where status = 1 order by id desc";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function getConversation($id=0) {
			$parameters = array();

			$parameters[] = $id;

			$q = "Select cm.*, ct.client_name, u.firstname as ufn, u.lastname as uln from chat_msgs cm left join chats ct on ct.id = cm.chat_id left join users u on u.id = ct.assisted_by where ct.id = ?";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function getMyClient($id=0) {
			$parameters = array();

			$parameters[] = $id;

			$q = "Select cm.*, ct.client_name,ct.last_updated,ct.concern from chat_msgs cm left join chats ct on ct.id = cm.chat_id  where ct.assisted_by = ? and ct.status =0";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->results();
			}
		}
		public function insertChat($id=0,$msg='',$me) {
			$parameters = array();

			$parameters[] = $id;
			$parameters[] = time();
			$parameters[] = $msg;
			$parameters[] = $me;

			$q = "INSERT INTO `chat_msgs`(`chat_id`, `created`, `msg`, `me`) VALUES (?,?,?,?)";

			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return true;
			}
		}
	}