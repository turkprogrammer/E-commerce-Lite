<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Entity\OrderStatus;
use App\Domain\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Фикстуры для заказов
 */
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Загрузить фикстуры заказов
     */
    public function load(ObjectManager $manager): void
    {
        $products = $manager->getRepository(Product::class)->findAll();

        if (empty($products)) {
            return;
        }

        $statuses = OrderStatus::cases();

        // Создаем 15 тестовых заказов
        for ($i = 1; $i <= 15; $i++) {
            $order = new Order();

            $order->setCustomerName("Клиент #$i");
            $order->setCustomerEmail("customer$i@example.com");
            $order->setCustomerPhone("+7 (999) 000-00" . str_pad((string)$i, 2, '0', STR_PAD_LEFT));
            $order->setDeliveryAddress("г. Москва, ул. Примерная, д. $i, кв. " . ($i * 10));
            $order->setStatus($statuses[array_rand($statuses)]);

            // Добавляем 1-3 товара в заказ
            $numItems = random_int(1, 3);
            $shuffledProducts = $products;
            shuffle($shuffledProducts);

            for ($j = 0; $j < $numItems; $j++) {
                $product = $shuffledProducts[$j];
                $quantity = random_int(1, 3);

                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setProduct($product);
                $orderItem->setProductName($product->getName());
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice($product->getPrice());

                $order->addItem($orderItem);
            }

            // Пересчитываем сумму заказа
            $order->recalculate();

            $manager->persist($order);
        }

        $manager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}
