<!-- Header -->
<div class="page-header">
  <h1>Select Roles</h1>
  <h4>Select at least one role so other teams can recruit you with ease.</h4>
</div>
<!-- Header -->
<div>
  <?php echo validation_errors(); ?>
  <?php echo form_open('market/join', array('class' => 'form-horizontal', 'id' => 'market_signup_form')); ?>
    <div class="form-group">
      <?php echo form_label('Roles', 'role', array('class' => 'col-sm-2 control-label')); ?>
      <div class="col-sm-10">
        <div class="btn-group" data-toggle="buttons">
          <?php foreach ($roles as $roleid => $role_name) { ?>
            <label class="btn btn-primary">
              <input name="role<?php echo $role_name?>" class="form-control" type="checkbox" value="<?php echo $roleid?>"><?php echo $role_name?></input>
            </label>
          <?php } ?>
        </div>
      </div>
    </div>
  <div class="form-group">
  <?php echo form_label('All Roles', 'label', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
    <label class="checkbox-inline">
      <input type="checkbox" name="allrolescheckbox" id="allrolescheckbox" value="nopreference">
      <div id="allrolescheckbox"><small>I can fill for whatever role</small></div>
    </label>
  </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Register', "class='btn btn-default'"); ?>
    </div>
  </div>
</div>
 <?php echo form_close(); ?>

