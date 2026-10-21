<?= view('templates/header', ['titulo' => $cliente['nombre']]) ?>

<?php $debe = $resumen['saldo'] > 0.004; ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h2 class="mb-0"><?= esc($cliente['nombre']) ?></h2>
        <div class="text-muted">
            <?= esc($cliente['telefono'] ?? '') ?>
            <?= ($cliente['telefono'] && $cliente['direccion']) ? ' · ' : '' ?>
            <?= esc($cliente['direccion'] ?? '') ?>
        </div>
        <?php if ($cliente['notas']): ?><div class="small mt-1"><?= nl2br(esc($cliente['notas'])) ?></div><?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('ventas/nueva?cliente=' . $cliente['id']) ?>" class="btn btn-queso">+ Nueva venta</a>
        <a href="<?= site_url('clientes/' . $cliente['id'] . '/editar') ?>" class="btn btn-outline-secondary">Editar</a>
        <?php if (! $ventas): ?>
            <form method="post" action="<?= site_url('clientes/' . $cliente['id'] . '/eliminar') ?>"
                  onsubmit="return confirm('¿Eliminar este cliente?');">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger">Eliminar</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card"><div class="card-body">
        <div class="text-muted small">Total vendido</div><div class="stat-num"><?= dinero($resumen['total_vendido']) ?></div>
    </div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body">
        <div class="text-muted small">Total pagado</div><div class="stat-num"><?= dinero($resumen['total_pagado']) ?></div>
    </div></div></div>
    <div class="col-md-4"><div class="card <?= $debe ? 'border-danger' : '' ?>"><div class="card-body">
        <div class="text-muted small"><?= $debe ? 'Nos debe' : 'Estado de cuenta' ?></div>
        <div class="stat-num <?= $debe ? 'saldo-deuda' : 'saldo-ok' ?>"><?= $debe ? dinero($resumen['saldo']) : 'Al día' ?></div>
    </div></div></div>
</div>

<h4>Ventas</h4>

<?php if (! $ventas): ?>
    <p class="text-muted">Todavía no se le vendió nada a este cliente.</p>
<?php endif; ?>

<div class="accordion" id="ventas">
<?php foreach ($ventas as $v):
    [$txt, $clase] = estado_pago($v['total'], $v['pagado']);
    $saldoVenta    = round((float) $v['total'] - (float) $v['pagado'], 2);
    $vid           = (int) $v['id'];
?>
    <div class="accordion-item" id="venta-<?= $vid ?>">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#detalle-<?= $vid ?>">
                <span class="me-3"><?= fecha_ar($v['fecha']) ?></span>
                <span class="me-3 fw-semibold"><?= dinero($v['total']) ?></span>
                <span class="badge text-bg-<?= $clase ?> me-3"><?= $txt ?></span>
                <?php if ($saldoVenta > 0.004): ?><span class="saldo-deuda small">Debe <?= dinero($saldoVenta) ?></span><?php endif; ?>
            </button>
        </h2>
        <div id="detalle-<?= $vid ?>" class="accordion-collapse collapse" data-bs-parent="#ventas">
            <div class="accordion-body">
                <div class="table-responsive"><table class="table table-sm">
                    <thead><tr><th>Producto</th><th class="text-end">Cantidad</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    <?php foreach ($items[$vid] ?? [] as $it): ?>
                        <tr>
                            <td><?= esc($it['producto_nombre']) ?></td>
                            <td class="monto"><?= cantidad($it['cantidad'], $it['unidad']) ?></td>
                            <td class="monto"><?= dinero($it['precio_unitario']) ?></td>
                            <td class="monto"><?= dinero($it['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr><th colspan="3" class="text-end">Total</th><th class="monto"><?= dinero($v['total']) ?></th></tr></tfoot>
                </table></div>

                <?php if ($v['notas']): ?><p class="small text-muted">Nota: <?= esc($v['notas']) ?></p><?php endif; ?>

                <h6>Pagos</h6>
                <?php if (empty($pagos[$vid])): ?>
                    <p class="small text-muted">Sin pagos registrados.</p>
                <?php else: ?>
                    <ul class="list-group mb-3">
                    <?php foreach ($pagos[$vid] as $pg): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= fecha_ar($pg['fecha']) ?> · <strong><?= dinero($pg['monto']) ?></strong>
                                <?= $pg['nota'] ? '<span class="text-muted small">— ' . esc($pg['nota']) . '</span>' : '' ?></span>
                            <form method="post" action="<?= site_url('pagos/' . $pg['id'] . '/eliminar') ?>"
                                  onsubmit="return confirm('¿Eliminar este pago?');" class="mb-0">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger">Quitar</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($saldoVenta > 0.004): ?>
                    <form method="post" action="<?= site_url('ventas/' . $vid . '/pagos') ?>" class="row g-2 align-items-end mb-3">
                        <?= csrf_field() ?>
                        <div class="col-sm-3">
                            <label class="form-label small mb-0">Monto</label>
                            <input type="number" step="0.01" min="0.01" max="<?= $saldoVenta ?>" name="monto" class="form-control form-control-sm"
                                   value="<?= $saldoVenta ?>" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label small mb-0">Fecha</label>
                            <input type="date" name="fecha" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small mb-0">Nota (opcional)</label>
                            <input type="text" name="nota" maxlength="255" class="form-control form-control-sm" placeholder="Efectivo, transferencia…">
                        </div>
                        <div class="col-sm-2"><button class="btn btn-sm btn-queso w-100">Registrar pago</button></div>
                    </form>
                <?php endif; ?>

                <form method="post" action="<?= site_url('ventas/' . $vid . '/eliminar') ?>"
                      onsubmit="return confirm('¿Eliminar esta venta con sus pagos? No se puede deshacer.');">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger">Eliminar venta</button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<script>
// Si se llega con #venta-ID (después de cargar una venta o un pago), se abre ese detalle.
(function () {
    var m = location.hash.match(/^#venta-(\d+)$/);
    if (!m) return;
    var el = document.getElementById('detalle-' + m[1]);
    if (el) { el.classList.add('show'); var b = el.previousElementSibling.querySelector('button'); if (b) b.classList.remove('collapsed'); }
})();
</script>

<?= view('templates/footer') ?>
