<div class="page-header">
	<h1>Market</h1>
	<h3>Look for potential teammates and recruit them</h3>
</div>

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
							</div>
						</div>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>

