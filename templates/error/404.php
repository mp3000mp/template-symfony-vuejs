<?php declare(strict_types=1);
/**
 * @var string $msg
 */
?>

<?php $this->layout('base', [
    'title' => '404 not found',
]); ?>

<div class="container">
    <h1>404 not found</h1>

    <p><?= $this->trans('msg'); ?></p>
</div>
