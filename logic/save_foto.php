<?php
/**
 * guardar_foto.php
 * Recibe la foto, la guarda en /fotos_pacientes/ y actualiza la tabla paciente
 */

header('Content-Type: application/json; charset=utf-8');

// ─── Configuración de base de datos ─────────────────────────────────────────
require_once 'conn.php';

// ─── Carpeta donde se guardan las fotos ─────────────────────────────────────
$carpeta = __DIR__ . '/../FotosPacientes/';

// Crear carpeta si no existe
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

// ─── Validaciones básicas ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$id_paciente = isset($_POST['id_paciente']) ? intval($_POST['id_paciente']) : 0;
if ($id_paciente <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID de paciente inválido']);
    exit;
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $errores = [
        UPLOAD_ERR_INI_SIZE   => 'Archivo muy grande (límite php.ini)',
        UPLOAD_ERR_FORM_SIZE  => 'Archivo muy grande (límite formulario)',
        UPLOAD_ERR_PARTIAL    => 'Subida incompleta',
        UPLOAD_ERR_NO_FILE    => 'No se recibió archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Sin carpeta temporal',
        UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
    ];
    $codigo = $_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE;
    echo json_encode(['ok' => false, 'error' => $errores[$codigo] ?? 'Error desconocido']);
    exit;
}

// ─── Validar que sea imagen ──────────────────────────────────────────────────
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
$tipoReal = mime_content_type($_FILES['foto']['tmp_name']);

if (!in_array($tipoReal, $tiposPermitidos)) {
    echo json_encode(['ok' => false, 'error' => 'Solo se permiten imágenes JPG, PNG o WEBP']);
    exit;
}

// Extensión según tipo MIME
$extensiones = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];
$ext = $extensiones[$tipoReal];

// ─── Nombre de archivo: paciente_ID_timestamp.ext ───────────────────────────
$nombreArchivo = 'paciente_' . $id_paciente . '_' . time() . '.' . $ext;
$rutaDestino   = $carpeta . $nombreArchivo;

// ─── Mover archivo ──────────────────────────────────────────────────────────
if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el archivo en el servidor']);
    exit;
}

// ─── Guardar ruta en base de datos ──────────────────────────────────────────
try {
    // Si el paciente ya tenía foto, borrar el archivo anterior
    $stmtAnterior = $mysqli->prepare("SELECT foto FROM paciente WHERE id_paciente = ?");
    $stmtAnterior->bind_param("i", $id_paciente);
    $stmtAnterior->execute();
    $stmtAnterior->bind_result($anterior);
    $stmtAnterior->fetch();
    $stmtAnterior->close();

    if (!empty($anterior) && file_exists($carpeta . $anterior)) {
        unlink($carpeta . $anterior);
    }

    // Actualizar con la nueva foto
    $stmt = $mysqli->prepare("UPDATE paciente SET foto = ? , fecha_foto = NOW() WHERE id_paciente = ?");
    $stmt->bind_param("si", $nombreArchivo, $id_paciente);
    $stmt->execute();

    // Si affected_rows es 0, podría ser que el paciente no exista
    // Validamos si de verdad existe:
    if ($stmt->affected_rows === 0) {
        $check = $mysqli->query("SELECT id_paciente FROM paciente WHERE id_paciente = '$id_paciente'");
        if ($check->num_rows === 0) {
            unlink($rutaDestino); // Borrar el archivo que ya se subió
            echo json_encode(['ok' => false, 'error' => 'Paciente no encontrado en la base de datos']);
            exit;
        }
    }
    $stmt->close();

    echo json_encode(['ok' => true, 'archivo' => $nombreArchivo]);

} catch (Exception $e) {
    // Si hay error de BD, borrar el archivo subido
    if (file_exists($rutaDestino)) unlink($rutaDestino);
    echo json_encode(['ok' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}