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
