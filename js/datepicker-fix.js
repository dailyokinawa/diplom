// Исправление для отображения placeholder в поле выбора даты на мобильных устройствах
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('dateInput');
    
    // Создаем элемент-обертку для поля даты
    const wrapper = document.createElement('div');
    wrapper.className = 'date-input-wrapper';
    wrapper.style.position = 'relative';
    wrapper.style.width = '100%';
    
    // Создаем элемент для отображения placeholder
    const placeholder = document.createElement('div');
    placeholder.textContent = 'Выберите дату';
    placeholder.style.position = 'absolute';
    placeholder.style.top = '50%';
    placeholder.style.left = '15px';
    placeholder.style.transform = 'translateY(-50%)';
    placeholder.style.pointerEvents = 'none';
    placeholder.style.color = '#757575';
    placeholder.style.transition = 'opacity 0.2s';
    
    // Заменяем поле даты в DOM
    if (dateInput) {
        const parent = dateInput.parentNode;
        parent.insertBefore(wrapper, dateInput);
        wrapper.appendChild(dateInput);
        wrapper.appendChild(placeholder);
        
        // Обработчики событий
        dateInput.addEventListener('change', function() {
            if (this.value) {
                placeholder.style.opacity = '0';
            } else {
                placeholder.style.opacity = '1';
            }
        });
        
        dateInput.addEventListener('focus', function() {
            placeholder.style.opacity = '0';
        });
        
        dateInput.addEventListener('blur', function() {
            if (!this.value) {
                placeholder.style.opacity = '1';
            }
        });
        
        // Инициализация
        if (dateInput.value) {
            placeholder.style.opacity = '0';
        }
    }
});