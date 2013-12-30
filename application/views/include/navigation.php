<div class="container">
    <!-- Static navbar -->
    <div class="navbar navbar-default" role="navigation">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo site_url('home'); ?>">Juicy</a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <?php if ($is_logged_in): ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_SESSION['user']['firstname']; ?><b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo site_url('user/select_esport'); ?>">Add ESport</a></li>
                  <li><a href="#">My Profile</a></li>
                  <li><a href="<?php echo site_url('user/create_team'); ?>">Create Teams</a></li>
                  <li><a href="<?php echo site_url('user/teams'); ?>">My Teams</a></li>
                  <li><a href="#">My ESports</a></li>
                </ul>
            </li>
            <li class="active"><a href="#">Link</a></li>
          <?php else: ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo site_url('sign_in'); ?>">Select ESport</a></li>
              <li><a href="#">Another action</a></li>
              <li><a href="#">Something else here</a></li>
              <li class="divider"></li>
              <li class="dropdown-header">Nav header</li>
              <li><a href="#">Separated link</a></li>
              <li><a href="#">One more separated link</a></li>
            </ul>
          </li>
          <?php endif; ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li class="divider-vertical"></li>
          <?php if ($is_logged_in): ?>
          <li><a href="<?php echo site_url('user/sign_out'); ?>">Sign out</a></li>
          <?php else: ?>
          <li><a href="<?php echo site_url('user/register'); ?>">Register</a></li>
          <li class="dropdown">
            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In<strong class="caret"></strong></a>
            <div class="dropdown-menu sign_in_mini">
              <?php echo form_open('user/sign_in', array('class' => 'form-horizontal', 'id' => 'signinform')); ?>
                <div class="form-group">
                  <div class="col-sm-12">
                    <?php echo form_input(array('name' => 'email', 'class' => 'form-control input-sm', 'placeholder' => 'Email' , 'size' => '30')); ?>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-12">
                    <?php echo form_password(array('name' => 'password', 'class' => 'form-control input-sm', 'placeholder' => 'Password', 'size' => '30')); ?>
                  </div>
                </div>
                <a href="<?php echo site_url('user/forgot_password'); ?>">Forgot Password?</a>
                <div class="form-group">
                  <div class="col-sm-5 pull-right">
                    <?php echo form_submit('submit', 'Sign In', "class='btn btn-default btn-sm pull-left'"); ?>
                  </div>
                </div>
              <?php echo form_close(); ?>
            </div>
          </li>
          <?php endif; ?>
          <li><a href="#">About</a></li>
        </ul>
      </div>
    </div><!-- Static navbar -->
<!-- Content -->