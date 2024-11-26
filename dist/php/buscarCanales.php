<?php
require 'db.php'; // Asegúrate de que la conexión a la base de datos funcione
header('Content-Type: application/json');

if (isset($_POST['query'])) {
    $query = trim($_POST['query']);

    if (empty($query)) {
        echo json_encode(['status' => 'error', 'message' => 'La consulta está vacía']);
        exit;
    }

    // Escapar la consulta para evitar inyecciones SQL
    $queryEscaped = $connection->real_escape_string($query);

    // Buscar en la tabla `canales` por el nombre del canal y obtener la imagen
    $sql = "SELECT canalname, image FROM canales WHERE canalname LIKE ? LIMIT 5";
    $stmt = $connection->prepare($sql);

    if ($stmt) {
        $likeQuery = "%{$queryEscaped}%";
        $stmt->bind_param("s", $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        $canales = [];
        while ($row = $result->fetch_assoc()) {
            $canales[] = [
                'canalname' => $row['canalname'],
                'image' => $row['image']
            ];
        }

        echo json_encode(['status' => 'ok', 'data' => $canales]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
    }

    $stmt->close();
    $connection->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Parámetros no válidos']);
}
?>
