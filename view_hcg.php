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
    <link rel="stylesheet" href="Static/css/styles.css">
</head>
<body class="doc-theme-hcg">
<body class="doc-theme-hcg">
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