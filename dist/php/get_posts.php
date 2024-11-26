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
        $stmt = $connection->prepare("SELECT 
            p.id_post,
            p.titulo_post,
            p.likes,
            p.numero_com,
            p.id_usuario,
            p.id_canal,
            p.contenido_texto,
            p.fecha,
            p.userLike,
            u.image AS user_image,
            u.username AS username
        FROM post p
        INNER JOIN usuario u ON p.id_usuario = u.iduser
        WHERE p.id_canal = ? order by p.fecha desc");
        $stmt->bind_param("i", $id_canal);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $posts = [];
            while ($row = $result->fetch_assoc()) {
                $posts[] = [
                    'id_post' => $row['id_post'],
                    'titulo_post' => $row['titulo_post'],
                    'likes' => $row['likes'],
                    'numero_com' => $row['numero_com'],
                    'id_usuario' => $row['id_usuario'],
                    'id_canal' => $row['id_canal'],
                    'contenido_texto' => $row['contenido_texto'],
                    'fecha' => $row['fecha'],
                    'userLike' => $row['userLike'],
                    'image' => $row['user_image'], // Agregar la imagen del usuario
                    'username' => $row['username']
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
