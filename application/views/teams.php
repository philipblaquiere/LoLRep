<?php if($invites) { ?>
<div class="page-header">
  <h1>You have been invited!</h1>
  <h4>Below are your invitations to teams, click "Accept" or "Decline" to let the team know of your decision.</h4>
</div>
	<div class="list-group">
	<?php foreach ($invites as $invite) { ?>
		<span class="list-group-item">
			<div class="row">
				<div class="col-md-7">
	                <h2 class="list-group-item-text"><?php echo $invite['team_name']?></h2>
	                <p class="list-group-item-text">Message: <?php echo $invite['message']?> </p>
	                <p class="list-group-item-text">Date: <?php echo $invite['invite_date']?> </p>
	                <p class="list-group-item-text">Status: <?php echo $invite['status']?> </p>
		        </div>  
		        <div class="col-md-5 ">
		        	<div class="btn-toolbar " role="toolbar">
	              		<div class="btn-group">
	              			<a href="<?php echo site_url('invite/accept_invite/' . $invite['inviteid'] . '/' . $invite['esportid'])?>" type="button" class="btn btn-default" role="button">
	              				<span class="glyphicon glyphicon-ok"></span>
	              			</a>
	              			<a href="<?php echo site_url('invite/decline_invite/' . $invite['inviteid'] . '/' . $invite['esportid'])?>" type="button" class="btn btn-default" role="button">
	              				<span class="glyphicon glyphicon-remove"></span>
	              			</a>
	              		</div>
		            </div>
		        </div>
	    	</div>  	
	    </span> 
	<?php } //endforeach ?>
	</div> 
<?php } ?>

<?php 
if(!$teams) {?>
<div class="page-header">
  <h1>Uh Oh!</h1>
  <h3>You aren't part of any team, create one <a href="<?php echo site_url('teams/create'); ?>">here</a>!</h3>
</div>
<?php }
else {?>
	<!-- Header -->
<div class="page-header">
  <h1>My Teams</h1>
  <h4>Quick information about teams you are currently part of.</h4>
</div>
<!-- Header -->
<div class="list-group">
<?php foreach($teams as $team):?>
	<span class="list-group-item">
		<div class="row">
			<div class="col-md-7">
                <a href="<?php echo site_url('teams/view/' . $team['teamid']) ?>"><h2 class="list-group-item-text"><?php echo $team['team_name']?></h2></a>
                <p class="list-group-item-text">Created: <?php echo $team['created']?> Summoner Name: <?php echo $team['player_name']?></p>
	        </div>  
	        <div class="col-md-5 ">
	        	<div class="btn-toolbar " role="toolbar">
              		<div class="btn-group">
              			<a href="#" type="button" class="btn btn-default" role="button">
              				<span class="glyphicon glyphicon-calendar"></span>
              			</a>
              			<a href="#" type="button" class="btn btn-default" role="button">
              				<span class="glyphicon glyphicon-pencil"></span>
              			</a>
              			<a href="#" type="button" class="btn btn-default" role="button">
              				<span class="glyphicon glyphicon-stats"></span>
              			</a>
              			<?php if($_SESSION['user']['UserId'] == $team['captainid']) { 
              			//user is captain, show captain settings?>
              			<a href="<?php echo site_url('teams/invite/' .  $team['teamid']) ?>" type="button" class="btn btn-default" role="button">
              				<span class="glyphicon glyphicon-plus"></span>
              			</a>
              			<a href="<?php echo site_url('trade/lol') ?>" type="button" class="btn btn-default" role="button">
              				<span class="glyphicon glyphicon-transfer"></span>
              			</a>
              			<?php } ?>
              		</div>
	            </div>
	        </div>
    	</div>  	
    </span> 
<?php endforeach; }?>
</div> 
