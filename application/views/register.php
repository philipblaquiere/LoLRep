<?php
$regions = array(
    'Africa' => DateTimeZone::AFRICA,
    'America' => DateTimeZone::AMERICA,
    'Antarctica' => DateTimeZone::ANTARCTICA,
    'Aisa' => DateTimeZone::ASIA,
    'Atlantic' => DateTimeZone::ATLANTIC,
    'Europe' => DateTimeZone::EUROPE,
    'Indian' => DateTimeZone::INDIAN,
    'Pacific' => DateTimeZone::PACIFIC
);
 
$timezones = array();
foreach ($regions as $name => $mask)
{
    $zones = DateTimeZone::listIdentifiers($mask);
    foreach($zones as $timezone)
    {
    // Lets sample the time there right now
    $time = new DateTime(NULL, new DateTimeZone($timezone));
 
    // Us dumb Americans can't handle millitary time
    $ampm = $time->format('H') > 12 ? ' ('. $time->format('g:i a'). ')' : '';
 
    // Remove region name and add a sample time
    $timezones[$name][$timezone] = substr($timezone, strlen($name) + 1) . ' - ' . $time->format('H:i') . $ampm;
  }
}
 
 
?>


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
    <?php echo form_label('Time Zone', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php
      print '<select class="form-control" name="timezone" >';
      foreach($timezones as $region => $list)
      {
        print '<optgroup label="' . $region . '">' . "\n";
        foreach($list as $timezone => $name)
        {
          print '<option value="' . $timezone . '">' . $name . '</option>' . "\n";
        }
        print '<optgroup>' . "\n";
      }
      print '</select>';
      ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Register', "class='btn btn-default'"); ?>
    </div>
  </div>
  <?php echo form_close(); ?>
<!-- Register Content -->