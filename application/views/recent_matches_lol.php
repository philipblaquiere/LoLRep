<?php foreach ($matches as $match) { ?>
<div class="row">
	<div class="col-md-2">
	</div>
	<div class="col-md-10">
		<table class="table table-condensed">
			<th></th>
			<th><?php echo $match['teama']['team_name'] ?></th>
			<th>Champion</th>
			<th>G</th>
			<th>CS</th>
			<th>K/D/A</th>
			<th>Spells</th>
			<th></th>
			<?php foreach ($match['teama']['teama_players'] as $playerid => $player) { ?>
				<tr>
					<td> </td>
					<td><?php echo $match['teama']['roster'][$playerid]['player_name'] ?></td>
					<td><img src="<?php echo $player['champion_icon'] ?>" class="img-responsive" alt="Responsive image"></td>
					<td><?php echo round(($match['teama']['teama_players'][$playerid]['stats']['goldEarned']/1000), 1) ?>k</td>
					<td><?php echo $match['teama']['teama_players'][$playerid]['stats']['minionsKilled'] ?></td>
					<td><?php echo array_key_exists('championsKilled', $match['teama']['teama_players'][$playerid]['stats']) ? 
																	$match['teama']['teama_players'][$playerid]['stats']['championsKilled'] : 0 ?>
																	/
						<?php echo array_key_exists('numDeaths', $match['teama']['teama_players'][$playerid]['stats']) ? 
																	$match['teama']['teama_players'][$playerid]['stats']['numDeaths'] : 0 ?>
																	/
						<?php echo array_key_exists('assists', $match['teama']['teama_players'][$playerid]['stats']) ? 
																	$match['teama']['teama_players'][$playerid]['stats']['assists'] : 0 ?>
					<td>
						<img src="<?php echo $player['spell1_icon'] ?>" class="img-responsive" alt="Responsive image"> 
						<img src="<?php echo $player['spell2_icon'] ?>" class="img-responsive" alt="Responsive image">
					</td>
					<td> </td>
				</tr>.
			<?php } ?>
		</table>
		<table class="table table-condensed">
			<th></th>
			<th><?php echo $match['teamb']['team_name'] ?></th>
			<th>Champion</th>
			<th>G</th>
			<th>CS</th>
			<th>K/D/A</th>
			<th>Spells</th>
			<?php foreach ($match['teamb']['teamb_players'] as $playerid => $player) { ?>
				<tr>
					<td> </td>
					<td><?php echo $match['teamb']['roster'][$playerid]['player_name'] ?></td>
					<td><img src="<?php echo $player['champion_icon'] ?>" class="img-responsive" alt="Responsive image"></td>
					<td><?php echo round(($match['teamb']['teamb_players'][$playerid]['stats']['goldEarned']/1000), 1) ?>k</td>
					<td><?php echo $match['teamb']['teamb_players'][$playerid]['stats']['minionsKilled'] ?></td>
					<td><?php echo array_key_exists('championsKilled', $match['teamb']['teamb_players'][$playerid]['stats']) ? 
																	$match['teamb']['teamb_players'][$playerid]['stats']['championsKilled'] : 0 ?>
																	/
						<?php echo array_key_exists('numDeaths', $match['teamb']['teamb_players'][$playerid]['stats']) ? 
																	$match['teamb']['teamb_players'][$playerid]['stats']['numDeaths'] : 0 ?>
																	/
						<?php echo array_key_exists('assists', $match['teamb']['teamb_players'][$playerid]['stats']) ? 
																	$match['teamb']['teamb_players'][$playerid]['stats']['assists'] : 0 ?>
					<td>
						<img src="<?php echo $player['spell1_icon'] ?>" class="img-responsive" alt="Responsive image"> 
						<img src="<?php echo $player['spell2_icon'] ?>" class="img-responsive" alt="Responsive image">
					</td>
					<td> </td>
				</tr>.
			<?php } ?>
		</table>
	</div>
</div>
<?php } ?>