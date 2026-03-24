<?php

namespace Src\Infrastructure\Views\pages;

use Src\Core\View;

/** @var string $title */
/** @var string $message */

?>

<?php View::extends('layouts/base'); ?>

<?php View::section('title'); ?> <?= $title; ?> <?php View::endSection(); ?>

<?php View::section('content'); ?>

<h1><?= View::e($message) ?></h1>
<h1>Customers do QueueFlow</h1>
<p>Sua aplicação de filas</p>
<a href="/customers">Ver clientes</a>

<?php View::endSection(); ?>
