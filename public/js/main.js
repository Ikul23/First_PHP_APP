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

document.addEventListener('DOMContentLoaded', function() {
    // Обработка удаления пользователя
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', async function() {
            const userId = this.dataset.userId;
            
            if (!confirm('Вы уверены, что хотите удалить этого пользователя?')) {
                return;
            }

            try {
                const response = await fetch(`/users/${userId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    document.getElementById(`user-row-${userId}`).remove();
                    showToast('Пользователь успешно удален', 'success');
                } else {
                    throw new Error(result.error || 'Ошибка при удалении');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Ошибка: ' + error.message, 'danger');
            }
        });
    });
});

function showToast(message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type}`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}