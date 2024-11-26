<?php
require 'db.php'; // Asegúrate de tener la conexión configurada correctamente
 // 7 días de duración, accesible en todo el sitio

header('Content-Type: application/json');

// Leer los datos enviados en el cuerpo de la solicitud
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['userId'])) {
    $userId = $input['userId'];

    // Obtener los canales del usuario
    $query = $connection->prepare("SELECT canales FROM usuario WHERE iduser = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $canales = json_decode($user['canales'], true) ?: [];
        echo json_encode(['status' => 'ok', 'canales' => $canales]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }

    $query->close();
    $connection->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
}
?>
