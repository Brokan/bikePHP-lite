<?php
/**
 * General template of HTML layout files.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo $pageTitle; ?></title>
        <meta name="keywords" content="<?php echo $pageKeywords; ?>" />
        <meta name="description" content="<?php echo $pageDescription; ?>" />
                
        <?php echo $cssFiles; ?>
        
    </head>

    <body> 
        <?php echo $content; ?>

        <?php echo $jsFiles; ?>
        <?php echo $jsScripts; ?>
    
    </body>
</html>