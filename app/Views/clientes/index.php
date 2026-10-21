<?= view('templates/header', ['titulo' => 'Clientes']) ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">Clientes</h2>
    <a href="<?= site_url('clientes/nuevo') ?>" class="btn btn-queso">+ Nuevo cliente</a>
</div>

<form method="get" action="<?= site_url('clientes') ?>" class="row g-2 mb-3 align-items-center">
    <div class="col-sm-6 col-md-5">
        <input type="search" name="q" class="form-control" placeholder="Buscar por nombre o teléfono" value="<?= esc($buscar) ?>">
    </div>
    <div class="col-auto">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="deuda" value="1" id="soloDeuda" <?= $soloDeuda ? 'checked' : '' ?>>
            <label class="form-check-label" for="soloDeuda">Solo con deuda</label>
        </div>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filtrar</button></div>
</form>

<div class="table-responsive">
<table class="table table-hover align-middle bg-white table-queso">
    <thead>
        <tr><th>Cliente</th><th>Teléfono</th><th class="text-end">Vendido</th><th class="text-end">Pagado</th><th class="text-end">Debe</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($clientes as $c): $debe = (float) $c['saldo'] > 0.004; ?>
        <tr>
            <td><a href="<?= site_url('clientes/' . $c['id']) ?>" class="fw-semibold text-decoration-none"><?= esc($c['nombre']) ?></a></td>
            <td><?= esc($c['telefono'] ?? '') ?></td>
            <td class="monto"><?= dinero($c['total_vendido']) ?></td>
            <td class="monto"><?= dinero($c['total_pagado']) ?></td>
            <td class="monto <?= $debe ? 'saldo-deuda' : 'saldo-ok' ?>"><?= $debe ? dinero($c['saldo']) : 'Al día' ?></td>
            <td class="text-end"><a href="<?= site_url('ventas/nueva?cliente=' . $c['id']) ?>" class="btn btn-sm btn-outline-success">Vender</a></td>
        </tr>
    <?php endforeach; ?>
    <?php if (! $clientes): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No hay clientes para mostrar.</td></tr>
    <?php endif; ?>
    </tbody>
    <?php if ($clientes): ?>
    <tfoot>
        <tr><th colspan="4" class="text-end">Total adeudado</th><th class="monto saldo-deuda"><?= dinero($totalDeuda) ?></th><th></th></tr>
    </tfoot>
    <?php endif; ?>
</table>
</div>

<?= view('templates/footer') ?>
