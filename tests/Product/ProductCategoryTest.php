<?php

namespace Marascon\ShoppingCart\Tests\Product;

use Marascon\ShoppingCart\Product\ProductCategory;
use PHPUnit\Framework\TestCase;

class ProductCategoryTest extends TestCase
{
    public function testEnumValues(): void
    {
        // Assert that the value of the Electronics category is 'electronics'
        $this->assertSame('electronics', ProductCategory::Electronics->value);

        // Assert that the value of the Furniture category is 'furniture'
        $this->assertSame('furniture', ProductCategory::Furniture->value);

        // Assert that the value of the Groceries category is 'groceries'
        $this->assertSame('groceries', ProductCategory::Groceries->value);

        // Assert that the value of the HomeAppliances category is 'homeappliances'
        $this->assertSame('homeappliances', ProductCategory::HomeAppliances->value);
    }
}
