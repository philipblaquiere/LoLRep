<!-- Header -->
<div class="page-header">
  	<h1><?php echo $league['league_name'] ?></h1>
 	<p>Type: <?php echo $league['league_type'] ?></p>
 	<p>Duration: <?php echo $season['season_duration'] ?> months</p>
 	<?php if($season['start_date'] == NULL) { ?>
 		<p>Season start not announced</p>
 	<?php }
 	else { ?>
 		<p>Starts: <?php echo $season['start_date'] ?></p>
 		<p>Ends: <?php echo $season['end_date'] ?></p>
 	<?php } ?>
 	<?php if(isset($_SESSION['userid']) && $league['ownerid'] == $_SESSION['userid'] && $season['start_date'] == NULL) { ?>
        <?php echo form_open('leagues/start_season/' . $season['seasonid']  , array('class' => 'form-horizontal')); ?>
 			<p><input type="hidden" name="leagueid" value="<?php echo $league['leagueid'] ?>"></p>
 			<p>Start Date: <input name="season_start_date" id="season_start_date" type="text" class="datepicker" value id="season_start_date" /></p>
	    	<p><button type="submit" class="btn btn-primary">Start Season</button></p>
	    <?php echo form_close(); ?>
	<?php } ?>
</div>
<!-- Header -->

<!-- Join League Button -->
<?php if($join_button['display_button']) { ?>
	<a href="<?php echo site_url($join_button['url']) ?>" class="btn btn-default btn-lg btn-block" role="button"><?php echo $join_button['label'] ?></a>
	<hr/>
<?php } ?>

<!-- Join League Button -->
<div class="row">
  <div class="col-md-4">
  	<div class="league-performers">
  		<h2>Performers</h2>
  		<ul class="nav nav-tabs" role="tablist" id="league_performer_tabs">
		  <li class="active"><a href="#kda" role="tab" data-toggle="tab">KDA</a></li>
		  <li><a href="#gpm" role="tab" data-toggle="tab">G/Min</a></li>
		  <li><a href="#gpg" role="tab" data-toggle="tab">G/Game</a></li>
		  <li><a href="#cs" role="tab" data-toggle="tab">CS</a></li>
		  <li><a href="#cspg" role="tab" data-toggle="tab">CS/Min</a></li>
		</ul>
		<div class="tab-content">
			<!-- AVERAGE KDA -->
			<div class="tab-pane fade in active" id="kda">
		  		<?php if($performers) { ?>
		  			<table class="table table-striped table-condensed ">
						<th>Rank</th>
						<th>Player</th>
						<th>Avg. KDA Ratio</th>

						<?php $i=1; foreach($performers['kda_average'] as $playerid => $stat) { ?>
						<tr>
							<td><?php echo $i ?></td>
							<td><a href="<?php echo site_url('players')."/".$playerid ?>"><?php echo $players[$playerid]['player_name'] ?></a></td>
							<td><?php echo $stat ?></td>
						</tr>
					<?php  $i+=1; }  ?>
					</table>
		  		<?php } ?>
			</div>
	  		<!-- AVERAGE GOLD PER MINUTE -->
	  		<div class="tab-pane fade in" id="gpm">
		  		<?php if($performers) { ?>
		  			<table class="table table-striped table-condensed ">
						<th>Rank</th>
						<th>Player</th>
						<th>Avg. Gold/Min</th>

						<?php $i=1; foreach($performers['gpm_average'] as $playerid => $stat) { ?>
						<tr>
							<td><?php echo $i ?></td>
							<td><a href="<?php echo site_url('players')."/".$playerid ?>"><?php echo $players[$playerid]['player_name'] ?></a></td>
							<td><?php echo $stat ?></td>
						</tr>
					<?php  $i+=1; }  ?>
					</table>
		  		<?php } ?>
			</div>
			<!-- AVERAGE GOLD PER GAME -->
			<div class="tab-pane fade in" id="gpg">
		  		<?php if($performers) { ?>
		  			<table class="table table-striped table-condensed ">
						<th>Rank</th>
						<th>Player</th>
						<th>Avg. Gold/Game</th>

						<?php $i=1; foreach($performers['gpg_average'] as $playerid => $stat) { ?>
						<tr>
							<td><?php echo $i ?></td>
							<td><a href="<?php echo site_url('players')."/".$playerid ?>"><?php echo $players[$playerid]['player_name'] ?></a></td>
							<td><?php echo round($stat/1000,1) ?>k</td>
						</tr>
					<?php  $i+=1; }  ?>
					</table>
		  		<?php } ?>
		  	</div>
	  		<!-- AVERAGE CS -->
	  		<div class="tab-pane fade in" id="cs">
		  		<?php if($performers) { ?>
		  			<table class="table table-striped table-condensed ">
						<th>Rank</th>
						<th>Player</th>
						<th>Avg. CS</th>

						<?php $i=1; foreach($performers['cs_average'] as $playerid => $stat) { ?>
						<tr>
							<td><?php echo $i ?></td>
							<td><a href="<?php echo site_url('players')."/".$playerid ?>"><?php echo $players[$playerid]['player_name'] ?></a></td>
							<td><?php echo $stat ?></td>
						</tr>
					<?php  $i+=1; }  ?>
					</table>
		  		<?php } ?>
	  		</div>
	  		<!-- AVERAGE CS PER MIN -->
	  		<div class="tab-pane fade in" id="cspg">
		  		<?php if($performers) { ?>
		  			<table class="table table-striped table-condensed ">
						<th>Rank</th>
						<th>Player</th>
						<th>Avg. CS/Min</th>

						<?php $i=1; foreach($performers['cspm_average'] as $playerid => $stat) { ?>
						<tr>
							<td><?php echo $i ?></td>
							<td><a href="<?php echo site_url('players')."/".$playerid ?>"><?php echo $players[$playerid]['player_name'] ?></a></td>
							<td><?php echo $stat ?></td>
						</tr>
					<?php  $i+=1; }  ?>
					</table>
		  		<?php } ?>
		  	</div>
  		</div>
  	</div>
  </div>
  <div class="col-md-4">
  	<div class="league-teams">
  		<h2>Teams <?php echo count($teams) ?>/<?php echo $league['max_teams'] ?></h2>
		<div class="list-group">
		<?php foreach($teams as $team):?>
			<span class="list-group-item">
				<div class="row">
					<div class="col-md-7">
		                <a href="<?php echo site_url('teams/view/' . $team['teamid']) ?>"><p class="list-group-item-text"><?php echo $team['team_name']?></p></a>
		                <p class="list-group-item-text">Active Since: <?php echo $team['joined']?> </p>
			        </div> 
		    	</div>  	
		    </span> 
		<?php endforeach; ?>
		</div>
  	</div>
  </div>
  <div class="col-md-4">
  	<h2>Latest Results</h2>
  </div>
</div>


<!-- STANDINGS -->
<?php if(!empty($standings)) { ?>
<hr/>
<h2>Standings</h2>

<table class="table table-striped table-condensed ">
	<th>Rank</th>
	<th></th>
	<th>Team</th>
	<th>Wins</th>
	<th>Losses</th>

	<?php foreach($standings as $standing):?>
	<tr>
		<td><?php echo $standing['rank'] ?></td>
		<td></td>
		<td><?php echo $standing['team_name']  ?></td>
		<td><?php echo $standing['wins'] ?></td>
		<td><?php echo $standing['loss']  ?></td>
	</tr>
<?php  endforeach;  ?>
</table>
<?php } ?>

<!-- STANDINGS -->


<!-- SCHEDULE -->
<hr/>
<h2>Schedule</h2>
<table class="table table-striped table-condensed table-hover">
	<th>Team A</th>
	<th></th>
	<th>Team B</th>
	<th>Date</th>
	<th>Winner</th>
<?php if(array_key_exists(0, $schedule)) {
	foreach($schedule as $match):?>
	<tr>
		<td><?php echo $teams[$match['teamaid']]['team_name'] ?></td>
		<td>vs.</td>
		<td><?php echo $teams[$match['teambid']]['team_name'] ?></td>
		<td><?php echo $match['match_date'] ?></td>
		<td><?php if(isset($match['winnerid'])) { echo $teams[$match['winnerid']]['team_name']; } ?></td>
	</tr>
<?php  endforeach; } ?>
</table>
<!-- SCHEDULE -->

<!-- Set Start Date Modal -->
<div class="modal fade" id="season_startdate_modal" tabindex="-1" role="dialog" aria-labelledby="season_startdate_modallabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="season_startdate_modallabel">Double Checking...</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->