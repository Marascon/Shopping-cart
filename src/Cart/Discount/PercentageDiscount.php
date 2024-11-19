<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Cart\Discount;

use Marascon\ShoppingCart\Exceptions\InvalidDiscountException;

class PercentageDiscount implements DiscountInterface
{
    private float $percentage;

    /**
     * PercentageDiscount constructor.
     *
     * @param float $percentage The percentage value of the discount (21 for 21%).
     * @throws InvalidDiscountException if the provided percentage is negative or exceeds 100.
     */
    public function __construct(float $percentage)
    {
        // Check for invalid discount (negative or greater than 100%)
        if ($percentage < 0) {
            $this->percentage = 0;
            throw new InvalidDiscountException("Negative discount detected({$percentage}). Not applying discount.");
        } elseif ($percentage > 100) {
            $this->percentage = 100; // Cap the discount at 100%
            throw new InvalidDiscountException("Discount percentage cannot be greater than 100({$percentage}). Not applying discount.");
        }

        $this->percentage = $percentage;
    }

    /**
     * Get the discount percentage.
     *
     * @return float The discount percentage (21for 21%).
     */
    public function getDiscountPercentage(): float
    {
        return $this->percentage;
    }

    /**
     * Apply the percentage discount to the total price.
     *
     * @param float $totalPrice The original total price before discount.
     * @return float The total price after applying the discount, with a minimum of 0.
     */
    public function apply(float $totalPrice): float
    {
        // Calculate the discount amount
        $discountAmount = $totalPrice * ($this->percentage / 100);

        // Calculate the new total price after discount
        $newTotalPrice = $totalPrice - $discountAmount;

        // Return the new total price, ensuring it does not go below 0
        return max(0.0, $newTotalPrice);
    }
}
