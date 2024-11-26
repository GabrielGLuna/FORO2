<?php
require 'db.php';
header('Content-Type: application/json');

if (isset($_POST['userId'], $_POST['email'], $_POST['telefono'], $_POST['canales'])) {
    $userId = $_POST['userId'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $canales = $_POST['canales'];

    // Procesar la imagen si se envía
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $uploadDir = 'uploads/';
        $imagePath = $uploadDir . uniqid() . '-' . $imageName;

        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            echo json_encode(['status' => 'error', 'message' => 'Error al subir la imagen']);
            exit;
        }
    }

    // Preparar la consulta SQL
    $sql = "UPDATE usuario SET email = ?, telefono = ?, canales = ?";
    if ($imagePath) {
        $sql .= ", image = ?";
    }
    $sql .= " WHERE iduser = ?";

    $query = $connection->prepare($sql);
    if (!$query) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta: ' . $connection->error]);
        exit;
    }

    if ($imagePath) {
        // Si $imagePath está definido, ajusta los tipos correctamente
        $query->bind_param("ssssi", $email, $telefono, $canales, $imagePath, $userId);
    } else {
        $query->bind_param("sssi", $email, $telefono, $canales, $userId);
    }
    

    if ($query->execute()) {
        echo json_encode(['status' => 'ok', 'message' => 'Perfil actualizado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el perfil']);
    }

    $query->close();
    $connection->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
}
?>
