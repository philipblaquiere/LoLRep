<!-- Header -->
<div class="page-header">
  <h1>Leagues</h1>
  <h4>You're captain of team <?php echo $captain_team['team_name'] ?>, join a league below.</h4>
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
									<label class="checkbox-inline"><input type="checkbox" id="leaguenotfullcheckbox" name="leaguenotfullcheckbox" value="leaguenotfullcheckbox">League Not Full</label>
								</div>
								<div class="input-group">
									<label class="checkbox-inline"><input type="checkbox" id="leaguenotfullcheckbox" name="leaguenotfullcheckbox" value="leaguenotfullcheckbox">League Not Empty</label>
								</div>
								<div class="input-group">
									<label class="checkbox-inline"><input type="checkbox" id="inviteonlycheckbox" name="inviteonlycheckbox" value="inviteonlycheckbox">I can Join</label>
								</div>
								<div class="input-group">
									<label class="checkbox-inline"><input type="checkbox" id="inviteonlycheckbox" name="inviteonlycheckbox" value="inviteonlycheckbox">Invite Only</label>
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
								<br/>
								<div class="form-group">
								    <div class="col-sm-2 input-append bootstrap-timepicker">
								    	<input name="leaguestarttime" value="12:00 PM"  type="text" class="form-control timepicker">
								    </div>
								    <div class="col-sm-2 input-append bootstrap-timepicker">
								    	<input name="leagueendtime" value="12:00 PM"  type="text" class="form-control timepicker">
								    </div>
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
		<div class="list-group">
			<div id="league_search_results">
				<?php if(count($leagues_info) > $max_league_count) { 
					$num_pages = ceil((count($leagues_info)/ $max_league_count));
					for ($i = 1; $i <= $num_pages; $i++) { } ?>
					<ul class="pagination">
						<li><a href="#">&laquo;</a></li>
							<?php for ($j = 1; $j <= $num_pages; $j++) { ?>
										<li><a href="#"><?php echo $j ?></a></li>
							<?php } ?>
						<li><a href="#">&raquo;</a></li>	
					</ul>
				<?php } 
				else { 
				  foreach($leagues_info as $league_info): ?>
					<div class="list-group-item">
						<div class="row">
							<div class="col-md-10">
				                <p class="list-group-item-text"><a href="<?php echo site_url('view_leagues/view/' . $league_info['leagueid']) ?>"><?php echo $league_info['league_name']?></a><p>
				                <p class="list-group-item-text"><?php echo $league_info['invite'] == 1 ? "Invite Only" : null ?></p>
				                <p class="list-group-item-text">Games/Week: <?php echo count($league_info['first_matches']) ?></p>
				                <p class="list-group-item-text">Teams: <?php echo $league_info['num_teams'] . "/" . $league_info['max_teams'] ?></p>
				                <p class="list-group-item-text">
				                	<?php foreach ($league_info['first_matches'] as $first_game) : ?>
					        			<?php echo date("D'\s \- h:i A.",strtotime($first_game)) ?>
					        		<?php endforeach; ?></p>
					        	<p class="list-group-item-text">Tooltip: <?php echo $league_info['join_status_tooltip'] ?></p>
					        </div>
					        <div class="col-md-2">
					        	<div class="btn-toolbar " role="toolbar">
				              		<div class="btn-group">
				              			<a href="#" type="button" class="btn btn-default" role="button">
				              				<span class="glyphicon glyphicon-pencil"></span>
				              			</a>
				              			<?php if($league_info['can_join']) { ?>
					              			<a href="<?php echo site_url('leagues/join/' . $league_info['leagueid']) ?>" type="button" class="btn btn-default" role="button">
					              				<?php echo $league_info['join_status'] ?>
					              			</a>
				              			<?php }
				              			else { ?>
					              			<a type="button" disabled class="btn btn-default" role="button">
					              				<?php echo $league_info['join_status'] ?>
					              			</a>
				              			<?php } ?>
				              		</div>
					            </div>
					        </div>
				    	</div>   	
				    </div> 
				<?php endforeach; 
				} ?>
			</div>
		</div>
	</div>
</div>

