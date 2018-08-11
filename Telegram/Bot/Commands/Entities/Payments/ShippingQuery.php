<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Telegram\Bot\Entities\Payments;

use App\Telegram\Bot\Entities\Entity;
use App\Telegram\Bot\Entities\User;
use App\Telegram\Bot\Request;

/**
 * Class ShippingQuery
 *
 * This object contains information about an incoming shipping query.
 *
 * @link https://core.telegram.org/bots/api#shippingquery
 *
 * @method string          getId()              Unique query identifier
 * @method User            getFrom()            User who sent the query
 * @method string          getInvoicePayload()  Bot specified invoice payload
 * @method ShippingAddress getShippingAddress() User specified shipping address
 **/
class ShippingQuery extends Entity
{
    /**
     * {@inheritdoc}
     */
    public function subEntities()
    {
        return [
            'user'             => User::class,
            'shipping_address' => ShippingAddress::class,
        ];
    }

    /**
     * Answer this shipping query.
     *
     * @param bool  $ok
     * @param array $data
     *
     * @return \App\Telegram\Bot\Entities\ServerResponse
     */
    public function answer($ok, array $data = [])
    {
        return Request::answerShippingQuery(array_merge([
            'shipping_query_id' => $this->getId(),
            'ok'                => $ok,
        ], $data));
    }
}
