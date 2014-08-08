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
<h2>Teams <?php echo count($teams['teams']) ?>/<?php echo $league['max_teams'] ?></h2>
<div class="list-group">
<?php foreach($teams['teams'] as $team):?>
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