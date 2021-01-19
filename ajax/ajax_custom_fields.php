<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");
	$functionName();

	function customStation(){
		$custom_field = new Custom_field();
		$custom_field_details = new Custom_field_details();
		$user = new User();
		$tbl = 'stations';
		$isExists = $custom_field->isExistsTable($tbl,$user->data()->company_id);

		$fields = (Input::get('jsonfields'));

		$name = (isset($fields['name'])) ? $fields['name']:'';
		$f_desc =(isset($fields['f_description'])) ? $fields['f_description']:'';
		$c_desc = (isset($fields['c_description'])) ?1:0;
		$f_region =  (isset($fields['f_region'])) ? $fields['f_region']:'';
		$c_region = (isset($fields['c_region'])) ? 1:0;
		$f_brand =(isset($fields['f_brand'])) ? $fields['f_brand']:'';
		$c_brand = (isset($fields['c_brand'])) ? 1:0;
		$f_package = (isset($fields['f_package'])) ? $fields['f_package']:'';
		$c_package = (isset($fields['c_package'])) ? 1:0;

		$arrayother = array();
		for($i=1;$i<30;$i++){
			$fn = 'field'.$i;
			$ch = 'checkbox'.$i;
			$timestamp = 'ftime'.$i;
			$fid = 'fid'.$i;
			if(isset($fields[$fn]) && !empty($fields[$fn])){
				$is_chk = isset( $fields[$ch]) ? 1:0;
				if(isset($fields[$fid])){
					$uniqid = $fields[$fid];
					$now = $fields[$timestamp];
				}else {
					$uniqid = uniqid();
					$now = time();
				}


				$arrayother[] = array('field-id'=>$uniqid,'field-label' => $fields[$fn],'field-visibility'=>$is_chk,'timestamp'=>$now);
			}
		}
		$otherfield= json_encode($arrayother);

		if($isExists){
			$getid = $custom_field->getIdCustom($tbl,$user->data()->company_id);
			$getid = $getid->id;
			$custom_field->update(array(
				'table_name' => $tbl,
				'other_field' => $otherfield,
				'label_name' => $name
			),$getid);
		} else {
			$custom_field->create(array(
				'table_name' => $tbl,
				'other_field' => $otherfield,
				'label_name' => $name,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
			$getid = $custom_field->getInsertedId();
		}

		if($custom_field_details->isExistsDet('description',$user->data()->company_id,$getid)){

			$custom_field_details->updateDet('description',$f_desc,$c_desc,$getid,$user->data()->company_id);
		} else {
			$custom_field_details->create(array(
				'field_name' => 'description',
				'field_label' =>$f_desc,
				'is_visible' => $c_desc,
				'cf_id' => $getid,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
		}
		if($custom_field_details->isExistsDet('region',$user->data()->company_id,$getid)){
			$custom_field_details->updateDet('region',$f_region,$c_region,$getid,$user->data()->company_id);
		} else {
			$custom_field_details->create(array(
				'field_name' => 'region',
				'field_label' =>$f_region,
				'is_visible' => $c_region,
				'cf_id' => $getid,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
		}
		if($custom_field_details->isExistsDet('brand',$user->data()->company_id,$getid)){
			$custom_field_details->updateDet('brand',$f_brand,$c_brand,$getid,$user->data()->company_id);
		} else {
			$custom_field_details->create(array(
				'field_name' => 'brand',
				'field_label' =>$f_brand,
				'is_visible' => $c_brand,
				'cf_id' => $getid,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
		}
		if($custom_field_details->isExistsDet('package',$user->data()->company_id,$getid)){
			$custom_field_details->updateDet('package',$f_package,$c_package,$getid,$user->data()->company_id);
		} else {
			$custom_field_details->create(array(
				'field_name' => 'package',
				'field_label' =>$f_package,
				'is_visible' => $c_package,
				'cf_id' => $getid,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
		}
		echo "Updated Successfully";
	}

	function customSupplier(){
		$custom_field = new Custom_field();
		$custom_field_details = new Custom_field_details();
		$user = new User();
		$tbl = 'suppliers';
		$isExists = $custom_field->isExistsTable($tbl,$user->data()->company_id);

		$fields = (Input::get('jsonfields'));

		$name = (isset($fields['name'])) ? $fields['name']:'';
		$f_desc =(isset($fields['f_description'])) ? $fields['f_description']:'';
		$c_desc = (isset($fields['c_description'])) ?1:0;

		$arrayother = array();
		for($i=1;$i<30;$i++){
			$fn = 'field'.$i;
			$ch = 'checkbox'.$i;
			$timestamp = 'ftime'.$i;
			$fid = 'fid'.$i;
			if(isset($fields[$fn]) && !empty($fields[$fn])){
				$is_chk = isset( $fields[$ch]) ? 1:0;
				if(isset($fields[$fid])){
					$uniqid = $fields[$fid];
					$now = $fields[$timestamp];
				}else {
					$uniqid = uniqid();
					$now = time();
				}


				$arrayother[] = array('field-id'=>$uniqid,'field-label' => $fields[$fn],'field-visibility'=>$is_chk,'timestamp'=>$now);
			}
		}
		$otherfield= json_encode($arrayother);

		if($isExists){
			$getid = $custom_field->getIdCustom($tbl,$user->data()->company_id);
			$getid = $getid->id;
			$custom_field->update(array(
				'table_name' => $tbl,
				'other_field' => $otherfield,
				'label_name' => $name
			),$getid);
		} else {
			$custom_field->create(array(
				'table_name' => $tbl,
				'other_field' => $otherfield,
				'label_name' => $name,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
			$getid = $custom_field->getInsertedId();
		}

		if($custom_field_details->isExistsDet('description',$user->data()->company_id,$getid)){

			$custom_field_details->updateDet('description',$f_desc,$c_desc,$getid,$user->data()->company_id);
		} else {
			$custom_field_details->create(array(
				'field_name' => 'description',
				'field_label' =>$f_desc,
				'is_visible' => $c_desc,
				'cf_id' => $getid,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
		}

		echo "Updated Successfully";
	}
	function customMember(){
		$custom_field = new Custom_field();
		$custom_field_details = new Custom_field_details();
		$user = new User();
		$tbl = 'members';
		$isExists = $custom_field->isExistsTable($tbl,$user->data()->company_id);

		$fields = (Input::get('jsonfields'));

		$name = (isset($fields['name'])) ? $fields['name']:'';

		$custom_arr = ['address','telephone','cellphone','fax','contact1','contact2','terms','payment_type','credit_limit','tin','remarks','email','member_since','agent','invoice','sales_man','tax_type','member_num','k_type'];

		$arrayother = array();
		for($i=1;$i<30;$i++){
			$fn = 'field'.$i;
			$ch = 'checkbox'.$i;
			$timestamp = 'ftime'.$i;
			$fid = 'fid'.$i;
			if(isset($fields[$fn]) && !empty($fields[$fn])){
				$is_chk = isset( $fields[$ch]) ? 1:0;
				if(isset($fields[$fid])){
					$uniqid = $fields[$fid];
					$now = $fields[$timestamp];
				}else {
					$uniqid = uniqid();
					$now = time();
				}


				$arrayother[] = array('field-id'=>$uniqid,'field-label' => $fields[$fn],'field-visibility'=>$is_chk,'timestamp'=>$now);
			}
		}
		$otherfield= json_encode($arrayother);

		if($isExists){
			$getid = $custom_field->getIdCustom($tbl,$user->data()->company_id);
			$getid = $getid->id;
			$custom_field->update(array(
				'table_name' => $tbl,
				'other_field' => $otherfield,
				'label_name' => $name
			),$getid);
		} else {
			$custom_field->create(array(
				'table_name' => $tbl,
				'other_field' => $otherfield,
				'label_name' => $name,
				'is_active' => 1,
				'company_id' => $user->data()->company_id
			));
			$getid = $custom_field->getInsertedId();
		}

		foreach($custom_arr as $arr){
			$vf = "f_".$arr;
			$vc = "c_".$arr;
			$holderf  =(isset($fields[$vf])) ? $fields[$vf]:'';
			$holderc = (isset($fields[$vc]) && !empty($fields[$vc])) ? 1: 0;

			if($custom_field_details->isExistsDet($arr,$user->data()->company_id,$getid)){

				$custom_field_details->updateDet($arr,$holderf,$holderc,$getid,$user->data()->company_id);
			} else {
				$custom_field_details->create(array(
					'field_name' => $arr,
					'field_label' =>$holderf,
					'is_visible' => $holderc,
					'cf_id' => $getid,
					'is_active' => 1,
					'company_id' => $user->data()->company_id
				));
			}
		}


		echo "Updated Successfully";
	}