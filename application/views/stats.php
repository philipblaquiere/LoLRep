<div class="season-stats">
	<?php if(isset($stats)){ ?>
		<ul class="stat-list">
			<?php foreach ($stats as $stat) { ?>
			<li>
				<div class="stat-label"><?php echo $stat['label'] ?></div>
				<div class="stat-value"><?php echo $stat['value'] ?></div>
			</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
	<p>No stats to display.</p>
	<?php }  ?>
</div>