<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Category;
use App\Repository\CategoryRepository;

use function Symfony\Component\String\u;

use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Category>
 *
 * @method static Category|Proxy                     createOne(array $attributes = [])
 * @method static Category[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Category[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Category|Proxy                     find(array|mixed|object $criteria)
 * @method static Category|Proxy                     findOrCreate(array $attributes)
 * @method static Category|Proxy                     first(string $sortedField = 'id')
 * @method static Category|Proxy                     last(string $sortedField = 'id')
 * @method static Category|Proxy                     random(array $attributes = [])
 * @method static Category|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Category[]|Proxy[]                 all()
 * @method static Category[]|Proxy[]                 findBy(array $attributes)
 * @method static Category[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Category[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static CategoryRepository|RepositoryProxy repository()
 * @method        Category|Proxy                     create(array|callable $attributes = [])
 */
final class CategoryFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => u(self::faker()->words(3, true))->title(),
            'description' => self::faker()->text(40),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Category $Category): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Category::class;
    }
}
