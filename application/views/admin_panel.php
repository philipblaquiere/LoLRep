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
				<input name="name" type="text" value="" placeholder="Season Name"/>
				<input name="startdate" type="text" value="" placeholder="Start Date"/>
				<input name="enddate" type="text" value="" placeholder="End Date"/>
		</td>
		<td><button type="submit">Create Season</button><?php echo form_close(); ?><td>
	</tr>
</table>