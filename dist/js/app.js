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

