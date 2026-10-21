<?= view('templates/header', ['titulo' => 'Ventas']) ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">Ventas</h2>
    <a href="<?= site_url('ventas/nueva') ?>" class="btn btn-queso">+ Nueva venta</a>
</div>

<div class="btn-group mb-3" role="group">
    <a href="<?= site_url('ventas') ?>" class="btn btn-outline-secondary <?= $estado === '' ? 'active' : '' ?>">Todas</a>
    <a href="<?= site_url('ventas?estado=deuda') ?>" class="btn btn-outline-secondary <?= $estado === 'deuda' ? 'active' : '' ?>">Con deuda</a>
    <a href="<?= site_url('ventas?estado=pagadas') ?>" class="btn btn-outline-secondary <?= $estado === 'pagadas' ? 'active' : '' ?>">Pagadas</a>
</div>

<div class="table-responsive">
<table class="table table-hover align-middle bg-white table-queso">
    <thead><tr><th>Fecha</th><th>Cliente</th><th class="text-end">Total</th><th class="text-end">Pagado</th><th class="text-end">Debe</th><th>Estado</th></tr></thead>
    <tbody>
    <?php foreach ($ventas as $v):
        [$txt, $clase] = estado_pago($v['total'], $v['pagado']);
        $saldo = round((float) $v['total'] - (float) $v['pagado'], 2);
    ?>
        <tr>
            <td><?= fecha_ar($v['fecha']) ?></td>
            <td><a href="<?= site_url('clientes/' . $v['cliente_id']) . '#venta-' . $v['id'] ?>" class="text-decoration-none"><?= esc($v['cliente_nombre']) ?></a></td>
            <td class="monto"><?= dinero($v['total']) ?></td>
            <td class="monto"><?= dinero($v['pagado']) ?></td>
            <td class="monto <?= $saldo > 0.004 ? 'saldo-deuda' : '' ?>"><?= $saldo > 0.004 ? dinero($saldo) : '—' ?></td>
            <td><span class="badge text-bg-<?= $clase ?>"><?= $txt ?></span></td>
        </tr>
    <?php endforeach; ?>
    <?php if (! $ventas): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No hay ventas para mostrar.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<p class="small text-muted">Se muestran las últimas 200 ventas.</p>

<?= view('templates/footer') ?>
