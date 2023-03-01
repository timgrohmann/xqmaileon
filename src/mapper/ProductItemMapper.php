<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013-2023 XQueue GmbH
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 *  @author    XQueue GmbH
 *  @copyright 2013-2023 XQueue
 *  @license   MIT
 */

namespace PrestaShop\Module\XQMaileon\Mapper;

class ProductItemMapper
{
    public static function map(array $in): array
    {
        $context = \Context::getContext();

        $product = new \Product((int) $in['product_id'], false, $context->language->id);
        $img = $product->getCover($product->id);
        $img_url = $context->link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int) $img['id_image']);

        return [
            'sku' => $product->reference,
            'product_id' => $product->id,
            'title' => $product->name,
            'description' => $product->description,
            'short_description' => $product->description_short,
            'release_date' => $product->date_add,
            'url' => $context->link->getProductLink($product),
            'image_url' => $img_url,
            'quantity' => (int) $in['cart_quantity'],
            'single_price' => doubleval($in['unit_price_tax_incl']),
            'total' => doubleval($in['total_price_tax_incl']),
        ];
    }

    public static function mapArray(array $in): array
    {
        return array_map([__CLASS__, 'map'], $in);
    }
}
