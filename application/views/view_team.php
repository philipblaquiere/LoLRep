<!-- Header -->
<div class="page-header">
	  <h1><?php echo $team['team_name'] ?></h1>
</div>
<!-- Header -->
<div class="team_calendar">
	<?php echo $calendar->generate(); ?>
</div>
<h2>Roster</h2>
<hr>
<table class="table table-striped table-condensed table-hover">
				<th></th>
				<th>Summoner Name</th>
				<th>Rank</th>
				<th>Region</th>
				<th>KDA</th>
				<?php foreach($roster as $player):?>
				<tr>
					<td><?php echo $player['ProfileIconId'] ?></td>
					<td><?php echo $player['SummonerName'] ?></td>
					<td><?php echo $player['rank'] . ' ' . $player['tier'] ?></td>
					<td><?php echo $player['region'] ?></td>
					<td>K/D/A</td>
				</tr>
				<?php endforeach; ?>
			</table>
<h2>Schedule</h2>
<hr>
<div class="panel-group" id="accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Schedule</a>
			</h4>
		</div>
	</div>
	<div id="collapseOne" class="panel-collapse collapse in">
		<div class="panel-body">
			<table class="table table-striped table-condensed table-hover">
				<th>Team A</th>
				<th></th>
				<th>Team B</th>
				<th>Date</th>
				<th>Winner</th>
				<th>Status</th>
				<?php foreach($schedule as $match):?>
				<tr>
					<td><?php echo $teams['teams'][$match['teamaid']]['team_name'] ?></td>
					<td>vs.</td>
					<td><?php echo $teams['teams'][$match['teambid']]['team_name'] ?></td>
					<td><?php echo $match['match_date'] ?></td>
					<td><?php echo $match['winnerid'] ?></td>
					<td><?php echo $match['status'] ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>

