<!-- Inner AJAX call, populated in register_LoL.php -->
<h2>Step 2 : Rename First Rune Page</h2>
<p><h3><small>Follow these steps in order to verify that you're the owner of this account and to complete the registration. Feel free to change it back once the verification is done.</small></h3></p>

<ol>
  <li>Sign into League of Legends</li>
  <li>Click on "view/edit" your summoner profile</li>
  <li>Select the Rune tab</li>
  <li>Select your first Rune Page</li>
  <li>Temporarily rename it by copy pasting the code below (rename by selecting the current name)</li>
  <li>Press save to lock in your changes</li>
  <li>Come back here and click "Verify Account"</li>
</ol>
<h3> Verification Code :</h3>
<?php echo form_open('user/summoner_registration_submit', array('class' => 'form-horizontal padded_10', 'id' => 'summoner_registration_submit')); ?>
  <div class="form-group">
    <div class="col-sm-12 well text-center ">
        <h2><strong><?php echo $runepagekey ?></strong></h2>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?php echo form_submit('submit', 'Verify Account', "class='btn btn-default pull-right'"); ?>
    </div>
  </div>
<?php echo form_close(); ?>
