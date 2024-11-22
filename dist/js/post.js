function sendMessage() {
    // Obtener el mensaje del input
    var message = document.getElementById("button-post").value;
    console.log("mensaje a enviar", message);

    // Verificar que el mensaje no esté vacío
    if (message.trim() === "") {
        alert("Por favor, escribe un mensaje.");
        return;
    }

    // Crear un objeto FormData para enviar el mensaje al servidor
    var formData = new FormData();
    formData.append("message", message);

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
    xhr.send(formData); // Enviar los datos
}
