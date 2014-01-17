<!-- Header -->
<div class="page-header">
  <h1>Leagues</h1>
</div>
<!-- Header -->
<div name="league_search_parameters_container">
	<div class="input-group col-sm-8">
 		<?php echo validation_errors(); ?>
 		<div class="panel-group " id="accordion">
			<div class="panel panel-default">
				<?php echo form_open('search_league', array('class' => 'form-horizontal', 'id' => 'registrationForm')); ?>
					<div class="panel-heading">
						<h6 class="panel-title">
							<div class="input-group">
								<?php echo form_input(array('name' => 'name', 'class' => 'form-control', 'placeholder' => 'Search', 'value' => set_value('search_text'))); ?>
								<span class="input-group-btn">
									<a data-toggle="collapse" data-parent="#accordion" class="btn btn-default" role="button" href="#collapseOne">
								        <span class="glyphicon glyphicon-filter"></span>
								    </a>
								</span>
							</div>
						</h6>
					</div>
					<div id="collapseOne" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="refine_search_parameters">
								<h4>Refine your search:</h4>
								<div class="input-group">
								<div class="input-group">
									<label class="checkbox-inline">
									        <input type="checkbox" id="leaguenotfullcheckbox" name="leaguenotfullcheckbox" value="leaguenotfullcheckbox">League Not Full
									</label>
								</div>
								<div class="input-group">
									<label class="checkbox-inline">
									        <input type="checkbox" id="leaguenotfullcheckbox" name="leaguenotfullcheckbox" value="leaguenotfullcheckbox">League Not Empty
									</label>
								</div>
								<div class="input-group">
									<label class="checkbox-inline">
									        <input type="checkbox" id="inviteonlycheckbox" name="inviteonlycheckbox" value="inviteonlycheckbox">Invite Only
									</label>
								</div>
								<div class="input-group">
									<label class="checkbox-inline">
										<input type="checkbox" id="mondaycheckbox" name="mondaycheckbox" value="mondaytimepicker">Mondays
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="tuesdaycheckbox" name="tuesdaycheckbox" value="tuesdaytimepicker">Tuesdays
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="wednesdaycheckbox" name="wednesdaycheckbox" value="wednesdaytimepicker">Wednesdays
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="thursdaycheckbox" name="thursdaycheckbox" value="thursdaytimepicker">Thursdays
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="fridaycheckbox" name="fridaycheckbox" value="fridaytimepicker">Fridays
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="saturdaycheckbox" name="saturdaycheckbox" value="saturdaytimepicker">Saturdays
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="sundaycheckbox" name="sundaycheckbox" value="sundaytimepicker">Sundays
									</label>
								</div>
							</div>
						</div>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<br/>
<div class="panel panel-default">
	<div class="panel-heading">Leagues</div>
	<div class="panel-body">
		<div id="league_search_results">
			<div class="list-group">
				<?php foreach($leagues_info as $league_info): ?>
					<div class="list-group-item">
						<div class="row">
							<div class="col-md-10">
				                <p class="list-group-item-text"><a href=""><?php echo $league_info['name']?></a><p>
				                <p class="list-group-item-text"><?php echo $league_info['invite'] == 1 ? "Invite Only" : null ?></p>
				                <p class="list-group-item-text">Games/Week: <?php echo count($league_info['first_games']) ?></p>
				                <p class="list-group-item-text">Teams: <?php echo (array_key_exists($league_info['name'], $league_teams) ? count($league_teams[$league_info['name']]['teamid']) : "0") . "/" . $league_info['max_teams'] ?></p>
				                <?php foreach ($league_info['first_games'] as $first_game) : ?>
					        		<?php echo date("D'\s \- h:i A.",strtotime($first_game)) ?>
					        	<?php endforeach; ?>
					        </div>
					        <div class="col-md-2">
					        	<div class="btn-toolbar " role="toolbar">
				              		<div class="btn-group">
				              			<a href="#" type="button" class="btn btn-default" role="button">
				              				<span class="glyphicon glyphicon-pencil"></span>
				              			</a>
				              			<?php if($league_info['invite'] == 0) { ?>
					              			<?php if(array_key_exists($league_info['name'], $league_teams) && count($league_teams[$league_info['name']]['teamid']) == $league_info['max_teams']) { ?>
						              			<a href="#" type="button" disabled class="btn btn-default" role="button">
						              				Full
						              			</a>
						              		<?php } else { ?>
						              			<a href="#" type="button" class="btn btn-default" role="button">
						              				Join
						              			</a>
				              				<?php } ?>
				              			<?php } ?>
				              		</div>
					            </div>
					        </div>
				    	</div>  	
				    </div> 
				<?php endforeach; ?>
			</div>
			<ul class="pagination">
					<li><a href="#">&laquo;</a></li>
					<li><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">5</a></li>
					<li><a href="#">&raquo;</a></li>
			</ul>
		</div>
	</div>
</div>
