<?php
	include 'ajax_connection.php';


	$functionName = Input::get("functionName");

	$functionName();


	function addLog(){
		$data = json_decode(Input::get('data'));
		$type = Input::get('type');
		$user = new User();
		if($data->number && $data->answered_by && $data->person_calling && $data->remarks){

			$call_log = new Call_log();
			$call_log->create(
				[
					'phone_number' => $data->number,
					'answered_by'=> $data->answered_by,
					'person_calling' => $data->person_calling,
					'remarks' => $data->remarks,
					'technician' => $data->technician,
					'user_id'=> $user->data()->id,
					'type' => $type,
					'created' => date('Y-m-d H:i:s')
				]
			);

			$lastid = $call_log->getInsertedId();

			echo $lastid;

		}


	}

	function getLog(){
			$current_page = Input::get('current_page');
			$user = new User();
			$page = new Pagination(new Call_log());
			$page->setCompanyId($user->data()->company_id);
			$page->setPageNum($current_page);
			$page->paginate();

	}

	function updateCloseTime(){

		$id = Input::get('id');
		$update_close_time = Input::get('update_close_time');

		$log = new Call_log();
		if($id && $update_close_time){
			$log->update(
				['close_time' => strtotime($update_close_time)], $id
			);
			echo "Updated successfully.";
		} else {
			echo "Invalid data";
		}


	}

	function getRemarks(){
		$ref_table= 'call_logs';
		$id = Input::get('id');
		$arr = [];
		if(is_numeric($id)){
			$rem_list = new Remarks_list();
			$user = new User();
			$remarks = $rem_list->getServices($id,$ref_table,$user->data()->company_id);
			if($remarks){
				foreach($remarks as $rem){

					$rem->fullname = ucwords($rem->firstname . " " . $rem->lastname);

					$rem->date = date('m/d/Y H:i A',$rem->created);

					$arr[] = $rem;
				}
			}
		}
		echo json_encode($arr);
	}

	function addRemarks(){
		$remarks = Input::get('remarks');
		$id = Input::get('id');
		$tbl = 'call_logs';
		if($id && $remarks){
			$rem_list = new Remarks_list();
			$user = new User();
			$rem_list->create(
				array(
					'ref_table' => $tbl,
					'ref_id' => $id,
					'remarks' => $remarks,
					'company_id' => $user->data()->company_id,
					'is_active' =>1,
					'user_id' =>$user->data()->id,
					'created' =>time()
				)
			);
			echo "Added successfully.";
		} else {
			echo "Failed to process your request.";
		}
	}