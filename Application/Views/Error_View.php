<?php
/**
 * Created by PhpStorm.
 * User: darevski
 * Date: 23.02.16
 * Time: 20:48
 * @author Darevski
 */
http_response_code($data['response_code']);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $data['title']; ?></title>
    <meta charset="UTF-8">
</head>
<body>
<div>
    <?php echo $data['message']; ?>
</div>


<json style="display: none">
    <?php echo $data['json']; ?>
</json>
</body>
</html>