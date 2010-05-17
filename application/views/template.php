<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo html::specialchars($title) ?></title>
<?php echo html::stylesheet(
    array('media/css/main'),
    array('screen')
);
?>

</head>
<body>
    <h1>Seating</h1>
    <?php echo client::messageFetchHtml(); ?>
    <?php echo $content ?>
    <?php echo html::script(array(
        'media/js/jquery-1.4.2.min.js'
        )
        ,false);
    ?>
    <?php echo isset($js_extra)?$js_extra:''; ?>
</body>
</html>