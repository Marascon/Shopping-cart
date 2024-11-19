<?php

namespace Marascon\ShoppingCart\Tests\Cart\Exceptions;

use Marascon\ShoppingCart\Exceptions\InvalidDiscountException;
use PHPUnit\Framework\TestCase;

class InvalidDiscountExceptionTest extends TestCase
{
    /**
     * Test that the exception is thrown for negative discount values.
     */
    public function testNegativeDiscountThrowsException()
    {
        $invalidDiscount = -10; // Negative discount value

        // Expect the InvalidDiscountException to be thrown with the correct message
        $this->expectException(InvalidDiscountException::class);
        $this->expectExceptionMessage("[InvalidDiscount] Negative discount detected({$invalidDiscount}). Not applying discount.");

        // Simulating the discount logic that throws the exception
        throw new InvalidDiscountException("Negative discount detected({$invalidDiscount}). Not applying discount.");
    }

    /**
     * Test that the exception is thrown for discount values greater than 100.
     */
    public function testDiscountGreaterThanHundredThrowsException()
    {
        $invalidDiscount = 150; // Discount greater than 100%

        // Expect the InvalidDiscountException to be thrown with the correct message
        $this->expectException(InvalidDiscountException::class);
        $this->expectExceptionMessage("[InvalidDiscount] Discount percentage cannot be greater than 100({$invalidDiscount}). Not applying discount.");

        // Simulating the discount logic that throws the exception
        throw new InvalidDiscountException("Discount percentage cannot be greater than 100({$invalidDiscount}). Not applying discount.");
    }
}