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
  <style>
    @import url('https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Source+Sans+3:wght@400;600;700&display=swap');

    :root {
      --font-serif:  'EB Garamond', Georgia, serif;
      --font-sans:   'Source Sans 3', Arial, sans-serif;
      --ink:         #1a1a1a;
      --ink-mid:     #333;
      --border:      #555;
      --page-bg:     #f7f5f0;
      --paper:       #ffffff;
      --base:        22px;
      --lh:          1.80;
      --page-w:      960px;
      --pad:         72px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--font-sans);
      font-size: var(--base);
      line-height: var(--lh);
      color: var(--ink);
      background: var(--page-bg);
      padding: 48px 24px 80px;
    }

    /* ── Botón imprimir ─────────────────────────────────────── */
    .print-bar {
      max-width: var(--page-w);
      margin: 0 auto 28px;
      display: flex;
      justify-content: flex-end;
    }
    .btn-print {
      font-family: var(--font-sans);
      font-size: 17px;
      font-weight: 600;
      color: #fff;
      background: #1a1a1a;
      border: none;
      border-radius: 8px;
      padding: 12px 28px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      letter-spacing: 0.02em;
      transition: background 0.18s;
    }
    .btn-print:hover { background: #333; }
    .btn-print svg   { width: 20px; height: 20px; }

    /* ── Hoja ───────────────────────────────────────────────── */
    .pagina {
      background: var(--paper);
      max-width: var(--page-w);
      margin: 0 auto;
      padding: var(--pad);
      border: 1px solid #ccc;
      box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    }

    /* ── Encabezado con logo ────────────────────────────────── */
    .encabezado {
      display: flex;
      align-items: center;
      gap: 28px;
      margin-bottom: 32px;
    }

    .encabezado img {
      width: 90px;
      height: 90px;
      object-fit: contain;
      flex-shrink: 0;
    }

    .encabezado-texto {
      font-family: var(--font-sans);
      font-size: 1.35rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--ink);
      line-height: 1.3;
    }

    /* ── Título documento ───────────────────────────────────── */
    .doc-titulo {
      font-family: var(--font-sans);
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: 0.10em;
      text-align: center;
      text-transform: uppercase;
      color: var(--ink);
      margin-bottom: 28px;
    }

    /* ── Línea de fecha ─────────────────────────────────────── */
    .linea-fecha {
      font-size: 1rem;
      text-align: center;
      margin-bottom: 28px;
      color: var(--ink);
    }

    .linea-fecha .subr {
      display: inline-block;
      min-width: 60px;
      border-bottom: 1.5px solid var(--border);
      margin: 0 4px;
      vertical-align: bottom;
    }

    .linea-fecha .subr-lg {
      min-width: 180px;
    }

    /* ── Línea YO ───────────────────────────────────────────── */
    .bloque-yo {
      font-size: 1rem;
      line-height: var(--lh);
      margin-bottom: 6px;
    }

    .bloque-yo .linea-yo {
      display: inline-block;
      min-width: 380px;
      border-bottom: 1.5px solid var(--border);
      margin: 0 6px;
      vertical-align: bottom;
    }

    /* ── Línea diagnóstico ──────────────────────────────────── */
    .bloque-dx {
      font-size: 1rem;
      margin-bottom: 32px;
    }

    .bloque-dx .linea-dx {
      display: inline-block;
      min-width: 280px;
      border-bottom: 1.5px solid var(--border);
      margin: 0 6px;
      vertical-align: bottom;
    }

    /* ── Subtítulo declaración ──────────────────────────────── */
    .subtitulo-declaro {
      font-family: var(--font-sans);
      font-size: 1rem;
      font-weight: 700;
      text-align: center;
      text-transform: uppercase;
      margin-bottom: 28px;
      color: var(--ink);
    }

    /* ── Puntos numerados ───────────────────────────────────── */
    .puntos {
      list-style: none;
      padding: 0;
      margin-bottom: 52px;
    }

    .puntos li {
      font-size: 1rem;
      line-height: var(--lh);
      text-align: justify;
      margin-bottom: 22px;
      padding-left: 0;
    }

    /* Punto 1: texto con parte en subrayado/itálica */
    .puntos li em {
      font-style: italic;
    }

    .puntos li strong {
      font-weight: 700;
    }

    /* Punto 2 bloque destacado */
    .bloque-italica {
      font-style: italic;
      font-size: 0.97rem;
      line-height: var(--lh);
      text-align: justify;
      margin-top: 8px;
      margin-bottom: 0;
    }

    /* ── Línea terapia (punto 1) ────────────────────────────── */
    .linea-terapia {
      display: inline-block;
      min-width: 240px;
      border-bottom: 1.5px solid var(--border);
      margin: 0 4px;
      vertical-align: bottom;
    }

    /* ── Bloque de firmas ───────────────────────────────────── */
    .firmas {
      margin-top: 16px;
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

    /* ── IMPRESIÓN ──────────────────────────────────────────── */
    @media print {
      body        { background: white; padding: 0; }
      .print-bar  { display: none; }
      .pagina {
        box-shadow: none;
        border: none;
        margin: 0;
        padding: 14mm 20mm 18mm;
        max-width: 100%;
      }
      :root {
        --base: 11pt;
        --pad: 0;
        --lh: 1.70;
      }

      .encabezado-texto { font-size: 13pt; }
      .doc-titulo       { font-size: 11pt; margin-bottom: 16pt; }
      .linea-fecha,
      .bloque-yo,
      .bloque-dx,
      .puntos li,
      .bloque-italica,
      .subtitulo-declaro { font-size: 10.5pt; }
      .firma-etiqueta    { font-size: 8.5pt; }

      .firmas-fila { margin-bottom: 36pt; }
      .firma-espacio { height: 48pt; }

      @page { size: Letter portrait; margin: 15mm 20mm; }
    }
  </style>
</head>
<body>

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
                <div class="signature-title">Estimado(a) Paciente: <strong><?php echo $nombre_paciente ?>.</strong> 
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