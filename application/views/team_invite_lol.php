<div class="page-header">
  <h1><?php echo $team['team_name'] ?> : Recruit</h1>
  <p><h3><small>Invite players to join your team. Write them a message as to why you want them on your team!</small></h3></p>
</div>
<!-- Ajax Call -> controllers/ajax/get_player_details -->
<?php echo form_open('teams/invite/' . $team['teamid'] ,array('class' => 'form-horizontal padded_10')); ?>
  <div class="form-group">
	<?php echo form_label('Summoner Name', 'playername', array('class' => 'col-sm-2 control-label', 'id' => 'playertype'));?>
	<div class="col-sm-10">
      <div class="input-group">
        <div class="input-group-btn">
          <button type="button" class="btn btn-default dropdown-toggle" id="region" data-toggle="dropdown">Region <span class="caret"></span></button>
          <ul class="dropdown-menu region-list">
            <li><a href="#">NA</a></li>
          </ul>
        </div><!-- /btn-group -->
        <input type="text" name="summonerlist" id="summonerlist" class="form-control" value="<?php echo isset($_POST['summonerlist']) ? $_POST['summonerlist'] : '' ?>" placeholder="Summoner Names (comma separated)"/>
      </div><!-- /input-group -->
    </div>
  </div>
  <div class="form-group">
  	<?php echo form_label('Message', 'invite_message', array('class' => 'col-sm-2 control-label'));?>
	<div class="col-sm-10">
		<textarea name="invite_message" maxlength= "140" id="invite_message" class="input-group form-control" value="<?php echo isset($_POST['invite_message']) ? $_POST['invite_message'] : '' ?>" placeholder="Max 140 characters"></textarea>
	</div>
  </div>
  <div class="form-group">
    <div class="col-sm-12 ">
      <?php echo form_submit('submit', 'Invite', "class='btn btn-default pull-right'"); ?>
    </div>
  </div>
<?php echo form_close(); ?>
