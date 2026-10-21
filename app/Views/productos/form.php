<?= view('templates/header', ['titulo' => $titulo]) ?>

<?php
$accion = $producto ? site_url('productos/' . $producto['id']) : site_url('productos');
$activo = $producto ? (bool) $producto['activo'] : true;
if (old('nombre') !== null) { $activo = (bool) old('activo'); }
$unidad = old('unidad', $producto['unidad'] ?? 'kilo');
?>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <h2 class="mb-3"><?= esc($titulo) ?></h2>
        <div class="card"><div class="card-body">
            <form method="post" action="<?= $accion ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="150" required
                           value="<?= esc(old('nombre', $producto['nombre'] ?? '')) ?>">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="precio" class="form-label">Precio ($) *</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" required
                               value="<?= esc(old('precio', isset($producto['precio']) ? (float) $producto['precio'] : '')) ?>">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="unidad" class="form-label">Se vende por</label>
                        <select class="form-select" id="unidad" name="unidad">
                            <option value="kilo" <?= $unidad === 'kilo' ? 'selected' : '' ?>>Kilo</option>
                            <option value="unidad" <?= $unidad === 'unidad' ? 'selected' : '' ?>>Unidad</option>
                        </select>
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" <?= $activo ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activo">Disponible para vender</label>
                </div>
                <button type="submit" class="btn btn-queso">Guardar</button>
                <a href="<?= site_url('productos') ?>" class="btn btn-link">Cancelar</a>
            </form>
        </div></div>
    </div>
</div>

<?= view('templates/footer') ?>
