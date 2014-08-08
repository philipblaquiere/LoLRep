<!-- Header -->
<div class="page-header">
	  <h1><?php echo $team['team_name'] ?></h1>
	  <?php if(isset($team['leagues']['current_league'])) { ?>
	  	<p>Current League: <a href="<?php echo site_url('leagues/view/' . $team['leagues']['current_league']) ?>" ><?php echo $team['leagues'][$team['leagues']['current_league']]['league_name'] ?></a></p>
	  <?php } ?>
	  <p><?php if(!isset($team['leagues']['current_season']))
	  {?>
	  	Season not started
	  <?php } ?>
	  </p>
</div>
<h2>Roster</h2>
<hr>
<table class="table table-striped table-condensed table-hover">
				<th></th>
				<th>Player Name</th>
				<th>KDA</th>
				<?php foreach($team['players'] as $player):?>
				<tr>
					<td></td>
					<td><a href="<?php echo site_url('players/'.$player['playerid']) ?>"><?php echo $player['player_name'] ?></a></td>
					<td>K/D/A</td>
				</tr>
				<?php endforeach; ?>
			</table>

<hr>

<?php if(isset($team['leagues']['current_season'])) { ?>
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
<?php } ?>

