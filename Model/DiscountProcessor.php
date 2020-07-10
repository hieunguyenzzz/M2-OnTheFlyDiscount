<?php

namespace Hieu\OnTheFlyDiscount\Model;
class DiscountProcessor
{
    /**
     * @param $quote \Magento\Quote\Model\Quote
     * @param $discountAmount int
     */
    public function execute($quote, $discountAmount)
    {
        // if $quote has no discount code yet, make a discount code prefix MOB (i.e MOB2312 where 2312 is quote id) with discountAmount and apply to that quote
        // if $quote already has applied a discount and it has prefix as MOB, then change the discount amount to update with the extra $discountAmount
    }
}