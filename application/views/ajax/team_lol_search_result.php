<div class="btn-group" data-toggle="buttons">
  <?php foreach ($team_lol_result as $summoner) { ?>
    <label class="btn btn-primary">
      <input type="radio" name="options" value="<?php $summoner['SummonerId']?>" id="option1"><?php echo $summoner['SummonerName']?>
    </label>
  <?php } ?>
</div>