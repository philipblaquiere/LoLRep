<div class="page-header">
  <h1><?php echo $team['name'] ?> : Trade</h1>
  <p><h3><small>Trade your players with players on other teams</small></h3></p>
</div>

<h3>Select a player to trade : </h3>
<div class="btn-group" data-toggle="buttons">
  <?php foreach ($team_lol as $summoner) { 
    if($summoner['UserId'] != $_SESSION['user']['UserId']) { ?>
    <label class="btn btn-primary">
      <input type="radio" name="options" value="<?php $summoner['SummonerId']?>" id="option1"><?php echo $summoner['SummonerName']?>
    </label>
  <?php } } ?>
  <?php $summoner['SummonerName']?>
</div>

<h3>Find a team to trade with : </h3>
<?php echo form_open('',array('class' => 'form-horizontal padded_10', 'id' => 'lolteamsearchform')); ?>
  <div class="form-group">
    <?php echo form_label('Team Name', 'teamname', array('class' => 'col-sm-2 control-label')); ?>
    <div class="col-sm-10">
      <div class="input-group">
        <div class="input-group-btn">
          <button type="button" class="btn btn-default dropdown-toggle" id="region" data-toggle="dropdown">Region <span class="caret"></span></button>
          <ul class="dropdown-menu region-list">
            <li><a href="#">NA</a></li>
          </ul>
        </div><!-- /btn-group -->
        <?php echo form_input(array('name' => 'teamname','id' => 'teamname', 'class' => 'form-control', 'placeholder' => 'Team Name')); ?>
      </div><!-- /input-group -->
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-1 col-sm-offset-3">
      <?php echo form_submit('submit', 'Find', "class='btn btn-default'"); ?>
    </div>
    <div class="col-sm-7" id="summoner_validation_error">
    </div>
  </div>
<?php echo form_close(); ?>

<div id="team_lol_search_result">
  <!-- Team Search Result  -->
</div>

