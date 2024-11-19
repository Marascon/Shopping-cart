<?php

namespace Marascon\ShoppingCart\Tests\Cart\Discount;

use Marascon\ShoppingCart\Cart\Cart;
use Marascon\ShoppingCart\Cart\Discount\PercentageDiscount;
use Marascon\ShoppingCart\Config\Config;
use Marascon\ShoppingCart\Exceptions\InvalidDiscountException;
use Marascon\ShoppingCart\Product\Product;
use Marascon\ShoppingCart\Product\ProductCategory;
use Marascon\ShoppingCart\Storage\SessionStorage;
use PHPUnit\Framework\TestCase;

class PercentageDiscountTest extends TestCase
{
    private Cart $cart;

    protected function setUp(): void
    {
        $config = new Config('EUR', 21.0);  // Currency: EUR, VAT: 21%
        $storage = new SessionStorage();
        $this->cart = new Cart($config, $storage);
        $this->cart->clearCart(); // Ensure a clean state before each test

        // Add products to the cart
        $product1 = new Product('001', 'Laptop', 1500, ProductCategory::Electronics, 2);
        $product2 = new Product('002', 'Smartphone', 800, ProductCategory::Electronics, 1);
        $product3 = new Product('003', 'Coffee Maker', 120, ProductCategory::HomeAppliances, 3);

        $this->cart->addProduct($product1);
        $this->cart->addProduct($product2);
        $this->cart->addProduct($product3);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyPercentageDiscountWithoutVat(): void
    {
        // Apply a 10% discount
        $this->cart->setDiscount(new PercentageDiscount(10));

        // Calculate the total price without VAT
        $totalPriceWithoutVat = $this->cart->calculateTotalPriceWithoutVat();

        // Calculate expected total price without VAT
        $expectedTotalPriceWithoutVat = (1500 * 2) + (800 * 1) + (120 * 3); // 4160 EUR
        $expectedDiscountedPriceWithoutVat = $expectedTotalPriceWithoutVat - ($expectedTotalPriceWithoutVat * 0.10); // 4160 - 416 = 3744 EUR

        // Assert the discounted price without VAT
        $this->assertEqualsWithDelta($expectedDiscountedPriceWithoutVat, $totalPriceWithoutVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyPercentageDiscountWithVat(): void
    {
        // Apply a 10% discount
        $this->cart->setDiscount(new PercentageDiscount(10));

        // Calculate the total price with VAT
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();

        // Calculate expected total price without VAT
        $expectedTotalPriceWithoutVat = (1500 * 2) + (800 * 1) + (120 * 3); // 4160 EUR
        $expectedDiscountedPriceWithoutVat = $expectedTotalPriceWithoutVat - ($expectedTotalPriceWithoutVat * 0.10); // 3744 EUR

        // Add VAT (21%) to the discounted price
        $vatRate = 0.21; // 21% VAT
        $expectedTotalPriceWithVat = $expectedDiscountedPriceWithoutVat + ($expectedDiscountedPriceWithoutVat * $vatRate); // 3744 + 785.24 = 4529.24 EUR

        // Assert the total price with VAT
        $this->assertEqualsWithDelta($expectedTotalPriceWithVat, $totalPriceWithVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyZeroDiscountWithoutVat(): void
    {
        // Apply a 0% discount (no discount)
        $this->cart->setDiscount(new PercentageDiscount(0));

        // Calculate the total price without VAT (should remain the same)
        $totalPriceWithoutVat = $this->cart->calculateTotalPriceWithoutVat();
        $expectedTotalPriceWithoutVat = (1500 * 2) + (800 * 1) + (120 * 3); // 4160 EUR

        // Assert that no discount is applied
        $this->assertEqualsWithDelta($expectedTotalPriceWithoutVat, $totalPriceWithoutVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyZeroDiscountWithVat(): void
    {
        // Apply a 0% discount (no discount)
        $this->cart->setDiscount(new PercentageDiscount(0));

        // Calculate the total price with VAT (should remain the same)
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();
        $expectedTotalPriceWithoutVat = (1500 * 2) + (800 * 1) + (120 * 3); // 4160 EUR
        $vatAmount = $expectedTotalPriceWithoutVat * 0.21; // 4160 * 0.21 = 873.6 EUR
        $expectedTotalPriceWithVat = $expectedTotalPriceWithoutVat + $vatAmount; // 4160 + 873.6 = 5033.6 EUR

        // Assert that no discount is applied
        $this->assertEqualsWithDelta($expectedTotalPriceWithVat, $totalPriceWithVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyHundredPercentDiscountWithoutVat(): void
    {
        // Apply a 100% discount (free cart)
        $this->cart->setDiscount(new PercentageDiscount(100));

        // Calculate the total price without VAT (should be 0)
        $totalPriceWithoutVat = $this->cart->calculateTotalPriceWithoutVat();
        $expectedTotalPriceWithoutVat = 0.00;

        // Assert the total price without VAT is 0
        $this->assertEqualsWithDelta($expectedTotalPriceWithoutVat, $totalPriceWithoutVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyHundredPercentDiscountWithVat(): void
    {
        // Apply a 100% discount (free cart)
        $this->cart->setDiscount(new PercentageDiscount(100));

        // Calculate the total price with VAT (should be 0)
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();
        $expectedTotalPriceWithVat = 0.00;

        // Assert the total price with VAT is 0
        $this->assertEqualsWithDelta($expectedTotalPriceWithVat, $totalPriceWithVat, 0.01);
    }

    /**
     */
    public function testApplyNegativeDiscountWithoutVat(): void
    {
        try {
            $this->cart->setDiscount(new PercentageDiscount(-10));  // Negative discount
        } catch (InvalidDiscountException $e) {
            // Assert the exception is thrown for negative discount
            $this->assertStringContainsString('Negative discount detected', $e->getMessage());
        }

        // Calculate the total price without VAT (should remain unchanged)
        $totalPriceWithoutVat = (1500 * 2) + (800 * 1) + (120 * 3); // 4160 EUR
        $vatAmount = $totalPriceWithoutVat * 0.21; // 4160 * 0.21 = 873.6 EUR
        $expectedTotalWithVat = $totalPriceWithoutVat + $vatAmount; // 4160 + 873.6 = 5033.6 EUR

        // Assert that no discount has been applied due to negative discount
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();
        $this->assertEqualsWithDelta($expectedTotalWithVat, $totalPriceWithVat, 0.01);
    }

    public function testApplyNegativeDiscountWithVat(): void
    {
        // Attempt to apply a negative discount
        try {
            $this->cart->setDiscount(new PercentageDiscount(-10));
        } catch (InvalidDiscountException $e) {
            // Assert the exception is thrown for negative discount
            $this->assertStringContainsString('Negative discount detected', $e->getMessage());
        }

        // Calculate the total price with VAT (should remain unchanged)
        $totalPriceWithVat = $this->cart->calculateTotalPriceWithVat();
        $expectedTotalWithVat = 5033.6;  // As calculated previously

        // Assert that the total price with VAT remains unchanged
        $this->assertEqualsWithDelta($expectedTotalWithVat, $totalPriceWithVat, 0.01);
    }

    /**
     * @throws InvalidDiscountException
     */
    public function testApplyDiscountGreaterThanHundredPercentWithoutVat(): void
    {
        // Expect the exception and its message
        $this->expectException(InvalidDiscountException::class);
        $this->expectExceptionMessage('[InvalidDiscount] Discount percentage cannot be greater than 100(150). Not applying discount.');

        // Instantiate the PercentageDiscount with an invalid percentage greater than 100
        new PercentageDiscount(150);  // This should throw the exception

    }

    public function testApplyDiscountGreaterThanHundredPercent()
    {
        // Expect the exception and its message
        $this->expectException(InvalidDiscountException::class);
        $this->expectExceptionMessage('[InvalidDiscount] Discount percentage cannot be greater than 100(150). Not applying discount.');

        // Instantiate the PercentageDiscount with an invalid percentage greater than 100
        new PercentageDiscount(150);  // This should throw the exception
}

    /**
     */
    public function testNegativeDiscountThrowsException(): void
    {
        // Try to apply a negative discount and catch the exception
        try {
            $this->cart->setDiscount(new PercentageDiscount(-10));
        } catch (InvalidDiscountException $e) {
            // Assert that the exception is thrown for negative discount
            $this->assertStringContainsString('Negative discount detected', $e->getMessage());
        }
    }
}
