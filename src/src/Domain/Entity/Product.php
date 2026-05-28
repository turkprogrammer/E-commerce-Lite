<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Infrastructure\Doctrine\Repository\ProductDoctrineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Товар
 *
 * Сущность товара, представляющая продукт в интернет-магазине.
 * Содержит информацию о названии, цене, наличии на складе, статусе активности
 * и связи с категорией. Также связана с элементами корзины и заказа.
 */
#[ORM\Entity(repositoryClass: ProductDoctrineRepository::class)]
#[ORM\Table(name: 'products')]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['api_read'])]
    private ?int $id = null;

    /**
     * Название товара
     *
     * @var string Название продукта (максимум 200 символов)
     */
    #[ORM\Column(type: 'string', length: 200)]
    #[Groups(['api_read'])]
    private string $name;

    /**
     * Цена товара
     *
     * @var float Цена продукта с двумя знаками после запятой
     */
    #[ORM\Column(type: 'float', scale: 2)]
    #[Groups(['api_read'])]
    private float $price;

    /**
     * Количество на складе
     *
     * @var int Количество единиц товара доступных для продажи
     */
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups(['api_read'])]
    private int $stock = 0;

    /**
     * Статус активности товара
     *
     * @var bool Флаг, указывающий, доступен ли товар для продажи
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['api_read'])]
    private bool $active = true;

    /**
     * Категория товара
     *
     * @var Category|null Ссылка на категорию, к которой принадлежит товар
     */
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Groups(['api_read'])]
    private ?Category $category = null;

    /**
     * Элементы корзины, связанные с этим товаром
     *
     * @var Collection<int, CartItem> Коллекция элементов корзины, содержащих этот товар
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'product')]
    private Collection $cartItems;

    /**
     * Элементы заказа, связанные с этим товаром
     *
     * @var Collection<int, OrderItem> Коллекция элементов заказа, содержащих этот товар
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'product')]
    private Collection $orderItems;

    /**
     * Дата создания товара
     *
     * @var \DateTimeImmutable Дата и время создания товара
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Дата обновления товара
     *
     * @var \DateTimeImmutable Дата и время последнего обновления товара
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * Описание товара
     *
     * @var string|null Подробное описание товара
     */
    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['api_read'])]
    private ?string $description = null;

    /**
     * Артикул товара
     *
     * @var string|null Уникальный артикул или SKU товара
     */
    #[ORM\Column(type: 'string', length: 100, unique: true, nullable: true)]
    #[Groups(['api_read'])]
    private ?string $sku = null;

    /**
     * Конструктор класса Product
     *
     * Инициализирует коллекции для элементов корзины и заказа, устанавливает даты создания и обновления.
     */
    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Получить идентификатор товара
     *
     * @return int|null Идентификатор товара или null, если товар еще не сохранен
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Установить идентификатор товара
     *
     * @param int|null $id Идентификатор товара
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Получить название товара
     *
     * @return string Название продукта
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Установить название товара
     *
     * @param string $name Название продукта
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Получить цену товара
     *
     * @return float Цена продукта
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Установить цену товара
     *
     * @param float $price Цена продукта
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Получить количество товара на складе
     *
     * @return int Количество единиц товара доступных для продажи
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * Установить количество товара на складе
     *
     * @param int $stock Количество единиц товара
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Проверить, активен ли товар
     *
     * @return bool Возвращает true, если товар активен и доступен для продажи, иначе false
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Установить статус активности товара
     *
     * @param bool $active Статус активности товара
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Получить категорию товара
     *
     * @return Category|null Ссылка на категорию, к которой принадлежит товар
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Установить категорию товара
     *
     * @param Category|null $category Категория, к которой будет принадлежать товар
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Получить элементы корзины, связанные с этим товаром
     *
     * @return Collection<int, CartItem> Коллекция элементов корзины
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    /**
     * Получить элементы заказа, связанные с этим товаром
     *
     * @return Collection<int, OrderItem> Коллекция элементов заказа
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * Получить дату создания товара
     *
     * @return \DateTimeImmutable Дата и время создания товара
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Получить дату обновления товара
     *
     * @return \DateTimeImmutable Дата и время последнего обновления товара
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Получить описание товара
     *
     * @return string|null Подробное описание товара
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Установить описание товара
     *
     * @param string|null $description Описание товара
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Получить артикул товара
     *
     * @return string|null Уникальный артикул или SKU товара
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * Установить артикул товара
     *
     * @param string|null $sku Артикул или SKU товара
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setSku(?string $sku): self
    {
        $this->sku = $sku;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Получить URL изображения товара
     *
     * @return string URL изображения-заглушки
     */
    #[Groups(['api_read'])]
    public function getImageUrl(): string
    {
        return 'https://placehold.co/600x600/18181b/88ff0d?text=' . urlencode($this->name);
    }

    /**
     * Проверить, есть ли товар в наличии
     *
     * @return bool Возвращает true, если товар есть в наличии, иначе false
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Проверить, можно ли купить товар
     *
     * @return bool Возвращает true, если товар активен и есть в наличии, иначе false
     */
    public function isAvailableForPurchase(): bool
    {
        return $this->isActive() && $this->isInStock();
    }

    /**
     * Обновить дату изменения при изменении сущности
     */
    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
