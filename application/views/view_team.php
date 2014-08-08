<div class="row">
	<div class="col-md-2">
	</div>
	<div class="col-md-10">
		<div class="player-links">
				<ul class="nav nav-pills">
					<li><a href="/Team_recent_matches" id="view-team-recent-matches" data-id="<?php echo $team['teamid'] ?>">Recent Matches</a></li>
					<li><a href="/Team_schedule" id="view-team-upcoming-matches" data-id="<?php echo $team['teamid'] ?>">Upcoming Matches</a></li>
					<li><a href="/Team_roster" id="view-team-roster" data-id="<?php echo $team['teamid'] ?>">Roster</a></li>
					<li><a href="/Team_standings" id="view-team_standings" data-id="<?php echo $team['teamid'] ?>">Standings</a></li>
				</ul>
			</div>
			<div id="main-content">
			</div>
		<div class="page-header">
			  <?php if(isset($team['leagues']['current_league'])) { ?>
			  	<p>Current League: <a href="<?php echo site_url('leagues/view/' . $team['leagues']['current_league']) ?>" ><?php echo $team['leagues'][$team['leagues']['current_league']]['league_name'] ?></a></p>
			  <?php } ?>
			  <p><?php if(!isset($team['leagues']['current_season']))
			  {?>
			  	Season not started
			  <?php } ?>
			  </p>
		</div>
		

		
	</div>
</div>

