<div class="page-header">
  <h1>Account Registration - League of Legends</h1>
  <h4><small>To begin linking your League of Legends account to our website, start by entering your League of Legends summoner name. Only one can be linked to an account at any given time. FYI: We'll never ask you for any League of Legends password.</small></h4>
</div>

<h2>Step 1 : Enter Summoner Name</h3>
<!-- Ajax Call -> controllers/ajax/authenticate_summoner -->
<?php echo form_open('',array('class' => 'form-horizontal padded_10', 'id' => 'lolSummonerRegistrationForm')); ?>
  <div class="form-group">
    <?php echo form_label('Summoner Name', 'summonername', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <div class="input-group">
        <div class="input-group-btn">
          <button type="button" class="btn btn-default dropdown-toggle" id="region" data-toggle="dropdown">Region <span class="caret"></span></button>
          <ul class="dropdown-menu">
            <li><a href="#">NA</a></li>
          </ul>
        </div><!-- /btn-group -->
        <?php echo form_input(array('name' => 'summonername','id' => 'summonername', 'class' => 'form-control', 'placeholder' => 'Summoner Name')); ?>
      </div><!-- /input-group -->
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3">
      <?php echo form_submit('submit', 'Validate', "class='btn btn-default'"); ?>
    </div>
  </div>
<?php echo form_close(); ?>

<div id="authenticate_runepage_page">
  <!-- Validation Content -->
</div>
