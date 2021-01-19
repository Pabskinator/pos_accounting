<?php $myCo = new Company($user->data()->company_id)?>
<div class='text-center'>
<h1>
	<img width='35' height='35' src='../css/img/logo.png' /> <?php echo $myCo->data()->name ?>
</h1>
	<p>	<?php echo $myCo->data()->address ?></p>
</div>
