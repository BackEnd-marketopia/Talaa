<?php

namespace App\Helpers;

class PriceHelpers
{
    public static function calcolatePriceForCart($carts)
    {
        $totalPrice = 0;
        $totalPriceAfterDiscount = 0;

        foreach ($carts as $cart) {
            $attributesPrice = 0;
            if (!empty($cart->ProductAttributeValues)) {
                foreach ($cart->ProductAttributeValues as $attr) {
                    $attributesPrice += $attr->price;
                }
            }

            $totalPrice += ($cart->product->price * $cart->quantity) + ($attributesPrice * $cart->quantity);

            $priceAfterDiscount = $cart->product->discount_price != 0 ? $cart->product->discount_price : $cart->product->price;
            $totalPriceAfterDiscount += ($priceAfterDiscount * $cart->quantity) + ($attributesPrice * $cart->quantity);
        }
        $config = \App\Models\Config::first();
        if ($config && $config->discount_enabled && $totalPrice >= $config->min_order_total_for_discount) {
            if ($config->discount_type === 'percentage') {
                $totalPriceAfterDiscount = $totalPriceAfterDiscount - ($totalPriceAfterDiscount * ($config->discount_value / 100));
            } elseif ($config->discount_type === 'fixed') {
                $totalPriceAfterDiscount = $totalPriceAfterDiscount - $config->discount_value;
                if ($totalPriceAfterDiscount < 0) {
                    $totalPriceAfterDiscount = 0;
                }
            }
        }
        $data = [];
        $data['total_price'] = $totalPrice;
        $data['total_price_after_discount'] = $totalPriceAfterDiscount;
        $data['discount'] = $totalPrice - $totalPriceAfterDiscount;
        return $data;
    }
}
