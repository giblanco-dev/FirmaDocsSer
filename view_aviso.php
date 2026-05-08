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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="Static/img/favicon.png">
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4/dist/signature_pad.umd.min.js"></script>
  <title>Aviso de Privacidad — Consultorio de Medicina Alternativa SER S.C.</title>
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
 
  <!-- ══════════════════ PÁGINA 1 ══════════════════ -->
  <div class="pagina">
 
    <h1 class="doc-titulo">Aviso de Privacidad</h1>
 
    <p class="intro">
      <span class="empresa">Consultorio de Medicina Alternativa SER S.C.</span>, con domicilio en
      calle ELENA No.9, colonia NATIVITAS, CIUDAD DE MEXICO, alcaldía BENITO JUAREZ, C.P. 03500,
      CIUDAD DE MÉXICO, es el responsable del uso y protección de sus datos personales, y al
      respecto le informamos lo siguiente:
    </p>
 
    <!-- Sección 1 -->
    <div class="seccion">
      <p class="seccion-titulo">¿Para qué fines utilizaremos sus datos personales?</p>
      <p>
        Los datos personales que recabamos de usted, los utilizaremos para las siguientes finalidades
        que son necesarias para el servicio que solicita:
      </p>
      <p>Historial Médico</p>
    </div>
 
    <!-- Sección 2 -->
    <div class="seccion">
      <p class="seccion-titulo">¿Qué datos personales utilizaremos para estos fines?</p>
      <p>
        Para llevar a cabo las finalidades descritas en el presente aviso de privacidad, utilizaremos
        los siguientes datos personales:
      </p>
 
      <!-- Dos columnas de datos -->
      <div class="cols">
        <ul>
          <li>Nombre</li>
          <li>Estado Civil</li>
          <li>Lugar de nacimiento</li>
          <li>Fecha de nacimiento</li>
          <li>Nacionalidad</li>
          <li>Domicilio</li>
          <li>Teléfono particular</li>
          <li>Teléfono celular</li>
          <li>Correo electrónico</li>
          <li>Firma autógrafa</li>
        </ul>
        <ul>
          <li>CURP</li>
          <li>Edad</li>
          <li>Estatura</li>
          <li>Peso</li>
          <li>Tipo de sangre</li>
          <li>Pasatiempos</li>
          <li>Deportes que practica</li>
          <li>Antecedentes Médicos</li>
          <li>Datos de identificación</li>
          <li>Datos de contacto</li>
          <li>Datos sobre características físicas</li>
        </ul>
      </div>
 
      <p>
        Además de los datos personales mencionados anteriormente, para las finalidades informadas en
        el presente aviso de privacidad utilizaremos los siguientes datos personales considerados como
        sensibles, que requieren de especial protección:
      </p>
 
      <ul class="lista">
        <li>Religión que profesa</li>
        <li>Estado de salud físico presente, pasado o futuro</li>
        <li>Estado de salud mental presente, pasado o futuro</li>
        <li>Información genética</li>
        <li>Preferencias sexuales</li>
        <li>Prácticas o hábitos sexuales</li>
        <li>Pertenencia a un pueblo, etnia o región</li>
      </ul>
    </div>
 
    <!-- Sección 3 -->
    <div class="seccion">
      <p class="seccion-titulo">
        ¿Cómo puede acceder, rectificar o cancelar sus datos personales, u oponerse a su uso?
      </p>
      <p>
        Usted tiene derecho a conocer qué datos personales tenemos de usted, para qué los utilizamos y
        las condiciones del uso que les damos (Acceso). Asimismo, es su derecho solicitar la corrección
        de su información personal en caso de que esté desactualizada, sea inexacta o incompleta
        (Rectificación); que la eliminemos de nuestros registros o bases de datos cuando considere que
        la misma no está siendo utilizada adecuadamente (Cancelación); así como oponerse al uso de sus
        datos personales para fines específicos (Oposición). Estos derechos se conocen como derechos ARCO.
      </p>
      <p>
        Para el ejercicio de cualquiera de los derechos ARCO, usted deberá presentar la solicitud
        respectiva a través del siguiente medio:
      </p>
      <p class="email-highlight">
        ENVIANDO CORREO ELECTRÓNICO A: clinicalternativaser@gmail.com
      </p>
    </div>
 
  </div><!-- /pagina 1 -->
 
 
  <!-- ══════════════════ PÁGINA 2 ══════════════════ -->
  <div class="pagina">
 
    <!-- Procedimiento ARCO -->
    <div class="seccion">
      <p>
        Con relación al procedimiento y requisitos para el ejercicio de sus derechos ARCO, rectificación
        y revocación de su consentimiento le informamos lo siguiente:
      </p>
 
      <ol class="incisos">
        <li>
          <span class="letra">a)</span>
          ¿A través de qué medios pueden acreditar su identidad el titular y, en su caso, su
          representante, así como la personalidad este último?
          <span class="respuesta">Identificación Oficial Vigente</span>
        </li>
        <li>
          <span class="letra">b)</span>
          — (Identificación oficial vigente)
        </li>
        <li>
          <span class="letra">c)</span>
          ¿Qué información y/o documentación deberá contener la solicitud?
          <span class="respuesta">Motivo de la solicitud</span>
        </li>
        <li>
          <span class="letra">d)</span>
          ¿En cuántos días le daremos respuesta a su solicitud?
          <span class="respuesta">15 Días Hábiles</span>
        </li>
        <li>
          <span class="letra">e)</span>
          ¿Por qué medio le comunicaremos la respuesta a su solicitud?
          <span class="respuesta">Correo Electrónico</span>
        </li>
        <li>
          <span class="letra">f)</span>
          ¿En qué medios se pueden reproducir los datos personales que, en su caso, solicite?
          <span class="respuesta">Copia Física</span>
        </li>
      </ol>
 
      <p>
        Los datos de contacto de la persona o departamento de datos personales, que está a cargo de dar
        trámite a las solicitudes de derechos ARCO, son los siguientes:
      </p>
 
      <ol class="contacto">
        <li>
          <span class="letra">a)</span>
          Nombre de la persona o departamento de datos personales: <strong>Administración.</strong>
        </li>
        <li>
          <span class="letra">b)</span>
          Domicilio: Calle ELENA No.9, colonia NATIVITAS, CIUDAD DE MEXICO, alcaldía BENITO JUAREZ,
          C.P. 03500, CIUDAD DE MEXICO
        </li>
        <li>
          <span class="letra">c)</span>
          Correo electrónico: <strong>clinicalternativaser@gmail.com</strong>
        </li>
        <li>
          <span class="letra">d)</span>
          Número telefónico: <strong>5563658396</strong><br>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Otro dato de contacto: <strong>5555799896</strong>
        </li>
      </ol>
    </div>
 
    <!-- Revocación -->
    <div class="seccion">
      <p class="seccion-titulo">Usted puede revocar su consentimiento para el uso de sus datos personales</p>
      <p>
        Usted puede revocar el consentimiento que, en su caso, nos haya otorgado para el tratamiento de
        sus datos personales. Sin embargo, es importante que tenga en cuenta que no en todos los casos
        podremos atender su solicitud o concluir el uso de forma inmediata, ya que es posible que por
        alguna obligación legal requiramos seguir tratando sus datos personales. Asimismo, usted deberá
        considerar que, para ciertos fines, la revocación de su consentimiento implicará que no le
        podamos seguir prestando el servicio que nos solicitó, o la conclusión de su relación con
        nosotros.
      </p>
      <p>
        Para revocar su consentimiento deberá presentar su solicitud a través del siguiente medio:
        <strong>clinicalternativaser@gmail.com</strong>
      </p>
    </div>
 
    <!-- Limitación de uso -->
    <div class="seccion">
      <p class="seccion-titulo">¿Cómo puede limitar el uso o divulgación de su información personal?</p>
      <p>
        Con objeto de que usted pueda limitar el uso y divulgación de su información personal, le
        ofrecemos los siguientes medios:
      </p>
      <p class="email-highlight">clinicalternativaser@gmail.com</p>
    </div>
 
    <!-- Cambios en el aviso -->
    <div class="seccion">
      <p class="seccion-titulo">¿Cómo puede conocer los cambios en este aviso de privacidad?</p>
      <p>
        El presente aviso de privacidad puede sufrir modificaciones, cambios o actualizaciones derivadas
        de nuevos requerimientos legales; de nuestras propias necesidades por los productos o servicios
        que ofrecemos; de nuestras prácticas de privacidad; de cambios en nuestro modelo de negocio, o
        por otras causas.
      </p>
      <p>
        Nos comprometemos a mantenerlo informado sobre los cambios que pueda sufrir el presente aviso
        de privacidad, a través de: <strong>CARTELES EN EL ESTABLECIMIENTO.</strong>
      </p>
    </div>
 
    <!-- Pie -->
    <p class="pie">Última actualización: 24/05/2021</p>
            
             <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Estimado(a) Paciente. <br><strong><?php echo $nombre_paciente ?>.</strong> 
                    <br>Por favor firme en el espacio inferior para aceptar los terminos y condiciones</div>
                
                <form action="logic/save_firmas.php" method="post" class="signature-form" id="firmaForm">
                    <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                    <input type="hidden" name="documento" value="AP">
                    <canvas id="firma" width="550" height="240"></canvas>
                    <p class="signature-help">✎ Firme con su dedo o ratón en el recuadro</p>
                    
                    <div class="signature-buttons">
                        <button type="button" class="btn-limpiar" onclick="limpiarFirma()">Limpiar Firma</button>
                        <button type="submit" class="btn-guardar" name="guardar_firma">Guardar Firma</button>
                    </div>
                </form>
            </div>
        </div>

  </div><!-- /pagina 2 -->
 

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