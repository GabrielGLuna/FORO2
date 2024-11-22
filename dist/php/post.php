<?php
require "db.php"; // Asegúrate de que db.php esté correctamente incluido

// Verificar si el mensaje fue enviado
if (isset($_POST['message'])) {
    $message = trim($_POST['message']);

    // Verificar que el mensaje no esté vacío
    if (!empty($message)) {
        // Escapar caracteres especiales para evitar SQL injection
        $message = $connection->real_escape_string($message);  // Usar $connection, no $conn

        // Insertar el mensaje en la base de datos (asegúrate de tener la tabla y las columnas adecuadas)
        $sql = "INSERT INTO post (contenido_texto) VALUES ('$message')";

        if ($connection->query($sql) === TRUE) {
            echo "Mensaje enviado con éxito.";
        } else {
            echo "Error: " . $sql . "<br>" . $connection->error;  // Usar $connection
        }
    } else {
        echo "El mensaje está vacío.";
    }
} else {
    echo "No se ha recibido el mensaje.";
}

// Cerrar la conexión
$connection->close();  // Usar $connection para cerrar la conexión
?>
