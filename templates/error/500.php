<?php declare(strict_types=1);
/**
 * @var Exception $error
 */
?>

<?php $this->layout('base', [
    'title' => 'Server error',
]); ?>

<div class="container" id="php-error">
    <h1>Server error</h1>

    <?php if (ENV === 'prod') { ?>
        <p>Unexpected error, support team has been notified.</p>
    <?php } else { ?>
        <p><strong>Error code <?= $error->getCode(); ?>: <?= $error->getMessage(); ?> in <?= $error->getFile(); ?> on line <?= $error->getLine(); ?></strong></p>
        <table>
            <?php
                $i = 0;

                foreach ($error->getTrace() as $trace) {
                    if (0 === $i) {
                        $trace['file'] = $error->getFile();
                        $trace['line'] = $error->getLine();
                    }
                    $file = isset($trace['file']) ? explode('\\', $trace['file']) : [];
                    $file = count($file) > 0 ? '../'.end($file).':' : ''; ?>
                    <tr>
                        <td>#<?= $i + 1; ?></td>
                        <td><?= $trace['class'] ?? ''; ?><?= $trace['type'] ?? ''; ?><?= $trace['function'] ? $trace['function'].'()' : ''; ?></td>
                        <td><?= $file; ?><?= $trace['line'] ?? ''; ?></td>
                    </tr>
                <?php
                    ++$i;
                } ?>
        </table>
    <?php } ?>

</div>
