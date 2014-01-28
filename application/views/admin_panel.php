<div class="page-header">
  <h1>Admin Panel</h1>
  <h4></h4>
</div>
<table class="table table-condensed">
	<tr>
		<td>Update LoL Champions</td>
		<td></td>
		<td><a href="<?php echo site_url('admin/update_lol_champions') ?>" role="button">Update</a><td>
	</tr>
	<tr>
		<td>Ban Player</td>
		<td>
			<?php echo form_open('admin/ban_summoner_byemail', array('class' => 'form-horizontal')); ?>
				<input name="ban_email" type="text" value="" placeholder="Email"/>
				<input name="ban_reason" type="text" value="" placeholder="Reason"/>
		</td>
		<td><button type="submit">Ban</button><?php echo form_close(); ?><td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo form_open('admin/ban_summoner_by_summonername', array('class' => 'form-horizontal')); ?>
				<input name="ban_summonername" type="text" value="" placeholder="Summoner Name"/>
				<input name="ban_reason" type="text" value="" placeholder="Reason"/>
		</td>
		<td><button type="submit">Ban</button><?php echo form_close(); ?><td>
	</tr>
	<tr>
		<td>Create a Season</td>
		<td><?php echo form_open('admin/create_season', array('class' => 'form-horizontal')); ?>
				<input name="name" type="text" placeholder="Season Name"/>
				<input name="registration_start" type="text" class="datepicker" placeholder="Registration Begins" />
				<input name="enddate" type="text" class="datepicker" placeholder="End Date"/>
		</td>
		<td><button type="submit">Create Season</button><?php echo form_close(); ?><td>
	</tr>
	<tr>
		<td>"Open" a Season</td>
		<td><?php echo form_open('admin/open_season', array('class' => 'form-horizontal')); ?>
			<select id="seasonid" class="form-control">
		        <?php foreach($new_seasons as $new_season):?>
		          <option value="<?php echo $new_season['seasonid']?>"><?php echo " Name : " .$new_season['name'] . " Starts : " . $new_season['startdate'] . " Ends : " .$new_season['enddate'] ?></option>
		        <?php endforeach; ?>
	    	</select>
		</td>
		<td><button type="submit">Open Season</button><?php echo form_close(); ?><td>
	</tr>
	<tr>
		<td>Create Schedule</td>
		<td>
			<?php echo form_open('admin/create_matches_for_season', array('class' => 'form-horizontal')); ?>
				<input name="leagueid" type="text" value="" placeholder="League ID"/>
		</td>
		<td><button type="submit">Create Schedule</button><?php echo form_close(); ?><td>
	</tr>
</table>