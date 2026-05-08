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
  <title>Foto Paciente</title>
  <link rel="stylesheet" href="Static/css/styles.css">
</head>
<body class="ui-theme">

<div class="card">
  <h2>Foto de Paciente</h2>

  <div class="campo">
    <p style="text-align: justify; font-size: 14px;">Por favor, proporcione una fotografía clara del paciente, donde se pueda identificar plenamente su rostro. Evite que las manos, otros objetos o personas tapen su cara. Para complementar su expediente clínico.</p>
           
  </div>

  <div class="campo">
    <label>Paciente</label>
    <input type="hidden" id="id_paciente"value="<?php echo htmlspecialchars($id_paciente); ?>">
    <input type="text" id="nom_paciente" readonly value="<?php echo htmlspecialchars($nombre_paciente); ?> Código: <?php echo htmlspecialchars($id_paciente); ?>" disabled
           style="background:#f9f9f9; color:#555;">
  </div>

  <!-- Foto actual del paciente (si ya tiene) -->
  <div class="foto-actual" id="foto-actual" style="display:none;">
    <img id="img-actual" src="" alt="Foto actual">
    <p>Foto actual</p>
  </div>

  <!-- Área de captura -->
  <div class="foto-area">
    <div id="preview-container">
      <!-- Placeholder clickeable -->
      <div id="placeholder" onclick="abrirCamara()">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="1.5">
          <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
          <circle cx="12" cy="13" r="4"/>
        </svg>
        Toca para tomar foto
      </div>
      <!-- Preview -->
      <img id="preview" src="" alt="Vista previa">
    </div>

    <!-- Input oculto que abre la cámara frontal -->
    <input type="file"
           id="inputFoto"
           accept="image/*"
           capture="user">

    <br>
    <button class="btn btn-camara" onclick="abrirCamara()">
      Tomar foto con cámara frontal
    </button>
  </div>

  <!-- Botón guardar -->
  <button class="btn btn-guardar" id="btnGuardar"
          onclick="guardarFoto()" disabled>
    Guardar foto
  </button>

  <div id="mensaje"></div>
  
  <a href="#" id="btnFinalizar" class="btn btn-finalizar" style="display: none;">Finalizar proceso</a>
</div>

<script>
  const inputFoto    = document.getElementById('inputFoto');
  const preview      = document.getElementById('preview');
  const placeholder  = document.getElementById('placeholder');
  const btnGuardar   = document.getElementById('btnGuardar');
  const mensaje      = document.getElementById('mensaje');

  let archivoFoto = null;

  /* Abre el input file (que dispara la cámara frontal) */
  function abrirCamara() {
    inputFoto.click();
  }

  /* Cuando el usuario toma/elige la foto */
  inputFoto.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;

    archivoFoto = file;

    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src     = e.target.result;
      preview.style.display    = 'block';
      placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);

    actualizarBoton();
  });

  /* Busca el nombre del paciente al escribir el ID */
  let timer;
  function buscarPaciente(id) {
    clearTimeout(timer);
    document.getElementById('nom_paciente').value = '';
    document.getElementById('foto-actual').style.display = 'none';
    actualizarBoton();

    if (id.length < 1) return;

    timer = setTimeout(() => {
      fetch(`buscar_paciente.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
          if (data.encontrado) {
            document.getElementById('nom_paciente').value = data.nombre;

            // Si ya tiene foto, mostrarla
            if (data.foto) {
              document.getElementById('img-actual').src = 'fotos_pacientes/' + data.foto;
              document.getElementById('foto-actual').style.display = 'block';
            } else {
              document.getElementById('foto-actual').style.display = 'none';
            }
          } else {
            document.getElementById('nom_paciente').value = 'Paciente no encontrado';
          }
          actualizarBoton();
        })
        .catch(() => {
          document.getElementById('nom_paciente').value = 'Error al buscar';
        });
    }, 500);
  }

  /* Activa el botón solo si hay paciente Y foto */
  function actualizarBoton() {
    const idOk   = document.getElementById('id_paciente').value.trim() !== '';
    const nomOk  = document.getElementById('nom_paciente').value !== '' &&
                   document.getElementById('nom_paciente').value !== 'Paciente no encontrado' &&
                   document.getElementById('nom_paciente').value !== 'Error al buscar';
    btnGuardar.disabled = !(idOk && nomOk && archivoFoto);
  }

  /* Envía la foto al servidor */
  function guardarFoto() {
    const id = document.getElementById('id_paciente').value.trim();
    if (!id || !archivoFoto) return;

    mostrarMensaje('Guardando...', '');
    btnGuardar.disabled = true;

    const formData = new FormData();
    formData.append('foto',        archivoFoto);
    formData.append('id_paciente', id);

    fetch('logic/save_foto.php', { method: 'POST', body: formData })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          mostrarMensaje('Foto guardada correctamente', 'ok');
          // Actualizar foto actual
          document.getElementById('img-actual').src = 'FotosPacientes/' + data.archivo;
          document.getElementById('foto-actual').style.display = 'block';
          archivoFoto = null;
          inputFoto.value = '';
          preview.style.display   = 'none';
          placeholder.style.display = 'flex';
          
          document.getElementById('btnFinalizar').href = 'finalizar_aceptación.php?id_paciente=' + id;
          document.getElementById('btnFinalizar').style.display = 'flex';

        } else {
          mostrarMensaje('Error: ' + data.error, 'err');
        }
        btnGuardar.disabled = false;
      })
      .catch(() => {
        mostrarMensaje('Error de conexión con el servidor', 'err');
        btnGuardar.disabled = false;
      });
  }

  function mostrarMensaje(texto, tipo) {
    mensaje.textContent    = texto;
    mensaje.className      = tipo;
    mensaje.style.display  = 'block';
  }
</script>
</body>
</html>