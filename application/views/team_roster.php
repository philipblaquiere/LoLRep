<div class="team_roster">
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
</div>
