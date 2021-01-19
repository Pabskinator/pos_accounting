<?php
	function makeBreadCrumb($l1=''){
		if($l1){
			?>
			<nav aria-label="breadcrumb" >
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="index.php">Home</a></li>
					<li class="breadcrumb-item active" aria-current="page"><?php echo $l1; ?></li>
				</ol>
			</nav>
			<?php
		} 
	}