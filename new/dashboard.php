<?php 
    include_once "include/api.config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php
        $response = api_request('GET', '/api/temperature');
        
        if ($response['success']) {
            echo "<ul>";
            foreach ($response['temperature'] as $temp) {
                echo "<li>{$temp['celsius']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Erro ao obter dados: " . htmlspecialchars($response['data']['error'] ?? 'Erro desconhecido') . "</p>";
        }
    ?>

</body>
</html>