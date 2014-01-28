<!-- Header -->
<div class="page-header">
  	<h1><?php echo $league['league_name'] ?></h1>
 	<h4>Esport: <?php echo $league['esport_name'] ?></h4>
 	<h4>Type: <?php echo $league['league_type'] ?></h4>
 	<h4>Duration: <?php echo $league['season_duration'] ?> months</h4>
 	<?php if($league['owner_UserId'] == $_SESSION['user']['UserId'] && $league['start_date'] == null ) { ?>
        <h4>Season hasn't started yet <a href="#" class="btn btn-sm btn-success" role="button">Start Season!</a> </h4>
        <h4>Season start date: <?php echo date('l, F d Y', strtotime('next monday')); ?></h4> 
    <?php } ?>
</div>
<!-- Header -->
<h2>Teams <?php echo count($teams['teams']) ?>/<?php echo $league['max_teams'] ?></h2>
<div class="list-group">
<?php foreach($teams['teams'] as $team):?>
	<span class="list-group-item">
		<div class="row">
			<div class="col-md-7">
                <a href="<?php echo site_url('teams/view/' . $team['teamid']) ?>"><h2 class="list-group-item-text"><?php echo $team['team_name']?></h2></a>
                <p class="list-group-item-text">Active Since: <?php echo $team['joined']?> </p>
	        </div> 
    	</div>  	
    </span> 
<?php endforeach; ?>
</div>
<h2>Standings</h2>
<h2>Schedule</h2>
<table class="table table-striped table-condensed table-hover">
	<th>Team A</th>
	<th></th>
	<th>Team B</th>
	<th>Date</th>
	<th>Winner</th>
	<th>Status</th>
<?php if(array_key_exists(0, $schedule)) {
	foreach($schedule as $match):?>
	<tr>
		<td><?php echo $teams['teams'][$match['teamaid']]['team_name'] ?></td>
		<td>vs.</td>
		<td><?php echo $teams['teams'][$match['teambid']]['team_name'] ?></td>
		<td><?php echo $match['match_date'] ?></td>
		<td><?php echo $match['winnerid'] ?></td>
		<td><?php echo $match['status'] ?></td>
	</tr>
<?php  endforeach; } ?>
</table>
