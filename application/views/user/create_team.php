<!-- Header -->
<div class="page-header">
  <h1>Create a Team</h1>
  <h4>Complete the information below to register an official team</h4>
</div>
<!-- Header -->
          
<!-- Create Team Content -->
<?php echo form_open('user/create_team_submit', array('class' => 'form-horizontal', 'id' => 'createTeamForm')); ?>
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
    <?php echo form_label('Team Name', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'teamname', 'class' => 'form-control', 'placeholder' => 'Team Name (Max 32 characters)' , 'maxlength' => '32')); ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="make_captain" checked>
            Make me team captain (there must a be team captain to participate in games).
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Register', "class='btn btn-default'"); ?>
    </div>
  </div>
<?php echo form_close(); ?>
<!-- Create Team Content -->