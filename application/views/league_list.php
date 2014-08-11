<div class="list-group">
	<ul class="default_list">
		<?php foreach($leagues as $league): ?>
		<li class="list-group-item">
            <h3><a href="<?php echo site_url('leagues/view/' . $league['leagueid']) ?>"><?php echo $league['league_name']?></a></h3>
            <p><?php echo $league['league_type']?></p>
            <p><?php echo isset($league['seasons'][$league['current_season']]['teams']) ? count($league['seasons'][$league['current_season']]['teams']) : 0 ?>/<?php echo $league['max_teams'] ?> teams</p>
	    </li>
		<?php endforeach; ?>
	</ul>
</div>