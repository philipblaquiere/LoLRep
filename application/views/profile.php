<div class="profile-header">
	<h1>Basic Profile Content to Display</h1>
</div>
<div class="profile">
	<div class="row">
		<div class="col-md-2">
		</div>
		<div class="col-md-8">
			<ul class="nav nav-pills">
				<li><a  href="#">Recent Matches</a></li>
				<li><a href="#">Upcoming Matches</a></li>
				<li><a href="/Team" id="view-profile-team">Team</a></li>
				<li><a href="/League" id="view-profile-league">League</a></li>
			</ul>
		</div>
	</div>
	<div id="profile-content">
		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-8">
				<table class="table table-condensed">
					<th></th>
					<th>Team a</th>
					<th>Items</th>
					<th>G</th>
					<th>CS</th>
					<th>K/D/A</th>
					<th>K/D/A</th>
					<th>CS</th>
					<th>G</th>
					<th>Items</th>
					<th>Team a</th>
					<th></th>
					<?php for ($i=0; $i < 5; $i++) { ?> 
						<tr>
							<td>t</td>
							<td>Me</td>
							<td>Items</td>
							<td>123</td>
							<td>123</td>
							<td>1/1/1</td>
							<td>1/2/4</td>
							<td>123</td>
							<td>123</td>
							<td>items</td>
							<td>You</td>
							<td>t</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	</div>
</div>