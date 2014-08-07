<?php if(empty($matches)) { ?>
<span class="open_sans">No matches have been played</span>
<?php } ?>
<?php foreach ($matches as $match) { ?>

<div class="lol-match row">
	<div class="col-md-9">
		<table class="table table-condensed">
			<th class="col-md-1"/>
			<th class="col-md-3"><?php echo $match['teama']['team_name'] ?><?php if($match['teama']['teamaid'] == $match['winnerid']){?>*<?php } ?></th>
			<th>Spells</th>
			<th>Items</th>
			<th>K/D/A</th>
			<th>G</th>
			<th>CS</th>
			<th></th>
			<?php foreach ($match['teama']['teama_players'] as $playerid => $player) { ?>
				<tr>
					<td>
						<div class="lol-match-item-group">
							<div class="lol-match-player-level" >
								<?php echo $player['stats']['level'] ?>
							</div>
						
							<div class="lol-match-item-group lol-match-icon">
								<img src="<?php echo $player['champion_icon'] ?>" class="img-responsive" alt="Responsive image">
							</div>
						</div>
					</td>
					<td >
						<div class="lol-match-player-name">
							<strong><?php echo $match['teama']['roster'][$playerid]['player_name'] ?></strong>
						</div>
					</td>
					<td>
						<div class="lol-match-item-group">
							<div class="lol-match-icon">
								<img src="<?php echo $player['spell1_icon'] ?>" class="img-responsive" alt="Responsive image"> 
							</div>
							<div class="lol-match-icon">
								<img src="<?php echo $player['spell2_icon'] ?>" class="img-responsive" alt="Responsive image">
							</div>
						</div>
					</td>
					<td><?php for ($i=0; $i < 7; $i++) { ?> 
							<div class="lol-match-icon">
								<?php if($player['stats']['item'.$i] != "0") { ?>
									<img src="<?php echo $match['teama']['teama_players'][$playerid]['stats']['item'.$i] ?>" class="img-responsive" alt="Responsive image">
								<?php } ?>
							</div> 
						<?php } ?>
					</td>
					<td><?php echo array_key_exists('championsKilled', $player['stats']) ? 
																	$player['stats']['championsKilled'] : 0 ?>
																	/
						<?php echo array_key_exists('numDeaths', $player['stats']) ? 
																	$player['stats']['numDeaths'] : 0 ?>
																	/
						<?php echo array_key_exists('assists', $player['stats']) ? 
																	$player['stats']['assists'] : 0 ?>
					
					</td>
					<td><?php echo round(($player['stats']['goldEarned']/1000), 1) ?>k</td>
					<td><?php echo $player['stats']['minionsKilled'] ?></td>
					<td/>
				</tr>
			<?php } ?>
		</table>
		<table class="table table-condensed">
			<th class="col-md-1"/>
			<th class="col-md-3"><?php echo $match['teamb']['team_name'] ?><?php if($match['teamb']['teambid'] == $match['winnerid']){?>*<?php } ?></th>
			<th>Spells</th>
			<th>Items</th>
			<th>K/D/A</th>
			<th>G</th>
			<th>CS</th>
			<?php foreach ($match['teamb']['teamb_players'] as $playerid => $player) { ?>
				<tr>
					<td>
						<div class="lol-match-item-group">
							<div class="lol-match-player-level" >
								<?php echo $player['stats']['level'] ?>
							</div>
							<div class="lol-match-item-group lol-match-icon">
								<img src="<?php echo $player['champion_icon'] ?>" class="img-responsive" alt="Responsive image">
							</div>
						</div>
					</td>
					<td><strong><?php echo $match['teamb']['roster'][$playerid]['player_name'] ?></strong></td>
					<td>
						<div class="lol-match-item-group">
							<div class="lol-match-icon">
								<img src="<?php echo $player['spell1_icon'] ?>" class="img-responsive" alt="Responsive image"> 
							</div>
							<div class="lol-match-icon">
								<img src="<?php echo $player['spell2_icon'] ?>" class="img-responsive" alt="Responsive image">
							</div>
						</div>
					</td>
					<td>
						<div class="lol-match-item-group">
						<?php for ($i=0; $i < 7; $i++) { ?> 
							<div class="lol-match-icon">
								<?php if($player['stats']['item'.$i] != "0") { ?>
								<img src="<?php echo $player['stats']['item'.$i] ?>" class="img-responsive" alt="Responsive image">
							<?php } ?>
							</div> 
						<?php } ?>
					</td>
					<td><?php echo array_key_exists('championsKilled', $player['stats']) ? 
																	$player['stats']['championsKilled'] : 0 ?>
																	/
						<?php echo array_key_exists('numDeaths', $player['stats']) ? 
																	$player['stats']['numDeaths'] : 0 ?>
																	/
						<?php echo array_key_exists('assists', $player['stats']) ? 
																	$player['stats']['assists'] : 0 ?>
					</td>
					<td><?php echo round(($player['stats']['goldEarned']/1000), 1) ?>k</td>
					<td><?php echo $player['stats']['minionsKilled'] ?></td>
					<td/>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>
<?php } ?>