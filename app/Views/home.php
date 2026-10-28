<?= view('templates/header', ['titulo' => 'Inicio']) ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Resumen</h2>
    <a href="<?= site_url('ventas/nueva') ?>" class="btn btn-queso">+ Nueva venta</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Total que le deben</div>
            <div class="stat-num <?= $deuda_total > 0.004 ? 'saldo-deuda' : 'saldo-ok' ?>"><?= dinero($deuda_total) ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Clientes con deuda</div>
            <div class="stat-num"><?= (int) $cant_deudores ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Vendido este mes</div>
            <div class="stat-num"><?= dinero($vendido_mes) ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted small">Cobrado este mes</div>
            <div class="stat-num"><?= dinero($cobrado_mes) ?></div>
        </div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <h5>Quiénes deben más</h5>
        <div class="table-responsive"><table class="table table-sm table-striped align-middle bg-white">
            <tbody>
            <?php foreach ($top_deudores as $c): ?>
                <tr>
                    <td><a href="<?= site_url('clientes/' . $c['id']) ?>"><?= esc($c['nombre']) ?></a></td>
                    <td class="monto saldo-deuda"><?= dinero($c['saldo']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $top_deudores): ?>
                <tr><td class="text-muted">Nadie debe plata. </td></tr>
            <?php endif; ?>
            </tbody>
        </table></div>
    </div>
    <div class="col-lg-7">
        <h5>Últimas ventas</h5>
        <div class="table-responsive"><table class="table table-sm table-striped align-middle bg-white">
            <tbody>
            <?php foreach ($ultimas_ventas as $v): [$txt, $clase] = estado_pago($v['total'], $v['pagado']); ?>
                <tr>
                    <td><?= fecha_ar($v['fecha']) ?></td>
                    <td><a href="<?= site_url('clientes/' . $v['cliente_id']) . '#venta-' . $v['id'] ?>"><?= esc($v['cliente_nombre']) ?></a></td>
                    <td class="monto"><?= dinero($v['total']) ?></td>
                    <td><span class="badge text-bg-<?= $clase ?>"><?= $txt ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (! $ultimas_ventas): ?>
                <tr><td class="text-muted">Todavía no hay ventas cargadas.</td></tr>
            <?php endif; ?>
            </tbody>
        </table></div>
    </div>
</div>

<?= view('templates/footer') ?>
