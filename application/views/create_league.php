<!-- Header -->
<div class="page-header">
  <h1>Custom League</h1>
  <h4>Fill out the information below to create a league!</h4>
</div>
<!-- Header -->
          
<!-- Create League Content -->
<?php echo validation_errors(); ?>
<?php echo form_open('create_league', array('class' => 'form-horizontal', 'id' => 'registrationForm')); ?>
  <div class="form-group">
    <?php echo form_label('Season', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <p class="form-control-static"><?php echo $season['name'] ?></p>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Registration Period', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <p class="form-control-static">From <?php echo $season['registration_start']?> to <?php echo $season['registration_end'] ?></p>
    </div>
  </div>
   <div class="form-group">
    <?php echo form_label('Starts', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <p class="form-control-static"><?php echo $season['startdate'] ?> at midnight</p>
    </div>
  </div>
   <div class="form-group">
    <?php echo form_label('Ends', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <p class="form-control-static"><?php echo $season['enddate'] ?></p>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('ESport', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <select name="esportid" class="form-control">
        <?php foreach($esports as $esport):?>
          <option value="<?php echo $esport['esportid']?>"><?php echo $esport['name']?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('League Name', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'name', 'class' => 'form-control', 'placeholder' => 'League Name', 'value' => set_value('leaguename'))); ?>
      <span class="help-block">Attract teams to your league by coming up with a descriptive name.</span>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Maximum # of Teams', 'label', array('class' => 'col-sm-2 control-label')); ?>
      <div class="col-sm-10">
        <select class="form-control" name="max_teams">
          <?php
            for ($i = 6; $i <= 32; $i++) { ?>
              <option value="<?php echo $i ?>"><?php echo $i ?></option>
          <?php } ?>
        </select>
      </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Type', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10"> 
      <select name="typeid" class="form-control">
        <?php foreach ($league_types as $key => $league_type): ?>
          <option value="<?php echo $league_type['league_type_id']?>"><?php echo $league_type['description']?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Invite-Only', 'label', array('class' => 'col-sm-2 control-label')); ?>
      <div class="col-sm-10">
        <label class="checkbox-inline">
          <input type="checkbox" name="inviteonlyleaguecheckbox" id="inviteonlyleaguecheckbox" value="inviteonly">
          <div id="inviteonlycheckboxhelper"><small>Only teams that have been invited by the League owner can join.</small></div>
        </label>
      </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Private League', 'label', array('class' => 'col-sm-2 control-label')); ?>
      <div class="col-sm-10">
        <label class="checkbox-inline">
          <input type="checkbox" name="privateleaguecheckbox" id="privateleaguecheckbox" value="private">
          <div id="privateleaguecheckboxhelper"><small>Only invited teams that have joined can see information about the league.</small></div>
        </label>
      </div>
  </div>
  <hr>
  <div class="form-group">
    <?php echo form_label('Matches Every', 'name', array('class' => 'col-sm-2 control-label')); ?>
      <div class="col-sm-10">
      <label class="checkbox-inline">
        <input type="checkbox" id="mondaycheckbox" name="mondaycheckbox" value="mondaytimepicker">Monday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="tuesdaycheckbox" name="tuesdaycheckbox" value="tuesdaytimepicker">Tuesday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="wednesdaycheckbox" name="wednesdaycheckbox" value="wednesdaytimepicker">Wednesday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="thursdaycheckbox" name="thursdaycheckbox" value="thursdaytimepicker">Thursday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="fridaycheckbox" name="fridaycheckbox" value="fridaytimepicker">Friday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="saturdaycheckbox" name="saturdaycheckbox" value="saturdaytimepicker">Saturday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="sundaycheckbox" name="sundaycheckbox" value="sundaytimepicker">Sunday
      </label>
    </div>
  </div>
  <div id="mondaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Monday's
      </label>
      <div class="input-append bootstrap-timepicker  col-sm-10 ">
        <input name="mondaytimepicker" id="mondaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="tuesdaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Tuesday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input name="tuesdaytimepicker" id="tuesdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="wednesdaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Wednesday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input name="wednesdaytimepicker" id="wednesdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="thursdaytime" class="hidden">
    <div class="form-group">
      <label  class="col-sm-2 control-label">
        Thursday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input name="thursdaytimepicker" id="thursdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="fridaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Friday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input name="fridaytimepicker" id="fridaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="saturdaytime" class="hidden">
    <div class="form-group">
      <label  class="col-sm-2 control-label">
        Saturday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input name="saturdaytimepicker" id="saturdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="sundaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Sunday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input name="sundaytimepicker" id="sundaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Create League', "class='btn btn-default pull-right'"); ?>
    </div>
  </div>
  <?php echo form_close(); ?>
<!-- Create League Content -->