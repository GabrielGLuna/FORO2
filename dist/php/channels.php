<?php
require "db.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función para enviar una respuesta JSON consistente
function jsonResponse($status, $message, $data = null) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'addChannel') {
        // Agregar un nuevo canal
        $canalName = $_POST['canalname'];
        $description = $_POST['description'];
        $numIntegrantes = $_POST['numintegrantes'];
        $category = $_POST['category'];
        $idAdmin = $_POST['id_admin'];
        $imagePath = null;

        if (empty($canalName) || empty($numIntegrantes) || empty($category) || empty($idAdmin)) {
            jsonResponse('error', 'Campos incompletos.');
        }

        if (!empty($_FILES['image']['name'])) {
            $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
            $uploadDir = 'uploads/';
            $imagePath = $uploadDir . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                jsonResponse('error', 'Error al subir la imagen.');
            }
        }

        $stmt = $connection->prepare("INSERT INTO canales (canalname, description, numintegrantes, image, category, id_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissi", $canalName, $description, $numIntegrantes, $imagePath, $category, $idAdmin);

        if ($stmt->execute()) {
            jsonResponse('ok', 'Canal creado exitosamente.');
        } else {
            jsonResponse('error', 'Error al crear el canal: ' . $stmt->error);
        }

        $stmt->close();
        $connection->close();
    } elseif ($action === 'getCategories') {
        // Obtener categorías
        $stmt = $connection->prepare("SELECT nombre FROM categorias_canales");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['nombre'];
            }
            jsonResponse('ok', 'Categorías obtenidas con éxito.', $categories);
        } else {
            jsonResponse('error', 'No se encontraron categorías.');
        }

        $stmt->close();
        $connection->close();
    } elseif ($action === 'getChannels') {
        // Obtener todos los canales, incluyendo id_canal
        $stmt = $connection->prepare("SELECT id_canal, canalname, description, numintegrantes, image, category FROM canales");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $channels = [];
            while ($row = $result->fetch_assoc()) {
                $channels[] = [
                    'id_canal' => $row['id_canal'],
                    'canalname' => $row['canalname'],
                    'description' => $row['description'],
                    'numintegrantes' => $row['numintegrantes'],
                    'image' => $row['image'],
                    'category' => $row['category']
                ];
            }
            jsonResponse('ok', 'Canales obtenidos con éxito.', $channels);
        } else {
            jsonResponse('error', 'No se encontraron canales.');
        }

        $stmt->close();
        $connection->close();
    } else {
        jsonResponse('error', 'Acción no permitida.');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getCategories') {
    // Manejar GET para obtener categorías
    $stmt = $connection->prepare("SELECT nombre FROM categorias_canales");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['nombre'];
        }
        jsonResponse('ok', 'Categorías obtenidas con éxito.', $categories);
    } else {
        jsonResponse('error', 'No se encontraron categorías.');
    }

    $stmt->close();
    $connection->close();
} else {
    jsonResponse('error', 'Método no permitido.');
}
?>
