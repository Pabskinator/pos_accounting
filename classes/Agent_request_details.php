<?php
	class Agent_request_details extends Crud{
		protected $_table = 'agent_request_details';
		public function __construct($id=null){
			parent::__construct($id);
		}
		public function getItems($id = 0){

			$parameters = [];

			$parameters[] = $id;

			return $this->select("o.*, i.item_code, i.description,i.barcode")
				->from("agent_request_details o")
				->join("left join items i on i.id = o.item_id")
				->where("o.request_id = ?")
				->get($parameters)
				->all();

		}
		public function deleteItem($id=0){
			$parameters = array();

			if ($id && is_numeric($id)) {

				$parameters[] = $id;

				return $this->from("agent_request_details")
					 ->where("id = ?")
					 ->destroy($parameters);

			}
		}


		public function updateRack($request_id=0,$item_id=0,$rack_id=0){
			$parameters = array();
			if ($request_id && $item_id &&$rack_id ) {

				$parameters[] = $rack_id;
				$parameters[] = $request_id;
				$parameters[] = $item_id;

				$this->updateTable("agent_request_details")
					->setValue("rack_id = ?")
					->where("request_id = ?")
					->where("and item_id = ?");

				return $this->updateQuery($parameters);


			}
		}

		public function isExists($request_id=0,$item_id=0){
			$parameters = array();

			if ($request_id && $item_id ) {

				$parameters[] = $request_id;
				$parameters[] = $item_id;
				$this->where("request_id = ?");
				$this->where("and item_id = ?");
				return $this->select("count(*) as cnt")
						->from("agent_request_details")
						->get($parameters)
						->first();

			}

		}

		public function getRack($request_id=0,$item_id=0){
			$parameters = array();
			if ($request_id && $item_id ) {

				$parameters[] = $request_id;
				$parameters[] = $item_id;

				return $this->select("rack_id")
					->from("agent_request_details")
					->where("request_id = ?")
					->where("and item_id = ?")
					->get($parameters)
					->first();


			}
		}
	}
?>