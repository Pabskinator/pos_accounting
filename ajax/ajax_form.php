<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");


	if(function_exists($functionName)){
		$functionName();
	}

	function insertForm(){
		$data = Input::get('data');
		$ref_id = Input::get('service_id');
		$ref_table = Input::get('ref_table');

		$form = new Form();
		$c = $form->checker($ref_table,$ref_id);
		if($c){

		} else {
			$form->create(
				[
					'json_data' => $data,
					'ref_id' => $ref_id,
					'ref_name' => $ref_table,
				]
			);
		}


	}

	function getForm(){

		$id = Input::get('id');
		$form = new Form($id);

	}

	function getList(){

		$ref_name = Input::get('ref_name');

		$form = new Form();

		$list = $form->getList($ref_name);

		$arr = [];

		if($list){

			foreach($list as $l){
				$l->decoded = json_decode($l->json_data);
				$l->ref_number = ($l->decoded->ref_number) ? $l->decoded->ref_number : 'NA';
				$arr[]  = $l;
				
			}

		}

		echo json_encode($arr);

	}

