<?php
require_once 'logic/conn.php';
require_once 'logic/datos_paciente.php';

if (!isset($_GET['id_paciente']) || empty($_GET['id_paciente'])) {
    die("<div style='text-align: center; padding: 50px; font-family: sans-serif; color: #333;'><h2>Aviso</h2><p>No es posible avanzar en el proceso de firma de documentos. (No se recibió el parámetro id_paciente)</p></div>");
}

$id_paciente = $_GET['id_paciente'];
$datos_paciente = obtener_datos_paciente($mysqli, $id_paciente);
$nombre_paciente = $datos_paciente['nombre_comp'];

if (!$datos_paciente) {
    die("<div style='text-align: center; padding: 50px; font-family: sans-serif; color: #333;'><h2>Aviso</h2><p>No es posible avanzar en el proceso de firma de documentos. (Los datos del paciente no existen o no se encontraron)</p></div>");
}

// $obtener fecha
$fecha = new DateTime();
$fecha_actual = $fecha->format('d');
$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$fecha_mes = $meses[date('n') - 1];
$fecha_anio = $fecha->format('Y');

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="Static/img/favicon.png">
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4/dist/signature_pad.umd.min.js"></script>
  <title>Carta de Consentimiento Informado — Consultorio de Medicina Alternativa SER S.C.</title>
  <link rel="stylesheet" href="Static/css/styles.css">
</head>
<body class="doc-theme-paper">

  <!-- Botón imprimir -->
  <!--div class="print-bar">
    <button class="btn-print" onclick="window.print()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="6 9 6 2 18 2 18 9"/>
        <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
        <rect x="6" y="14" width="12" height="8"/>
      </svg>
      Imprimir
    </button>
  </div-->

  <div class="pagina">

    <!-- ENCABEZADO -->
    <div class="encabezado">
      <img src="Static/img/logo.png" alt="Logo Consultorio SER">
      <div class="encabezado-texto">
        Consultorio de Medicina Alternativa SER, S.C.
      </div>
    </div>

    <!-- TÍTULO -->
    <p class="doc-titulo">Carta de Consentimiento Informado</p>

    <!-- FECHA -->
    <p class="linea-fecha">
      Ciudad de México a
      <span class="subr" style="min-width:50px;"><?php echo $fecha_actual; ?></span>
      de
      <span class="subr subr-lg"><?php echo $fecha_mes; ?></span>
      de
      <span class="subr" style="min-width:70px;"><?php echo $fecha_anio; ?></span>
    </p>

    <!-- YO -->
    <p class="bloque-yo">
      YO <span class="linea-yo"><strong><?php echo $nombre_paciente ?></strong></span>,
    
      en pleno uso de mis facultades mentales y en mi calidad de paciente o representante de este, con el diagnóstico realizado previamente.
    </p>


    <!-- DECLARO -->
    <p class="subtitulo-declaro">Declaro en forma libre y voluntaria lo siguiente:</p>

    <!-- PUNTOS -->
    <ol class="puntos">

      <li>
        <strong>1.-</strong> Me han explicado que puede haber <strong>RIESGOS</strong> durante el
        <u>procedimiento</u> de las terapias 
        <span class="linea-terapia">aplicadas en el Consultorio de Medicina Alternativa SER</span>
        que, aunque poco probables, son posibles y pueden ser: <strong>Dolor,
        Inflamación, hemorragia, hematoma, etc.</strong>
      </li>

      <li>
        <strong>2.-</strong> El <strong>BENEFICIO</strong> que obtendré con este procedimiento es para
        <strong>intentar mejorar mi estado de salud.</strong>
        <p class="bloque-italica">
          Entiendo también que todo acto médico implica una <em>serie de riesgos que puede deberse a mi
          estado de salud, alteraciones congénitas o anatómicas que padezca, mis antecedentes de
          enfermedades, tratamientos actuales y previos al procedimiento médico al que he decidido
          someterme.</em> Se me ha informado que, al presentarse eventos adversos durante el procedimiento, el
          Médico podrá modificar la técnica del procedimiento o realizar otros procedimientos con el objetivo
          de preservar mi vida y limitar los daños.
        </p>
      </li>

      <li>
        <strong>3.-</strong> Puedo requerir de tratamiento complementarios y la participación de otros
        servicios o unidades médicas.
      </li>

      <li>
        <strong>4.-</strong> Se me ha informado que, de no existir este documento en mi expediente,
        no se podrá llevar a cabo el procedimiento planeado y así mismo, puedo revocar mi consentimiento
        sí esa es mi decisión.
      </li>

    </ol>

    <!-- FIRMAS -->
    <div class="firmas">

      <!-- Fila 1: Paciente | Médico -->
      <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Estimado(a) Paciente. <br> <strong><?php echo $nombre_paciente ?>.</strong> 
                    <br>Por favor firme en el espacio inferior para aceptar los terminos y condiciones</div>
                
                <form action="logic/save_firmas.php" method="post" class="signature-form" id="firmaForm">
                    <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                    <input type="hidden" name="documento" value="CIN">
                    <canvas id="firma" width="550" height="240"></canvas>
                    <p class="signature-help">✎ Firme con su dedo o ratón en el recuadro</p>
                    
                    <div class="signature-buttons">
                        <button type="button" class="btn-limpiar" onclick="limpiarFirma()">Limpiar Firma</button>
                        <button type="submit" class="btn-guardar" name="guardar_firma">Guardar Firma</button>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /firmas -->

  </div><!-- /pagina -->

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