<?php

namespace Hieu\OnTheFlyDiscount\Model;

use Exception;
use Hieu\OnTheFlyDiscount\Model\Sales\Rule\Processor;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupsCollectionFactory;

class DiscountProcessor
{
    const COUPON_PREFIX = "MOB";

    /**
     * @var Processor
     */
    private $ruleProcessor;

    /**
     * @var QuoteResource
     */
    private $quoteResource;

    private $groupsCollectionFactory;

    /**
     * DiscountProcessor constructor.
     *
     * @param Processor $ruleProcessor
     * @param QuoteResource $quoteResource
     * @param GroupsCollectionFactory $groupsCollectionFactory
     */
    public function __construct(
        Processor $ruleProcessor,
        QuoteResource $quoteResource,
        GroupsCollectionFactory $groupsCollectionFactory
    ) {
        $this->ruleProcessor = $ruleProcessor;
        $this->quoteResource = $quoteResource;
        $this->groupsCollectionFactory = $groupsCollectionFactory;
    }

    /**
     * Applies a discount
     *
     * @param $quote Quote
     * @param $discountAmount int
     */
    public function execute(Quote $quote, $discountAmount)
    {
        $couponCode = $this->generateCouponName($quote->getId());

        $quoteCoupon = $quote->getCouponCode();

        $groups = $quote->getCustomer()->getGroupId();
        if ($groups == null) {
            $groups = implode(',', $this->getAvailableGroups());
        }

        if (empty ($quoteCoupon) || strpos($quoteCoupon, self::COUPON_PREFIX) !== 0) {
            $this->ruleProcessor->process(
                $couponCode,
                $discountAmount,
                $quote->getStore()->getWebsiteId(),
                $groups
            );

            $quote->setCouponCode($couponCode)
                ->collectTotals();

            try {
                $this->quoteResource->save($quote);
            } catch (Exception $e) {
                // We can log it if we need
            }
        }

        // if $quote has no discount code yet, make a discount code prefix MOB (i.e MOB2312 where 2312 is quote id) with discountAmount and apply to that quote
        // if $quote already has applied a discount and it has prefix as MOB, then change the discount amount to update with the extra $discountAmount
    }

    /**
     * Generates coupon code
     *
     * @param $quoteId
     *
     * @return string
     */
    protected function generateCouponName($quoteId)
    {
        return self::COUPON_PREFIX . $quoteId;
    }

    /**
     * Returns all available group IDs
     *
     * @return array
     */
    protected function getAvailableGroups()
    {
        $collection = $this->groupsCollectionFactory->create();

        return $collection->getAllIds();
    }
}