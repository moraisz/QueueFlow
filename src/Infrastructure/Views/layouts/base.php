<?php

namespace Src\Infrastructure\Views\layouts;

use Src\Core\View;

?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="/assets/css/style.css">
        <script src="/assets/js/app.js" defer></script>
        <link rel="icon" type="image/png" href="/assets/favicon/favicon.ico">

        <title><?= View::yield('title', 'QueueFlow') ?></title>
    </head>
    <body>
        <div id="app">
            <?= View::yield('content') ?>
        </div>
    </body>
</html>
