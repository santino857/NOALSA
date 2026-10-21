<?= view('templates/header', ['titulo' => 'Productos y precios']) ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Productos y precios</h2>
    <a href="<?= site_url('productos/nuevo') ?>" class="btn btn-queso">+ Nuevo producto</a>
</div>

<div class="table-responsive">
<table class="table align-middle bg-white table-queso">
    <thead><tr><th>Producto</th><th class="text-end">Precio</th><th>Unidad</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($productos as $p): ?>
        <tr class="<?= $p['activo'] ? '' : 'text-muted' ?>">
            <td><?= esc($p['nombre']) ?></td>
            <td class="monto"><?= dinero($p['precio']) ?></td>
            <td>por <?= $p['unidad'] === 'kilo' ? 'kilo' : 'unidad' ?></td>
            <td><span class="badge text-bg-<?= $p['activo'] ? 'success' : 'secondary' ?>"><?= $p['activo'] ? 'Activo' : 'Pausado' ?></span></td>
            <td class="text-end text-nowrap">
                <a href="<?= site_url('productos/' . $p['id'] . '/editar') ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form method="post" action="<?= site_url('productos/' . $p['id'] . '/alternar') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-warning"><?= $p['activo'] ? 'Pausar' : 'Activar' ?></button>
                </form>
                <form method="post" action="<?= site_url('productos/' . $p['id'] . '/eliminar') ?>" class="d-inline"
                      onsubmit="return confirm('¿Quitar este producto? Las ventas anteriores no se modifican.');">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger">Quitar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (! $productos): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">No hay productos cargados.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<p class="small text-muted">Los cambios de precio solo afectan a las ventas nuevas; las ventas ya cargadas conservan el precio con que se vendieron.</p>

<?= view('templates/footer') ?>
