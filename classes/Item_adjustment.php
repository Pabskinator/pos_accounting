
<?php 
	class Item_adjustment extends Crud{

		public function getSelectRack($r,$c,$branch){
			$parameters = array();
				if($c){
					// set the company
					$parameters[] = $c;
					$parameters[] = $branch;

					$whererack='';
					if($r){

						$parameters[] = "%$r%";
						$whererack = " and r.rack like ? ";


					}
					$q = "Select r.* from inventories i left join racks r on r.id = i.rack_id  where r.company_id=? and i.branch_id=? and i.qty > 0 $whererack group by i.rack_id";
					$q.= " ORDER BY r.rack asc";
					// submit the query to DB class
					$data = $this->_db->query($q, $parameters);
					if($data->count()){
						// return the data if exists
						return $data->results();
					}
				}
		}
		public function getThumbRack(){

		}
	}
?>