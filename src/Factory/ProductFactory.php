<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use App\Repository\ProductRepository;

use function Symfony\Component\String\u;

use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Food>
 *
 * @method static Product|Proxy                     createOne(array $attributes = [])
 * @method static Product[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Product[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Product|Proxy                     find(array|mixed|object $criteria)
 * @method static Product|Proxy                     findOrCreate(array $attributes)
 * @method static Product|Proxy                     first(string $sortedField = 'id')
 * @method static Product|Proxy                     last(string $sortedField = 'id')
 * @method static Product|Proxy                     random(array $attributes = [])
 * @method static Product|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Product[]|Proxy[]                 all()
 * @method static Product[]|Proxy[]                 findBy(array $attributes)
 * @method static Product[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Product[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ProductRepository|RepositoryProxy repository()
 * @method        Product|Proxy                     create(array|callable $attributes = [])
 */
final class ProductFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => u(self::faker()->words(5, true))->title(),
            'description' => self::faker()->text(20, true),
            'price' => self::faker()->randomFloat(2, 1, 3),
            // 'category' => CategoryFactory::createOne(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Food $food): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Product::class;
    }
}
