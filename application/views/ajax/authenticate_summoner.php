<!-- Inner AJAX call, populated in register_lol.php -->
<h2>Step 2 : Rename First Rune Page</h2>
<p><h3><small>Follow these steps in order to verify that you're the owner of this account and to complete the registration. Feel free to change it back once the verification is done.</small></h3></p>

<ol>
  <li>Sign into League of Legends</li>
  <li>Click on "view/edit" your summoner profile</li>
  <li>Select the Rune tab</li>
  <li>Select your first Rune Page</li>
  <li>Temporarily rename it by copy pasting the code below (rename by selecting the current name)</li>
  <li>Press save to lock in your changes</li>
  <li>Wait 5-10 seconds and click "Check Rune Page"</li>
  <li>Click "Register" to complete</li>
</ol>
<h3> Verification Code :</h3>

<div class="col-sm-12 well text-center">
    <h2><strong><?php echo $runepagekey ?></strong></h2>
</div>
<div class="col-sm-offset-2 col-sm-10">
  <?php echo form_open('',array('class' => 'form-horizontal padded_10', 'id' => 'rune_page_verification')); ?>
    <input type="submit" name="submit" data-loading-text="Verifying..." value="Check Rune Page" id="rune_page_verification_button" class="btn btn-default pull-right">
  </form>
  <div class="pull-right padded_10" id="rune_page_verification_result">
  </div>
</div>
