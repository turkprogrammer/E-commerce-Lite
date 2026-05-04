<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Элемент корзины
 *
 * Сущность элемента корзины, представляющая товар в корзине пользователя с указанием количества.
 * Связывает корзину пользователя и продукт.
 */
#[ORM\Entity]
#[ORM\Table(name: 'cart_items')]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Корзина, которой принадлежит этот элемент
     *
     * @var Cart|null Ссылка на корзину пользователя
     */
    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'cart_id', referencedColumnName: 'id', nullable: false)]
    private ?Cart $cart = null;

    /**
     * Продукт, представленный в этом элементе корзины
     *
     * @var Product|null Ссылка на продукт
     */
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'cartItems')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private ?Product $product = null;

    /**
     * Количество единиц продукта
     *
     * @var int Количество единиц продукта в корзине (не может быть меньше 1)
     */
    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    /**
     * Конструктор класса CartItem
     */
    public function __construct()
    {
    }

    /**
     * Получить идентификатор элемента корзины
     *
     * @return int|null Идентификатор элемента корзины или null, если элемент еще не сохранен
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить корзину, которой принадлежит этот элемент
     *
     * @return Cart|null Ссылка на корзину пользователя
     */
    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    /**
     * Установить корзину для этого элемента
     *
     * @param Cart|null $cart Ссылка на корзину пользователя
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     * Получить продукт, представленный в этом элементе корзины
     *
     * @return Product|null Ссылка на продукт
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * Установить продукт для этого элемента корзины
     *
     * @param Product|null $product Ссылка на продукт
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Получить количество единиц продукта
     *
     * @return int Количество единиц продукта в корзине
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Установить количество единиц продукта
     *
     * @param int $quantity Количество единиц продукта (будет нормализовано к значению >= 1)
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = max(1, $quantity);
        return $this;
    }

    /**
     * Получить общую цену для этого элемента корзины
     *
     * @return float Общая цена (цена продукта * количество)
     */
    public function getTotalPrice(): float
    {
        return $this->product ? $this->product->getPrice() * $this->quantity : 0.0;
    }
}
