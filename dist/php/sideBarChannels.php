<?php
require "db.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function jsonResponse($status, $message, $data = null) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'getChannels') {
    $stmt = $connection->prepare("SELECT id_canal, canalname, image FROM canales");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $channels = [];
        while ($row = $result->fetch_assoc()) {
            $channels[] = $row;
        }
        jsonResponse('ok', 'Canales obtenidos con éxito.', $channels);
    } else {
        jsonResponse('error', 'No se encontraron canales.');
    }

    $stmt->close();
    $connection->close();
} else {
    jsonResponse('error', 'Método no permitido.');
}
?>





