<?php
	include 'ajax_connection.php';

	$functionName = Input::get("functionName");

	$user = new User();
	$company_id = $user->data()->company_id;
	if(function_exists($functionName) && $company_id){
		$functionName($company_id);
	}

	function members($company_id) {
		$search = Input::get('q');
		$my_client = Input::get('my_client');
		$k_type = Input::get('k_type');
		$http_host = $_SERVER['HTTP_HOST'];
		if($http_host == 'aquabest.apollosystems.com.ph'){
			$my_client = 0;
		}
		if($my_client == 1){
			$user = new User();
			$auth = new User_auth();
			$list_excempt = $auth->get_active('user_auth',['ref_tbl','=','wh_order_client']);
			$arrEx  = [];
			if($list_excempt){
				foreach($list_excempt as $ex){
					$arrEx[] = $ex->user_id;
				}
			}

			if(in_array($user->data()->id,$arrEx)){
				$user_id = 0;
			} else {
				$user_id = $user->data()->id;
			}

		} else {
			$user_id = 0;
		}
		$member = new Member();

		$list = $member->memberJSON($company_id,$search,$user_id,$k_type);
		if(!$list) $list = [];
		echo json_encode($list);

	}
	function members2($company_id) {
		$search = Input::get('q');
		$k_type = 0;
		$user_id = 0;

		$member = new Member();

		$list = $member->memberJSON($company_id,$search,$user_id,$k_type);
		if(!$list) $list = [];

		$list = array_slice($list,0,15);
		echo json_encode($list);

	}
	function rack_tags($company_id) {
		$search = Input::get('q');

		$tech = new Rack_tag();
		$list = $tech->rackTags($company_id,$search);
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function technicians($company_id) {
		$search = Input::get('q');
		$tech = new Technician();
		$list = $tech->techJSON($company_id,$search);
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function users($company_id) {
		$search = Input::get('q');
		$u = new User();
		$list = $u->userJSON($company_id,$search);
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function branches($company_id) {
		$search = Input::get('q');
		$u = new Branch();
		$list = $u->branchJSON($company_id,$search);
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function racks($company_id) {
		$search = Input::get('q');
		$branch_id = Input::get('branch_id');
		$user_cur = new User();
		$u = new Rack();
		if(!$branch_id){
			$branch_id = $user_cur->data()->branch_id;
		}
		// tags
		$racktags = new Rack_tag();
		$my_tags = $racktags->get_my_tags($user_cur->data()->id);
		$tagcat = "";
		if($my_tags){

			foreach($my_tags as $m){
				$tagcat .= $m->id . ",";
			}
			$tagcat = rtrim($tagcat,",");
		}

		$list = $u->rackJSON($company_id,$branch_id,$search,$tagcat);
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function racksBranchFilter($company_id){
		$search = Input::get('q');
		$branch_id = Input::get('branch_id');
		$user_cur = new User();
		$u = new Rack();
		if(!$branch_id){
			$branch_id = $user_cur->data()->branch_id;
		}
		$list = $u->rackJSON($company_id,$branch_id,$search,0);
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function allItems($company_id) {
		$item = new Product();
		$cur = new User();
		$list = $item->getItemsAndInventories($cur->data()->branch_id,$company_id);
		if(!$list) $list = [];
		echo json_encode($list);
	}
	function supplierItems($company_id) {

		$cur = new User();
		$supplier_id = Input::get('supplier_id');
		$search =Input::get('search');

		$sup = new Supplier_item();

		$list = $sup->getSupplierItem($company_id,$supplier_id,$search);
		if(!$list) $list = [];

		echo json_encode($list);

	}