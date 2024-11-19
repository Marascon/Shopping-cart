<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Config;

use InvalidArgumentException;

class Config
{
    private string $currency;
    private float $vatRate;

    /**
     * Config constructor.
     *
     * @param string $currency Currency code ("USD", "EUR").
     * @param float $vatRate VAT rate (21 for 21%).
     * @throws InvalidArgumentException If the VAT rate is not within a valid range.
     */
    public function __construct(string $currency, float $vatRate)
    {
        if ($vatRate < 0 || $vatRate > 100) {
            throw new InvalidArgumentException("VAT rate must be between 0 and 100");
        }

        $this->currency = $currency;
        $this->vatRate = $vatRate;
    }

    /**
     * Get the currency code.
     *
     * @return string Currency code ("USD", "EUR").
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get the VAT rate.
     *
     * @return float VAT rate as a fraction (21 for 21%).
     */
    public function getVatRate(): float
    {
        return $this->vatRate;
    }

    /**
     * String representation of the Config object.
     *
     * @return string A human-readable string representing the configuration.
     */
    public function __toString(): string
    {
        return sprintf(
            'Config { Currency: %s, VAT Rate: %.2f%% }',
            $this->currency,
            $this->vatRate
        );
    }
}
