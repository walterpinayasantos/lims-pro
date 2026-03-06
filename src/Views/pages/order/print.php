<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden #<?= $order['code'] ?> - Imprimir</title>
    <link href="<?= BASE_URL ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASE_URL ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-color: #f0f2f5;
            padding-top: 20px;
        }

        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        /* ESTILOS DE IMPRESIÓN */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .ticket-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }

            .d-print-none {
                display: none !important;
            }

            .bg-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
        }

        .lab-logo {
            font-size: 24px;
            font-weight: bold;
            color: #3e60d5;
        }

        .qr-placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="d-print-none mb-3 text-center">
            <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
                <i class="ti ti-printer me-1"></i> Imprimir Comprobante
            </button>
            <a href="<?= BASE_URL ?>order/create" class="btn btn-light btn-lg ms-2">
                <i class="ti ti-arrow-left me-1"></i> Volver a Admisión
            </a>
        </div>

        <div class="ticket-container">

            <div class="row mb-4 border-bottom pb-3">
                <div class="col-8">
                    <div class="lab-logo"><i class="ti ti-microscope"></i> LIMS ARCHITECT LAB</div>
                    <div class="text-muted small mt-1">
                        Av. Panamericana #123, Oruro - Bolivia<br>
                        Tel: (591) 2-5252525 | NIT: 1020304050
                    </div>
                </div>
                <div class="col-4 text-end">
                    <h3 class="mb-0 text-dark">ORDEN DE TRABAJO</h3>
                    <h5 class="text-primary"><?= $order['code'] ?></h5>
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small fw-bold">Paciente</h6>
                    <div class="fs-5 fw-bold">
                        <?= $order['first_name'] . ' ' . $order['last_name'] ?>
                    </div>
                    <div>CI/NIT: <?= $order['document_id'] ?></div>
                    <div>Edad: <?= date_diff(date_create($order['birth_date']), date_create('today'))->y ?> años</div>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-uppercase text-muted small fw-bold">Médico Solicitante</h6>
                    <div class="fs-6">
                        <?= $order['doctor_name'] ? $order['doctor_name'] : 'Particular / A petición' ?>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-striped table-sm table-bordered border-light">
                    <thead class="bg-light text-center">
                        <tr>
                            <th style="width: 10%">Cod</th>
                            <th class="text-start">Examen / Estudio</th>
                            <th style="width: 20%" class="text-end">Precio (Bs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="text-center"><small><?= $item['code'] ?></small></td>
                                <td><?= $item['name'] ?></td>
                                <td class="text-end"><?= number_format((float)$item['price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="border rounded p-3 text-center bg-light">
                        <p class="small fw-bold mb-2">Escanee para ver Resultados</p>
                        <div id="qrcode" class="qr-placeholder"></div>
                        <small class="text-muted d-block mt-2 font-monospace" style="font-size: 10px;">
                            TOKEN: <?= md5($order['code']) // Simulación de token seguro 
                                    ?>
                        </small>
                    </div>
                </div>
                <div class="col-6">
                    <table class="table table-sm table-borderless text-end">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="fw-bold"><?= number_format((float)$order['subtotal'], 2) ?></td>
                        </tr>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <tr class="text-danger">
                                <td>Descuento (<?= $order['discount_percent'] ?>%):</td>
                                <td>-<?= number_format((float)$order['discount_amount'], 2) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr class="border-top border-dark">
                            <td class="fs-5 fw-bold">TOTAL:</td>
                            <td class="fs-5 fw-bold"><?= number_format((float)$order['total_amount'], 2) ?> Bs</td>
                        </tr>

                        <tr>
                            <td>A Cuenta (Pagado):</td>
                            <td><?= number_format((float)$order['paid_amount'], 2) ?></td>
                        </tr>

                        <tr class="<?= $order['balance_due'] > 0 ? 'bg-danger text-white' : 'bg-success text-white' ?>">
                            <td class="fw-bold p-2">SALDO PENDIENTE:</td>
                            <td class="fw-bold p-2 fs-5"><?= number_format((float)$order['balance_due'], 2) ?> Bs</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top text-center text-muted small">
                <p>
                    Este documento es un comprobante de solicitud de análisis clínicos.<br>
                    Los resultados serán entregados únicamente presentando este ticket o mediante validación de identidad.<br>
                    <strong>Usuario:</strong> Admisión LIMS
                </p>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        // Generar QR automáticamente al cargar
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "<?= BASE_URL ?>results/view/<?= $order['code'] ?>", // URL futura para el paciente
            width: 128,
            height: 128,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Auto-Imprimir (Opcional, se puede quitar si es molesto)
        // window.onload = function() { window.print(); }
    </script>

</body>

</html>