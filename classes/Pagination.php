<?php
	class Pagination{
		protected $company_id = 0;
		protected $pageNum = 0;
		protected $paginInterface;
		public function __construct(PagingInterface $pagingInterface){
			$this->paginInterface = $pagingInterface;
		}
		public function paginate(){
			$this->paginInterface->paginate($this->companyId(),$this->pageNum());
		}
		public function setCompanyId($cid=0){
			$this->company_id = $cid;
		}
		public function setPageNum($p=0){
			$this->pageNum = $p;
		}
		private function companyId(){
			return $this->company_id;
		}
		private function pageNum(){
			return $this->pageNum;
		}
	}