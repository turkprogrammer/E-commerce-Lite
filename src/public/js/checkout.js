/**
 * Checkout Module — Главный модуль оформления заказа
 * Объединяет все компоненты checkout процесса
 */
(function() {
    // Инициализация сервисов
    const checkoutService = new CheckoutService();
    const checkoutForm = window.checkoutForm;
    const checkoutSuccess = window.checkoutSuccess;

    // Текущая корзина (будет установлена извне)
    let getCartCallback = null;

    /**
     * Обработка состояния checkout
     */
    checkoutService.subscribe((state, data) => {
        console.log('Checkout state:', state, data);

        switch (state) {
            case CheckoutService.State.PROCESSING:
                // Показываем лоадер на кнопке
                const checkoutBtn = document.querySelector('#cart-total button.checkout-btn');
                if (checkoutBtn) {
                    checkoutBtn.disabled = true;
                    checkoutBtn.innerHTML = `
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-2">Обработка...</span>
                    `;
                }
                break;

            case CheckoutService.State.SUCCESS:
                // Скрываем форму, показываем success
                checkoutForm.close();
                checkoutSuccess.show(data.order);
                
                // Очищаем корзину
                if (getCartCallback) {
                    const cart = getCartCallback();
                    cart.length = 0;
                    if (window.updateCartDisplay) {
                        window.updateCartDisplay();
                    }
                }
                
                // Сбрасываем кнопку
                resetCheckoutButton();
                break;

            case CheckoutService.State.ERROR:
                // Показываем ошибку
                alert('Ошибка: ' + data.error);
                resetCheckoutButton();
                break;
        }
    });

    /**
     * Сброс кнопки checkout
     */
    function resetCheckoutButton() {
        const checkoutBtn = document.querySelector('#cart-total button.checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = '<span>Checkout</span>';
        }
    }

    /**
     * Обработчик кнопки Checkout
     */
    function handleCheckoutClick() {
        const cart = getCartCallback ? getCartCallback() : [];
        
        if (!cart || cart.length === 0) {
            alert('Ваша корзина пуста');
            return;
        }

        // Открываем форму
        checkoutForm.open();
    }

    /**
     * Инициализация checkout формы
     */
    checkoutForm.onSubmit = async (customerData) => {
        const cart = getCartCallback ? getCartCallback() : [];
        
        if (!cart || cart.length === 0) {
            alert('Ваша корзина пуста');
            return;
        }

        // Отправляем заказ
        await checkoutService.process(cart, customerData);
    };

    /**
     * Установить callback для получения корзины
     */
    window.initCheckout = function(getCartFn) {
        getCartCallback = getCartFn;
        
        // Навешиваем обработчик на кнопку
        const checkoutBtn = document.querySelector('#cart-total .checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', handleCheckoutClick);
        }
        
        console.log('Checkout initialized');
    };

    /**
     * Обновить кнопку checkout (для динамического обновления)
     */
    window.updateCheckoutButton = function() {
        const checkoutBtn = document.querySelector('#cart-total .checkout-btn');
        if (checkoutBtn) {
            // Удаляем старые обработчики
            checkoutBtn.replaceWith(checkoutBtn.cloneNode(true));
            
            // Навешиваем новый обработчик
            const newBtn = document.querySelector('#cart-total .checkout-btn');
            if (newBtn) {
                newBtn.addEventListener('click', handleCheckoutClick);
            }
        }
    };
})();
