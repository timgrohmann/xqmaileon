<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013-2021 XQueue GmbH
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
 *  @copyright 2013-2021 XQueue
 *  @license   MIT
 */

namespace PrestaShop\Module\XQMaileon\Model;

use DateTime;

class AbandonedCart
{

    /**
     * @var int
     */
    public $id_cart;

    /**
     * @var int
     */
    public $id_customer;

    /**
     * @var DateTime
     */
    public $date_added;

    public function __construct($sqlResultArray)
    {
        $this->id_cart = (int) $sqlResultArray['id_cart'];
        $this->id_customer = (int) $sqlResultArray['id_customer'];
        $this->date_added = DateTime::createFromFormat('Y-d-m H:i:s', $sqlResultArray['date_add']);
    }
}
