<!-- Header -->
<div class="page-header">
  <h1>Sign In</h1>
  <h4>Sign in to see your upcoming games, stats, and more!.</h4>
</div>
<!-- Header -->

<!-- Sign In Content -->
<?php echo form_open('user/sign_in', array('class' => 'form-horizontal', 'id' => 'signinform')); ?>
  <div class="form-group">
    <?php echo form_label('Email', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'email', 'class' => 'form-control', 'placeholder' => 'Email')); ?>
    </div>
  </div>
  <div class="form-group">
    <?php echo form_label('Password', 'name', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
       <?php echo form_password(array('name' => 'password', 'class' => 'form-control', 'placeholder' => 'Password')); ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <div class="checkbox">
        <label>
          <input type="checkbox"> Remember me
        </label>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <label>
        <h4><small>No account? Register <a href="<?php echo "index.php?/RegisterNew"?>">here</a></small></h4>
      </label>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Sign In', "class='btn btn-default'"); ?>
    </div>
  </div>
<?php echo form_close(); ?>
<!-- Sign In Content -->