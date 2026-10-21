<?= view('templates/header', ['titulo' => $titulo]) ?>

<?php $accion = $cliente ? site_url('clientes/' . $cliente['id']) : site_url('clientes'); ?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <h2 class="mb-3"><?= esc($titulo) ?></h2>
        <div class="card"><div class="card-body">
            <form method="post" action="<?= $accion ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del cliente *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="150" required
                           value="<?= esc(old('nombre', $cliente['nombre'] ?? '')) ?>">
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" maxlength="50"
                           value="<?= esc(old('telefono', $cliente['telefono'] ?? '')) ?>">
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" maxlength="200"
                           value="<?= esc(old('direccion', $cliente['direccion'] ?? '')) ?>">
                </div>
                <div class="mb-3">
                    <label for="notas" class="form-label">Notas</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3" maxlength="1000"><?= esc(old('notas', $cliente['notas'] ?? '')) ?></textarea>
                </div>
                <button type="submit" class="btn btn-queso">Guardar</button>
                <a href="<?= $cliente ? site_url('clientes/' . $cliente['id']) : site_url('clientes') ?>" class="btn btn-link">Cancelar</a>
            </form>
        </div></div>
    </div>
</div>

<?= view('templates/footer') ?>
