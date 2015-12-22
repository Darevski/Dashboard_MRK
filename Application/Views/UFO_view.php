<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 16.09.15
 * Time: 11:58
 */
http_response_code($data['Code']);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $data['Error_status']; ?></title>
    <meta charset="UTF-8">
</head>
<body>

    <?php echo $data['Message']; ?>
</body>
</html>