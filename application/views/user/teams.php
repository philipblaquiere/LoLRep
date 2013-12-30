  <?php 
  	if(!$teams) {?>
  		<div class="page-header">
		  <h1>Uh Oh!</h1>
		  <h3>You aren't part of any team, create one <a href="<?php echo site_url('create_team'); ?>">here</a>!</h3>
		</div>
  	<?php }
  	else {?>
  		<!-- Header -->
		<div class="page-header">
		  <h1>My Teams</h1>
		  <h4>Quick information about teams you are currently part of.</h4>
		</div>
		<!-- Header -->
		<?php foreach($teams as $team):?>
		  	<div class="list-group">
		    <a href="<?php echo site_url('teams/'.$team['teamid'])?>" class="list-group-item">
		      <div class="row ">
		          <div class="col-md-7">
		              <h1 class="list-group-item-text"><?php echo $team['name']?></h1>
		              <p class="list-group-item-text"><?php echo $team['created']?> <?php echo $team['SummonerName']?></p>
		          </div>
		      </div>
		    </a>
		    </div>
		<?php endforeach; 
  	}?>


