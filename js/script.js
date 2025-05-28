window.addEventListener("DOMContentLoaded", () => {

    function getCorrectWordForm(count) {
        if (count % 10 === 1 && count % 100 !== 11) {
            return "товар";
        } else if (
            [2, 3, 4].includes(count % 10) &&
            ![12, 13, 14].includes(count % 100)
        ) {
            return "товара";
        } else {
            return "товаров";
        }
    }

    // Clean up undefined or invalid data from localStorage
    let basket = JSON.parse(localStorage.getItem('basket')) || [];
    basket = basket.filter((item) => item.id && item.title && item.subtitle && item.price && item.image);
    localStorage.setItem('basket', JSON.stringify(basket));

    // Basket Page: Render Items from LocalStorage
    const basketProductsContainer = document.querySelector('.basket__products');
    const basketSubtitle = document.querySelector('.basket__subtitle');
    const paymentCostValue = document.querySelector('.payment__form-cost-value');

    if (basketProductsContainer) {
        // Retrieve basket data from localStorage
        let basket = JSON.parse(localStorage.getItem('basket')) || [];
    
        // Update basket subtitle with the number of items
        basketSubtitle.textContent = `У вас в корзине ${basket.length} ${getCorrectWordForm(basket.length)}`;
    
        // Calculate total price
        let totalPrice = 0;
    
        // Render basket items
        basket.forEach((item) => {
            totalPrice += item.price * item.quantity;
    
            const productHTML = `
                <div class="basket__product" data-id="${item.id}">
                    <div class="basket__product-left">
                        <img src="${item.image}" alt="${item.title}" class="basket__product-img">
                        <div class="basket__product-content">
                            <p class="basket__product-name">${item.title}</p>
                            <p class="basket__product-desc">${item.subtitle}</p>
                        </div>
                    </div>
                    <div class="basket__product-right">
                        <div class="basket__product-amount">
                            <span class="basket__product-amount-value">${item.quantity}</span>
                            <div class="basket__product-amount-btns">
                                <button class="basket__product-amount-btn basket__product-amount-btn-incr" type="button">
                                    <svg width="52" height="21" viewBox="0 0 52 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M51.8185 20.8571L26.0721 0.5L0.325684 20.8571H51.8185Z" fill="#393939" />
                                    </svg>
                                </button>
                                <button class="basket__product-amount-btn basket__product-amount-btn-decr" type="button">
                                    <svg width="52" height="21" viewBox="0 0 52 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M51.8185 0.643101L26.0721 21.0002L0.325684 0.643101H51.8185Z" fill="#393939" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="basket__product-cost">
                            <span class="basket__product-cost-value">${item.price * item.quantity}</span>₽
                            <span class="basket__product-cost-value-hide" style="display: none;">${item.price}</span>
                        </div>
                        <button class="basket__product-remove" type="button">
                            <img src="img/basket/TrashCan.png" alt="img" class="basket__product-remove-icon">
                        </button>
                    </div>
                </div>
            `;
            basketProductsContainer.insertAdjacentHTML('beforeend', productHTML);
        });
    
        // Update total price in the payment section
        if (paymentCostValue) {
            paymentCostValue.textContent = `${totalPrice}₽`;
        }
    
        // Add functionality to increment/decrement quantities and update costs
        basketProductsContainer.addEventListener('click', (e) => {
            const target = e.target;
    
            if (target.closest('.basket__product-amount-btn-incr')) {
                const product = target.closest('.basket__product');
                const productId = product.dataset.id;
                const amountValue = product.querySelector('.basket__product-amount-value');
                const costValue = product.querySelector('.basket__product-cost-value');
                const costValueHide = product.querySelector('.basket__product-cost-value-hide');
    
                // Update quantity in localStorage
                const basket = JSON.parse(localStorage.getItem('basket')) || [];
                const item = basket.find((item) => item.id === productId);
                if (item) {
                    item.quantity += 1;
                    localStorage.setItem('basket', JSON.stringify(basket));
                }
    
                // Update DOM
                amountValue.textContent = item.quantity;
                costValue.textContent = item.price * item.quantity;
    
                // Update total price
                totalPrice += Number(costValueHide.textContent);
                paymentCostValue.textContent = `${totalPrice}₽`;
            }
    
            if (target.closest('.basket__product-amount-btn-decr')) {
                const product = target.closest('.basket__product');
                const productId = product.dataset.id;
                const amountValue = product.querySelector('.basket__product-amount-value');
                const costValue = product.querySelector('.basket__product-cost-value');
                const costValueHide = product.querySelector('.basket__product-cost-value-hide');
    
                // Update quantity in localStorage
                const basket = JSON.parse(localStorage.getItem('basket')) || [];
                const item = basket.find((item) => item.id === productId);
                if (item && item.quantity > 1) {
                    item.quantity -= 1;
                    localStorage.setItem('basket', JSON.stringify(basket));
                }
    
                // Update DOM
                amountValue.textContent = item.quantity;
                costValue.textContent = item.price * item.quantity;
    
                // Update total price
                totalPrice -= Number(costValueHide.textContent);
                paymentCostValue.textContent = `${totalPrice}₽`;
            }
    
            // Remove item from basket
            if (target.closest('.basket__product-remove')) {
                const product = target.closest('.basket__product');
                const productId = product.dataset.id;
    
                // Remove item from localStorage
                basket = basket.filter((item) => item.id !== productId);
                localStorage.setItem('basket', JSON.stringify(basket));
    
                // Remove item from DOM
                product.remove();
    
                // Update total price and subtitle
                const productCost = product.querySelector('.basket__product-cost-value').textContent;
                totalPrice -= Number(productCost);
                paymentCostValue.textContent = `${totalPrice}₽`;
                basketSubtitle.textContent = `У вас в корзине ${basket.length} ${getCorrectWordForm(basket.length)}`;
            }
        });
    }

    // burger

    const burgerBtn = document.querySelector('.header__right')
    const burgerMenu = document.querySelector('.burger-menu')

    if (burgerBtn) {

        burgerBtn.addEventListener('click', () => {
            burgerMenu.classList.toggle('burger-menu--visible')
        })

    }

    // menu

    const menuCardBtns = document.querySelectorAll('.menu__card-btn');
    const linkBasket = document.querySelector('.link-basket');
    const linkBasketPrice = document.querySelector('.link-basket-price');

    let totalPrice = 0;

    if (linkBasket) {
        menuCardBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.menu__card');
                const id = card.dataset.id;
                const title = card.dataset.title;
                const subtitle = card.dataset.subtitle;
                const price = parseInt(card.dataset.price);
                const image = card.dataset.image;
        
                // Получаем корзину из localStorage
                let basket = JSON.parse(localStorage.getItem('basket')) || [];
        
                // Проверяем, есть ли уже такой товар в корзине
                const existingItem = basket.find((item) => item.id === id);
        
                if (existingItem) {
                    // Если товар уже есть, увеличиваем его количество
                    existingItem.quantity += 1;
                } else {
                    // Если товара нет, добавляем его с количеством 1
                    basket.push({ id, title, subtitle, price, image, quantity: 1 });
                }
        
                // Сохраняем обновленную корзину в localStorage
                localStorage.setItem('basket', JSON.stringify(basket));
        
                // Обновляем общую стоимость и видимость корзины
                totalPrice += price;
                linkBasketPrice.textContent = `₽${totalPrice}`;
                linkBasket.classList.add('link-basket--visible');
            });
        });
    }

    // form

    const form = document.querySelector('.form');
    const subscribeModal = document.getElementById('subscribeModal');
    const closeSubscribeModal = document.getElementById('closeSubscribeModal');

    if (form) {
    new window.JustValidate('.form', {
        messages: {
            email: {
                required: 'Введите E-mail',
                email: 'Введите корректный E-mail'
            }
        },
        submitHandler: function (thisForm) {
            let formData = new FormData(thisForm);

            let xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log('Форма успешно отправлена:', xhr.responseText);

                        // Показываем модальное окно
                        subscribeModal.classList.remove('hidden');
                        subscribeModal.style.visibility = 'visible';
                        subscribeModal.style.opacity = '1';

                        // Очищаем форму
                        thisForm.reset();
                    } else {
                        console.error('Ошибка при отправке формы:', xhr.status);
                        alert('Произошла ошибка при подписке. Попробуйте снова.');
                    }
                }
            };

            xhr.open('POST', 'mail.php', true);
            xhr.send(formData);
        }
    });

    // Закрытие модального окна
    closeSubscribeModal.addEventListener('click', function () {
        subscribeModal.classList.add('hidden');
        subscribeModal.style.visibility = 'hidden';
        subscribeModal.style.opacity = '0';
    });
}

})

    // about-us

    let slideIndex = 1;
    showSlides(slideIndex);

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    
    if (n > slides.length) {slideIndex = 1}    
    if (n < 1) {slideIndex = slides.length}
    
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    
    slides[slideIndex - 1].style.display = "block";  
    dots[slideIndex - 1].className += " active";
}