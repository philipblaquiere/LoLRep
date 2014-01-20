<!-- Header -->
<div class="page-header">
  	<h1>League: <?php echo $league['league_name'] ?></h1>
 	<h4>Teams: <?php echo count($teams['teams']) ?>/<?php echo $league['max_teams'] ?> </h4>
</div>
<!-- Header -->
<div class="list-group">
<?php foreach($teams['teams'] as $team):?>
	<span class="list-group-item">
		<div class="row">
			<div class="col-md-7">
                <a href=""><h2 class="list-group-item-text"><?php echo $team['team_name']?></h2></a>
                <p class="list-group-item-text">Active Since: <?php echo $team['joined']?> </p>
	        </div> 
    	</div>  	
    </span> 
<?php endforeach; ?>
</div>