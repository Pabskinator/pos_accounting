<?php
	class Selling_point extends Crud{

		protected $_table = 'sell_points';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function countRecord($cid) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$q = "Select count(sp.id) as cnt from sell_points sp where sp.company_id = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				$q = "Select sp.*, m1.lastname as from_name , m2.lastname as to_name, pp.name as point_name from sell_points sp left join members as m1 on m1.id=sp.member_id_from  left join members as m2 on m2.id=sp.member_id_to left join points pp on pp.id = sp.point_id where sp.company_id = ? order by sp.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function create_sell_request(User $user,$point_type=0,$point_value=0,$amount=0,$remarks=''){
			// deduct
			$member_id_from = $user->data()->member_id;
			$point = new Point();
			$point->updateUserPoint($member_id_from,$user,0,0,$point_type,$point_value,1,2); // deduct = 1
			$this->create(array(
				'member_id_from' => $member_id_from,
				'point_id' => $point_type,
				'point_value' => $point_value,
				'selling_amount' => $amount,
				'remarks' => $remarks,
				'is_active' => 1,
				'created' => time(),
				'company_id' => $user->data()->company_id,
				'status' => 1
			));
		}
	}