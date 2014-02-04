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
			<table class="match-results-lol">
				<th class="blue-side"></th>
				<th class="left">Team a</th>
				<th class="items-lol">Items</th>
				<th class="blue-side">G</th>
				<th class="left">CS</th>
				<th class="left">K/D/A</th>
				<th class="right">K/D/A</th>
				<th class="right">CS</th>
				<th class="right">G</th>
				<th class="items-lol">Items</th>
				<th class="right">Team a</th>
				<th class="right purple-side"></th>
				<tr>
					<td class="blue-side">t</td>
					<td class="left">Me</td>
					<td class="left">Items</td>
					<td class="left">123</td>
					<td class="left">123</td>
					<td class="left">1/1/1</td>
					<td>1/2/4</td>
					<td>123</td>
					<td>123</td>
					<td>items</td>
					<td>You</td>
					<td class="purple-side">t</td>
				</tr>

			</table>
		</div>
	</div>
</div>