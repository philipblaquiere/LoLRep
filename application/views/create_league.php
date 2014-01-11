<!-- Header -->
<div class="page-header">
  <h1>Create League</h1>
  <h4>Fill out the information below to create a league!</h4>
</div>
<!-- Header -->
          
<!-- Register Content -->
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
      <p class="form-control-static"><?php echo date("F j, Y",strtotime($season['registration_start']))?> - <?php echo date("F j, Y",strtotime($season['registration_end']))?></p>
    </div>
  </div>
   <div class="form-group">
    <?php echo form_label('Starts', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <p class="form-control-static"><?php echo date("F j, Y",strtotime($season['startdate']))?></p>
    </div>
  </div>
   <div class="form-group">
    <?php echo form_label('Ends', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <p class="form-control-static"><?php echo date("F j, Y",strtotime($season['enddate']))?></p>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Type', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10"> 
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-primary">
          <input type="radio" name="type" id="singlematch">Single Match
        </label>
        <label class="btn btn-primary">
          <input type="radio" name="type" id="bestofthree">Best-of-three
        </label>
      </div>
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
    <?php echo form_label('League Name', 'leaguename', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'leaguename', 'class' => 'form-control', 'placeholder' => 'League Name', 'value' => set_value('leaguename'))); ?>
      <span class="help-block">Attract teams to your league by coming up with a descriptive name.</span>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Invite-Only', 'name', array('class' => 'col-sm-2 control-label')); ?>
      <div class="col-sm-10">
        <label class="checkbox-inline">
          <input type="checkbox" value="makeprivate">
        </label>
      </div>
  </div>
  <hr>
  <div class="form-group">
    <?php echo form_label('Matches Every', 'name', array('class' => 'col-sm-2 control-label')); ?>
      <div class="col-sm-10">
      <label class="checkbox-inline">
        <input type="checkbox" id="mondaycheckbox" value="Monday">Monday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="tuesdaycheckbox" value="Tuesday">Tuesday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="wednesdaycheckbox" value="Wednesday">Wednesday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="thursdaycheckbox" value="Thursday">Thursday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="fridaycheckbox" value="Friday">Friday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="saturdaycheckbox" value="Saturday">Saturday
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" id="sundaycheckbox" value="Sunday">Sunday
      </label>
    </div>
  </div>
  <div id="mondaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Monday's
      </label>
      <div class="input-append bootstrap-timepicker  col-sm-10 ">
        <input id="mondaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="tuesdaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Tuesday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input id="tuesdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="wednesdaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Wednesday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input id="wednesdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="thursdaytime" class="hidden">
    <div class="form-group">
      <label  class="col-sm-2 control-label">
        Thursday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input id="thursdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="fridaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Friday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input id="fridaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="saturdaytime" class="hidden">
    <div class="form-group">
      <label  class="col-sm-2 control-label">
        Saturday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input id="saturdaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div id="sundaytime" class="hidden">
    <div class="form-group">
      <label class="col-sm-2 control-label">
        Sunday's
      </label>
      <div class="col-sm-10 input-append bootstrap-timepicker">
        <input id="sundaytimepicker" type="text" class="form-control">
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Create League', "class='btn btn-default pull-right'"); ?>
    </div>
  </div>
  <?php echo form_close(); ?>
<!-- Register Content -->