<div class="player-stats">
	<?php if(isset($stats['kda_average'])) { ?>
		<ul>
			<li>Average KDA : <?php echo $stats['kda_average'] ?></li>
			<li>Average Gold/Minute : <?php echo $stats['gpm_average'] ?></li>
			<li>Average Gold/Game: <?php echo $stats['gpg_average'] ?></li>
			<li>Average CS : <?php echo $stats['cs_average'] ?></li>
			<li>Average CS/Minute : <?php echo $stats['cspm_average'] ?></li>
		</ul>
	<?php } else { ?>
	<p>No stats to display.</p>
	<?php }  ?>
</div>