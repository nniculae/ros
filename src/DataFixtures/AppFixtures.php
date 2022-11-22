<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\AddressFactory;
use App\Factory\CategoryFactory;
use App\Factory\OrderFactory;
use App\Factory\OrderItemFactory;
use App\Factory\PaymentFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(9);

        AddressFactory::createMany(
            9,
            fn () => ['user' => UserFactory::random()]
        );

        CategoryFactory::createMany(5);

        ProductFactory::createMany(
            15,
            fn () => ['category' => CategoryFactory::random()]
        );

        $orders = OrderFactory::createMany(
            9,
            fn () => ['user' => UserFactory::random()]
        );

        OrderItemFactory::createMany(
            50,
            fn () => ['orderContainer' => OrderFactory::random(), 'product' => ProductFactory::random()]
        );

        PaymentFactory::createSequence(
            function () use ($orders) {
                foreach ($orders as $order) {
                    $amount = 0;
                    $orderItems = OrderItemFactory::findBy(['orderContainer' => $order]);

                    foreach ($orderItems as $orderItem) {
                        $amount += $orderItem->getQuantity() * (float) $orderItem->getProduct()->getPrice();
                    }

                    yield ['amount' => (string) $amount, 'orderContainer' => $order];
                }
            }
        );
    }
}
