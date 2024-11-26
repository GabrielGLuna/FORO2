<?php
require "db.php"; 
// Cerrar la conexión$action = $_POST['action'] ?? null;
$action = $_POST['action'] ?? null;
$id_post = $_POST['id_post'] ?? null;
$idUser = $_POST['idUser'] ?? null;
$comment = $_POST['comment'] ?? null;// Asegúrate de que db.php esté correctamente incluido
$idComment = $_POST['idComment'] ?? null;// Asegúrate de que db.php esté correctamente incluido

// Verificar si el mensaje fue enviado
if (isset($_POST['message'])) {
    $message = trim($_POST['message']);  // El mensaje a insertar
    $titulo_post = "Titulo";  // Titulo fijo
    $contenido_image = isset($_POST['contenido_image']) ? $_POST['contenido_image'] : NULL;  // Imagen, por ahora vacía
    $likes = isset($_POST['likes']) ? $_POST['likes'] : NULL;  // Likes, por ahora vacíos
    $numero_com = isset($_POST['numero_com']) ? $_POST['numero_com'] : NULL;  // Número de comentarios, por ahora vacío
    $id_usuario = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : NULL;;  // ID del usuario fijo
    $id_canal = isset($_POST['id_canal']) ? $_POST['id_canal'] : NULL;;  // ID del canal fijo

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
            
        } else {
            echo "Error: " . $sql . "<br>" . $connection->error;  // Mostrar errores si los hay
        }
    } else {
        echo "El mensaje está vacío.";  // Si el mensaje está vacío, se muestra este mensaje
    }
}elseif ($action === 'like') {

            if (!empty($id_post)) {    

                // Primero, obtener el valor actual de userLike
                $check_sql = "SELECT userLike, likes FROM post WHERE id_post = ?";
                $check_stmt = $connection->prepare($check_sql);
                $check_stmt->bind_param("i", $id_post);
                $check_stmt->execute();
                $check_stmt->store_result();
                
                // Verificamos si la consulta devolvió resultados
                if ($check_stmt->num_rows > 0) {
                    $check_stmt->bind_result($existingUserLikes, $currentLikes);
                    $check_stmt->fetch();

                    // Verificamos si el idUser ya está en la lista de userLike
                    // Añadimos comas al principio y al final para evitar falsos positivos
                    if (strpos(',' . $existingUserLikes . ',', ',' . $idUser . ',') === false) {
                        // Si no está en la lista, actualizar el valor
                        $sql = "UPDATE post SET likes = likes + 1, userLike = CONCAT(userLike, ?, ',') WHERE id_post = ?";
                        $stmt = $connection->prepare($sql);
                        $stmt->bind_param("si", $idUser, $id_post); // Cambiamos a "ii" porque ahora hay dos enteros

                        if ($stmt->execute()) {
                            // Obtener el nuevo valor de likes
                            $likes_query = "SELECT likes FROM post WHERE id_post = ?";
                            $likes_stmt = $connection->prepare($likes_query);
                            $likes_stmt->bind_param("i", $id_post);
                            $likes_stmt->execute();
                            $likes_stmt->bind_result($new_likes);
                            $likes_stmt->fetch();

                            echo json_encode(['success' => true, 'new_likes' => $new_likes]);
                            $likes_stmt->close();
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'error' => $stmt->error]);
                        }
                        $stmt->close();
                    } else {
                        // Si el idUser ya está en userLike, restamos 1 a likes y eliminamos el idUser de la lista
                        // Restamos 1 a likes
                        $sql = "UPDATE post SET likes = likes - 1, userLike = REPLACE(userLike, CONCAT(?, ','), '') WHERE id_post = ?";
                        $stmt = $connection->prepare($sql);
                        $stmt->bind_param("si", $idUser, $id_post); // Nuevamente "si" porque uno es string y el otro es int

                        if ($stmt->execute()) {
                            // Obtener el nuevo valor de likes después de la resta
                            $likes_query = "SELECT likes FROM post WHERE id_post = ?";
                            $likes_stmt = $connection->prepare($likes_query);
                            $likes_stmt->bind_param("i", $id_post);
                            $likes_stmt->execute();
                            $likes_stmt->bind_result($new_likes);
                            $likes_stmt->fetch();

                            echo json_encode(['success' => false, 'new_likes' => $new_likes]);
                            $likes_stmt->close();
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'error' => $stmt->error]);
                        }
                        $stmt->close();
                    }
                } else {
                    // Si no existe el id_post, puedes hacer algo aquí si es necesario
                    echo json_encode(['success' => false, 'message' => 'Post no encontrado.']);
                }

                $check_stmt->close();
            }
        }elseif ($action === 'likeComment') {
            if (!empty($idComment)) {    
                // Primero, obtener el valor actual de noLikes y likes
                $check_sql = "SELECT noLikes, likes FROM comentario WHERE id_comentario = ?";
                $check_stmt = $connection->prepare($check_sql);
                $check_stmt->bind_param("i", $idComment);
                $check_stmt->execute();
                $check_stmt->store_result();
                
                // Verificamos si la consulta devolvió resultados
                if ($check_stmt->num_rows > 0) {
                    $check_stmt->bind_result($existingNoLikes, $currentLikes);
                    $check_stmt->fetch();
        
                    // Verificamos si el idUser ya está en la lista de likes (columna 'likes')
                    // Añadimos comas al principio y al final para evitar falsos positivos
                    if (strpos(',' . $currentLikes . ',', ',' . $idUser . ',') === false) {
                        // Si no está en la lista, actualizamos el valor:
                        // Incrementamos `noLikes` en 1 y añadimos el idUser a la lista de likes
                        $sql = "UPDATE comentario SET noLikes = noLikes + 1, likes = CONCAT(likes, ?, ',') WHERE id_comentario = ?";
                        $stmt = $connection->prepare($sql);
                        $stmt->bind_param("si", $idUser, $idComment);
        
                        if ($stmt->execute()) {
                            // Obtener el nuevo valor de noLikes
                            $likes_query = "SELECT noLikes FROM comentario WHERE id_comentario = ?";
                            $likes_stmt = $connection->prepare($likes_query);
                            $likes_stmt->bind_param("i", $idComment);
                            $likes_stmt->execute();
                            $likes_stmt->bind_result($new_noLikes);
                            $likes_stmt->fetch();
        
                            echo json_encode(['success' => true, 'new_noLikes' => $new_noLikes]);
                            $likes_stmt->close();
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'error' => $stmt->error]);
                        }
                        $stmt->close();
                    } else {
                        // Si el idUser ya está en likes, restamos 1 a noLikes y eliminamos el idUser de la lista de likes
                        $sql = "UPDATE comentario SET noLikes = noLikes - 1, likes = REPLACE(likes, CONCAT(?, ','), '') WHERE id_comentario = ?";
                        $stmt = $connection->prepare($sql);
                        $stmt->bind_param("si", $idUser, $idComment);
        
                        if ($stmt->execute()) {
                            // Obtener el nuevo valor de noLikes después de la resta
                            $likes_query = "SELECT noLikes FROM comentario WHERE id_comentario = ?";
                            $likes_stmt = $connection->prepare($likes_query);
                            $likes_stmt->bind_param("i", $idComment);
                            $likes_stmt->execute();
                            $likes_stmt->bind_result($new_noLikes);
                            $likes_stmt->fetch();
        
                            echo json_encode(['success' => false, 'new_noLikes' => $new_noLikes]);
                            $likes_stmt->close();
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'error' => $stmt->error]);
                        }
                        $stmt->close();
                    }
                } else {
                    // Si no existe el idComment, puedes hacer algo aquí si es necesario
                    echo json_encode(['success' => false, 'message' => 'Comentario no encontrado.']);
                }
        
                $check_stmt->close();
            }
        }elseif ($action === 'comment') {
    if (!empty($id_post)) {
        
        // Realizar un SELECT para obtener los comentarios asociados al post
        $sql = "SELECT c.id_comentario, c.contenido, c.id_usuario, u.username,u.image, c.id_post, c.likes, c.noLikes
                FROM comentario c
                INNER JOIN usuario u ON c.id_usuario = u.iduser
                WHERE c.id_post = ?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id_post);
        
        if ($stmt->execute()) {
            // Obtener los resultados
            $result = $stmt->get_result();
            $comments = [];

            while ($row = $result->fetch_assoc()) {
                $comments[] = $row; // Agregar cada fila al array de comentarios
            }
            echo json_encode(['success' => true, 'comments' => $comments]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        http_response_code(400);
    }
} elseif ($action === 'sendComment') {
    if (!empty($id_post) && !empty($idUser)) {
        $sql = "INSERT INTO comentario( contenido, id_usuario , id_post)
                VALUES('$comment','$idUser','$id_post')";
                $stmt = $connection -> prepare($sql);
                $stmt->execute();
                echo json_encode(["success" => true]);
                $stmt->close();
    }
} elseif ($action === 'loadChanels') {
    if (!empty($idUser)) {
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
                        echo json_encode(["success" => true, "list" => $lista_canales]);
                    }
                    $stmt2->close();
                }
            }
        }
        $stmt1->close();
    }
}


$connection->close();  // Cerrar la conexión a la base de datos
?>
