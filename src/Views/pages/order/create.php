<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3"><i class="ti ti-user-plus me-1"></i> Datos del Paciente</h4>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Buscar Paciente (Nombre o CI)</label>
                        <select id="adm_patient_select" class="form-control" style="width: 100%;"></select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Médico Solicitante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-stethoscope"></i></span>
                            <input type="text" id="adm_doctor_name" class="form-control" placeholder="Escriba el nombre o deje vacío si es particular">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3"><i class="ti ti-flask me-1"></i> Selección de Exámenes</h4>

                <div class="mb-3">
                    <select id="adm_test_select" class="form-control" style="width: 100%;"></select>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Examen</th>
                                <th class="text-end" style="width: 120px;">Precio</th>
                                <th class="text-center" style="width: 50px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="adm_cart_body">
                            <tr id="adm_empty_row">
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="ti ti-shopping-cart-off fs-2"></i><br>
                                    No hay exámenes seleccionados
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-primary border-2">
            <div class="card-body">
                <h4 class="header-title mb-3 text-primary"><i class="ti ti-calculator me-1"></i> Resumen de Orden</h4>

                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span class="fw-bold" id="adm_display_subtotal">0.00 Bs</span>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Descuento (%)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" id="adm_input_discount" class="form-control" value="0" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-3">
                    <span class="fs-4 fw-bold">TOTAL:</span>
                    <span class="fs-4 fw-bold text-primary" id="adm_display_total">0.00 Bs</span>
                </div>

                <div class="bg-light p-3 rounded mb-3">
                    <label class="form-label fw-bold">A Cuenta / Adelanto</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text">Bs</span>
                        <input type="number" id="adm_input_paid" class="form-control fw-bold" value="0" min="0">
                    </div>

                    <div class="d-flex justify-content-between text-danger">
                        <span>Saldo Pendiente:</span>
                        <span class="fw-bold" id="adm_display_balance">0.00 Bs</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago</label>
                    <select id="adm_payment_method" class="form-select">
                        <option value="CASH">💵 Efectivo (Cash)</option>
                        <option value="QR">📱 QR / Transferencia Simple</option>
                        <option value="CARD">💳 Tarjeta Débito/Crédito</option>
                        <option value="INSURANCE">🏥 Seguro Médico</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="button" id="adm_btn_save" class="btn btn-primary btn-lg">
                        <i class="ti ti-device-floppy me-1"></i> Generar Orden
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>