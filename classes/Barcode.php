<?php
	class Barcode extends Crud{
		protected $_table = 'barcode_generator';
		public function __construct($b=null){
			parent::__construct($b);
		}
		public function updateStyle($f,$s,$c,$branch_id=0,$user_id=0){
			if($f && $s && $c){
				$parameters = [];
				$parameters[] = $s;
				$parameters[] = $f;
				$parameters[] = $c;

				if($branch_id){
					$whereBranch = " and branch_id = ? ";
					$parameters[] = $branch_id;
				} else {
					$whereBranch = " and branch_id = 0 ";
				}
				if($user_id){
					$whereUser = " and user_id = ? ";
					$parameters[] = $user_id;
				} else {
					$whereUser = " and user_id = 0 ";
				}
				$q = "Update barcode_generator set styling=? where family=? and company_id=? $whereBranch $whereUser";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
				return false;
			}
		}
		public function get_invoice_format($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				 $q= 'Select * from barcode_generator  where  branch_id= 0 and  is_active=1 and company_id=? and family=\'INVOICE\'';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function get_cr_format($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				 $q= 'Select * from barcode_generator  where  branch_id= 0 and  is_active=1 and company_id=? and family=\'CR\'';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function get_dr_format($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$q= 'Select * from barcode_generator  where  branch_id= 0 and is_active=1 and company_id=? and family=\'DR\'';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function get_format_by_branch($branch_id,$fam){
			$parameters = array();
			if($branch_id){
				$parameters[] =$branch_id;
				$parameters[] =$fam;
				$q= 'Select * from barcode_generator  where  is_active=1 and branch_id=? and family=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function get_format_by_user($user_id,$fam){
			$parameters = array();
			if($user_id){
				$parameters[] =$user_id;
				$parameters[] =$fam;
				$q= 'Select * from barcode_generator  where  is_active=1 and user_id=? and family=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function get_print_layout($c,$l){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$parameters[] =$l;
				$q= 'Select * from print_layout where    is_active=1 and company_id=? and name=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function get_sv_format($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$q= 'Select * from barcode_generator  where  branch_id= 0 and  is_active=1 and company_id=? and family=\'SV\'';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function get_ir_format($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$q= 'Select * from barcode_generator  where  branch_id= 0 and  is_active=1 and company_id=? and family=\'IR\'';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function getFormat($c,$f){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$parameters[] =$f;
				$q= 'Select * from barcode_generator  where  branch_id= 0 and  is_active=1 and company_id=? and family=?';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->first();
				}
			}
		}
		public function getBarcodeFormat($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$q= 'Select * from barcode_generator  where  branch_id= 0 and is_active=1 and company_id=? and family !=\'DR\' and family !=\'INVOICE\' and family !=\'IR\'';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}

		public function getFormats($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$q= 'Select * from barcode_generator  where  branch_id = 0 and is_active=1 and company_id=? ';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}
		public function getFormatsByBranch($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				 $q= 'Select * from barcode_generator  where  branch_id = ? and is_active=1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}
		public function getFormatsByUser($c){
			$parameters = array();
			if($c){
				$parameters[] =$c;
				$q= 'Select * from barcode_generator  where user_id = ? and is_active=1';
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return $e->results();
				}
			}
		}
		public function savePrintFormats($type,$layout,$cid){
			$parameters = array();
			if($type && $layout && $cid){
				$parameters[] =$layout;
				$parameters[] =$type;
				$parameters[] =$cid;
				$q= "update `print_layout` set `layout`=? where branch_id= 0 and  `name`=? and `company_id`=?";
				$e = $this->_db->query($q, $parameters);
				if($e->count()){
					return true;
				}
			}
		}
	}
