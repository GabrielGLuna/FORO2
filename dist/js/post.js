window.onload = function() {
    modal.style.display = "block"; // Este debería mostrar el modal al cargar
    console.log("Modal mostrado al cargar la página");
    // Función para cargar los posts
    loadPosts();
};

// Obtener elementos de la ventana emergente
const modal = document.getElementById("commentModal");
const closeBtn = document.querySelector(".close-btn");
const existingComments = document.querySelector(".existing-comments");
const commentInput = document.getElementById("comment-input");

// Seleccionar el contenedor de posts
const postsContainer = document.getElementById('posts-container');

//maneja los click en los posts
postsContainer.addEventListener('click', (event) => {
    if (event.target.closest('.like-button')) {
        console.log("diste like");
        const postId = event.target.closest('.like-button').getAttribute('data-id');
        likePost(postId, event.target.closest('.like-button'));
    } else if (event.target.closest('.comment-button')) {
        const postId = event.target.closest('.comment-button').getAttribute('data-id');
        commentPost(postId, event.target.closest('.comment-button'))
        modal.style.display = "block";
        console.log("hiciste clic en comentar");
    }
});

//maneja los clicks en la seccion de comentarios
modal.addEventListener('click', (event)=>{
    if(event.target.closest('#submit-comment')){  
        console.log("presionaste comentar");
        var data = event.target.closest('#submit-comment').getAttribute('data-id');
        var dataId = data.split(',');
        var idUser = dataId[0];
        var postId = dataId[1];
        console.log("post", postId);
        console.log("user", idUser);
        sendComment(postId,idUser);            
    }else if (event.target.closest('#submit-comment-like')) {
        var dataComment = event.target.closest('#submit-comment-like').getAttribute('data-id');
        likeComment(dataComment, event.target.closest('#submit-comment-like'));    
    }
});

// Cerrar la ventana al hacer clic en la 'X'
closeBtn.addEventListener('click', () => {
    modal.style.display = "none";
    var group = document.getElementById('comment-group');
    group.remove();
});

// Cerrar la ventana al hacer clic fuera del contenido
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
        var group = document.getElementById('comment-group');
        group.remove();
    }
});
// Agregar un nuevo comentario


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
                iduser = getCookie("iduser");
                
                // Limpiar los posts anteriores
                postsContainer.empty();
                
                if (posts.length > 0) {
                    // Construir los posts dinámicamente
                    posts.forEach(post => {
                        var likesArray = post.userLike;
                        console.log("esto devuelve:", likesArray);
                        var button;
        
                        if (likesArray.includes(iduser.toString())) {
                            button= "bx bxs-like";
                        }else{
                            button= "bx bx-like";
                        }
                        const postHTML = `
                            <div class="post">
                                <div class="head-post">
                                    <img src="dist/php/${post.image}" alt="User Photo">
                                    <div class="info">
                                        <span class="name">${post.username}</span>
                                        <span class="time">${post.fecha}</span>
                                        </div>                   
                                        </div>
                                <div class="body-post">
                                    ${post.contenido_texto}
                                </div>
                                <div class="interaction-post">
                                <button class="like-button" data-id="${post.id_post}">
                                <i class='${button}'></i>
                                <span> Likes ${post.likes}</span>
                                </button>
                                <button class="comment-button" data-id="${post.id_post}">
                                        <i class='bx bx-comment-detail'></i>Comentar
                                    </button>
                                    <button>
                                        <i class='bx bx-share-alt'></i> Compartir
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
    idUser = getCookie("iduser");
    $.ajax({
        url: "http://localhost/FORO2/dist/php/post.php",
        type: "POST",
        data: { action: "loadChanels" , idUser: idUser},
        dataType: "json",
        success: function (response) {
            if (response.success) {
                const channels = response.data;
                const groupButton = $(".group-button");

                // Limpiar contenido anterior
                groupButton.empty();

                // Crear botones dinámicos para cada canal
                response.list.forEach(channel => {                    

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
        }
    });
}
function loadSidebarChannels2() {

    idUser = getCookie("iduser");
    console.log("entro en load js", idUser);
    $.ajax({
        url: 'http://localhost/FORO2/dist/php/post.php',
                type: 'POST',
                data: { action: 'loadChanels', idUser: idUser},
                dataType: 'json',
                success: function(response){
                    console.log("canales: ", response);
                    if (response.success) {      
                        response.list.forEach(canal => {
                            
                            console.log("canales: ", canal.canalname);
                                // Crear el formato HTML para cada post
                                var postHTML = `
                        <button class="button-chanel" data-id="${channel.id_canal}">
                            <img src="dist/php/${channel.image}" class="mini-image" alt="${channel.canalname}">
                            <p>${channel.canalname}</p>
                        </button>
                    
                                `;
                                // Agregar el nuevo post al contenedor
                                document.getElementById('.group-button').innerHTML += postHTML;
                            });                        
                    }
                },
                error: function(xhr) {
                    console.error("Error en la solicitud:", xhr.responseText);
                }
    });
}

// Función para manejar el like
function likePost(postId, button) {
    idUser= getCookie("iduser");
    $.ajax({
        url: 'http://localhost/FORO2/dist/php/post.php',
        type: 'POST',
        data: { action: 'like', id_post: postId, idUser: idUser},
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                console.log('Like añadido correctamente');
                const newLikes = response.new_likes || 1;
                button.querySelector('i').className = 'bx bxs-like';
                button.querySelector('span').textContent = ` Likes ${newLikes}`;

            } else {
                const newLikes = response.new_likes;
                button.querySelector('i').className = 'bx bx-like';
                button.querySelector('span').textContent = ` Likes ${newLikes}`;
                console.error('Error al añadir el like: ya diste like');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la soli citud AJAX:', error);
        }
    });
}

function likeComment(idComment, button) {
    idUser= getCookie("iduser");
    $.ajax({
        url: 'http://localhost/FORO2/dist/php/post.php',
        type: 'POST',
        data: { action: 'likeComment', idComment: idComment, idUser: idUser},
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                console.log('Like añadido correctamente');
                const newLikes = response.new_noLikes || 1;
                button.querySelector('i').className = 'bx bxs-like';
                button.querySelector('span').textContent = ` Likes ${newLikes}`;

            } else {
                const newLikes = response.new_noLikes;
                button.querySelector('i').className = 'bx bx-like';
                button.querySelector('span').textContent = ` Likes ${newLikes}`;
                console.error('Error al añadir el like: ya diste like');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la soli citud AJAX:', error);
        }
    });
}
function commentPost(postId, button) {  
    idUser= getCookie("iduser");
    var postHTML = `<div id="comment-group">
                    <div id="existing-comments" >
                        <!-- Aquí se mostrarán comentarios existentes -->
                        <p>No hay comentarios aún. ¡Sé el primero en comentar!</p>
                    </div>
                    <div class="add-comment">
                    <textarea placeholder="Escribe tu comentario aquí..." id="comment-input"></textarea>
                    <button id="submit-comment" data-id="${idUser},${postId}">comentar</button>                                        
                    </div>
                    </div>
                    `;
                    // Agregar el nuevo post al contenedor
                    document.getElementById('comments-part').innerHTML += postHTML;
                    console.log(modal.style.display);
                    $.ajax({
                        url: 'dist/php/post.php',
                        type: 'POST',
                        data: { action: 'comment', id_post: postId },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                var commentsContainer = document.getElementById('existing-comments');
                                commentsContainer.innerHTML = ''; // Limpiar el contenido
                console.log("respuesta:", iduser);
                
                
                
                // Recorrer y mostrar cada comentario
                response.comments.forEach(comments => {

                    var likesArray = comments.likes;
                      console.log("esto devuelve:", likesArray);
                        var button;
        
                        if (likesArray.includes(iduser.toString())) {
                            button= "bx bxs-like";
                        }else{
                            button= "bx bx-like";
                        }

                    commentsContainer.innerHTML += `
                    <div class="comment" style="display:flex; flex-direction:row; justify-content: space-between;">
                    <p><strong>${comments.username}:</strong> ${comments.contenido}</p>
                    <button class="like-button-comment" data-id="${comments.id_comentario}" id="submit-comment-like">
                                <i class='${button}'></i>
                                <span> Likes ${comments.noLikes}</span>
                                </button>
                    </div>
                    `;
                });
            } else {
                console.error('Error al obtener comentarios:', response.error);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la solicitud en esta parte:', error);
        }
    });    
}

function sendComment(postId, idUser) {
    var comment = document.getElementById("comment-input").value;
            console.log("esto es el coment", comment);
            document.getElementById("comment-input").value="";            
            $.ajax({
                url: 'dist/php/post.php',
                type: 'POST',
                data: { action: 'sendComment', id_post: postId, idUser: idUser, comment:comment },
                dataType: 'json',
                success: function(response){
                    if (response.success) {
                        console.log("si jalo w");
                        document.getElementById("comment-input").value = "";
                        comment = document.getElementById("comment-input").value;
                        var group = document.getElementById('comment-group');
                        group.remove();
                        commentPost(postId);
                    }
                },
                error: function nojalo(error) {
                }
            });
}

function sendMessage() {
    userId = getCookie("iduser");
    channelId = getCookie("id_canal");
    
    // Obtener el mensaje del input
    var message = document.getElementById("button-post").value;

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
    formData.append("id_usuario", userId);  // ID de usuario fijo
    formData.append("id_canal", channelId);  // ID de canal fijo

    // Usar AJAX para enviar los datos al servidor
    if (userId != null && channelId != null) {
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "http://localhost/FORO2/dist/php/post.php", true); // URL del archivo PHP
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Mostrar una respuesta del servidor
                console.log("mensaje enviado correctamente");
                document.getElementById("response").innerHTML = xhr.responseText;
                // Limpiar el campo de texto
                document.getElementById("button-post").value = "";
                loadPosts();
            } else {
                alert("Hubo un error al enviar el mensaje.");
            }
        };
        xhr.send(formData); // Enviar los datos al servidor
    }else{
        console.log("esta vacio w");
    }
    
}

function obtenerLikes(id, type) {
    if (type === 'post') {
        return
    }else if( type === 'comment'){

    }     
}

window.onload = function () {
    console.log("solo para ver los cambios jaja");
    loadSidebarChannels(); // Cargar canales en la barra lateral
    loadPosts(); // Cargar posts del canal seleccionado (si existe cookie)
};

