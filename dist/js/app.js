let userID;

// Selección de elementos
const themeToggleButtons = document.querySelectorAll('.toggle-theme');
const body = document.body;

// Cargar el tema desde localStorage o usar el tema por defecto
const currentTheme = localStorage.getItem('theme') || 'light';
setTheme(currentTheme);

// Función para alternar el tema
function setTheme(theme) {
    if (theme === 'dark') {
        body.classList.add('dark');
        updateIcons('dark');
    } else {
        body.classList.remove('dark');
        updateIcons('light');
    }
    localStorage.setItem('theme', theme);
}

// Función para actualizar íconos de los botones de tema
function updateIcons(theme) {
    themeToggleButtons.forEach(button => {
        const icon = button.querySelector('i');
        if (theme === 'dark') {
            icon.classList.remove('bx-sun');
            icon.classList.add('bx-moon');
        } else {
            icon.classList.remove('bx-moon');
            icon.classList.add('bx-sun');
        }
    });
}

// Añadir eventos a los botones
themeToggleButtons.forEach(button => {
    button.addEventListener('click', () => {
        const newTheme = body.classList.contains('dark') ? 'light' : 'dark';
        setTheme(newTheme);
    });
});

// Selección de elementos
const modal = document.getElementById('modal-search');
const modalOverlay = document.querySelector('.modal-overlay');
const closeModalButton = document.querySelector('.close-modal');
const btnSearch = document.querySelector('.btn-search');
const btnSearchResponsive = document.querySelector('.btn-search-responsive');
const searchInput = document.querySelector('.search-input');

// Función para abrir el modal
const openModal = () => {
    modal.classList.add('active');
    setTimeout(() => searchInput.focus(), 50);
};

// Función para cerrar el modal
const closeModal = () => {
    modal.classList.remove('active');
};

// Listeners para abrir el modal
btnSearch.addEventListener('click', openModal);
btnSearchResponsive.addEventListener('click', openModal);

// Listener para cerrar el modal con el botón [x]
closeModalButton.addEventListener('click', closeModal);

// Listener para cerrar el modal al hacer clic en el overlay
modalOverlay.addEventListener('click', closeModal);

// Listener para cerrar el modal con la tecla ESC
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Listener para abrir el modal con la combinación de teclas Windows + C
document.addEventListener('keydown', (event) => {
    if ((event.key === 'c' || event.key === 'C') && (event.ctrlKey || event.metaKey)) {
        openModal();
    }
});

// Selección de elementos
const modalLogin = document.getElementById('modal-login');
const modalRegister = document.getElementById('modal-register');
const closeModalLogin = document.getElementById('close-modal');
const closeModalRegister = document.getElementById('close-register');
const userButtons = document.querySelectorAll('.btn-user');
const openRegisterModal = document.getElementById('open-register-modal');
const backToLogin = document.getElementById('back-to-login');

// Mostrar modal de login al hacer clic en cualquier botón de usuario
userButtons.forEach(button => {
    button.addEventListener('click', () => {
        modalLogin.style.display = 'flex';
    });
});

// Cerrar modal de login
closeModalLogin.addEventListener('click', () => {
    modalLogin.style.display = 'none';
});

// Mostrar modal de registro
openRegisterModal.addEventListener('click', () => {
    modalLogin.style.display = 'none'; // Cerrar modal de login
    modalRegister.style.display = 'flex';
});

// Cerrar modal de registro
closeModalRegister.addEventListener('click', () => {
    modalRegister.style.display = 'none';
});

// Volver al modal de login desde registro
backToLogin.addEventListener('click', () => {
    modalRegister.style.display = 'none'; // Cerrar modal de registro
    modalLogin.style.display = 'flex';
});

// Cerrar modales al hacer clic fuera del contenido
window.addEventListener('click', (event) => {
    if (event.target === modalLogin) {
        modalLogin.style.display = 'none';
    }
    if (event.target === modalRegister) {
        modalRegister.style.display = 'none';
    }
});

// Seleccionar elementos
const fileInput = document.getElementById('image');
const fileLabel = document.getElementById('file-label');

// Cambiar texto y color del botón cuando se selecciona un archivo
fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        fileLabel.textContent = 'Listo';
        fileLabel.classList.add('selected'); // Agregar clase para cambiar el color
    } else {
        fileLabel.textContent = 'Seleccionar foto';
        fileLabel.classList.remove('selected'); // Remover clase si no hay archivo
    }
});

    // Función para iniciar sesión
    function logUser(event) {
        event.preventDefault();

        var email = $("#email").val();
        var password = $("#password").val();

        // Validar campos
        if (!email || !password) {
            alert("Por favor, completa todos los campos.");
            return;
        }

        $.ajax({
            url: 'http://localhost/FORO/dist/php/login.php',
            type: 'POST',
            data: {
                action: 'login',
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    alert('Inicio de sesión exitoso.');
                } else {
                    alert('Error: ' + response.answer);
                }
            },
            error: function(error) {
                alert('Error en la solicitud.');
                console.error(error);
            }
        });
    }

    // Función para registrar usuario
    function registerUser(event) {
        event.preventDefault();

        var username = $("#username").val();
        var email = $("#email-register").val();
        var password = $("#password-register").val();
        var phone = $("#phone").val();
        var age = $("#age").val();
        var image = $("#image")[0].files[0];

        // Validar campos
        if (!username || !email || !password || !phone || !age) {
            alert("Por favor, completa todos los campos.");
            return;
        }

        var formData = new FormData();
        formData.append("action", "register");
        formData.append("username", username);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("phone", phone);
        formData.append("age", age);
        formData.append("image", image);

        $.ajax({
            url: 'http://localhost/FORO/dist/php/login.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'ok') {
                    alert('Registro exitoso.');
                } else {
                    alert('Error: ' + response.answer);
                }
            },
            error: function(error) {
                alert('Error en la solicitud.');
                console.error(error);
            }
        });
    }

    // Manejo de los botones para cambiar entre modales
    $("#open-register-modal").on("click", function () {
        $("#modal-login").hide();
        $("#modal-register").show();
    });

    $("#back-to-login").on("click", function () {
        $("#modal-register").hide();
        $("#modal-login").show();
    });

    // Botones de cerrar modal
    $("#close-modal").on("click", function () {
        $("#modal-login").hide();
    });

    $("#close-register").on("click", function () {
        $("#modal-register").hide();
    });
