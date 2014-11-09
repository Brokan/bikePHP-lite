<?php 
/**
 * Default layout template file
 */
?>
<div>
    <?php echo bController::getModuleAction('foo','header', array()); ?>
</div>
<div>
    <?php echo $content; ?>
</div>
<div>
    <?php echo bController::getModuleAction('foo','footer', array()); ?>
</div>
