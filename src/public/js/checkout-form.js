/**
 * CheckoutForm — UI компонент формы оформления заказа
 */
class CheckoutForm {
    constructor(onSubmit) {
        this.onSubmit = onSubmit;
        this.isOpen = false;
    }

    /**
     * Открыть форму
     */
    open() {
        this.isOpen = true;
        this.render();
    }

    /**
     * Закрыть форму
     */
    close() {
        this.isOpen = false;
        this.render();
    }

    /**
     * Получить данные формы
     */
    getData() {
        return {
            customerName: document.getElementById('checkout-name')?.value || '',
            customerEmail: document.getElementById('checkout-email')?.value || '',
            customerPhone: document.getElementById('checkout-phone')?.value || '',
            deliveryAddress: document.getElementById('checkout-address')?.value || ''
        };
    }

    /**
     * Валидация данных
     */
    validate(data) {
        const errors = [];

        if (!data.customerName || data.customerName.trim().length < 2) {
            errors.push('Введите имя (минимум 2 символа)');
        }

        if (!data.customerEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.customerEmail)) {
            errors.push('Введите корректный email');
        }

        if (!data.customerPhone || data.customerPhone.trim().length < 5) {
            errors.push('Введите телефон');
        }

        if (!data.deliveryAddress || data.deliveryAddress.trim().length < 5) {
            errors.push('Введите адрес доставки');
        }

        return errors;
    }

    /**
     * Обработать отправку
     */
    async handleSubmit() {
        const data = this.getData();
        const errors = this.validate(data);

        if (errors.length > 0) {
            alert(errors.join('\n'));
            return;
        }

        await this.onSubmit(data);
    }

    /**
     * Рендер компонента
     */
    render() {
        let container = document.getElementById('checkout-modal-container');
        
        if (!container) {
            container = document.createElement('div');
            container.id = 'checkout-modal-container';
            document.body.appendChild(container);
        }

        if (!this.isOpen) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="checkoutForm.close()">
                <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                        <h2 class="text-xl font-bold">Оформление заказа</h2>
                        <button onclick="checkoutForm.close()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="p-6 space-y-4">
                        <!-- Имя -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Имя <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="checkout-name"
                                placeholder="Иван Иванов"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="checkout-email"
                                placeholder="ivan@example.com"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all"
                            />
                        </div>

                        <!-- Телефон -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Телефон <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="checkout-phone"
                                placeholder="+7 (999) 123-45-67"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all"
                            />
                        </div>

                        <!-- Адрес -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Адрес доставки <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="checkout-address"
                                placeholder="г. Москва, ул. Пушкина, д. 10, кв. 5"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition-all resize-none"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-2xl">
                        <button 
                            onclick="checkoutForm.handleSubmit()"
                            class="w-full py-4 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition-colors flex items-center justify-center"
                        >
                            <span>Подтвердить заказ</span>
                        </button>
                        <p class="text-xs text-gray-500 text-center mt-2">
                            Нажимая кнопку, вы соглашаетесь с условиями обработки персональных данных
                        </p>
                    </div>
                </div>
            </div>
        `;
    }
}

// Экспорт глобально
window.checkoutForm = new CheckoutForm(async (customerData) => {
    // Будет переопределено при инициализации
    console.log('Checkout form submitted', customerData);
});
