<!-- Header -->
<div class="page-header">
  <h1>Leagues</h1>
  <?php if(!empty($captain_team)) { ?>
  <h4>You're captain of team <?php echo $captain_team['team_name'] ?>, join a league below.</h4>
  <?php } ?>
</div>
<!-- Header -->
	
<?php echo form_input(array('name' => 'search_leagues', 'data-toggle' => 'hide-seek', 'id' => 'search_leagues', 'class' => 'form-control', 'data-list' =>'.default_list', 'placeholder' => 'Search', 'autocomplete' => 'off')); ?>
<br/>
<div class="list-group">
	<ul class="list-group default_list">
		<?php foreach($leagues as $league): ?>
		<li  class="list-group-item">
            <a href="<?php echo site_url('leagues/view/' . $league['leagueid']) ?>"><?php echo $league['league_name']?></a>
	    </li>
		<?php endforeach; ?>
	</ul>
</div>

