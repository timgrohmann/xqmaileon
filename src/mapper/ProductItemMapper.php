<?php

namespace PrestaShop\Module\XQMaileon\Mapper;


class ProductItemMapper
{
    public static function map(array $in): array
    {
        $context = \Context::getContext();

        $product = new \Product((int) $in['product_id'], false, $context->language->id);
        $img = $product->getCover($product->id);
        $img_url = $context->link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int)$img['id_image']);
        return [
            'sku' => $product->reference,
            'product_id' => $product->id,
            'title' => $product->name,
            'description' => $product->description,
            'short_description' => $product->description_short,
            'release_date' => $product->date_add,
            'url' => $context->link->getProductLink($product),
            'image_url' => $img_url,
            'quantity' => intval($in['cart_quantity']),
            'single_price' => doubleval($in['unit_price_tax_incl']),
            'total' => doubleval($in['total_price_tax_incl'])
        ];
    }

    public static function mapArray(array $in): array
    {
        return array_map([__CLASS__, 'map'], $in);
    }
}
