<?php if ($system_messages): ?>
<div class="container">
  <div class="" id="system-messages">
    <?php foreach($system_messages as $type => $messages): ?>
      <ul class="<?php echo $type; ?>">
        <?php foreach($messages as $message): ?>
          <li class="message"><?php echo $message; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
