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
    // Leer el id_canal de la cookie
    const cookies = document.cookie.split(";").reduce((acc, cookie) => {
        const [key, value] = cookie.trim().split("=");
        acc[key] = value;
        return acc;
    }, {});
    const id_canal = cookies.id_canal;

    if (!id_canal) {
        alert("Por favor, selecciona un canal primero.");
        return;
    }

    // Crear una solicitud AJAX
    $.ajax({
        url: "http://localhost/FORO2/dist/php/get_posts.php",
        type: "POST",
        data: { id_canal: id_canal },
        dataType: "json",
        success: function (response) {
            if (response.status === "ok") {
                const posts = response.data;
                const postsContainer = $("#posts-container");

                // Limpiar los posts anteriores
                postsContainer.empty();

                if (posts.length > 0) {
                    // Construir los posts dinámicamente
                    posts.forEach(post => {
                        const postHTML = `
                            <div class="post">
                                <div class="head-post">
                                    <img src="dist/img/image.png" alt="User Photo">
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
                                        ${40}<i class='bx bx-like'></i>
                                    </button>
                                    <button class='comment-button'>
                                        <i class='bx bx-comment-detail'></i>
                                    </button>
                                    <button>
                                        <i class='bx bx-share-alt'></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        postsContainer.append(postHTML);
                    });
                } else {
                    postsContainer.html("No hay posts disponibles.");
                }
            } else {
                alert("Error al cargar posts: " + response.message);
            }
        },
        error: function (error) {
            console.error("Error en la solicitud:", error);
            alert("Error al cargar los posts.");
        }
    });
}

function loadSidebarChannels() {
    $.ajax({
        url: "http://localhost/FORO2/dist/php/sideBarChannels.php",
        type: "POST",
        data: { action: "getChannels" },
        dataType: "json",
        success: function (response) {
            if (response.status === "ok") {
                const channels = response.data;
                const groupButton = $(".group-button");

                // Limpiar contenido anterior
                groupButton.empty();

                // Crear botones dinámicos para cada canal
                channels.forEach(channel => {
                    const channelButton = `
                        <button class="button-chanel" data-id="${channel.id_canal}">
                            <img src="dist/php/${channel.image}" class="mini-image" alt="${channel.canalname}">
                            <p>${channel.canalname}</p>
                        </button>
                    `;
                    groupButton.append(channelButton);
                });

                // Agregar evento a cada botón
                $(".button-chanel").on("click", function () {
                    const id_canal = $(this).data("id");

                    // Guardar el id_canal en una cookie
                    document.cookie = `id_canal=${id_canal}; path=/`;

                    // Indicar visualmente qué canal está abierto
                    $(".button-chanel").removeClass("channel-open"); // Quitar la clase de todos los botones
                    $(this).addClass("channel-open"); // Agregar la clase al botón actual

                    // Cargar los posts correspondientes
                    loadPosts();
                });

                // Resaltar el canal abierto basado en la cookie
                const cookies = document.cookie.split(";").reduce((acc, cookie) => {
                    const [key, value] = cookie.trim().split("=");
                    acc[key] = value;
                    return acc;
                }, {});
                const id_canal_cookie = cookies.id_canal;

                if (id_canal_cookie) {
                    // Resaltar el botón correspondiente
                    $(`.button-chanel[data-id="${id_canal_cookie}"]`).addClass("channel-open");
                }
            } else {
                alert("Error al cargar canales: " + response.message);
            }
        },
        error: function (error) {
            console.error("Error en la solicitud:", error);
            alert("Error al cargar los canales.");
        }
    });
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
    xhr.open("POST", "http://localhost/FORO2/dist/php/post.php", true); // URL del archivo PHP
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

window.onload = function () {
    console.log("solo para ver los cambios jaja");
    loadSidebarChannels(); // Cargar canales en la barra lateral
    loadPosts(); // Cargar posts del canal seleccionado (si existe cookie)
};

