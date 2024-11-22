<?php
require "db.php"; // Asegúrate de que db.php esté correctamente incluido

// Verificar si el mensaje fue enviado
if (isset($_POST['message'])) {
    $message = trim($_POST['message']);  // El mensaje a insertar
    $titulo_post = "Titulo";  // Titulo fijo
    $contenido_image = isset($_POST['contenido_image']) ? $_POST['contenido_image'] : NULL;  // Imagen, por ahora vacía
    $likes = isset($_POST['likes']) ? $_POST['likes'] : NULL;  // Likes, por ahora vacíos
    $numero_com = isset($_POST['numero_com']) ? $_POST['numero_com'] : NULL;  // Número de comentarios, por ahora vacío
    $id_usuario = 4;  // ID del usuario fijo
    $id_canal = 1;  // ID del canal fijo

    // Verificar que el mensaje no esté vacío
    if (!empty($message)) {
        // Escapar caracteres especiales para evitar SQL injection
        $message = $connection->real_escape_string($message);
        $titulo_post = $connection->real_escape_string($titulo_post);
        $contenido_image = $connection->real_escape_string($contenido_image);
        $likes = $connection->real_escape_string($likes);
        $numero_com = $connection->real_escape_string($numero_com);

        // Insertar el mensaje en la base de datos
        $sql = "INSERT INTO post (titulo_post, contenido_texto, contenido_image, likes, numero_com, id_usuario, id_canal) 
                VALUES ('$titulo_post', '$message', '$contenido_image', '$likes', '$numero_com', '$id_usuario', '$id_canal')";

        if ($connection->query($sql) === TRUE) {
            echo "Mensaje enviado con éxito.";
        } else {
            echo "Error: " . $sql . "<br>" . $connection->error;  // Mostrar errores si los hay
        }
    } else {
        echo "El mensaje está vacío.";  // Si el mensaje está vacío, se muestra este mensaje
    }
} else {
    echo "No se ha recibido el mensaje.";  // Si no se ha enviado el mensaje, se muestra este mensaje
}

// Cerrar la conexión
$connection->close();  // Cerrar la conexión a la base de datos
?>
