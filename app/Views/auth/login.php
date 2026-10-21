<?= view('templates/header', ['titulo' => 'Ingresar']) ?>

<div class="row justify-content-center my-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center">Iniciar sesión</h3>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('exito')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('exito')) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('login') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= esc((string) session()->getFlashdata('ultimo_usuario')) ?>"
                               autocomplete="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password"
                               autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-queso w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('templates/footer') ?>
