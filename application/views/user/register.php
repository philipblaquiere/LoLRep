<!-- Header -->
<div class="page-header">
  <h1>Register</h1>
  <h4>This account will be used to login to our website, as well as to link any game you want to play.</h4>
</div>
<!-- Header -->
          
<!-- Register Content -->
<?php echo validation_errors(); ?>
<?php echo form_open('register', array('class' => 'form-horizontal', 'id' => 'registrationForm')); ?>
  <div class="form-group">
    <?php echo form_label('First Name', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'fname', 'class' => 'form-control', 'placeholder' => 'First name', 'value' => set_value('fname'))); ?>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Last Name', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'lname', 'class' => 'form-control', 'placeholder' => 'Last name' , 'value' => set_value('lname'))); ?>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Email', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'email', 'class' => 'form-control', 'placeholder' => 'Email (also your username)', 'value' => set_value('email'))); ?>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Password', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_password(array('name' => 'password1', 'class' => 'form-control', 'placeholder' => 'Password')); ?>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Re-Enter Password', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_password(array('name' => 'password2', 'class' => 'form-control', 'placeholder' => 'Re-Enter Password')); ?>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Country', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <select class="form-control" name="countryid">
        <option <?php echo set_select('countryid', 'error', TRUE); ?>>Select Country</option>
        <option value="1" <?php echo set_select('regionid', '1'); ?>>1</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Province/State', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <select class="form-control" name="provincestateid">
        <option <?php echo set_select('provincestateid', 'error', TRUE); ?> >Select Province/State</option>
        <option value="68" <?php echo set_select('provincestateid', '68'); ?>>68</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Region', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <select class="form-control" name="regionid">
          <option <?php echo set_select('regionid', 'error', TRUE); ?>>Select Region</option>
          <option value="6" <?php echo set_select('regionid', '6'); ?>>6</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Register', "class='btn btn-default'"); ?>
    </div>
  </div>
  <?php echo form_close(); ?>
<!-- Register Content -->