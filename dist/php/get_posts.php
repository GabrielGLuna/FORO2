<?php
require "db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

function jsonResponse($status, $message, $data = null) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_canal'])) {
    $id_canal = intval($_POST['id_canal']);

    if ($connection) {
        $stmt = $connection->prepare("SELECT contenido_texto, fecha FROM post WHERE id_canal = ?");
        $stmt->bind_param("i", $id_canal);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $posts = [];
            while ($row = $result->fetch_assoc()) {
                $posts[] = [
                    'contenido_texto' => $row['contenido_texto'],
                    'fecha' => $row['fecha']
                ];
            }
            jsonResponse('ok', 'Posts obtenidos con éxito.', $posts);
        } else {
            // Enviar respuesta con array vacío en lugar de error
            jsonResponse('ok', 'No hay posts para este canal.', []);
        }

        $stmt->close();
        $connection->close();
    } else {
        jsonResponse('error', 'No se pudo conectar a la base de datos.');
    }
} else {
    jsonResponse('error', 'Datos incompletos o método no permitido.');
}
