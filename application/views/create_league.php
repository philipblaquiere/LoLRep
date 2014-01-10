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
      <?php echo form_input(array('name' => 'leaguename', 'class' => 'form-control', 'placeholder' => 'League Name', 'value' => 'Season Name','disabled' => 'disabled')); ?>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Type', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10"> 
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-primary">
          <input type="radio" name="type" id="Casual">Casual
        </label>
        <label class="btn btn-primary">
          <input type="radio" name="type" id="Competitive">Competitive
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
    <?php echo form_label('League Name', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'leaguename', 'class' => 'form-control', 'placeholder' => 'League Name', 'value' => set_value('leaguename'))); ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Register', "class='btn btn-default'"); ?>
    </div>
  </div>
  <?php echo form_close(); ?>
<!-- Register Content -->