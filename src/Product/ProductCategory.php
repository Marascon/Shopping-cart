<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Product;

enum ProductCategory: string
{
    case Electronics = 'electronics';
    case Furniture = 'furniture';
    case Groceries = 'groceries';
    case HomeAppliances = 'homeappliances';
}
