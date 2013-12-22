<!-- Inner AJAX call, populated in register_LoL.php -->
<?php
  //$this->load->model('user_model');
  $summonerinput = $_POST['summonername'];
  echo $summonerinput
    /*if(!$summonerinput) {
      //user didn't enter anything, show eror message and reload.
      $this->system_message_model->set_message('You must enter a summoner name to validate.', MESSAGE_ERROR);
      redirect('user/register_LoL', 'location');
    }
    else {
      //check if summoner exists already
      $summoner = $this->user_model->registered_summoner($summonerinput);

      if(!$summoner) {
        //summoner doesn't exist in db yet. Continue verification
        if(!$_SESSION['runepagekey'])
          $_SESSION['runepagekey'] = $this->user_model->_generate_user_validation_key();
        ?>
          <h3>We need to verify that you're the owner of this account. Follow these steps to complete the registration.</h3>
          <ol>
            <li>Sign into League of Legends</li>
            <li>Click on "view/edit" your summoner profile</li>
            <li>Select the Rune tab</li>
            <li>Select your first Rune Page</li>
            <li>Temporarily rename it by copy pasting the code below (rename by selecting the current name)</li>
            <li>Come back here and click "Verify Account"</li>
          </ol>
          <?php echo form_open('user/summoner_registration_submit', array('class' => 'form-horizontal', 'id' => 'summoner_registration_submit')); ?>
            <p>
              <row>
                <div class="col-sm-3">
                  <h2>Verification Code : </h2>
                </div>
                <div class="col-sm-9 well">
                    <h2><?php echo form_label($_SESSION['runepagekey'], 'runepagekey', array('class' => 'col-sm-2 control-label')); ?></h2>
                </div>
              </row>
            </p>
            <?php echo form_submit('submit', 'Verify Account', "class='btn btn-default'"); ?>
          <?php echo form_close(); ?>
<?php}
      else {
        //summoner already existing return error
      }
    }?>*/



  ?>
  authenticate test
