<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3">Resultados Validados (Listos para Entrega)</h4>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Orden</th>
                        <th>Paciente</th>
                        <th>Fecha Validación</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?= $order['code'] ?></strong></td>
                            <td>
                                <?= $order['first_name'] . ' ' . $order['last_name'] ?><br>
                                <small class="text-muted"><?= $order['document_id'] ?></small>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($order['validated_at'])) ?></td>
                            <td><span class="badge bg-success">Validado</span></td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>result/print/<?= $order['id'] ?>" target="_blank" class="btn btn-sm btn-danger">
                                    <i class="ti ti-file-type-pdf"></i> PDF
                                </a>

                                <a href="https://wa.me/591<?= $order['phone'] ?? '' ?>?text=Su%20resultado%20está%20listo" target="_blank" class="btn btn-sm btn-success">
                                    <i class="ti ti-brand-whatsapp"></i> WSP
                                </a>

                                <button onclick="openDeliveryModal(<?= $order['id'] ?>, '<?= $order['first_name'] . ' ' . $order['last_name'] ?>')"
                                    class="btn btn-sm btn-primary" title="Entregar">
                                    <i class="ti ti-checkbox"></i> Entregar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<div class="modal fade" id="deliveryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Registrar Entrega de Resultados</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="delivery_order_id">

                <div class="mb-3">
                    <label class="form-label fw-bold">¿Quién recoge el resultado?</label>
                    <input type="text" class="form-control" id="delivery_receiver" placeholder="Ej: Juan Pérez (Titular) o María Gómez (Esposa)">
                    <div class="form-text">Si es el titular, puedes escribir "TITULAR".</div>
                </div>

                <div class="alert alert-warning py-2 small">
                    <i class="ti ti-alert-circle"></i> Esta acción archivará la orden y quedará registrada en el historial.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" onclick="confirmDelivery()" class="btn btn-primary">Confirmar Entrega</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Variable para manejar el modal
    let deliveryModal;

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializamos el modal cuando carga la página
        var modalEl = document.getElementById('deliveryModal');
        if (modalEl) {
            deliveryModal = new bootstrap.Modal(modalEl);
        }
    });

    function openDeliveryModal(id, patientName) {
        document.getElementById('delivery_order_id').value = id;
        document.getElementById('delivery_receiver').value = "TITULAR: " + patientName;
        deliveryModal.show();
        setTimeout(() => document.getElementById('delivery_receiver').focus(), 500);
    }

    function confirmDelivery() {
        const id = document.getElementById('delivery_order_id').value;
        const receiver = document.getElementById('delivery_receiver').value;

        if (receiver.trim() === '') {
            alert('Por favor escriba quién recoge el resultado.');
            return;
        }

        fetch('<?= BASE_URL ?>result/archive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: id,
                    delivered_to: receiver
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    deliveryModal.hide();
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }
</script>