<?php
require "db.php";

$action = $_POST['action'] ?? null;
$id_post = $_POST['id_post'] ?? null;
$idUser = $_POST['idUser'] ?? null;
$comment = $_POST['comment'] ?? null;
$idComment = $_POST['idComment'] ?? null;

if (isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $titulo_post = "Titulo";
    $contenido_image = $_POST['contenido_image'] ?? null;
    $likes = $_POST['likes'] ?? null;
    $numero_com = $_POST['numero_com'] ?? null;
    $id_usuario = $_POST['id_usuario'] ?? null;
    $id_canal = $_POST['id_canal'] ?? null;

    if (!empty($message)) {
        $message = $connection->real_escape_string($message);
        $titulo_post = $connection->real_escape_string($titulo_post);
        $contenido_image = $connection->real_escape_string($contenido_image);
        $likes = $connection->real_escape_string($likes);
        $numero_com = $connection->real_escape_string($numero_com);

        $sql = "INSERT INTO post (titulo_post, contenido_texto, contenido_image, likes, numero_com, id_usuario, id_canal) 
                VALUES ('$titulo_post', '$message', '$contenido_image', '$likes', '$numero_com', '$id_usuario', '$id_canal')";

        if ($connection->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $connection->error;
        }
    } else {
        echo "El mensaje está vacío.";
    }
} elseif ($action === 'like') {
    if (!empty($id_post)) {
        $check_sql = "SELECT userLike, likes FROM post WHERE id_post = ?";
        $check_stmt = $connection->prepare($check_sql);
        $check_stmt->bind_param("i", $id_post);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($existingUserLikes, $currentLikes);
            $check_stmt->fetch();

            if (strpos(',' . $existingUserLikes . ',', ',' . $idUser . ',') === false) {
                $sql = "UPDATE post SET likes = likes + 1, userLike = CONCAT(userLike, ?, ',') WHERE id_post = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("si", $idUser, $id_post);

                if ($stmt->execute()) {
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
                $sql = "UPDATE post SET likes = likes - 1, userLike = REPLACE(userLike, CONCAT(?, ','), '') WHERE id_post = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("si", $idUser, $id_post);

                if ($stmt->execute()) {
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
            echo json_encode(['success' => false, 'message' => 'Post no encontrado.']);
        }

        $check_stmt->close();
    }
} elseif ($action === 'likeComment') {
    if (!empty($idComment)) {
        $check_sql = "SELECT noLikes, likes FROM comentario WHERE id_comentario = ?";
        $check_stmt = $connection->prepare($check_sql);
        $check_stmt->bind_param("i", $idComment);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($existingNoLikes, $currentLikes);
            $check_stmt->fetch();

            if (strpos(',' . $currentLikes . ',', ',' . $idUser . ',') === false) {
                $sql = "UPDATE comentario SET noLikes = noLikes + 1, likes = CONCAT(likes, ?, ',') WHERE id_comentario = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("si", $idUser, $idComment);

                if ($stmt->execute()) {
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
                $sql = "UPDATE comentario SET noLikes = noLikes - 1, likes = REPLACE(likes, CONCAT(?, ','), '') WHERE id_comentario = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("si", $idUser, $idComment);

                if ($stmt->execute()) {
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
            echo json_encode(['success' => false, 'message' => 'Comentario no encontrado.']);
        }

        $check_stmt->close();
    }
} elseif ($action === 'comment') {
    if (!empty($id_post)) {
        $sql = "SELECT c.id_comentario, c.contenido, c.id_usuario, u.username, u.image, c.id_post, c.likes, c.noLikes
                FROM comentario c
                INNER JOIN usuario u ON c.id_usuario = u.iduser
                WHERE c.id_post = ?;";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id_post);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $comments = [];

            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
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
    if (!empty($id_post) && !empty($idUser) && !empty($comment)) {
        $sql = "INSERT INTO comentario (contenido, id_usuario, id_post) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sii", $comment, $idUser, $id_post);

        if ($stmt->execute()) {
            $newCommentId = $connection->insert_id;

            $sqlFetch = "SELECT c.id_comentario, c.contenido, u.username, c.likes, c.noLikes
                         FROM comentario c
                         INNER JOIN usuario u ON c.id_usuario = u.iduser
                         WHERE c.id_comentario = ?";
            $stmtFetch = $connection->prepare($sqlFetch);
            $stmtFetch->bind_param("i", $newCommentId);

            if ($stmtFetch->execute()) {
                $result = $stmtFetch->get_result();
                $newComment = $result->fetch_assoc();

                echo json_encode(['success' => true, 'comment' => $newComment]);
            } else {
                echo json_encode(['success' => false, 'error' => $stmtFetch->error]);
            }
            $stmtFetch->close();
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
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

$connection->close();
?>
