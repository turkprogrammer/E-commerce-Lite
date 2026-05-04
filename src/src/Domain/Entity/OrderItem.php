<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Элемент заказа
 *
 * Сущность элемента заказа, представляющая конкретный товар в заказе с его названием,
 * количеством, ценой и общей стоимостью. Связывает заказ и продукт.
 */
#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Заказ, которому принадлежит этот элемент
     *
     * @var Order|null Ссылка на заказ, содержащий этот элемент
     */
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    private ?Order $order = null;

    /**
     * Название продукта
     *
     * @var string Название продукта в момент создания заказа (максимум 200 символов)
     */
    #[ORM\Column(type: 'string', length: 200)]
    private string $productName;

    /**
     * Количество единиц продукта
     *
     * @var int Количество единиц продукта в заказе
     */
    #[ORM\Column(type: 'integer')]
    private int $quantity = 0;

    /**
     * Цена за единицу продукта
     *
     * @var float Цена одного экземпляра продукта на момент заказа
     */
    #[ORM\Column(type: 'float', scale: 2)]
    private float $price = 0.0;

    /**
     * Общая цена для этого элемента
     *
     * @var float Общая стоимость (цена * количество) для этого элемента заказа
     */
    #[ORM\Column(type: 'float', scale: 2)]
    private float $totalPrice = 0.0;

    /**
     * Продукт, связанный с этим элементом заказа
     *
     * @var Product|null Ссылка на оригинальный продукт (может быть null если продукт удален)
     */
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: true)]
    private ?Product $product = null;

    /**
     * Конструктор класса OrderItem
     */
    public function __construct()
    {
    }

    /**
     * Получить идентификатор элемента заказа
     *
     * @return int|null Идентификатор элемента заказа или null, если элемент еще не сохранен
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить заказ, которому принадлежит этот элемент
     *
     * @return Order|null Ссылка на заказ, содержащий этот элемент
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * Установить заказ для этого элемента
     *
     * @param Order|null $order Заказ, которому будет принадлежать этот элемент
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Получить название продукта
     *
     * @return string Название продукта в момент создания заказа
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * Установить название продукта
     *
     * @param string $productName Название продукта
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setProductName(string $productName): self
    {
        $this->productName = $productName;
        return $this;
    }

    /**
     * Получить количество единиц продукта
     *
     * @return int Количество единиц продукта в заказе
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Установить количество единиц продукта
     *
     * @param int $quantity Количество единиц продукта
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        $this->recalculate();
        return $this;
    }

    /**
     * Получить цену за единицу продукта
     *
     * @return float Цена одного экземпляра продукта на момент заказа
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Установить цену за единицу продукта
     *
     * @param float $price Цена за единицу продукта
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        $this->recalculate();
        return $this;
    }

    /**
     * Получить продукт, связанный с этим элементом заказа
     *
     * @return Product|null Ссылка на оригинальный продукт
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * Установить продукт для этого элемента заказа
     *
     * @param Product|null $product Продукт, связанный с этим элементом
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Получить общую цену для этого элемента
     *
     * @return float Общая стоимость (цена * количество) для этого элемента заказа
     */
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    /**
     * Автоматический пересчет общей цены
     *
     * Пересчитывает общую цену элемента заказа на основе цены за единицу и количества.
     */
    private function recalculate(): void
    {
        $this->totalPrice = $this->price * $this->quantity;
    }

    /**
     * Строковое представление элемента заказа
     *
     * @return string Название продукта и количество
     */
    public function __toString(): string
    {
        return sprintf('%s (x%d)', $this->productName, $this->quantity);
    }
}
