<div class="profile-header">
	<h1>Basic Profile Content to Display</h1>
</div>
<div class="profile-content">
	<div class="col-md-12">
		<ul class="nav nav-pills">
			<li class="active"><a href="#">Recent Matches</a></li>
			<li ><a href="#">Upcoming Matches</a></li>
			<li ><a href="<?php echo site_url('teams/view/'.$_SESSION['user']['league_info']['teamid']); ?>">Team</a></li>
			<li ><a href="<?php echo site_url('view_leagues/view/'.$_SESSION['user']['league_info']['leagueid']); ?>">League</a></li>
		</ul>
		<div id="profile_info">
			Test content
		</div>
	</div>
</div>