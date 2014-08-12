<div class="profile">
	<div class="row">
		<div class="player-links">
			<div class="col-md-2">
				<?php if(isset($player['teams']) && count($player['teams']) > 1){ ?>
				<ul class="nav nav-pills nav-stacked">
					<li><a href="#">All</a></li>
					<?php  foreach($player['teams'] as $teamid) { ?>
					<li><a href="#" data-id="<?php echo $teamid ?>"><?php echo $player['teams_meta'][$teamid]['team_name'] ?></a></li>
				</ul>
				<?php } } ?>
			</div>
			<div class="col-md-10">
					<ul class="nav nav-pills">
						<li><a href="/Player_recent_matches" id="view-player-recent-matches" data-id="<?php echo $player['playerid'] ?>">Recent Matches</a></li>
						<li><a href="/Player_upcoming_matches" id="view-player-upcoming-matches" data-id="<?php echo $player['playerid'] ?>">Upcoming Matches</a></li>
						<li><a href="/Player_stats" id="view-player-stats" data-id="<?php echo $player['playerid'] ?>">Stats</a></li>
						<li><a href="/Player_team" id="view-player-team">Team</a></li>
					</ul>
					<div id="main-content">
					</div>
			</div>
		</div>
		
	</div>
</div>
	