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
        max-width: 700px;
        width: 100%;
        text-align: center;
    }

    h2 {
        font-size: 36px;
        color: #2d83a0;
        font-weight: 700;
        margin-bottom: 30px;
    }

    .campo { margin-bottom: 25px; text-align: left; }
    .campo label { 
        display: block; 
        font-size: 18px; 
        color: #333; 
        margin-bottom: 8px; 
        font-weight: 600;
    }
    .campo input[type="text"] {
        width: 100%;
        padding: 18px 22px;
        font-size: 22px;
        border: 2px solid #c8d3db;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
        background: #fff;
    }
    
    .campo input[type="text"]:disabled {
        background: #f5f5f5;
        color: #666;
    }

    /* Área de foto */
    .foto-area {
      text-align: center;
      margin: 30px 0;
    }

    /* Vista previa */
    #preview-container {
      position: relative;
      display: inline-block;
      margin-bottom: 20px;
    }

    #preview {
      width: 250px;
      height: 250px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #2d83a0;
      display: none;
      box-shadow: 0 8px 20px rgba(45, 131, 160, 0.3);
    }

    #placeholder {
      width: 250px;
      height: 250px;
      border-radius: 50%;
      background: #f8f9fa;
      border: 3px dashed #c8d3db;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: #6c757d;
      font-size: 18px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    #placeholder:hover {
        background: #eef2f5;
        border-color: #2d83a0;
        color: #2d83a0;
    }

    #placeholder svg { margin-bottom: 12px; width: 60px; height: 60px; }

    /* Input file oculto */
    #inputFoto { display: none; }

    /* Botones */
    .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px 30px;
      border-radius: 10px;
      font-size: 20px;
      font-weight: 700;
      cursor: pointer;
      border: none;
      transition: all 0.3s ease;
      width: 100%;
    }

    .btn-camara {
      background: #e8f4f8;
      color: #2d83a0;
      margin-bottom: 15px;
      border: 2px solid #bce0ed;
    }
    
    .btn-camara:hover {
      background: #d4eaf2;
    }

    .btn-guardar {
      background-color: #2d83a0;
      color: white;
      margin-top: 10px;
    }

    .btn-guardar:hover:not(:disabled) {
      background-color: #1e5a7a;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(45, 131, 160, 0.3);
    }

    .btn-guardar:disabled {
      background: #b0caced;
      cursor: not-allowed;
      box-shadow: none;
      transform: none;
    }

    .btn-finalizar {
      background-color: #028a0f;
      color: white;
      margin-top: 15px;
      text-decoration: none;
    }

    .btn-finalizar:hover {
      background-color: #02660b;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(2, 138, 15, 0.3);
    }

    /* Mensaje resultado */
    #mensaje {
      display: none;
      margin-top: 20px;
      padding: 16px;
      border-radius: 10px;
      font-size: 18px;
      font-weight: 600;
      text-align: center;
    }
    #mensaje.ok  { background: #e8f5e9; color: #2e7d32; border: 2px solid #c8e6c9; }
    #mensaje.err { background: #ffebee; color: #c62828; border: 2px solid #ffcdd2; }

    .foto-actual {
      text-align: center;
      margin-bottom: 20px;
    }
    .foto-actual img {
      width: 180px; height: 180px;
      border-radius: 50%; object-fit: cover;
      border: 4px solid #2d83a0;
      box-shadow: 0 4px 12px rgba(45, 131, 160, 0.2);
    }
    .foto-actual p { font-size: 16px; color: #666; margin-top: 10px; font-weight: 500; }
    
    /* Optimización para tablet y pantallas pequeñas */
    @media (max-width: 799px) {
        .card { padding: 30px 20px; }
        h2 { font-size: 28px; }
        #preview, #placeholder { width: 200px; height: 200px; }
        .btn { padding: 15px 20px; font-size: 18px; }
        .campo input[type="text"] { font-size: 18px; padding: 14px 18px; }
    }
  </style>
</head>
<body>

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