<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Product;

class Product
{
    public function __construct(
        private string $articleNumber,
        private string $description,
        private float $price,
        private ProductCategory $category,
        private int $quantity = 1
    ) {
        if ($price <= 0) {
            throw new \InvalidArgumentException("Price must be greater than zero.");
        }
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Quantity must be a positive integer.");
        }
    }

    /**
     * @return string
     */
    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return ProductCategory
     */
    public function getCategory(): ProductCategory
    {
        return $this->category;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function withQuantity(int $quantity): self
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Quantity must be a positive integer.");
        }

        $clone = clone $this;
        $clone->quantity = $quantity;
        return $clone;
    }

    /**
     * Get a string representation of the product for easier debugging
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            'Product { Article Number: %s, Description: %s, Price: %.2f, Quantity: %d, Category: %s }',
            $this->articleNumber,
            $this->description,
            $this->price,
            $this->quantity,
            $this->category->value
        );
    }

    /**
     * Get a human-readable category name
     *
     * @return string
     */
    public function getCategoryName(): string
    {
        return ucfirst($this->category->value); // Capitalize category name
    }
}
