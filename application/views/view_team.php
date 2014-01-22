<!-- Header -->
<div class="page-header">
	  <h1><?php echo $team['team_name'] ?></h1>
</div>
<!-- Header -->
<div class="team_calendar">
	<?php echo $calendar->generate(); ?>
</div>
<h2>Schedule</h2>
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