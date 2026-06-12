<?php 
// Funcion para recuperar los datos del paciente
function obtener_datos_paciente($mysqli, $id_paciente) {
    if (empty($id_paciente)) {
        return false;
    }
    $id_paciente = $mysqli->real_escape_string($id_paciente);
    $query = "SELECT id_paciente, concat(nombres,' ',a_paterno,' ', a_materno) nombre_comp, fecha_consetimiento_hgc, url_firma_hgc, 
                        fecha_consetimiento_avisop, url_firma_avisop, fecha_consetimientoinf, url_firma_consent, foto 
                        FROM paciente where id_paciente = '$id_paciente'";
    
    $resultado = $mysqli->query($query);
    if ($resultado && $resultado->num_rows == 1) {
        return $resultado->fetch_assoc();
    }
    return false;
}
?>