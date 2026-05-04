<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Категория товаров
 *
 * Сущность категории товаров, представляющая иерархическую структуру категорий.
 * Может содержать дочерние категории и быть связанной с родительской категорией.
 */
#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['api_read'])]
    private ?int $id = null;

    /**
     * Название категории
     *
     * @var string Уникальное название категории (максимум 100 символов)
     */
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    #[Groups(['api_read'])]
    private string $name;

    /**
     * Статус активности категории
     *
     * @var bool Флаг, указывающий, активна ли категория (по умолчанию true)
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['api_read'])]
    private bool $active = true;

    /**
     * Родительская категория
     *
     * @var self|null Ссылка на родительскую категорию для иерархической структуры
     */
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?self $parent = null;

    /**
     * Дочерние категории
     *
     * @var Collection<int, self> Коллекция дочерних категорий
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * Продукты в категории
     *
     * @var Collection<int, Product> Коллекция продуктов, принадлежащих этой категории
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
    private Collection $products;

    /**
     * Конструктор класса Category
     *
     * Инициализирует коллекции для дочерних категорий и продуктов.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * Получить идентификатор категории
     *
     * @return int|null Идентификатор категории или null, если категория еще не сохранена
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Получить название категории
     *
     * @return string|null Название категории
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Установить название категории
     *
     * @param string $name Название категории
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Проверить, активна ли категория
     *
     * @return bool Возвращает true, если категория активна, иначе false
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Установить статус активности категории
     *
     * @param bool $active Статус активности категории
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Получить родительскую категорию
     *
     * @return self|null Ссылка на родительскую категорию или null, если это корневая категория
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * Установить родительскую категорию
     *
     * @param self|null $parent Родительская категория
     * @return self Возвращает текущий экземпляр для цепочки вызовов
     */
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Получить дочерние категории
     *
     * @return Collection<int, self> Коллекция дочерних категорий
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * Получить продукты в категории
     *
     * @return Collection<int, Product> Коллекция продуктов, принадлежащих этой категории
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Строковое представление категории
     *
     * @return string Название категории
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
