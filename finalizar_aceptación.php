<?php
require_once 'logic/conn.php';
require_once 'logic/datos_paciente.php';

if (!isset($_GET['id_paciente']) || empty($_GET['id_paciente'])) {
    die("<div style='text-align: center; padding: 50px; font-family: sans-serif; color: #333;'><h2>Aviso</h2><p>No es posible avanzar. (No se recibió el parámetro id_paciente)</p></div>");
}

$id_paciente = $_GET['id_paciente'];
$datos_paciente = obtener_datos_paciente($mysqli, $id_paciente);

if (!$datos_paciente) {
    die("<div style='text-align: center; padding: 50px; font-family: sans-serif; color: #333;'><h2>Aviso</h2><p>Los datos del paciente no existen o no se encontraron.</p></div>");
}

$nombre_paciente = $datos_paciente['nombre_comp'];
$foto = !empty($datos_paciente['foto']) ? 'FotosPacientes/' . $datos_paciente['foto'] : '';

// Firmas
$firma_hgc = !empty($datos_paciente['url_firma_hgc']) ? $datos_paciente['url_firma_hgc'] : '';
$firma_aviso = !empty($datos_paciente['url_firma_avisop']) ? $datos_paciente['url_firma_avisop'] : '';
$firma_consent = !empty($datos_paciente['url_firma_consent']) ? $datos_paciente['url_firma_consent'] : '';


// Fechas
function formatearFecha($fecha) {
    if (!$fecha) return 'Pendiente';
    $t = strtotime($fecha);
    return $t ? date('d/m/Y \a \l\a\s H:i:s', $t) : $fecha;
}

$fecha_hgc = formatearFecha($datos_paciente['fecha_consetimiento_hgc']);
$fecha_aviso = formatearFecha($datos_paciente['fecha_consetimiento_avisop']);
$fecha_consent = formatearFecha($datos_paciente['fecha_consetimientoinf']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="Static/img/favicon.png">
    <title>Proceso Finalizado</title>
    <link rel="stylesheet" href="Static/css/styles.css">
</head>
<body class="ui-theme">

<div class="card">
    <h2>
        <img src="Static/img/logo.png" alt="Logo Consultorio SER" style="height: 60px; vertical-align: middle; margin-right: 15px;">
        Gracias por aceptar nuestros términos y condiciones
    </h2>
    
    <div class="table-container">
        <table>
            <tr>
                <th style="width: 25%;">Paciente</th>
                <th colspan="3" style="text-align: left; padding-left: 20px;"><?php echo htmlspecialchars($nombre_paciente); ?></th>
            </tr>
            <tr>
                <td rowspan="3" style="vertical-align: middle;">
                    <?php if ($foto && file_exists($foto)): ?>
                        <img src="<?php echo $foto; ?>" alt="Foto Paciente" class="foto-paciente">
                    <?php else: ?>
                        <div style="width: 150px; height: 150px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 3px dashed #ccc;">
                            <span style="color: #999; font-size: 14px; font-weight: 600;">Sin foto</span>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="td-header">Consentimiento Historia Clínica</td>
                <td class="td-header">Consentimiento Aviso Privacidad</td>
                <td class="td-header">Consentimiento Informado</td>
            </tr>
            <tr>
                <td><strong>Fecha consentimento:</strong><br><br><?php echo htmlspecialchars($fecha_hgc); ?></td>
                <td><strong>Fecha consentimento:</strong><br><br><?php echo htmlspecialchars($fecha_aviso); ?></td>
                <td><strong>Fecha consentimento:</strong><br><br><?php echo htmlspecialchars($fecha_consent); ?></td>
            </tr>
            <tr>
                <td style="vertical-align: middle;">
                    <strong>Firma:</strong><br>
                    <?php if ($firma_hgc && file_exists($firma_hgc)): ?>
                        <img src="<?php echo $firma_hgc; ?>" alt="Firma HGC" class="firma">
                    <?php else: ?>
                        <span class="no-data-placeholder">Sin firma capturada</span>
                    <?php endif; ?>
                </td>
                <td style="vertical-align: middle;">
                    <strong>Firma:</strong><br>
                    <?php if ($firma_aviso && file_exists($firma_aviso)): ?>
                        <img src="<?php echo $firma_aviso; ?>" alt="Firma Aviso" class="firma">
                    <?php else: ?>
                        <span class="no-data-placeholder">Sin firma capturada</span>
                    <?php endif; ?>
                </td>
                <td style="vertical-align: middle;">
                    <strong>Firma:</strong><br>
                    <?php if ($firma_consent && file_exists($firma_consent)): ?>
                        <img src="<?php echo $firma_consent; ?>" alt="Firma Consentimiento" class="firma">
                    <?php else: ?>
                        <span class="no-data-placeholder">Sin firma capturada</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <a href="index.php" class="btn-inicio">Salir</a>
</div>

</body>
</html>
