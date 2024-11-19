<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Exceptions;

class ProductNotFoundException extends \Exception
{
    /**
     * The article number that caused the exception.
     *
     * @var string
     */
    private string $articleNumber;

    /**
     * ProductNotFoundException constructor.
     *
     * @param string $articleNumber The article number of the product that was not found.
     */
    public function __construct(string $articleNumber)
    {
        // Set the article number for later retrieval
        $this->articleNumber = $articleNumber;


        // Pass a formatted message to the parent constructor
        parent::__construct(sprintf('Product with article number %s not found.', $this->getArticleNumber()));
    }

    /**
     * Get the article number that caused the exception.
     *
     * @return string
     */
    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }
}
