<?php if(empty($matches)) { ?>
<span class="open_sans">No scheduled matches</span>
<?php } ?>

<?php foreach ($matches as $match) { ?>

<div class="lol-match row">
	<div class="col-md-9">
		<table class="table table-condensed">
			<th>Team A</th>
			<th>vs</th>
			<th>Team B</th>
			<th>Date</th>
			<tr>
				<td><?php echo $match['teama']['team_name'] ?></td>
				<td>vs</td>
				<td><?php echo $match['teamb']['team_name'] ?></td>
				<td><?php echo $match['match_date'] ?></td>
			</tr>
		</table>
	</div>
</div>


<?php } ?>