<?php foreach (['exito' => 'success', 'error' => 'danger'] as $clave => $clase): ?>
    <?php if ($msg = session()->getFlashdata($clave)): ?>
        <div class="alert alert-<?= $clase ?> alert-dismissible fade show" role="alert">
            <?= esc($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if ($errores = session()->getFlashdata('errores')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ((array) $errores as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
