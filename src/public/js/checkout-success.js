/**
 * CheckoutSuccess — UI компонент успешного завершения заказа
 */
class CheckoutSuccess {
    constructor() {
        this.isOpen = false;
    }

    /**
     * Показать success modal
     * @param {Object} order - Данные заказа
     */
    show(order) {
        this.order = order;
        this.isOpen = true;
        this.render();
    }

    /**
     * Закрыть modal
     */
    close() {
        this.isOpen = false;
        this.render();
    }

    /**
     * Рендер компонента
     */
    render() {
        let container = document.getElementById('checkout-success-container');
        
        if (!container) {
            container = document.createElement('div');
            container.id = 'checkout-success-container';
            document.body.appendChild(container);
        }

        if (!this.isOpen) {
            container.innerHTML = '';
            return;
        }

        const orderNumber = this.order?.orderNumber || 'ORD-XXXXXX';
        const totalAmount = this.order?.totalAmount || 0;

        container.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="checkoutSuccess.close()">
                <div class="bg-white rounded-2xl max-w-md w-full" onclick="event.stopPropagation()">
                    <!-- Icon -->
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        
                        <h2 class="text-2xl font-bold mb-2">Заказ оформлен!</h2>
                        <p class="text-gray-500 mb-6">Спасибо за вашу покупку</p>
                        
                        <!-- Order Details -->
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <div class="text-sm text-gray-500 mb-1">Номер заказа</div>
                            <div class="text-xl font-bold text-gray-900 mb-3">${orderNumber}</div>
                            
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Сумма заказа</span>
                                    <span class="text-lg font-bold text-gray-900">${totalAmount.toFixed(0)} ₽</span>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500 mb-6">
                            Мы отправили подтверждение на вашу электронную почту
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="p-6 pt-0">
                        <button 
                            onclick="checkoutSuccess.close()"
                            class="w-full py-4 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition-colors"
                        >
                            Отлично
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
}

// Экспорт глобально
window.checkoutSuccess = new CheckoutSuccess();
