<?php
require 'db.php'; // Asegúrate de tener la conexión configurada correctamente

header('Content-Type: application/json');

// Comprobar si userId está presente en la solicitud
if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];

    // Preparar la consulta para obtener los datos del usuario
    $query = $connection->prepare("SELECT username, password, email, telefono, image, canales FROM usuario WHERE iduser = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['status' => 'ok', 'data' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
    }

    $query->close();
    $connection->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado']);
}
?>
