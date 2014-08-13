<!-- Header -->
<div class="page-header">
  <h1>Leagues</h1>
  <?php if(!empty($captain_team)) { ?>
  <h4>You're captain of team <?php echo $captain_team['team_name'] ?>, join a league below.</h4>
  <?php } ?>
</div>
<!-- Header -->
	
<?php echo form_input(array('name' => 'league-search-text', 'id' => 'league-search-text', 'class' => 'form-control', 'placeholder' => 'Search', 'autocomplete' => 'off')); ?>
<div class="checkbox">
  <label><input type="checkbox" id="league-not-full-checkbox" name="league-not-full-checkbox" value="league_not_full">Not Full</label>
</div>
<div class="checkbox">
  <label><input type="checkbox" id="league-not-empty-checkbox" name="league-not-empty-checkbox" value="league_not_empty">Not Empty</label>
</div>
<div class="checkbox">
  <label><input type="checkbox" id="league-invite-only-checkbox" name="league-invite-only-checkbox" value="league_invite_only">Invite Only</label>
</div>
<br/>
<div id="league-search-results">
<div>

