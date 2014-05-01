<!-- Sign In Mini Content -->
<?php echo form_open('user/sign_in', array('class' => 'form-horizontal', 'id' => 'signinminiform')); ?>
  <div class="form-group">
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'email', 'class' => 'form-control', 'placeholder' => 'Email')); ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-10">
      <?php echo form_password(array('name' => 'password1', 'class' => 'form-control', 'placeholder' => 'Password')); ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">Sign In</button>
    </div>
  </div>
</form>
<!-- Sign Mini Content -->