const createUserForm = () => {
        const form = document.createElement('form');
        form.innerHTML = `
            <input type="text" name="name" placeholder="Имя" required>
            <input type="date" name="birthday" required>
            <button type="submit">Сохранить пользователя</button>
        `;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = form.name.value;
            const birthday = form.birthday.value;
            saveUser(name, birthday);
        });

        document.querySelector('main').appendChild(form);
    };

    // Вызываем создание формы
    createUserForm();

    // Дополнительные интерактивные элементы
    const toggleSidebar = () => {
        const sidebar = document.querySelector('aside');
        sidebar.classList.toggle('hidden');
    };

    // Создаем кнопку для скрытия/показа sidebar
    const sidebarToggleBtn = document.createElement('button');
    sidebarToggleBtn.textContent = 'Показать/Скрыть меню';
    sidebarToggleBtn.addEventListener('click', toggleSidebar);
    document.querySelector('main').prepend(sidebarToggleBtn);


// Функция для динамического обновления времени
function updateTime() {
    const timeElement = document.querySelector('.current-time');
    if (timeElement) {
        timeElement.textContent = new Date().toLocaleString();
    }
}

// Обновляем время каждую секунду
setInterval(updateTime, 1000);