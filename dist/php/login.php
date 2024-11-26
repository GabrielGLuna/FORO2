<?php
require "db.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función para enviar una respuesta JSON
function jsonResponse($status, $answer, $iduser = null) {
    echo json_encode(['status' => $status, 'answer' => $answer, 'iduser' => $iduser]);
    exit;
}

// Verifica el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'login') {
        // Manejo del inicio de sesión
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validar campos
        if (empty($email) || empty($password)) {
            jsonResponse('error', 'Campos incompletos.');
        }

        // Verificar si el correo existe
        $stmt = $connection->prepare("SELECT iduser, password FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($iduser, $hashedPassword);
            $stmt->fetch();

            // Verificar contraseña
            if (password_verify($password, $hashedPassword)) {
                jsonResponse('ok', 'Inicio de sesión exitoso.', $iduser); // Incluir el iduser
            } else {
                jsonResponse('error', 'Contraseña incorrecta.');
            }
        } else {
            jsonResponse('error', 'Correo no encontrado.');
        }

        $stmt->close();

    } elseif ($action === 'register') {
        // Manejo del registro
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $age = $_POST['age'];

        // Validar campos
        if (empty($username) || empty($email) || empty($password) || empty($phone) || empty($age)) {
            jsonResponse('error', 'Campos incompletos.');
        }

        // Validar formato de correo
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse('error', 'Correo no válido.');
        }

        // Validar longitud de contraseña
        if (strlen($password) < 8) {
            jsonResponse('error', 'La contraseña debe tener al menos 8 caracteres.');
        }

        // Validar que el teléfono sea numérico
        if (!preg_match('/^\d{10,15}$/', $phone)) {
            jsonResponse('error', 'Teléfono no válido.');
        }

        // Validar edad
        if ($age < 18 || $age > 120) {
            jsonResponse('error', 'Edad no válida.');
        }

        // Verificar si el correo ya está registrado
        $stmt = $connection->prepare("SELECT iduser FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            jsonResponse('error', 'El correo ya está registrado.');
        }

        // Verificar si el teléfono ya está registrado
        $stmt = $connection->prepare("SELECT iduser FROM usuario WHERE telefono = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            jsonResponse('error', 'El teléfono ya está registrado.');
        }

        // Procesar la imagen si se subió
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
            $uploadDir = 'uploads/';
            $imagePath = $uploadDir . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                jsonResponse('error', 'Error al subir la imagen.');
            }
        }

        // Cifrar la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertar en la base de datos
        $stmt = $connection->prepare("INSERT INTO usuario (username, password, email, telefono, edad, image, canales) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $canales = ''; // Valor predeterminado para 'canales'
        $stmt->bind_param("ssssiss", $username, $hashedPassword, $email, $phone, $age, $imagePath, $canales);

        if ($stmt->execute()) {
            $iduser = $stmt->insert_id; // Obtener el ID insertado
            jsonResponse('ok', 'Registro exitoso.', $iduser); // Incluir el iduser
        } else {
            jsonResponse('error', 'Error al registrar: ' . $stmt->error);
        }

        $stmt->close();
    }
    
    $connection->close();
} else {
    jsonResponse('error', 'Método no permitido.');
}
?>
