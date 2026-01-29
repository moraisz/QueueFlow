<?php

namespace Src\Infrastructure\Views\pages;

use Src\Core\View;

/** @var string $title */
/** @var string $message */
/** @var array $customers */

?>

<?php View::extends('layouts/base'); ?>

<?php View::section('title'); ?> <?= $title; ?> <?php View::endSection(); ?>

<?php View::section('content'); ?>

<h1><?= $message ?></h1>
<h1>Customers do QueueFlow</h1>
<p>Sua aplicação de filas</p>
<?php if (isset($customers) && count($customers) > 0) : ?>
<ul>
    <?php foreach ($customers as $customer) : ?>
    <li>
        <strong>ID:</strong> <?= $customer->id; ?> |
        <strong>Name:</strong> <?= $customer->name; ?> |
        <strong>Email:</strong> <?= $customer->email; ?> |
        <strong>Telephone:</strong> <?= $customer->telephone; ?> |
        <strong>Priority:</strong> <?= $customer->priority; ?> |
        <strong>Type:</strong> <?= $customer->type; ?> |
        <strong>Status:</strong> <?= $customer->status; ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php View::endSection(); ?>
