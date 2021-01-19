<?php
	class Transfer_point extends Crud{
		protected $_table = 'transfer_points';
		public function __construct($t=null){
			parent::__construct($t);
		}
		public function countRecord($cid,$m=0,$status=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = $m;
				$parameters[] = $m;
				$parameters[] = $status;


				$q = "Select count(t.id) as cnt from transfer_points t where t.company_id=? and (t.member_id_from = ? or t.member_id_to = ? ) and t.status = ?";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function get_transfer($cid, $start, $limit,$m=0,$status=0) {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$parameters[] = $m;
				$parameters[] = $m;
				$parameters[] = $status;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				$q = "Select t.*, m1.lastname as from_name, m2.lastname as to_name, p.name as point_name, p.unit_name from transfer_points t left join members m1 on m1.id = t.member_id_from  left join members m2 on m2.id = t.member_id_to left join points p on p.id=t.point_type  where t.company_id=? and (t.member_id_from = ? or t.member_id_to = ? ) and t.status = ? order by t.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
		public function countPrevTransfer($member_id_from,$member_id_to,$point_type,$point_type_to){
			if($member_id_from && $member_id_to && $point_type && $point_type_to){

			}
		}
		public function transfer_points(User $user,$member_id_to=0,$point_type=0,$point_value=0,$point_type_to=0,$remarks=''){
			// deduct
			$member_id_from = $user->data()->member_id;
			$point = new Point();
			$point->updateUserPoint($member_id_from,$user,0,0,$point_type,$point_value,1,1); // deduct = 1
			$this->create(array(
				'member_id_from' => $member_id_from,
				'member_id_to' => $member_id_to,
				'point_type' => $point_type,
				'point_type_to' => $point_type_to,
				'point_value' => $point_value,
				'remarks' => $remarks,
				'is_active' => 1,
				'created' => time(),
				'company_id' => $user->data()->company_id,
				'status' => 1
			));
		}
	}
