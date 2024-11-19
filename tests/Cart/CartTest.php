<?php

namespace Marascon\ShoppingCart\Tests\Cart;

use Marascon\ShoppingCart\Cart\Cart;
use Marascon\ShoppingCart\Cart\Discount\PercentageDiscount;
use Marascon\ShoppingCart\Config\Config;
use Marascon\ShoppingCart\Exceptions\InvalidDiscountException;
use Marascon\ShoppingCart\Product\Product;
use Marascon\ShoppingCart\Product\ProductCategory;
use Marascon\ShoppingCart\Storage\SessionStorage;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private Cart $cart;

    protected function setUp(): void
    {
        // Create config with VAT rate and currency
        $config = new Config('EUR', 21.0);
        $storage = new SessionStorage();
        $this->cart = new Cart($config, $storage);
        $this->cart->clearCart(); // Ensure a clean state

        // Create sample products
        $product1 = new Product('001', 'Laptop', 1500, ProductCategory::Electronics, 1);
        $product2 = new Product('002', 'Smartphone', 800, ProductCategory::Electronics, 2);

        // Add products to cart
        $this->cart->addProduct($product1);
        $this->cart->addProduct($product2);
    }

    public function testAddProduct(): void
    {
        // Get products from the cart
        $products = $this->cart->getProducts();

        // Assert that two products were added
        $this->assertCount(2, $products);
        // Assert product prices
        $this->assertEquals(1500, $products['001']->getPrice());
        $this->assertEquals(800, $products['002']->getPrice());
    }

    public function testCalculateTotalPriceWithVat(): void
    {
        // Calculate the total price without VAT
        $totalPriceWithoutVat = (1500 * 1) + (800 * 2);  // 1500 + 1600 = 3100
        $vatAmount = $totalPriceWithoutVat * 0.21;  // 21% VAT
        $expectedTotalWithVat = $totalPriceWithoutVat + $vatAmount;  // Total with VAT

        // Calculate the actual total price with VAT from the cart
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();

        // Assert that the total price with VAT matches the expected value, with a delta of 0.01 for precision
        $this->assertEqualsWithDelta($expectedTotalWithVat, $totalPriceWithVat, 0.01);
    }

    public function testCalculateTotalPriceWithoutVat(): void
    {
        // Calculate the total price without VAT
        $totalPriceWithoutVat = (1500 * 1) + (800 * 2);  // 1500 + 1600 = 3100

        // Calculate the actual total price without VAT from the cart
        $totalPriceWithoutVatActual = $this->cart->calculateTotalPriceWithoutVat();

        // Assert that the total price without VAT matches the expected value
        $this->assertEquals($totalPriceWithoutVat, $totalPriceWithoutVatActual);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyPercentageDiscount(): void
    {
        // Apply a 10% discount
        $this->cart->setDiscount(new PercentageDiscount(10));

        // Calculate the total price without VAT
        $totalPriceWithoutVat = (1500 * 1) + (800 * 2);  // 1500 + 1600 = 3100
        $vatAmount = $totalPriceWithoutVat * 0.21;  // 21% VAT
        $expectedTotalWithVat = $totalPriceWithoutVat + $vatAmount;  // Total price with VAT
        $expectedTotalWithDiscount = $expectedTotalWithVat - ($expectedTotalWithVat * 0.10);  // Apply 10% discount

        // Calculate the actual total price with discount
        $totalPriceWithDiscount = $this->cart->calculateTotalPriceWithVat();

        // Assert the total price with discount matches the expected value
        $this->assertEqualsWithDelta($expectedTotalWithDiscount, $totalPriceWithDiscount, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyZeroDiscount(): void
    {
        // Apply a 0% discount (no change in price)
        $this->cart->setDiscount(new PercentageDiscount(0));

        // Calculate the total price with VAT (should remain unchanged)
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();
        $totalPriceWithoutVat = (1500 * 1) + (800 * 2);  // 1500 + 1600 = 3100
        $vatAmount = $totalPriceWithoutVat * 0.21;  // 21% VAT
        $expectedTotalWithVat = $totalPriceWithoutVat + $vatAmount;

        // Assert that no discount is applied, and the total with VAT matches the expected value
        $this->assertEqualsWithDelta($expectedTotalWithVat, $totalPriceWithVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyHundredPercentDiscount(): void
    {
        // Apply a 100% discount (total price should become 0)
        $this->cart->setDiscount(new PercentageDiscount(100));

        // Calculate the total price (should be 0 after applying a 100% discount)
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();
        $expectedTotalPrice = 0.00;

        // Assert that the total price is 0 after applying a 100% discount
        $this->assertEqualsWithDelta($expectedTotalPrice, $totalPriceWithVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyNegativeDiscount(): void
    {
        // Expect an exception to be thrown for negative discounts
        $this->expectException(InvalidDiscountException::class);
        $this->expectExceptionMessage('[InvalidDiscount] Negative discount detected(-10). Not applying discount.');


        // Attempt to create a discount with a negative value (-10)
        new PercentageDiscount(-10);
    }
}
