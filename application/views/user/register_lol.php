<div class="page-header">
  <h1>Register - League of Legends</h1>
  <h4>To begin linking your League of Legends account to our website, start by entering your League of Legends summoner name. Only one can be linked to an account at any given time.</h4>
  <h4><small>FYI: We'll never ask you for any League of Legends password.</small></h4>
</div>

<!--<?php echo form_open('',array('class' => 'form-horizontal', 'id' => 'lolSummonerRegistrationForm')); ?>-->
<form class="form-horizontal" id="lolSummonerRegistrationForm">
  <div class="form-group">
    <?php echo form_label('Summoner Name', 'summonername', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <?php echo form_input(array('name' => 'summonername','id' => 'summonername', 'class' => 'form-control', 'placeholder' => 'Summoner Name')); ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Validate', "class='btn btn-default'"); ?>
    </div>
  </div>
</form>

<div id="authenticate_runepage_page">
  
</div>
<!-- Register Content -->