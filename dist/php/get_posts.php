<?php
require "db.php"; // Asegúrate de que db.php esté correctamente incluido

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función para enviar una respuesta JSON consistente
function jsonResponse($status, $message, $data = null) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();  // Detener la ejecución después de enviar la respuesta
}

// Verificar que la conexión a la base de datos fue exitosa
if ($connection) {
    // Consulta para obtener los posts
    $sql = "SELECT contenido_texto, fecha FROM post";
    $result = $connection->query($sql);

    // Verificar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        // Crear un array para almacenar los posts
        $posts = [];
        
        // Recorrer los resultados y agregarlos al array
        while ($row = $result->fetch_assoc()) {
            $posts[] = [
                'contenido_texto' => $row['contenido_texto'],
                'fecha' => $row['fecha']
            ];
        }
        
        // Enviar la respuesta con los posts
        jsonResponse('ok', 'Posts obtenidos con éxito.', $posts);
    } else {
        // Si no se encontraron posts, enviar un mensaje de error
        jsonResponse('error', 'No se encontraron posts.');
    }

    // Cerrar la conexión
    $connection->close();
} else {
    // Si no hay conexión a la base de datos, devolver un error
    jsonResponse('error', 'No se pudo conectar a la base de datos.');
}
?>
