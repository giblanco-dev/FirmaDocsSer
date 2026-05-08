<?php
// save_firma.php

// Incluir conexión a BD
require_once 'conn.php';

$id_paciente = intval($_POST['id_paciente'] ?? 0);
$firma_base64 = $_POST['firma_base64'] ?? '';  // llega como "data:image/png;base64,iVBOR..."
$message = '';
$success = false;
$documento = $_POST['documento'] ?? '';

// Validación de datos
if (!$id_paciente || !$firma_base64) {
    $message = '❌ Datos incompletos. No se pudo procesar la firma.';
} elseif (strpos($firma_base64, 'data:image') === false) {
    $message = '❌ Formato de imagen inválido.';
} else {
    try {
        // Decodificar imagen base64
        $datos_imagen = base64_decode(
            preg_replace('/^data:image\/\w+;base64,/', '', $firma_base64)
        );

        if (!$datos_imagen) {
            throw new Exception('Error al decodificar la imagen');
        }

        // Crear directorio si no existe
        $directorio = __DIR__ . '/../Firmas/';
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0755, true)) {
                throw new Exception('No se pudo crear el directorio de firmas');
            }
        }

        // Generar nombre de archivo

        switch($documento){
            case 'HGC':
                $nombre_archivo = 'HistoriaClinicaGen_' . $id_paciente . '_' . date('Y-m-d_H-i') . '.png';
                // Preparar y ejecutar query con prepared statement
        $sql_update = "UPDATE paciente 
                       SET url_firma_hgc = ?, 
                           date_firma_hgc = NOW(), 
                           fecha_consetimiento_hgc = NOW() 
                       WHERE id_paciente = ?";

            $url_ok = '<a class="button" href="../view_aviso.php?id_paciente='.$id_paciente.'">Continuar (Aviso de Privacidad)</a>';
                break;
            case 'AP':
                $nombre_archivo = 'AvisoPrivacidad_' . $id_paciente . '_' . date('Y-m-d_H-i') . '.png';
                $sql_update = "UPDATE paciente 
                       SET url_firma_avisop = ?, 
                           date_firma_avisop = NOW(), 
                           fecha_consetimiento_avisop = NOW() 
                       WHERE id_paciente = ?";

                       $url_ok = '<a class="button" href="../view_consentimiento.php?id_paciente='.$id_paciente.'">Continuar (Consentimiento Informado)</a>';
                break;
            case 'CIN':
                $nombre_archivo = 'ConsentimientoInformado_' . $id_paciente . '_' . date('Y-m-d_H-i') . '.png';
                $sql_update = "UPDATE paciente 
                       SET url_firma_consent = ?, 
                           date_firma_consent = NOW(), 
                           fecha_consetimientoinf = NOW() 
                       WHERE id_paciente = ?";
                       $url_ok = '<a class="button" href="../fotopaciente.php?id_paciente='.$id_paciente.'">Continuar (Tomar Foto del Paciente)</a>';
                break;
        }
        $ruta_fisica = $directorio . $nombre_archivo;
        $ruta_bd = 'Firmas/' . $nombre_archivo;  // Ruta relativa para guardar en BD

        // Guardar archivo
        if (!file_put_contents($ruta_fisica, $datos_imagen)) {
            throw new Exception('Error al guardar el archivo de firma');
        }

        // Verificar que se guardó correctamente
        if (!file_exists($ruta_fisica)) {
            throw new Exception('El archivo no se guardó correctamente');
        }

        

        $stmt = $mysqli->prepare($sql_update);
        if (!$stmt) {
            throw new Exception('Error en prepare: ' . $mysqli->error);
        }

        // Vincular parámetros
        if (!$stmt->bind_param('si', $ruta_bd, $id_paciente)) {
            throw new Exception('Error en bind_param: ' . $stmt->error);
        }

        // Ejecutar
        if (!$stmt->execute()) {
            throw new Exception('Error al ejecutar query: ' . $stmt->error);
        }

        // Verificar filas afectadas
        if ($stmt->affected_rows > 0) {
            $message = '✅ Firma guardada correctamente';
            $success = true;
        } else {
            throw new Exception('No se actualizó el registro. Verifique que el ID de paciente sea válido.');
        }

        $stmt->close();

    } catch (Exception $e) {
        $message = '❌ Error: ' . $e->getMessage();
        // Log del error
        error_log('Error al guardar firma: ' . $e->getMessage());
    }
}
?>


<html>
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <link rel="icon" type="image/png" href="../Static/img/favicon.png">
                            <style>
                                * {
                                    margin: 0;
                                    padding: 0;
                                    box-sizing: border-box;
                                }

                                body {
                                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    min-height: 100vh;
                                    background: linear-gradient(135deg, #2d83a0 0%, #00e5ff 100%);
                                    padding: 20px;
                                }

                                .container {
                                    background: white;
                                    padding: 50px 40px;
                                    border-radius: 12px;
                                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
                                    text-align: center;
                                    max-width: 700px;
                                    width: 100%;
                                }

                                .message {
                                    font-size: 28px;
                                    color: #333;
                                    margin-bottom: 40px;
                                    line-height: 1.7;
                                    font-weight: 600;
                                    letter-spacing: 0.3px;
                                }

                                .code {
                                    font-size: 36px;
                                    color: #2d83a0;
                                    font-weight: 700;
                                    margin: 30px 0;
                                }

                                .logo {
                                    width: 120px;
                                    height: 120px;
                                    margin: 0 auto 30px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                }

                                .logo img {
                                    max-width: 100%;
                                    max-height: 100%;
                                    object-fit: contain;
                                    filter: drop-shadow(0 4px 8px rgba(45, 131, 160, 0.2));
                                }

                               

                                .button {
                                    background-color: #2d83a0;
                                    color: white;
                                    border: none;
                                    padding: 20px 40px;
                                    font-size: 24px;
                                    font-weight: 700;
                                    border-radius: 10px;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    min-height: 70px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    -webkit-tap-highlight-color: transparent;
                                    text-decoration: none;
                                }

                                .button:hover {
                                    background-color: #1e5a7a;
                                    transform: translateY(-3px);
                                    box-shadow: 0 8px 20px rgba(45, 131, 160, 0.3);
                                }

                                .button:active {
                                    transform: translateY(-1px);
                                }

                               

                                @keyframes slideDown {
                                    from {
                                        opacity: 0;
                                        transform: translateY(-10px);
                                    }
                                    to {
                                        opacity: 1;
                                        transform: translateY(0);
                                    }
                                }

                                /* Optimización para tablet 11.5 pulgadas */
                                @media (min-width: 800px) and (max-width: 1600px) {
                                    .container {
                                        padding: 60px 50px;
                                        max-width: 800px;
                                    }

                                    .error-message {
                                        padding: 20px 25px;
                                        font-size: 20px;
                                    }

                                    .logo {
                                        width: 150px;
                                        height: 150px;
                                        margin-bottom: 40px;
                                    }

                                    .message {
                                        font-size: 32px;
                                        margin-bottom: 50px;
                                    }

                                  

                                    .button {
                                        padding: 22px 50px;
                                        font-size: 26px;
                                        min-height: 75px;
                                    }
                                }

                                /* Pantallas más grandes */
                                @media (min-width: 1600px) {
                                    .container {
                                        padding: 70px 60px;
                                    }

                                    .error-message {
                                        padding: 22px 28px;
                                        font-size: 22px;
                                    }

                                    .logo {
                                        width: 180px;
                                        height: 180px;
                                        margin-bottom: 50px;
                                    }

                                    .message {
                                        font-size: 36px;
                                    }

                                 

                                    .button {
                                        padding: 26px 60px;
                                        font-size: 28px;
                                        min-height: 85px;
                                    }
                                }

                                /* Pantallas pequeñas */
                                @media (max-width: 799px) {
                                    .container {
                                        padding: 40px 30px;
                                    }

                                  

                                    .logo {
                                        width: 100px;
                                        height: 100px;
                                        margin-bottom: 25px;
                                    }

                                    .message {
                                        font-size: 24px;
                                        margin-bottom: 30px;
                                    }

                                   

                                    .button {
                                        padding: 18px 35px;
                                        font-size: 22px;
                                        min-height: 65px;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="logo">
                                    <img src="../Static/img/logo.png" alt="Logo Clínica">
                                </div>
                                
                                <p class="message"><?php echo $message; ?></p>
                                
                                <?php if ($success): ?>
                                    <p style="color: #4caf50; font-size: 18px; margin-bottom: 30px; font-weight: 600;">La firma se ha registrado exitosamente</p>
                                    <br>
                                    <?php echo $url_ok; ?>
                                    <br>
                                <?php endif; ?>
                                
                                <a class="button" style="background-color: #E9D502;" href="../">Salir</a>
                                
                            </div>

                            
                        </body>
                        </html>

