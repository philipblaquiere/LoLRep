<div class="page-header">
  <h1>Select a game</h1>
  <p><h3><small>Click on the image of the game to start the registration process for the game.</small></h3></p>
  <h3><small> Once selected, we'll ask for some information which will allow us to link your game profile to our website!</small></h3>
</div>
                  
<!-- Select Game List -->
<div class="list-group">
  <?php foreach($esports as $esport):?>
    <a href="<?php echo site_url('add_esport/register_' . $esport['abbrv'])?>" class="list-group-item">
      <div class="row ">
          <div class="col-md-7">
              <h1 class="list-group-item-text"><?php echo $esport['esport_name']?></h1>
              <p class="list-group-item-text"><?php echo $esport['esport_name']?> <?php echo $esport['esport_description']?></p>
          </div>
          <div class="col-md-5 text-center">
              <img class="img-responsive"  src="<?php echo $esport['imageurl']?>" >
          </div>
      </div>
    </a>
  <?php endforeach; ?>
</div>
<!-- Select Game List -->

<div >
  <button class="btn btn-default">Not Now</button>  You'll be redirected to a "limited" profile page.
</div>
                         