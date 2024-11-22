window.onload = function() {
    modal.style.display = "block"; // Este debería mostrar el modal al cargar
    console.log("Modal mostrado al cargar la página");
    // Función para cargar los posts
    loadPosts();
};

// Obtener elementos de la ventana emergente
const modal = document.getElementById("comment-modal");
const closeBtn = document.querySelector(".close-btn");
const submitComment = document.getElementById("submit-comment");
const existingComments = document.querySelector(".existing-comments");
const commentInput = document.getElementById("comment-input");

// Seleccionar el contenedor de posts
const postsContainer = document.getElementById('posts-container');

// Delegar eventos al contenedor
postsContainer.addEventListener('click', (event) => {
    if (event.target.closest('.comment-button')) {
        modal.style.display = "block";
        console.log("hiciste click");
    }
});

// Cerrar la ventana al hacer clic en la 'X'
closeBtn.addEventListener('click', () => {
    modal.style.display = "none";
});

// Cerrar la ventana al hacer clic fuera del contenido
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Agregar un nuevo comentario
submitComment.addEventListener('click', () => {
    const newComment = commentInput.value.trim();

    if (newComment) {
        const commentElement = document.createElement("p");
        commentElement.textContent = newComment;
        existingComments.appendChild(commentElement);

        // Limpiar el campo de texto
        commentInput.value = "";
    } else {
        alert("Por favor, escribe un comentario antes de enviar.");
    }
});


function loadPosts() {
    // Crear una solicitud AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "http://localhost/FORO/dist/php/get_posts.php", true);
    
    // Cuando la solicitud se haya completado con éxito
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log(xhr.responseText);  // Imprime la respuesta del servidor
            try {
                var response = JSON.parse(xhr.responseText); // Parsear el JSON devuelto
                var posts = response.data; // Parsear el JSON devuelto
                // Si hay posts
                if (posts.length > 0) {
                    posts.forEach(post => {
                        // Crear el formato HTML para cada post
                        var postHTML = `
                            <div class="post">
                                <div class="head-post">
                                    <img src="../../dist/img/image.png" alt="User Photo">
                                    <div class="info">
                                        <span class="name">Krlos</span>
                                        <span class="time">${post.fecha}</span>
                                    </div>                   
                                </div>
                                <div class="body-post">
                                    ${post.contenido_texto}
                                </div>
                                <div class="interaction-post">
                                    <button>
                                        <i class='bx bx-like'></i>Like
                                    </button>
                                    <button class='comment-button'>
                                        <i class='bx bx-comment-detail'></i>Comentar
                                    </button>
                                    <button>
                                        <i class='bx bx-share-alt'></i> Compartir
                                    </button>
                                </div>
                            </div>
                        `;
                        // Agregar el nuevo post al contenedor
                        document.getElementById('posts-container').innerHTML += postHTML;
                    });
                } else {
                    document.getElementById('posts-container').innerHTML = "No hay posts disponibles.";
                }
            } catch (e) {
                alert("Hubo un error al procesar los datos: " + e.message);
            }
        } else {
            alert("Hubo un error al cargar los posts.");
        }
    };
    

    xhr.send(); // Enviar la solicitud
}


function sendMessage() {
    // Obtener el mensaje del input
    var message = document.getElementById("button-post").value;
    console.log("mensaje a enviar", message);

    // Verificar que el mensaje no esté vacío
    if (message.trim() === "") {
        alert("Por favor, escribe un mensaje.");
        return;
    }

    // Crear un objeto FormData para enviar el mensaje y otros parámetros al servidor
    var formData = new FormData();
    formData.append("message", message);  // El mensaje a enviar
    formData.append("titulo_post", "Titulo");  // Titulo fijo
    formData.append("contenido_image", "");  // Imagen vacía (puedes cambiarlo si envías imágenes)
    formData.append("likes", "");  // Likes vacíos (puedes poner un valor si quieres)
    formData.append("numero_com", "");  // Número de comentarios vacíos (puedes poner un valor si quieres)
    formData.append("id_usuario", 4);  // ID de usuario fijo
    formData.append("id_canal", 1);  // ID de canal fijo

    // Usar AJAX para enviar los datos al servidor
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "http://localhost/FORO/dist/php/post.php", true); // URL del archivo PHP
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Mostrar una respuesta del servidor
            console.log("mensaje enviado correctamente");
            document.getElementById("response").innerHTML = xhr.responseText;
            // Limpiar el campo de texto
            document.getElementById("button-post").value = "";
        } else {
            alert("Hubo un error al enviar el mensaje.");
        }
    };
    xhr.send(formData); // Enviar los datos al servidor
}

