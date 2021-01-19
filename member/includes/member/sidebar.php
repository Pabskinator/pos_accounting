
<header>
	<ul id="slide-out" class="side-nav fixed">

		<li>

			<div class="userView">
				<div class="background">
					<img src="img/1.jpg" style='width:100%;'>
				</div>
				<a href="#!user" ><img class="circle" src="img/default_img.jpg"></a>
				<h4 class='white-text'><?php echo $_SESSION['member_name']; ?></h4>
				<br>
			</div>
		</li>

		<li>
			<a href='members.php' class="waves-effect waves-teal btn-flat">
				<i class="material-icons left">home</i>
				Home
			</a>
		</li>
		<li>
			<a href='progress.php' class="waves-effect waves-teal btn-flat">
				<i class="material-icons left">trending_up</i>
				Progress
			</a>
		</li>
		<li>

			<a href='booking.php' class="waves-effect waves-teal btn-flat">
				<i class="material-icons left">book</i>
				Booking
			</a>
		</li>
		<li>
			<a href='module.php' class="waves-effect waves-teal btn-flat">
				<i class="material-icons left">list</i>
				Workout Module
			</a>
		</li>
		
		<li>
			<a href='#' class="waves-effect waves-teal btn red" id='btnSignOut'>
						Sign Out
			</a>
		</li>

	</ul>
	<nav class="top-nav  grey lighten-4">
		<div class="container">
			<div class="nav-wrapper">
				<a href="#" data-activates="slide-out" class="button-collapse top-nav full hide-on-large-only black-text"><i class="material-icons">menu</i></a>
				<a class="page-title black-text" href='#'><strong>Safehouse Fight Academy</strong></a>
			</div>
		</div>
	</nav>
</header>