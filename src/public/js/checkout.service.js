/**
 * CheckoutService — State Machine для процесса оформления заказа
 * 
 * States: IDLE → PROCESSING → SUCCESS | ERROR
 */
class CheckoutService {
    // Состояния
    static State = {
        IDLE: 'idle',
        PROCESSING: 'processing',
        SUCCESS: 'success',
        ERROR: 'error'
    };

    constructor() {
        this.state = CheckoutService.State.IDLE;
        this.listeners = new Set();
        this.error = null;
        this.order = null;
    }

    /**
     * Подписка на изменения состояния
     */
    subscribe(callback) {
        this.listeners.add(callback);
        return () => this.listeners.delete(callback);
    }

    /**
     * Уведомить подписчиков об изменении
     */
    notify() {
        this.listeners.forEach(callback => callback(this.state, {
            error: this.error,
            order: this.order
        }));
    }

    /**
     * Получить текущее состояние
     */
    getState() {
        return this.state;
    }

    /**
     * Обработать заказ
     * @param {Array} cartItems - Элементы корзины
     * @param {Object} customerData - Данные покупателя
     */
    async process(cartItems, customerData) {
        try {
            // Переход в состояние PROCESSING
            this.state = CheckoutService.State.PROCESSING;
            this.error = null;
            this.notify();

            // Валидация
            if (!cartItems || cartItems.length === 0) {
                throw new Error('Корзина пуста');
            }

            // Формирование payload
            const payload = {
                customerName: customerData.customerName,
                customerEmail: customerData.customerEmail,
                customerPhone: customerData.customerPhone,
                deliveryAddress: customerData.deliveryAddress,
                items: cartItems.map(item => ({
                    productId: item.productId,
                    quantity: item.quantity
                }))
            };

            // Отправка запроса с cookies
            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload),
                credentials: 'include' // Отправлять cookies для сессии
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Ошибка при создании заказа');
            }

            // Успех
            this.order = result.data.order;
            this.state = CheckoutService.State.SUCCESS;
            this.notify();

            return { success: true, order: this.order };

        } catch (error) {
            // Ошибка
            this.error = error.message;
            this.state = CheckoutService.State.ERROR;
            this.notify();

            return { success: false, error: error.message };
        }
    }

    /**
     * Сбросить состояние
     */
    reset() {
        this.state = CheckoutService.State.IDLE;
        this.error = null;
        this.order = null;
        this.notify();
    }
}

// Экспорт глобально
window.CheckoutService = CheckoutService;
