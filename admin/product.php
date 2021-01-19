<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')) {
		// redirect to denied page
		Redirect::to(1);
	}
	function get_nested($array,$child = FALSE,$iischild='',$selectedid=0){
		$str = '';
		if (count($array)){
			$iischild .= $child == FALSE ? '' : '-';
			foreach ($array as $item){
				if($selectedid){
					if($selectedid == $item['id']){
						$selected = 'selected';
						$selectedid=0;
					}
				} else {
					$selected = '';
				}
				if(isset($item['children']) && count($item['children'])){

					$str .= '<option value="'.$item['id'].'" '.$selected.'>'.$iischild.$item['name'].'</option>';
					$str .= get_nested($item['children'], true, $iischild,$selectedid);
				} else {
					if($child == false) $iischild='';
					$str .= '<option value="'.$item['id'].'" '.$selected.'>'.$iischild.($item['name']).'</option>';
				}
			}
		}
		return $str;
	}

	function objectToArray ($object) {
		if(!is_object($object) && !is_array($object))
			return $object;

		return array_map('objectToArray', (array) $object);

	}
	function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'children') {
		$m = array();
		foreach ($d as $e) {
			isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
			isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
			$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
		}
		return $m[$r]; // remove [0] if there could be more than one root nodes
	}
	$ccc = new Category();
	$cc = objectToArray($ccc->getCategory($user->data()->company_id));

	require_once 'views/product/product.view.php';

	require_once '../includes/admin/page_tail2.php';