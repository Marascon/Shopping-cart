<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Cart\Discount;

use Marascon\ShoppingCart\Cart\Exceptions\InvalidDiscountException;

interface DiscountInterface
{
    /**
     * Apply the discount to the given total price.
     *
     * This method applies the discount to the total price of the cart and returns the discounted price.
     *
     * @param float $totalPrice The total price of the cart (before applying the discount).
     * @return float The price after the discount has been applied.
     * @throws InvalidDiscountException If the discount cannot be applied due to an invalid condition (negative or excessive discount).
     */
    public function apply(float $totalPrice): float;
}