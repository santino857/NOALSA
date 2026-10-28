<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(($titulo ?? 'Inicio') . ' · Distribuidora de lácteos') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
<?php
$logueado = (bool) session()->get('isLoggedIn');
$seccion  = explode('/', trim(uri_string(), '/'))[0];
?>
<nav class="navbar navbar-expand-md navbar-dark navbar-queso mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>"> Distribuidora de lácteos</a>
        <?php if ($logueado): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link <?= $seccion === '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link <?= $seccion === 'clientes' ? 'active' : '' ?>" href="<?= site_url('clientes') ?>">Clientes</a></li>
                    <li class="nav-item"><a class="nav-link <?= $seccion === 'ventas' ? 'active' : '' ?>" href="<?= site_url('ventas') ?>">Ventas</a></li>
                    <li class="nav-item"><a class="nav-link <?= $seccion === 'productos' ? 'active' : '' ?>" href="<?= site_url('productos') ?>">Productos y precios</a></li>
                </ul>
                <form method="post" action="<?= site_url('logout') ?>" class="d-flex align-items-center gap-2 mb-0">
                    <?= csrf_field() ?>
                    <span class="text-white small">Hola, <?= esc(strtok((string) session()->get('name'), ' ')) ?></span>
                    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</nav>

<div class="container flex-grow-1 pb-4">
<?php if ($logueado) { echo view('templates/mensajes'); } ?>
