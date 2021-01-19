<?php
	interface PagingInterface
	{
		public function getPageNavigation($page, $total_pages, $limit, $stages);
		public function paginate($cid,$args);
	}


