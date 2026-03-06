<?php
// ==========================================
// 1. HELPER: CONVERTIR IMÁGENES A BASE64
// ==========================================
// Esto es vital para DomPDF. Convierte el archivo físico en código puro.
function imageToBase64($fullPath)
{
    if (!file_exists($fullPath)) {
        return ''; // Retorna vacío si no encuentra la imagen
    }

    $type = pathinfo($fullPath, PATHINFO_EXTENSION);
    $data = file_get_contents($fullPath);

    if ($data === false) return '';

    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// ==========================================
// 2. PREPARAR LAS IMÁGENES (FIRMA Y LOGO)
// ==========================================

// A. LOGO (Asegúrate de tener un logo.png en assets/images/)
// Usamos DOCUMENT_ROOT para obtener la ruta física del disco duro (C:/laragon/...)
$pathLogo = $_SERVER['DOCUMENT_ROOT'] . '/lims/public/assets/images/logo.png';
$base64Logo = imageToBase64($pathLogo);

// B. FIRMA DIGITAL
// Obtenemos el nombre del archivo desde la BD (ej: 'firma_5566778.png')
$sigFileName = $order['digital_signature'] ?? 'sin_firma.png';
$pathSignature = $_SERVER['DOCUMENT_ROOT'] . '/lims/public/uploads/signatures/' . $sigFileName;
$base64Signature = imageToBase64($pathSignature);

// ==========================================
// 3. GENERAR CÓDIGO QR (API EXTERNA)
// ==========================================
// Creamos una URL única para validar.
// Usamos la API de qrserver.com para generar la imagen al vuelo.
$qrContent = "VALIDAR: " . $order['code'] . " - PACIENTE: " . $order['document_id'];
$qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrContent);

// Leemos la imagen de internet y la convertimos a Base64
// Nota: Requiere que 'allow_url_fopen' esté activo en php.ini (casi siempre lo está en Laragon)
try {
    $qrData = file_get_contents($qrApiUrl);
    $base64QR = 'data:image/png;base64,' . base64_encode($qrData);
} catch (Exception $e) {
    $base64QR = ''; // Si falla el internet, sale sin QR
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultado_<?= $order['code'] ?></title>
    <style>
        /* Estilos optimizados para PDF A4 / Carta */
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 15px;
        }

        /* Tabla Cabecera */
        .header-tbl {
            width: 100%;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo-box {
            width: 20%;
            vertical-align: middle;
        }

        .company-box {
            width: 80%;
            text-align: right;
            font-size: 10px;
            color: #555;
            vertical-align: middle;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 5px;
        }

        /* --- CORRECCIÓN AQUÍ: CAJA DE INFO --- */
        .info-box {
            width: 100%;
            background-color: #f4f6f8;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            /* QUITAMOS EL PADDING DEL DIV PARA EVITAR DESBORDE */
            padding: 0px;
        }

        .info-tbl {
            width: 100%;
        }

        .info-tbl td {
            /* DAMOS EL ESPACIO AQUÍ ADENTRO */
            padding: 8px 10px;
        }

        .label {
            font-weight: bold;
            color: #004494;
            width: 12%;
        }

        /* Tabla de Resultados */
        .area-header {
            background-color: #0056b3;
            color: white;
            padding: 4px 8px;
            font-weight: bold;
            margin-top: 15px;
            font-size: 11px;
        }

        .res-tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .res-tbl th {
            border-bottom: 1px solid #aaa;
            text-align: left;
            padding: 5px;
            font-size: 10px;
            text-transform: uppercase;
            background: #eee;
        }

        .res-tbl td {
            border-bottom: 1px solid #eee;
            padding: 6px 5px;
        }

        .val-abnormal {
            color: #d9534f;
            font-weight: bold;
        }

        /* Pie de Página */
        .footer-tbl {
            width: 100%;
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        .qr-cell {
            width: 20%;
            text-align: center;
            vertical-align: bottom;
        }

        .legal-cell {
            width: 40%;
            font-size: 9px;
            color: #777;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }

        .sig-cell {
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-img {
            height: 70px;
            display: block;
            margin: 0 auto;
        }

        .sig-line {
            border-top: 1px solid #333;
            width: 80%;
            margin: 5px auto 0 auto;
        }

        .doc-name {
            font-weight: bold;
            font-size: 11px;
            margin-top: 3px;
        }

        .doc-meta {
            font-size: 10px;
        }

        /* ESTILO MARCA DE AGUA (Centrado corregido) */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.1);
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            line-height: 0.9;
            border: 5px solid rgba(0, 0, 0, 0.1);
            padding: 20px 40px;
            border-radius: 20px;
            z-index: -1000;
        }
    </style>
</head>

<body>
    <?php if (!empty($isCopy) && $isCopy): ?>
        <div class="watermark">COPIA REIMPRESA</div>
    <?php endif; ?>

    <table class="header-tbl">
        <tr>
            <td class="logo-box">
                <?php if ($base64Logo): ?>
                    <img src="<?= $base64Logo ?>" style="height: 50px;">
                <?php else: ?>
                    <h2 style="color:#0056b3; margin:0;">LIMS</h2>
                <?php endif; ?>
            </td>
            <td class="company-box">
                <div class="company-name">LABORATORIO CLÍNICO CENTRAL</div>
                <div>Av. Principal #123, Oruro - Bolivia</div>
                <div>Tel: (591) 52-12345 | Email: info@limspro.com</div>
                <div>Resolución SEDES N° 999/2026</div>
            </td>
        </tr>
    </table>

    <div class="info-box">
        <table class="info-tbl">
            <tr>
                <td class="label">PACIENTE:</td>
                <td width="40%"><strong><?= strtoupper($order['first_name'] . ' ' . $order['last_name']) ?></strong></td>
                <td class="label">EDAD/SEXO:</td>
                <td>
                    <?php
                    $dob = new DateTime($order['birth_date']);
                    $now = new DateTime();
                    echo $now->diff($dob)->y . ' Años / ' . $order['gender'];
                    ?>
                </td>
            </tr>
            <tr>
                <td class="label">CI:</td>
                <td><?= $order['document_id'] ?></td>
                <td class="label">MÉDICO:</td>
                <td><?= strtoupper($order['doctor_name'] ?? 'PARTICULAR') ?></td>
            </tr>
            <tr>
                <td class="label">ORDEN:</td>
                <td><strong><?= $order['code'] ?></strong></td>
                <td class="label">FECHA:</td>
                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
            </tr>
        </table>
    </div>

    <?php foreach ($groupedResults as $areaName => $results): ?>

        <div class="area-header"><?= strtoupper($areaName) ?></div>

        <table class="res-tbl">
            <thead>
                <tr>
                    <th width="35%">Examen</th>
                    <th width="20%">Resultado</th>
                    <th width="15%">Unidad</th>
                    <th width="30%">Referencia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $res): ?>
                    <tr>
                        <td>
                            <?= $res['test_name'] ?>
                            <?php if (!empty($res['comments'])): ?>
                                <br><span style="font-size:9px; font-style:italic; color:#666;">Nota: <?= $res['comments'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="<?= ($res['flag'] != 'NORMAL') ? 'val-abnormal' : '' ?>">
                            <?= $res['result_value'] ?>
                            <?php if ($res['flag'] != 'NORMAL') echo ' <small>(' . $res['flag'] . ')</small>'; ?>
                        </td>
                        <td><?= $res['unit'] ?></td>
                        <td><?= $res['dec_ref_low'] ?> - <?= $res['dec_ref_high'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endforeach; ?>

    <table class="footer-tbl">
        <tr>
            <td class="qr-cell">
                <?php if ($base64QR): ?>
                    <img src="<?= $base64QR ?>" style="width: 70px; height: 70px;">
                    <div style="font-size:8px; margin-top:2px;">Escanee para validar</div>
                <?php endif; ?>
            </td>

            <td class="legal-cell">
                Este documento es un informe de apoyo diagnóstico.<br>
                La interpretación corresponde exclusivamente al médico tratante.<br>
                Generado el <?= date('d/m/Y H:i') ?>
            </td>

            <td class="sig-cell">
                <?php if ($base64Signature): ?>
                    <img src="<?= $base64Signature ?>" class="sig-img">
                <?php else: ?>
                    <div style="height: 70px;"></div> <?php endif; ?>

                <div class="sig-line"></div>
                <div class="doc-name">Dra. <?= $order['bio_name'] . ' ' . $order['bio_lastname'] ?></div>
                <div class="doc-meta">
                    Bioquímica - Mat: <?= $order['professional_license'] ?? 'PENDIENTE' ?>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>