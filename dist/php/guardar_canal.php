<?php
require 'db.php'; // Asegúrate de que este archivo tiene la conexión configurada correctamente

header('Content-Type: application/json');

// Leer los datos enviados en el cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['iduser'], $input['id_canal'])) {
    $userId = $input['iduser'];
    $id_canal = $input['id_canal'];

    // Obtener la lista actual de canales del usuario (como cadena de números separados por comas)
    $query = $connection->prepare("SELECT canales FROM usuario WHERE iduser = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $currentChannels = $user['canales'] ? explode(',', $user['canales']) : []; // Convertir la cadena en un array

        // Agregar el nuevo canal si no existe
        if (!in_array($id_canal, $currentChannels)) {
            $currentChannels[] = $id_canal;

            // Convertir el array de canales nuevamente en una cadena separada por comas
            $updatedChannels = implode(',', $currentChannels);

            // Actualizar la base de datos
            $updateQuery = $connection->prepare("UPDATE usuario SET canales = ? WHERE iduser = ?");
            $updateQuery->bind_param("si", $updatedChannels, $userId);

            if ($updateQuery->execute()) {
                echo json_encode(['status' => 'ok', 'message' => 'Canal añadido']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar los canales']);
            }
            $updateQuery->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'El canal ya está añadido']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }

    $query->close();
    $connection->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
}

?>
