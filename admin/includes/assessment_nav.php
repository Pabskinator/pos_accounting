<?php 	if($user->hasPermission('member_m')) { ?>
	<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
		<a class='btn btn-default' href='assessment_list.php' title='Add Assessment'>
			<span class='glyphicon glyphicon-list-alt'></span> <span class='hidden-xs'>Drills</span>
		</a>
		<a class='btn btn-default' href='add_assessment.php' title='Add Assessment'>
			<span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add Drills</span>
		</a>
		<a class='btn btn-default' href='member-util.php' title='For assessment'>
			<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>For assessment</span>
		</a>
		<a class='btn btn-default' href='assess_member.php' title='Assess member'>
			<span class='glyphicon glyphicon-user'></span> <span class='hidden-xs'>Assess Member</span>
		</a>
		<a class='btn btn-default' href='assessment_history.php' title='Assess History'>
			<span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Assessment History</span>
		</a>

	</div>
<?php } ?>