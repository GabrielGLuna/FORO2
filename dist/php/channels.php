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
            $canalId = $stmt->insert_id;
            $updateStmt = $connection->prepare("UPDATE usuario SET canales = CONCAT(IFNULL(canales, ''),?, ',') WHERE iduser = ?");
            $updateStmt->bind_param("ii", $canalId, $idAdmin);

            if ($updateStmt->execute()) {
                jsonResponse('ok', 'Canal creado y asignado exitosamente.');
            } else {
                jsonResponse('error', 'Error al asignar el canal al administrador: ' . $updateStmt->error);
            }

            $updateStmt->close();
        } else {
            jsonResponse('error', 'Error al crear el canal: ' . $stmt->error);
        }

        $stmt->close();
        $connection->close();

    } elseif ($action === 'getCategories') {
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
        $idUser = $_POST['idUser'] ?? null;

        if ($idUser) {
            $sql1 = "SELECT canales FROM usuario WHERE iduser = ?";
            $stmt1 = $connection->prepare($sql1);
            $stmt1->bind_param("i", $idUser);

            if ($stmt1->execute()) {
                $result1 = $stmt1->get_result();

                if ($result1->num_rows > 0) {
                    $row = $result1->fetch_assoc();
                    $canales = rtrim($row['canales'], ',');

                    if (!empty($canales)) {
                        $sql2 = "SELECT * FROM canales WHERE id_canal IN ($canales)";
                        $stmt2 = $connection->prepare($sql2);

                        if ($stmt2->execute()) {
                            $result2 = $stmt2->get_result();
                            $lista_canales = [];
                            while ($canal = $result2->fetch_assoc()) {
                                $lista_canales[] = $canal;
                            }
                            jsonResponse('ok', 'Canales obtenidos con éxito.', $lista_canales);
                        }
                        $stmt2->close();
                    } else {
                        jsonResponse('ok', 'No está unido a ningún canal.', []);
                    }
                }
            }
            $stmt1->close();
        }
    } elseif ($action === 'getAllChannels') {
        $sql = "SELECT * FROM canales";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            $allChannels = [];
            while ($canal = $result->fetch_assoc()) {
                $allChannels[] = $canal;
            }
            jsonResponse('ok', 'Todos los canales obtenidos con éxito.', $allChannels);
        } else {
            jsonResponse('error', 'No hay canales disponibles.');
        }
    } elseif ($action === 'joinChannel') {
        // Corrección implementada aquí
        $idUser = $_POST['idUser'] ?? null;
        $idCanal = $_POST['idCanal'] ?? null;

        if ($idUser && $idCanal) {
            $sql1 = "SELECT canales FROM usuario WHERE iduser = ?";
            $stmt1 = $connection->prepare($sql1);
            $stmt1->bind_param("i", $idUser);

            if ($stmt1->execute()) {
                $result1 = $stmt1->get_result();

                if ($result1->num_rows > 0) {
                    $row = $result1->fetch_assoc();
                    $canales = trim($row['canales'], ',');
                    $canalesArray = empty($canales) ? [] : explode(',', $canales);

                    if (!in_array($idCanal, $canalesArray)) {
                        $canalesArray[] = $idCanal;
                        sort($canalesArray);
                        $newCanales = implode(',', $canalesArray);

                        $sql2 = "UPDATE usuario SET canales = ? WHERE iduser = ?";
                        $stmt2 = $connection->prepare($sql2);
                        $stmt2->bind_param("si", $newCanales, $idUser);

                        if ($stmt2->execute()) {
                            jsonResponse('ok', 'Te has unido al canal exitosamente.');
                        } else {
                            jsonResponse('error', 'Error al actualizar los canales: ' . $stmt2->error);
                        }
                        $stmt2->close();
                    } else {
                        jsonResponse('error', 'Ya estás unido a este canal.');
                    }
                }
                $stmt1->close();
            } else {
                jsonResponse('error', 'Error al obtener los canales actuales.');
            }
        } else {
            jsonResponse('error', 'Faltan parámetros (idUser o idCanal).');
        }
    } else {
        jsonResponse('error', 'Acción no permitida.');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getCategories') {
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
