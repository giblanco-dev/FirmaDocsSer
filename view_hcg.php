<?php

include_once 'logic/conn.php';

// Validación de datos POST
if (!isset($_POST['codigo']) || empty($_POST['codigo'])) {
    header('Location: index.php?error=1');
    exit();
}

$id_paciente = trim($_POST['codigo']);

// Validar que sea numérico
if (!is_numeric($id_paciente)) {
    header('Location: index.php?error=2');
    exit();
}

// Validar que sea un número positivo
if ($id_paciente <= 0) {
    header('Location: index.php?error=3');
    exit();
}

$sql_h_clin = "SELECT his_clinica_gen.*,
CONCAT(paciente.nombres,' ',paciente.a_paterno,' ',paciente.a_materno) Nombre_completo,
paciente.genero, paciente.fecha_nacimiento, paciente.id_paciente, paciente.fecha_captura,
CONCAT(user.nombre,' ',user.apellido) Medico
FROM his_clinica_gen
INNER JOIN paciente ON his_clinica_gen.id_paciente = paciente.id_paciente
INNER JOIN user ON his_clinica_gen.medico = user.usuario
where his_clinica_gen.id_paciente = '$id_paciente'";

$res_h_clin = $mysqli->query($sql_h_clin);
$val_his_clin = $res_h_clin->num_rows;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="Static/img/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4/dist/signature_pad.umd.min.js"></script>
    <title>Historia Clínica Paciente <?php echo $id_paciente; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            background-color: #f5f5f5;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
                font-size: 11px;
            }
            
            @page {
                size: letter;
                margin: 0.75in;
            }
            
            .no-print {
                display: none !important;
            }
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 25px;
            background: white;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            border-bottom: 3px solid #2d83a0;
            padding-bottom: 15px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .header-info {
            flex: 1;
            font-size: 14px;
            line-height: 1.5;
            min-width: 200px;
        }

        .header-clinic {
            flex: 1;
            text-align: center;
            min-width: 200px;
        }

        .header-clinic h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #2d83a0;
            font-weight: 700;
        }

        .header-patient {
            flex: 1;
            font-size: 14px;
            line-height: 1.5;
            min-width: 200px;
        }

        .logo {
            width: 100px;
            height: 100px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            color: #2d83a0;
            margin: 30px 0 20px 0;
            page-break-after: avoid;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            background-color: #e8f4f8;
            padding: 10px 12px;
            margin-bottom: 10px;
            border-left: 5px solid #2d83a0;
        }

        .section-subtitle {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
            padding: 6px 10px;
            background-color: #f5f5f5;
            border-left: 3px solid #ccc;
        }

        .content-row {
            display: flex;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .content-label {
            width: 35%;
            font-weight: 700;
            padding-right: 15px;
            flex-shrink: 0;
            font-size: 15px;
        }

        .content-value {
            flex: 1;
            word-wrap: break-word;
            font-size: 15px;
            line-height: 1.5;
        }

        .vital-signs {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .vital-item {
            page-break-inside: avoid;
            background-color: #f9f9f9;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

        .vital-label {
            font-weight: 700;
            font-size: 14px;
            background-color: #e8f4f8;
            padding: 6px 8px;
            margin-bottom: 8px;
            border-radius: 3px;
        }

        .vital-value {
            font-size: 15px;
            padding: 4px 0;
            font-weight: 500;
        }

        .exploration-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .exploration-item {
            page-break-inside: avoid;
            background-color: #f9f9f9;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

        .exploration-label {
            font-weight: 700;
            font-size: 14px;
            background-color: #e8f4f8;
            padding: 6px 8px;
            margin-bottom: 8px;
            border-radius: 3px;
        }

        .exploration-value {
            font-size: 15px;
            padding: 4px 0;
            line-height: 1.5;
        }

        .divider {
            border-top: 2px solid #ddd;
            margin: 20px 0;
            page-break-after: avoid;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .signature-box {
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .signature-title {
            font-size: 18px;
            font-weight: 700;
            color: #2d83a0;
            margin-bottom: 15px;
            text-align: center;
        }

        .signature-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        #firma {
            border: 3px solid #2d83a0;
            border-radius: 8px;
            background-color: #ffffff;
            cursor: crosshair;
            box-shadow: 0 4px 12px rgba(45, 131, 160, 0.2);
            touch-action: none;
            display: block;
            margin: 0 auto;
        }

        .signature-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
        }

        .signature-buttons button {
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 50px;
            flex: 1;
            min-width: 150px;
        }

        .btn-guardar {
            background-color: #2d83a0;
            color: white;
        }

        .btn-guardar:hover {
            background-color: #1e5a7a;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(45, 131, 160, 0.3);
        }

        .btn-guardar:active {
            transform: translateY(0);
        }

        .btn-limpiar {
            background-color: #f57c00;
            color: white;
        }

        .btn-limpiar:hover {
            background-color: #e65100;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 124, 0, 0.3);
        }

        .btn-limpiar:active {
            transform: translateY(0);
        }

        .signature-help {
            font-size: 13px;
            color: #666;
            margin-top: 10px;
            font-style: italic;
        }

        .signature-line {
            border-top: 2px solid #000;
            margin-top: 60px;
            padding-top: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .no-data {
            background-color: #fff3cd;
            padding: 25px;
            border: 2px solid #ffc107;
            border-radius: 6px;
            text-align: center;
            font-size: 16px;
        }

        @media screen and (max-width: 1280px) {
            .header {
                flex-direction: column;
            }
            
            .header-info,
            .header-clinic,
            .header-patient {
                min-width: auto;
                width: 100%;
            }
            
            .exploration-grid {
                grid-template-columns: 1fr;
            }
            
            .vital-signs {
                grid-template-columns: 1fr 1fr;
            }

            #firma {
                width: 100% !important;
                height: auto !important;
                max-width: 100%;
            }

            .signature-buttons button {
                flex: 1 1 calc(50% - 6px);
                min-width: 120px;
            }
        }

        @media screen and (min-width: 800px) and (max-width: 1600px) {
            .vital-signs {
                grid-template-columns: 1fr 1fr 1fr;
            }
            
            body {
                font-size: 17px;
            }

            .signature-title {
                font-size: 22px;
                margin-bottom: 20px;
            }

            #firma {
                width: 100% !important;
                height: 280px !important;
                border-width: 4px;
            }

            .signature-buttons button {
                padding: 18px 36px;
                font-size: 18px;
                min-height: 60px;
                flex: 1 1 calc(50% - 8px);
                min-width: 160px;
            }

            .signature-help {
                font-size: 15px;
            }
        }

        @media screen and (min-width: 1600px) {
            body {
                font-size: 18px;
            }
            
            .title {
                font-size: 22px;
            }
            
            .section-title {
                font-size: 17px;
            }
            
            .content-value,
            .exploration-value,
            .vital-value {
                font-size: 16px;
            }

            .signature-title {
                font-size: 24px;
                margin-bottom: 25px;
            }

            #firma {
                width: 100% !important;
                height: 320px !important;
                border-width: 4px;
            }

            .signature-buttons button {
                padding: 20px 40px;
                font-size: 20px;
                min-height: 70px;
                flex: 1 1 calc(50% - 10px);
                min-width: 180px;
            }

            .signature-help {
                font-size: 16px;
            }
        }

        @media screen {
            .container {
                background: white;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                margin: 15px auto;
                border-radius: 6px;
            }
            
            .print-button {
                text-align: center;
                margin: 25px 0;
            }
            
            .print-button button {
                padding: 12px 24px;
                background-color: #2d83a0;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 16px;
                margin: 0 8px;
                transition: all 0.3s ease;
                font-weight: 600;
            }
            
            .print-button button:hover {
                background-color: #1e5a7a;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            
            .print-button .close-btn {
                background-color: #d32f2f;
            }
            
            .print-button .close-btn:hover {
                background-color: #b71c1c;
            }
        }
    </style>
</head>
<body>
<body>
    <?php 
    if($val_his_clin == 1){
        $row_h_c = mysqli_fetch_assoc($res_h_clin);
    ?>
    
    <div class="container">
        <!-- ENCABEZADO -->
        <div class="header">
            <div class="header-info">
                <strong>Clínica de Medicina Alternativa SER</strong><br>
                Elena 9, Colonia Nativitas<br>
                Del. Benito Juárez, Distrito Federal<br>
                (55) 5579-9896, 6365-8396
            </div>
            <div class="header-clinic">
                <h3>HISTORIA CLÍNICA</h3>
            </div>
            <div class="header-patient">
                <strong>Paciente:</strong> <?php echo $row_h_c['Nombre_completo']; ?><br>
                <strong>Fecha Nac.:</strong> <?php echo $row_h_c['fecha_nacimiento']; ?><br>
                <strong>Género:</strong> <?php echo $row_h_c['genero']; ?><br>
                <strong>Fecha Alta:</strong> <?php echo $row_h_c['fecha_captura']; ?>
            </div>
        </div>

        <!-- ANTECEDENTES -->
        <div class="title">ANTECEDENTES CLÍNICOS</div>

        <div class="section">
            <div class="section-title">Antecedentes Heredo Familiares</div>
            <div class="content-value"><?php echo $row_h_c['hcg2'] ?: 'No reportado'; ?></div>
        </div>

        <div class="section">
            <div class="section-title">Antecedentes Personales No Patológicos</div>
            <div class="content-value"><?php echo $row_h_c['hcg3'] ?: 'No reportado'; ?></div>
        </div>

        <div class="section">
            <div class="section-title">Antecedentes Personales Patológicos</div>
            <div class="content-value"><?php echo $row_h_c['hcg4'] ?: 'No reportado'; ?></div>
        </div>

        <div class="section">
            <div class="section-title">Antecedentes Gineco-Obstétricos</div>
            <div class="content-value"><?php echo $row_h_c['hcg5'] ?: 'No reportado'; ?></div>
        </div>

        <div class="section">
            <div class="section-title">Padecimiento Actual</div>
            <div class="content-value"><?php echo $row_h_c['hcg6'] ?: 'No reportado'; ?></div>
        </div>

        <div class="divider"></div>

        <!-- INTERROGATORIO POR APARATOS Y SISTEMAS -->
        <div class="title">INTERROGATORIO POR APARATOS Y SISTEMAS</div>

        <div class="exploration-grid">
            <div class="exploration-item">
                <div class="exploration-label">Respiratorio</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg7'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Gastrointestinal</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg8'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Genitourinario</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg9'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Hematopoyético y Linfático</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg10'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Endocrino</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg11'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Nervioso</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg12'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Músculos Esqueléticos</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg13'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Piel, Mucosa y Anexos</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg14'] ?: '—'; ?></div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- SIGNOS VITALES -->
        <div class="title">SIGNOS VITALES</div>

        <div class="vital-signs">
            <div class="vital-item">
                <div class="vital-label">T/A</div>
                <div class="vital-value"><?php echo $row_h_c['hcg15'] ?: '—'; ?> mm Hg</div>
            </div>
            <div class="vital-item">
                <div class="vital-label">Temperatura</div>
                <div class="vital-value"><?php echo $row_h_c['hcg16'] ?: '—'; ?> °C</div>
            </div>
            <div class="vital-item">
                <div class="vital-label">Frecuencia Cardíaca</div>
                <div class="vital-value"><?php echo $row_h_c['hcg17'] ?: '—'; ?> lpm</div>
            </div>
            <div class="vital-item">
                <div class="vital-label">Frecuencia Respiratoria</div>
                <div class="vital-value"><?php echo $row_h_c['hcg18'] ?: '—'; ?> rpm</div>
            </div>
            <div class="vital-item">
                <div class="vital-label">Peso</div>
                <div class="vital-value"><?php echo $row_h_c['hcg19'] ?: '—'; ?> Kg</div>
            </div>
            <div class="vital-item">
                <div class="vital-label">Talla</div>
                <div class="vital-value"><?php echo $row_h_c['hcg20'] ?: '—'; ?> m</div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- EXPLORACIÓN FÍSICA -->
        <div class="title">EXPLORACIÓN FÍSICA</div>

        <div class="exploration-grid">
            <div class="exploration-item">
                <div class="exploration-label">Habitus Exterior</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg21'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Cabeza y Cuello</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg22'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Tórax</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg23'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Abdomen</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg24'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Genitales</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg25'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Extremidades</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg26'] ?: '—'; ?></div>
            </div>
            <div class="exploration-item">
                <div class="exploration-label">Piel</div>
                <div class="exploration-value"><?php echo $row_h_c['hcg27'] ?: '—'; ?></div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- RESULTADOS Y DIAGNÓSTICOS -->
        <div class="section">
            <div class="section-title">Resultados de Laboratorio, Gabinete y Otros</div>
            <div class="content-value"><?php echo $row_h_c['hcg28'] ?: 'No reportado'; ?></div>
        </div>

        <div class="section">
            <div class="section-title">Diagnósticos o Problemas Clínicos</div>
            <div class="content-value"><?php echo $row_h_c['hcg29'] ?: 'No reportado'; ?></div>
        </div>

        <div class="divider"></div>

        <!-- TERAPÉUTICA -->
        <div class="title">TRATAMIENTO FARMACOLÓGICO</div>

        <div class="section">
            <div class="section-title">Terapéutica Empleada y Resultados (Previos)</div>
            <div class="content-value"><?php echo $row_h_c['hcg30'] ?: 'No reportado'; ?></div>
        </div>

        <div class="section">
            <div class="section-title">Terapéutica Actual</div>
            <div class="content-value"><?php echo $row_h_c['hcg31'] ?: 'No reportado'; ?></div>
        </div>

        <div class="section">
            <div class="section-title">Pronósticos</div>
            <div class="content-value"><?php echo $row_h_c['hcg32'] ?: 'No reportado'; ?></div>
        </div>

        <div class="divider"></div>

        <!-- FIRMA DEL MÉDICO -->
        
        
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Firme en el espacio inferior</div>
                
                <form action="logic/save_firmas.php" method="post" class="signature-form" id="firmaForm">
                    <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                    <input type="hidden" name="documento" value="HGC">
                    <canvas id="firma" width="550" height="240"></canvas>
                    <p class="signature-help">✎ Firme con su dedo o ratón en el recuadro</p>
                    
                    <div class="signature-buttons">
                        <button type="button" class="btn-limpiar" onclick="limpiarFirma()">Limpiar Firma</button>
                        <button type="submit" class="btn-guardar" name="guardar_firma">Guardar Firma</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <?php 
    } else {
        echo '<div class="container"><div class="no-data"><h3>Error de Datos</h3><p>No se encontró historia clínica para el código: '.$id_paciente.'</p></div></div>';
    }
    ?>

</body>

<script>
  // Inicializar SignaturePad
  const canvas = document.getElementById('firma');
  const pad = new SignaturePad(canvas, {
    penColor: '#000000',
    minWidth: 2,
    maxWidth: 4,
    throttle: 16
  });

  // Función para limpiar la firma
  function limpiarFirma() {
    pad.clear();
  }

  // Validar que hay firma antes de enviar
  document.getElementById('firmaForm').addEventListener('submit', function(e) {
    if (pad.isEmpty()) {
      e.preventDefault();
      alert('⚠️ Por favor firme en el espacio designado antes de guardar.');
      return false;
    }
    
    // Enviar la firma como base64
    const firmaData = pad.toDataURL();
    
    // Crear input hidden para enviar la firma
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'firma_base64';
    input.value = firmaData;
    this.appendChild(input);
  });

  // Ajustar canvas al tamaño del contenedor en responsive
  function resizeCanvas() {
    const box = canvas.parentElement;
    const ratio = window.devicePixelRatio || 1;
    
    canvas.width = Math.min(550 * ratio, box.offsetWidth * ratio);
    canvas.height = 240 * ratio;
    
    canvas.style.width = Math.min(550, box.offsetWidth) + 'px';
    canvas.style.height = '240px';
    
    const ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);
  }

  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();
</script>
</html>