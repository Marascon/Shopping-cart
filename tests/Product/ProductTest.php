<?php

namespace Marascon\ShoppingCart\Tests\Product;

use Marascon\ShoppingCart\Product\Product;
use Marascon\ShoppingCart\Product\ProductCategory;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCreateProduct(): void
    {
        // Create a new product instance with sample values
        $product = new Product('001', 'Laptop', 1500, ProductCategory::Electronics, 1);

        // Assert that the product's article number is correctly set
        $this->assertSame('001', $product->getArticleNumber());

        // Assert that the product's description is correctly set
        $this->assertSame('Laptop', $product->getDescription());

        // Assert that the product's price is correctly set
        $this->assertSame(1500.0, $product->getPrice());

        // Assert that the product's quantity is correctly set
        $this->assertSame(1, $product->getQuantity());

        // Assert that the product's category is correctly set
        $this->assertSame(ProductCategory::Electronics, $product->getCategory());
    }

    public function testUpdateQuantity(): void
    {
        // Create a new product instance with initial quantity
        $product = new Product('001', 'Laptop', 1500, ProductCategory::Electronics, 1);

        // Update the product's quantity by creating a new instance with updated quantity
        $updatedProduct = $product->withQuantity(5);

        // Assert that the original product's quantity remains unchanged
        $this->assertSame(1, $product->getQuantity());

        // Assert that the updated product's quantity is correctly set to 5
        $this->assertSame(5, $updatedProduct->getQuantity());
    }
}
