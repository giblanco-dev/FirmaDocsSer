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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #2d83a0 0%, #00e5ff 100%);
            padding: 20px;
        }

        .card {
            background: white;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 1000px;
            width: 100%;
            text-align: center;
        }

        h2 {
            font-size: 30px;
            color: #2d83a0;
            font-weight: 700;
            margin-bottom: 35px;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            background-color: #fff;
        }

        th, td {
            border: 2px solid #e0e6ed;
            padding: 18px 15px;
            font-size: 16px;
            color: #444;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 700;
            color: #2d83a0;
            font-size: 18px;
        }

        .td-header {
            background-color: #f1f5f8;
            font-weight: 600;
            color: #2d83a0;
        }

        img.foto-paciente {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #2d83a0;
            box-shadow: 0 6px 15px rgba(45, 131, 160, 0.2);
            margin: 10px;
        }

        img.firma {
            max-width: 160px;
            max-height: 90px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        
        .no-data-placeholder {
            color: #999;
            font-style: italic;
            font-size: 14px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 6px;
            display: inline-block;
        }

        .btn-inicio {
            display: inline-block;
            background-color: #2d83a0;
            color: white;
            padding: 18px 40px;
            border-radius: 10px;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
            margin-top: 40px;
            transition: all 0.3s ease;
        }

        .btn-inicio:hover {
            background-color: #1e5a7a;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(45, 131, 160, 0.3);
        }

        @media (max-width: 899px) {
            .card { padding: 30px 20px; }
            h2 { font-size: 24px; }
            th, td { padding: 12px 10px; font-size: 14px; }
            img.foto-paciente { width: 120px; height: 120px; }
            img.firma { max-width: 120px; }
        }
    </style>
</head>
<body>

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
