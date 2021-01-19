<?php
	class Member_term extends Crud{
		protected $_table = 'member_adjustments';
		public function __construct($m=null){
			parent::__construct($m);
		}
		public function  memberTermsExist($member_id=0,$item_id=0, $adj=0,$type = 0){
			$parameters = array();

			$parameters[] = $member_id;
			$parameters[] = $item_id;
			$parameters[] = $adj;
			$parameters[] = $type;


			$q = "Select IFNULL(count(id),0) as cnt from member_adjustments where status = 2 and member_id = ? and item_id = ? and `adjustment` = ? and `type` = ?";
			$data = $this->_db->query($q, $parameters);
			if($data->count()) {
				// return the data if exists
				return $data->first();
			}


		}
		public function countRecord($cid,$search='',$member_id=0,$status = 0,$sales_type=[],$branch_id=0,$user_id=0){
			$parameters = array();
			if($cid) {
				$parameters[] = $cid;
				$likewhere = "";
				$whereMember = "";
				$whereStatus = "";
				$wheresalestype="";
				$whereBranch = "";
				$whereUser = "";
				if($search) {
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (mm.lastname like ? or mm.firstname like ? or mm.middlename like ? or i.item_code like ? or i.description like ? ) ";
				}
				if($member_id){
					$parameters[] = $member_id;
					$whereMember = " and m.member_id = ? ";
				}
				if($status){
					$parameters[] = $status;
					$whereStatus = " and m.status = ? ";
				}
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and mm.salestype in ($tempsalestype)";
				}
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and m.branch_id = ? ";
				}
				if($user_id){

					$user_id = (int) $user_id;
					$whereUser = " and CONCAT( ',', mm.agent_id , ',' ) LIKE '%,$user_id,%'";

				}

				$q = "Select IFNULL(count(m.id),0) as cnt from member_adjustments m left join members mm on mm.id=m.member_id left join items i on i.id = m.item_id where m.company_id=? $likewhere $whereMember $whereStatus $wheresalestype $whereBranch $whereUser";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}

		public function get_record($cid,$start,$limit,$search='',$member_id=0,$status = 0,$sales_type=[],$branch_id=0,$user_id=0){
			$parameters = array();
			if($cid){
				$parameters[] = $cid;
				$likewhere = "";
				$whereMember = "";
				$whereStatus = "";
				$wheresalestype="";
				$whereBranch = "";
				$whereUser = "";
				if($user_id){
					$user_id = (int) $user_id;
					$whereUser = " and CONCAT( ',', mm.agent_id , ',' ) LIKE '%,$user_id,%'";

				}
				if($limit){
					$l = " LIMIT $start,$limit";
				} else {
					$l='';
				}
				if($search){
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";
					$parameters[] = "%$search%";

					$likewhere = " and (mm.lastname like ? or mm.firstname like ? or mm.middlename like ? or i.item_code like ? or i.description like ? ) ";
				}
				if($member_id){
					$parameters[] = $member_id;
					$whereMember = " and m.member_id = ? ";
				}
				if($status){
					$parameters[] = $status;
					$whereStatus = " and m.status = ? ";
				}
				if ($sales_type){
					$tempsalestype = "";

					foreach($sales_type as $ca){
						$parameters[] = $ca;
						$tempsalestype .= "?,";
					}
					$tempsalestype = rtrim($tempsalestype,',');
					$wheresalestype = " and mm.salestype in ($tempsalestype)";
				}
				if($branch_id){
					$parameters[] = $branch_id;
					$whereBranch = " and m.branch_id = ? ";
				}
				$q= "Select i.item_code,ip.adjustment as branch_adjustment, i.description, u.lastname as uln, u.firstname as ufn, u.middlename as umn, mm.lastname,mm.firstname, mm.middlename, m.* from member_adjustments m left join members mm on mm.id=m.member_id left join users u on u.id=m.user_id left join items i on i.id = m.item_id left join  ( select item_id, adjustment from item_price_adjustment where branch_id=$branch_id) ip on  i.id = ip.item_id where m.company_id=?  $likewhere  $whereMember $whereStatus $wheresalestype $whereBranch $whereUser order by m.member_id $l  ";
				$data = $this->_db->query($q, $parameters);
				// return results if there is any
				if($data->count()){
					return $data->results();
				}
			}
		}
		public function getAdjustmentMember($member_id= 0,$item_id=0){
			$parameters = array();
			if($member_id && $item_id) {

				$parameters[] = $member_id;
				$parameters[] = $item_id;

				$q = "Select * from member_adjustments where member_id = ? and item_id = ? and status = 2 order by id desc limit 1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->first();
				}
			}
		}
		public function getAdjustment($member_id= 0,$item_id=0){
			$parameters = array();
			if($member_id && $item_id) {

				$parameters[] = $member_id;
				$parameters[] = $item_id;

				$q = "Select * from member_adjustments
					where (member_id = ? or member_id = -1) and item_id = ? and status = 2 order by id desc";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function updateSingleUseTerms($member_id= 0,$item_id=0){
			$parameters = array();
			if($member_id && $item_id) {
				$parameters[] = $member_id;
				$parameters[] = $item_id;


				$q = "update member_adjustments set status = 4 where member_id = ? and item_id = ? and status = 2 and transaction_type=1";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return $data->results();
				}
			}
		}
		public function updateSameTypAndQty($member_id= 0,$item_id=0,$qty = 0,$type=0){
			$parameters = array();
			if($member_id && $item_id && $qty && $type) {
				$parameters[] = $member_id;
				$parameters[] = $item_id;
				$parameters[] = $qty;
				$parameters[] = $type;


				$q = "Update `member_adjustments` set `status` = 3 where `member_id` = ? and `item_id` = ? and `qty` = ? and `type` = ? and `status` = 2 and `transaction_type` = 0";
				$data = $this->_db->query($q, $parameters);
				if($data->count()) {
					// return the data if exists
					return true;
				}
				return false;
			}
		}

	} // end class
?>