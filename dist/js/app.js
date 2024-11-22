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

// Variable global para el ID del usuario
let userId = null;

// Función para establecer cookies
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
}

// Función para obtener cookies
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Función para eliminar cookies
function deleteCookie(name) {
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/`;
}

// Función para renderizar el menú dinámico
function renderMenu() {
    const leftGroup = document.querySelector(".left-group");
    const rightGroup = document.querySelector(".right-group");

    // Verificar si userId está presente
    if (userId) {
        // Comprobar si "Para ti / Siguiendo" ya existe
        if (!document.querySelector(".btns-content")) {
            const btnsContent = document.createElement("li");
            btnsContent.className = "btns-content";
            btnsContent.innerHTML = `
                <button>Para ti</button>
                <button class="section-feed-active">Siguiendo</button>
            `;
            leftGroup.insertAdjacentElement("afterend", btnsContent);
        }

        // Reemplazar solo los botones de usuario dinámicos
        rightGroup.innerHTML = `
            <li class="search-container">
                <button class="btn-search">
                    <div class="btn-srch-right">
                        <i class='bx bx-search'></i> Buscar
                    </div>
                    <div class="btn-srch-left">
                        <div class="wnd">
                            <i class='bx bxl-windows'></i>
                        </div>
                        <div class="wnd">
                            <p>C</p>
                        </div>
                    </div>
                </button>
            </li>
            <li class="icon-search-responsive">
                <button class="btn-search-responsive"><i class='bx bx-search'></i></button>
            </li>
            <li class="menu-items"><button class="toggle-theme"><i class='bx bx-sun'></i></button></li>
            <li class="menu-items"><button class="btn-account"><i class='bx bx-user-circle'></i></button></li>
            <li class="menu-items"><button class="btn-logout"><i class='bx bx-log-out'></i></button></li>
            <li class="menu-toggle">
                <button><i class='bx bx-dots-vertical-rounded'></i></button>
                <div class="dropdown-menu">
                    <button class="btn-account"><i class='bx bx-user-circle'></i> Cuenta</button>
                    <button class="btn-logout"><i class='bx bx-log-out'></i> Logout</button>
                    <button class="toggle-theme"><i class='bx bx-sun'></i> Tema</button>
                </div>
            </li>
        `;

        // Reasignar eventos
        attachDynamicEvents();
    } else {
        // Sin userId: Quitar "Para ti / Siguiendo"
        const btnsContent = document.querySelector(".btns-content");
        if (btnsContent) btnsContent.remove();

        // Restaurar los botones predeterminados
        rightGroup.innerHTML = `
            <li class="search-container">
                <button class="btn-search">
                    <div class="btn-srch-right">
                        <i class='bx bx-search'></i> Buscar
                    </div>
                    <div class="btn-srch-left">
                        <div class="wnd">
                            <i class='bx bxl-windows'></i>
                        </div>
                        <div class="wnd">
                            <p>C</p>
                        </div>
                    </div>
                </button>
            </li>
            <li class="icon-search-responsive">
                <button class="btn-search-responsive"><i class='bx bx-search'></i></button>
            </li>
            <li class="menu-items"><button class="toggle-theme"><i class='bx bx-sun'></i></button></li>
            <li class="menu-items"><button class="btn-user"><i class='bx bx-user'></i></button></li>
            <li class="menu-toggle">
                <button><i class='bx bx-dots-vertical-rounded'></i></button>
                <div class="dropdown-menu">
                    <button class="btn-user"><i class='bx bx-user'></i> Iniciar Sesión</button>
                    <button class="toggle-theme"><i class='bx bx-sun'></i> Tema</button>
                </div>
            </li>
        `;

        // Reasignar eventos
        attachDynamicEvents();
    }
}

// Función para reasignar eventos dinámicos
// Función para reasignar eventos dinámicos
function attachDynamicEvents() {
    // Botones de logout
    document.querySelectorAll(".btn-logout").forEach(btn => {
        btn.addEventListener("click", handleLogout);
    });

    // Botones de usuario (Iniciar sesión o Cuenta)
    document.querySelectorAll(".btn-user").forEach(btn => {
        btn.addEventListener("click", () => {
            const modalLogin = document.getElementById("modal-login");
            modalLogin.style.display = "flex";
        });
    });

    // Botones de tema
    const themeToggleButtons = document.querySelectorAll('.toggle-theme');
    themeToggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const newTheme = body.classList.contains('dark') ? 'light' : 'dark';
            setTheme(newTheme);
        });
    });

    // Reasignar eventos de los botones de búsqueda
    const btnSearch = document.querySelector('.btn-search');
    const btnSearchResponsive = document.querySelector('.btn-search-responsive');

    if (btnSearch) {
        btnSearch.addEventListener('click', openModal);
    }

    if (btnSearchResponsive) {
        btnSearchResponsive.addEventListener('click', openModal);
    }
}

// Función para inicializar la aplicación
function initializeApp() {
    // Inicializar userId desde las cookies
    userId = getCookie("iduser");

    // Renderizar el menú dinámico
    renderMenu();

    // Asignar eventos iniciales
    attachDynamicEvents();
}

// Inicialización al cargar el DOM
$(document).ready(function () {
    initializeApp();
});


// Función para manejar el logout
function handleLogout() {
    deleteCookie("iduser");
    userId = null;
    renderMenu(); // Reconstruir el menú
}

// Inicialización al cargar el DOM
$(document).ready(function () {
    // Inicializar userId desde las cookies
    userId = getCookie("iduser");

    // Renderizar el menú dinámico
    renderMenu();
});


// Función para obtener el valor actual de userId desde las cookies
function getUserId() {
    return getCookie("iduser");
}

// Ejemplo de uso
$(document).ready(function () {
    console.log("userId desde función:", getUserId());
});

// Función para mostrar mensajes con SweetAlert
function showAlert(type, message) {
    Swal.fire({
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 1500
    });
}

// Función para iniciar sesión
function logUser(event) {
    event.preventDefault();

    const email = $("#email").val();
    const password = $("#password").val();

    if (!email || !password) {
        showAlert("error", "Por favor, completa todos los campos.");
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
                // Guardar iduser en cookies
                setCookie("iduser", response.iduser, 7);
                // Actualizar variable global
                userId = response.iduser;

                showAlert("success", "Inicio de sesión exitoso.");
                setTimeout(() => location.reload(), 1500); // Recargar el DOM
            } else {
                showAlert("error", response.answer);
            }
        },
        error: function(error) {
            showAlert("error", "Error en la solicitud.");
            console.error(error);
        }
    });
}

// Función para registrar usuario
function registerUser(event) {
    event.preventDefault();

    const username = $("#username").val();
    const email = $("#email-register").val();
    const password = $("#password-register").val();
    const phone = $("#phone").val();
    const age = $("#age").val();
    const image = $("#image")[0].files[0];

    if (!username || !email || !password || !phone || !age) {
        showAlert("error", "Por favor, completa todos los campos.");
        return;
    }

    const formData = new FormData();
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
                // Guardar iduser en cookies
                setCookie("iduser", response.iduser, 7);
                // Actualizar variable global
                userId = response.iduser;

                showAlert("success", "Registro exitoso.");
                setTimeout(() => location.reload(), 1500); // Recargar el DOM
            } else {
                showAlert("error", response.answer);
            }
        },
        error: function(error) {
            showAlert("error", "Error en la solicitud.");
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

// Inicialización de la variable global desde las cookies al cargar el DOM
$(document).ready(function () {
    userId = getCookie("iduser");
});


