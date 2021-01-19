<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
		Redirect::to(1);
	}


	function get_nested($array,$child = FALSE,$iischild=''){

		$str = '';

		if (count($array)){
			$iischild .= $child == FALSE ? '' : '-';

			foreach ($array as $item){


				if(isset($item['children']) && count($item['children'])){

					$str .= '<option value="'.$item['id'].'">'.$iischild.$item['name'].'</option>';
					$str .= get_nested($item['children'], true, $iischild);
				} else {
					if($child == false) $iischild='';
					$str .= '<option value="'.$item['id'].'">'.$iischild.($item['name']).'</option>';
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
	$array = array();

	$dt1 = 0;
	$dt2 = 0;
	if (Input::exists()){
		$dt1 = Input::get('gdateStart');
		$dt2 = Input::get('gdateEnd');

	}
	$gsales = new Sales();
	// base on branch
	$branchsales = $gsales->getTotalSalesPerBranch($user->data()->company_id,$dt1,$dt2);
	$saleslist = '';
	$gtable1='<hr><table class="table" style="background:#eee;"><tr><th>Branch</th><th>Total</th></tr>';
	$bbi = 0;
	if($branchsales){
		foreach($branchsales as $bb){
			if($bbi % 2 == 0) $style = "red"; else $style = "blue";

			$saleslist .= "['" . $bb->name ."',".number_format( $bb->saletotal, 2, '.', '').",'$style'],";

			$gtable1 .="<tr><td> ".$bb->name."</td><td><span class='badge'>".number_format( $bb->saletotal, 2)."</span></td></tr>";
			$bbi +=1;
		}
	}
	$gtable1 .= '</table>';
	$saleslist = rtrim($saleslist,",");

	// base on item
	$itemsales = $gsales->getTotalSalesBaseOnItem($user->data()->company_id,$dt1,$dt2);
	$saleslist2 = '';
	$gtable2 ='<hr><table class="table"><tr><th>Item</th><th>Total</th></tr>';
	$bbi = 0;
	if($itemsales){
		foreach($itemsales as $bb){
			if($bbi % 2 == 0) $style = "red"; else $style = "blue";

			$saleslist2 .= "['" . $bb->item_code ."',".number_format( $bb->saletotal, 2, '.', '').",'$style'],";
			$gtable2 .="<tr><td> ".$bb->item_code."</td><td><span class='badge'>".number_format( $bb->saletotal, 2)."</span></td></tr>";
			$bbi +=1;
		}
	}
	$gtable2 .='</table>';
	$saleslist2 = rtrim($saleslist2,",");

	// base on qty
	$itemsalesqty = $gsales->getTotalSalesBaseOnItemQty($user->data()->company_id,$dt1,$dt2);
	$saleslist3 = '';
	$gtable3 = '<hr><table class="table"><tr><th>Item</th><th>Total</th></tr>';
	$bbi = 0;
	if($itemsalesqty){
		foreach($itemsalesqty as $bb){
			if($bbi % 2 == 0) $style = "red"; else $style = "blue";

			$saleslist3 .= "['" . $bb->item_code ."',". $bb->qtytotal.",'$style'],";
			$gtable3 .="<tr><td> ".$bb->item_code."</td><td><span class='badge'>". $bb->qtytotal."</span></td></tr>";
			$bbi +=1;
		}
	}
	$gtable3 .= '</table>';
	$saleslist3 = rtrim($saleslist3,",");

?>


	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Reports </h1>

	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('salesflash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
		}
	?>

		<div class="panel panel-primary">
			<!-- Default panel contents -->
			<div class="panel-heading">Reports</div>

			</div>
		</div>

	</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:70%;' >
			<div class="modal-content"  >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>

		$(function(){



		});

	</script>

	<script type="text/javascript">

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>