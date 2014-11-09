<?php 
/**
 * Default layout template file
 */
?>
<div>
    <?php echo $this->moduleActionExecute('foo','header', array()); ?>
</div>
<div>
    <?php echo $content; ?>
</div>
<div>
    <?php echo $this->moduleActionExecute('foo','footer', array()); ?>
</div>
