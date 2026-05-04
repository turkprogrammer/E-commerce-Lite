<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Корзина покупок
 *
 * Сущность корзины покупок, представляющая временное хранилище товаров перед оформлением заказа.
 * Связана с сессией пользователя и содержит коллекцию элементов корзины.
 */
#[ORM\Entity]
#[ORM\Table(name: 'carts')]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Идентификатор сессии пользователя
     *
     * @var string Уникальный идентификатор сессии, связанный с этой корзиной
     */
    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $sessionId;

    /**
     * Элементы корзины
     *
     * @var Collection<int, CartItem> Коллекция элементов корзины
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cart', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $items;

    /**
     * Общая сумма заказа
     *
     * @var float Общая стоимость всех товаров в корзине
     */
    #[ORM\Column(type: 'float', scale: 2, options: ['default' => 0.0])]
    private float $totalAmount = 0.0;

    /**
     * Общее количество товаров
     *
     * @var int Общее количество единиц товаров в корзине
     */
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $totalItems = 0;

    /**
     * Дата создания корзины
     *
     * @var \DateTimeImmutable Дата и время создания корзины
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Дата обновления корзины
     *
     * @var \DateTimeImmutable Дата и время последнего обновления корзины
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * Конструктор класса Cart
     *
     * Инициализирует коллекцию элементов корзины.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Получить идентификатор корзины
     *
     * @return int|null Идентификатор корзины или null, если корзина еще не сохранена
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить идентификатор сессии
     *
     * @return string Идентификатор сессии пользователя
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Установить идентификатор сессии
     *
     * @param string $sessionId Идентификатор сессии пользователя
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Получить элементы корзины
     *
     * @return Collection<int, CartItem> Коллекция элементов корзины
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Добавить элемент в корзину
     *
     * @param CartItem $item Элемент корзины для добавления
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function addItem(CartItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCart($this);
        }
        return $this;
    }

    /**
     * Удалить элемент из корзины
     *
     * @param CartItem $item Элемент корзины для удаления
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function removeItem(CartItem $item): self
    {
        $this->items->removeElement($item);
        return $this;
    }

    /**
     * Получить общую сумму заказа
     *
     * @return float Общая стоимость всех товаров в корзине
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * Установить общую сумму заказа
     *
     * @param float $totalAmount Общая стоимость всех товаров в корзине
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    /**
     * Получить общее количество товаров
     *
     * @return int Общее количество единиц товаров в корзине
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * Установить общее количество товаров
     *
     * @param int $totalItems Общее количество единиц товаров в корзине
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setTotalItems(int $totalItems): self
    {
        $this->totalItems = $totalItems;
        return $this;
    }

    /**
     * Проверить, пуста ли корзина
     *
     * @return bool Возвращает true, если корзина пуста, иначе false
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Пересчитать корзину
     *
     * Обновляет общую сумму и количество товаров в корзине на основе элементов корзины.
     */
    public function recalculate(): void
    {
        $this->totalAmount = 0.0;
        $this->totalItems = 0;

        foreach ($this->items as $item) {
            $this->totalAmount += $item->getTotalPrice();
            $this->totalItems += $item->getQuantity();
        }
    }

    /**
     * Очистить корзину
     *
     * Удаляет все элементы из корзины и обнуляет общую сумму и количество товаров.
     */
    public function clear(): void
    {
        $this->items->clear();
        $this->totalAmount = 0.0;
        $this->totalItems = 0;
    }

    /**
     * Получить дату создания корзины
     *
     * @return \DateTimeImmutable Дата и время создания корзины
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Получить дату обновления корзины
     *
     * @return \DateTimeImmutable Дата и время последнего обновления корзины
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Установить дату обновления корзины
     *
     * @param \DateTimeImmutable $updatedAt Дата и время обновления
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
