<?= view('templates/header', ['titulo' => 'Nueva venta']) ?>

<?php
$productosJs = array_map(static fn ($p) => [
    'id'     => (int) $p['id'],
    'nombre' => $p['nombre'],
    'precio' => (float) $p['precio'],
    'unidad' => $p['unidad'],
], $productos);

// Renglones cargados antes de un error de validación
$prevP  = (array) old('producto_id', null, false);
$prevC  = (array) old('cantidad', null, false);
$prevPr = (array) old('precio', null, false);
$previos = [];
foreach ($prevP as $i => $pid) {
    $previos[] = ['producto_id' => $pid, 'cantidad' => $prevC[$i] ?? '', 'precio' => $prevPr[$i] ?? ''];
}
$clienteSel = (int) old('cliente_id', $clienteId);
?>

<h2 class="mb-3">Nueva venta</h2>

<?php if (! $clientes): ?>
    <div class="alert alert-warning">Primero tenés que <a href="<?= site_url('clientes/nuevo') ?>">agregar un cliente</a>.</div>
<?php endif; ?>
<?php if (! $productos): ?>
    <div class="alert alert-warning">No hay productos activos. <a href="<?= site_url('productos') ?>">Cargá o activá productos</a>.</div>
<?php endif; ?>

<form method="post" action="<?= site_url('ventas') ?>" id="formVenta">
    <?= csrf_field() ?>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="cliente_id" class="form-label">Cliente *</label>
            <select name="cliente_id" id="cliente_id" class="form-select" required>
                <option value="">Elegí un cliente…</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $clienteSel === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="fecha" class="form-label">Fecha *</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required value="<?= esc(old('fecha', date('Y-m-d'))) ?>">
        </div>
    </div>

    <div class="table-responsive">
    <table class="table align-middle bg-white table-queso mb-2">
        <thead><tr><th style="min-width:220px">Producto</th><th style="width:150px">Cantidad</th><th style="width:150px">Precio unit. ($)</th><th class="text-end" style="width:150px">Subtotal</th><th style="width:50px"></th></tr></thead>
        <tbody id="items"></tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total</th><th class="monto" id="total">$ 0</th><th></th></tr></tfoot>
    </table>
    </div>
    <button type="button" class="btn btn-outline-success mb-4" id="btnAgregar">+ Agregar producto</button>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="pago_inicial" class="form-label">¿Cuánto pagó ahora? ($)</label>
            <div class="input-group">
                <input type="number" step="0.01" min="0" name="pago_inicial" id="pago_inicial" class="form-control"
                       value="<?= esc(old('pago_inicial', '0')) ?>">
                <button type="button" class="btn btn-outline-secondary" id="btnPagoTotal">Pagó todo</button>
            </div>
            <div class="form-text">Lo que no pague queda como deuda del cliente.</div>
        </div>
        <div class="col-md-8">
            <label for="notas" class="form-label">Notas</label>
            <input type="text" name="notas" id="notas" class="form-control" maxlength="255" value="<?= esc(old('notas', '')) ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-queso btn-lg" <?= (! $clientes || ! $productos) ? 'disabled' : '' ?>>Guardar venta</button>
    <a href="<?= site_url('ventas') ?>" class="btn btn-link">Cancelar</a>
</form>

<script>
(function () {
    var productos = <?= json_encode($productosJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var previos   = <?= json_encode($previos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var body      = document.getElementById('items');
    var totalEl   = document.getElementById('total');
    var fmt = function (n) { return '$ ' + new Intl.NumberFormat('es-AR', { maximumFractionDigits: 2 }).format(n); };
    var total = 0;

    function recalcular() {
        total = 0;
        body.querySelectorAll('tr').forEach(function (tr) {
            var c = parseFloat(tr.querySelector('.cant').value) || 0;
            var p = parseFloat(tr.querySelector('.precio').value) || 0;
            var s = Math.round(c * p * 100) / 100;
            tr.querySelector('.sub').textContent = fmt(s);
            total += s;
        });
        total = Math.round(total * 100) / 100;
        totalEl.textContent = fmt(total);
    }

    function agregarFila(datos) {
        datos = datos || {};
        var tr = document.createElement('tr');

        var sel = document.createElement('select');
        sel.name = 'producto_id[]'; sel.className = 'form-select'; sel.required = true;
        sel.add(new Option('Elegí un producto…', ''));
        productos.forEach(function (p) { sel.add(new Option(p.nombre + ' (' + fmt(p.precio) + ')', p.id)); });

        var cant = document.createElement('input');
        cant.type = 'number'; cant.name = 'cantidad[]'; cant.className = 'form-control cant';
        cant.step = '0.001'; cant.min = '0.001'; cant.required = true; cant.placeholder = 'kg';

        var precio = document.createElement('input');
        precio.type = 'number'; precio.name = 'precio[]'; precio.className = 'form-control precio';
        precio.step = '0.01'; precio.min = '0'; precio.required = true;

        var sub = document.createElement('span'); sub.className = 'sub'; sub.textContent = fmt(0);

        var quitar = document.createElement('button');
        quitar.type = 'button'; quitar.className = 'btn btn-sm btn-outline-danger'; quitar.textContent = '×';
        quitar.setAttribute('aria-label', 'Quitar renglón');
        quitar.onclick = function () { tr.remove(); recalcular(); };

        [sel, cant, precio].forEach(function (el) { var td = document.createElement('td'); td.appendChild(el); tr.appendChild(td); });
        var tdSub = document.createElement('td'); tdSub.className = 'monto'; tdSub.appendChild(sub); tr.appendChild(tdSub);
        var tdQ = document.createElement('td'); tdQ.appendChild(quitar); tr.appendChild(tdQ);

        sel.onchange = function () {
            var p = productos.find(function (x) { return String(x.id) === sel.value; });
            if (p) { precio.value = p.precio; cant.placeholder = p.unidad === 'kilo' ? 'kg' : 'unidades'; }
            recalcular();
        };
        cant.oninput = precio.oninput = recalcular;

        if (datos.producto_id) { sel.value = datos.producto_id; }
        if (datos.cantidad !== undefined) { cant.value = datos.cantidad; }
        if (datos.precio !== undefined) { precio.value = datos.precio; }

        body.appendChild(tr);
        recalcular();
    }

    document.getElementById('btnAgregar').onclick = function () { agregarFila(); };
    document.getElementById('btnPagoTotal').onclick = function () { document.getElementById('pago_inicial').value = total; };

    if (previos.length) { previos.forEach(agregarFila); } else { agregarFila(); }
})();
</script>

<?= view('templates/footer') ?>
