<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo html::specialchars($title) ?></title>
<?php echo html::stylesheet(
    array('media/css/site'),
    array('screen')
);
?>

</head>
<body>
    <h1>Seating</h1>
    <?php echo $content ?>
</body>
</html>