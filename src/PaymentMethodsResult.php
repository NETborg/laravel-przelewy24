<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: netborg
 * Date: 10.12.17
 * Time: 13:43
 */

namespace NetborgTeam\P24;

class PaymentMethodsResult extends P24Response
{
    /**
     * @param $result
     * @return PaymentMethod[]
     */
    public function parseResult($result): array
    {
        $methods = [];

        if (is_array($result)) {
            foreach ($result as $method) {
                $methods[] = new PaymentMethod($method);
            }
        } elseif (is_object($result)) {
            $methods[] = new PaymentMethod($result);
        }

        return $methods;
    }
}
