<?php
	class Point extends Crud{
		protected $_table = 'points';
		public function __construct($p=null){
			parent::__construct($p);
		}
		public function getMembers($cid=0){
			$parameters = array();
			if($cid){

				$parameters[] = $cid;

				$q= "Select pgr.*,p.id as point_id, p.name ,p.points, p.amount, pg.name as group_name,pg.supplementary from point_group_rel pgr left join points p on p.id = pgr.point_id left join point_groups pg on pg.id=pgr.pg_id where pgr.company_id = ? and pgr.is_active = 1 order by pgr.pg_id asc";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function getPoints($cid=0,$id=0){
			$parameters = array();
			if($cid){

				$parameters[] = $cid;

				if($id){
					$parameters[] = $id;
					$col = "id";
					if(!is_numeric($id)){
						$col = "name";
					}
					$where_id = " and $col = ? ";
					$method = "first";
				} else {
					$where_id="";
					$method = "results";
				}
				$q= "Select * from points where company_id = ? and is_active = 1 $where_id";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->$method();
				}
			}
		}
		public function getSameUnit($member_id=0,$point_unit =0){
			$parameters = array();
			if($member_id){
				$parameters[] = $point_unit;
				$parameters[] = $member_id;
				$q = "select p.id, p.points from members m left join point_groups pg on pg.id = m.membership_id left join point_group_rel pgl on pgl.pg_id = pg.id left join points p on p.id=pgl.point_id where p.unit_name=? and m.id=? limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getActiveUserPoint($member_id=0,$point_type =0){
			$parameters = array();
			if($member_id){
				$parameters[] = $member_id;
				$parameters[] = $member_id;
				$method = "results";
				$wherePointType ="";
				if($point_type){
					$method = "first";
					$parameters[] = $point_type;
					$wherePointType =" and p.point_id=? ";
				}
				$q= "Select p.*,pp.name as point_name, pg.name as pg_name, pp.unit_name  from user_points p left join points pp on pp.id=p.point_id left join members m on m.id = ? left join point_groups pg on pg.id = m.membership_id where p.member_id = ? $wherePointType and p.is_active= 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->$method();
				}
			}
		}
		private function getCurUserPoint($member_id,$point_type){
			$parameters = array();
			if($member_id){

				$parameters[] = $member_id;
				$parameters[] = $point_type;
				$q= "Select * from user_points where member_id = ? and point_id=? and is_active = 1 limit 1";

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $data->first();
				}
			}
		}
		private function insertUserHistory($from=0,$to=0,$member_id=0,$cid=0,$payment_id=0,$point_type=0,$rem =''){

			if($to && $member_id){
				$his = new User_point_history();
				$his->create(array(
					'from_points' => $from,
					'to_points' => $to,
					'member_id' => $member_id,
					'created' => time(),
					'company_id' => $cid,
					'is_active' => 1,
					'payment_id' => $payment_id,
					'point_id' => $point_type,
					'remarks' => $rem,
				));
			}
		}
		public function updateUserPoint($mem_id=0,User $user=null,$total_all=0,$payment_id=0,$point_type = 0,$init=0,$process = 0,$remarksType=0){
			$parameters = array();
			if($user){
				$remarksArr = ['From sales','From transfer','From Buy/Sell Point'];
				$gain_points = 0;
				if($total_all != 0){
					$point_cls = new Point($point_type);
					$p_amount = $point_cls->data()->amount;
					$p_point =$point_cls->data()->points;
					$gain_points = floor($total_all / $p_amount);
					if($gain_points){
						$gain_points  = $gain_points * $p_point;
					} else {
						return false;
					}
				}
				if($init) $gain_points = $init;
				$operation = "+";
				if($process == 1){
					$gain_points = $init;
					$operation = "-";
				}

				$cur_points = $this->getCurUserPoint($mem_id,$point_type);
				if($cur_points && is_numeric($gain_points)){
					// update
					$parameters[] = $mem_id;
					$parameters[] = $point_type;
					$q= "Update user_points set points = points $operation $gain_points where member_id = ? and point_id = ?";
					$to_points = ($operation == "+") ? $cur_points->points + $gain_points : $cur_points->points - $gain_points;
					$cur = $cur_points->points;

				} else{
					// insert
					$parameters[] = $user->data()->id;
					$parameters[] =$gain_points;
					$parameters[] = time();
					$parameters[] = $user->data()->company_id;
					$parameters[] = 1;
					$parameters[] = $mem_id;
					$parameters[] = $point_type;
					$q= "INSERT INTO `user_points`(`user_id`, `points`, `created`, `company_id`, `is_active`,`member_id`,`point_id`)
 							VALUES (?,?,?,?,?,?,?)";
					$to_points = $gain_points;
					$cur = 0;
				}
				$remarks = isset($remarksArr[$remarksType]) ? $remarksArr[$remarksType] : '';
				$this->insertUserHistory($cur,$to_points,$mem_id,$user->data()->company_id,$payment_id,$point_type,$remarks);

				$data = $this->_db->query($q, $parameters);
				if($data->count()){
					// return the data if exists
					return $gain_points;
				}
			}
		}

			public function countRecord($cid,$m = 0,$s='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($m) {
					$parameters[] = $m;
					$memberWhere  = " and p.member_id = ? ";
				} else {
					$memberWhere  = "";
				}
				if($s) {
					$parameters[] = "%$s%";
					$searchWhere  = " and m.lastname  like ? ";
				} else {
					$searchWhere  = "";
				}

				$q = "Select count(p.id) as cnt from user_points p left join members m  on m.id = p.member_id  where p.company_id=? and p.is_active=1 $memberWhere $searchWhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid, $start, $limit,$m=0,$s='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				if($m) {
					$parameters[] = $m;
					$memberWhere  = " and p.member_id = ? ";
				} else {
					$memberWhere  = "";
				}
				if($s) {
					$parameters[] = "%$s%";
					$searchWhere  = " and m.lastname  like ? ";
				} else {
					$searchWhere  = "";
				}

				$q = "Select p.* ,pg.supplementary, pg.name as pg_name, pp.name as point_name, m.lastname, m.firstname, m.middlename from user_points p left join points pp on pp.id = p.point_id left join members m  on m.id = p.member_id  left join point_groups pg on pg.id=m.membership_id where p.company_id=? and p.is_active=1 $memberWhere $searchWhere order by p.member_id $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
		public function countRecordPointLog($cid=0,$s='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$where = "";
				if($s){
					$parameters[] = "%$s%";
					$where = " and pp.name like ? " ;
				}

				$q = "Select count(p.id) as cnt from point_history p  left join points pp on pp.id = p.point_id where p.company_id=? $where";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record_point_log($cid, $start, $limit,$s='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}
				$where = "";
				if($s){
					$parameters[]= "%$s%";

					$where = " and pp.name like ? " ;
				}

				$q = "Select p.* ,pp.name as point_name, u.lastname, u.firstname, u.middlename from point_history p left join users u  on u.id = p.user_id left join points pp on pp.id = p.point_id  where p.company_id=? and p.is_active=1 $where order by p.created desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}
		public function countRecordUserLog($cid,$m = 0,$s='',$type='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;

				if($m) {
					$parameters[] = $m;
					$memberWhere  = " and p.member_id = ? ";
				} else {
					$memberWhere  = "";
				}
				if($s) {
					$parameters[] = "%$s%";
					$searchWhere  = " and m.lastname like ? ";
				} else {
					$searchWhere  = "";
				}
				if($type) {
					$parameters[] = $type;
					$typeWhere  = " and p.point_id=?";
				} else {
					$typeWhere  = "";
				}

				$q = "Select count(p.id) as cnt from user_points_history p left join members m  on m.id = p.member_id  where p.company_id=? and p.is_active=1 $memberWhere $searchWhere $typeWhere";

				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record_user_log($cid, $start, $limit,$m=0,$s='',$type='') {
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				if($limit) {
					$l = " LIMIT $start,$limit";
				} else {
					$l = '';
				}

				if($m) {
					$parameters[] = $m;
					$memberWhere  = " and p.member_id = ? ";
				} else {
					$memberWhere  = "";
				}
				if($s) {
					$parameters[] = "%$s%";
					$searchWhere  = " and m.lastname like ? ";
				} else {
					$searchWhere  = "";
				}
				if($type) {
					$parameters[] = $type;
					$typeWhere  = " and p.point_id=?";
				} else {
					$typeWhere  = "";
				}
				$q = "Select p.* ,pp.name as point_name, m.lastname, m.firstname, m.middlename from user_points_history p left join points pp on pp.id = p.point_id left join members m  on m.id = p.member_id  where p.company_id=? and p.is_active=1 $memberWhere $searchWhere  $typeWhere order by p.id desc $l";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()) {
					return $data->results();
				}
			}
		}

		public function addMemberGroup($name = '',$user_id=0,$company_id=0){

			if($name && $user_id && $company_id){
				$parameters = array();
				$parameters[] = $name;
				$parameters[] = $company_id;
				$parameters[] = $user_id;
				$parameters[] = time();
				$q = "INSERT INTO `point_groups`(`name`, `company_id`, `is_active`, `user_id`, `created`) VALUES (?,?,1,?,?)";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					return $this->_db->lastInsertedId();
				}
			}
		}
	}
?>