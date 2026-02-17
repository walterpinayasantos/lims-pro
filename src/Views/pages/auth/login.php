<div class="card">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="text-uppercase mt-0">Ingreso LIMS</h4>
            <p class="text-muted">Introduce tus credenciales para acceder al laboratorio.</p>
        </div>

        <form id="auth_login_form">
            <div class="mb-3">
                <label for="auth_username" class="form-label">Usuario o Correo</label>
                <input class="form-control" type="text" id="auth_username" name="username" required placeholder="Ej: bq_perez">
            </div>

            <div class="mb-3">
                <label for="auth_password" class="form-label">Contraseña</label>
                <input class="form-control" type="password" required id="auth_password" name="password" placeholder="Tu clave de acceso">
            </div>

            <div class="mb-3 d-grid">
                <button class="btn btn-primary" type="submit" id="auth_btn_login">
                    <i class="ti ti-login me-1"></i> Ingresar
                </button>
            </div>
        </form>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12 text-center">
        <p class="text-muted">¿Olvidaste tu contraseña? <br> Contacta al administrador del sistema.</p>
    </div>
</div>