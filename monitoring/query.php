<?php
	require_once '../core/admininit.php';
	$functionName = Input::get("functionName");
	$functionName();
	function saveOrder(){
		 $arr = Input::get('arr');
		$arr = json_decode($arr);

		if($arr){
			//print_r($arr);
			$update = new FormRequest();
			foreach($arr as $a){
			//	print_r($a);

				$update->updateProcess($a->id,$a->i);

			}
			echo "Form arranged successfully.";
		}
	}
	function deleteForm(){
		$id = Input::get('id');
		if($id){
			//print_r($arr);
			$delete = new FormRequest();
			$delete->update(['is_active' => 0],$id);
			echo "Form deleted successfully.";
		}
	}
	function saveEditedForm(){
		$id = Input::get('id');
		$label = Input::get('label');
		$element_name = Input::get('element_name');
		$choices = Input::get('choices');
		$data_type = Input::get('data_type');
		$is_required = Input::get('is_required');
		$update = new FormRequest();
		$update->update(
				[
					'label' => $label,
					'element_name' => $element_name,
					'choices' => $choices,
					'data_type' => $data_type,
					'is_required' => $is_required,
				],$id);
		echo "Form updated successfully.";
	}
	function cancelRequest(){
		 $id = Input::get('mon_id');
		$mon = new Monitoring($id);
		$mon->update(['from_step'=>$mon->data()->current_step,'current_step' => -1,'from_cancel' => 1],$id);
		echo "Request processed successfully";
	}
	function reSubmitRequest(){
		 $data = Input::get('datajson');
		$data = json_decode($data,true);
		$id = 0;
		$to_step = 0;
		$datacls = new Data();
		$moncls = new Monitoring();
		foreach($data as $d){
			if($d['name'] == 'hid_id'){
				$id = $d['value'];
			} else if($d['name'] == 'from_step'){
				$to_step = $d['value'];
			}
		}
		foreach($data as $d){
			if($d['name'] == 'hid_id'){

			} else if($d['name'] == 'from_step'){

			} else {
				$datacls->updateDetail($id,$d['name'],$d['value']);
			}
		}
		$moncls->update(['current_step' => $to_step],$id);
		echo "Request was resubmitted successfully";
	}

	function updatePosition(){
		$id = Input::get('pid');
		$pos = json_decode(Input::get('position'),true);
		$update = new FormRequest();
		if($id && $pos){
			$pos = implode(',',$pos);
			$update->updatePosition($id,$pos);
			echo "Update successfully";
		} else {
			echo "Update Failed";
		}
	}
	function deleteAttachment(){
		$id = Input::get('id');
		if(is_numeric($id)){
			$attachment = new Attachment();
			$attachment->update(array('is_active'=>0),$id);
			//echo "Deleted successfully";
		} else {
			//echo "Request Failed";
		}
	}
	function deleteReport(){
		$id = Input::get('id');
		if(is_numeric($id)){
			$remarks = new Remarks_list();
			$remarks->update(array('is_active'=>0),$id);
			//echo "Deleted successfully";
		} else {
			//echo "Request Failed";
		}
	}
	function saveReports(){
		$id = Input::get('id');
		$remarks = Input::get('remarks');
		if($id && $remarks){
			$user = new User();
			$remarkcls = new Remarks_list();

			$remarkcls->create(
				array(
					'company_id' => $user->data()->company_id,
					'user_id' => $user->data()->id,
					'ref_table' => 'monitoring',
					'is_active' => 1,
					'ref_id' => $id,
					'remarks' => $remarks,
					'created' => time()
				)
			);
			echo "Reports added successfully.";
		}
	}