<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 16.09.15
 * Time: 11:58
 * @author Darevski
 */
http_response_code($data['code']);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $data['error_status']; ?></title>
    <meta charset="UTF-8">
</head>
<body>

    <?php echo $data['message']; ?>
</body>
</html>