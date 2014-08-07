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
		<td>Update LoL Items</td>
		<td></td>
		<td><a href="<?php echo site_url('admin/update_lol_items') ?>" role="button">Update Items</a><td>
	</tr>
	<tr>
		<td>Update LoL Spells</td>
		<td></td>
		<td><a href="<?php echo site_url('admin/update_lol_spells') ?>" role="button">Update Spells</a><td>
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