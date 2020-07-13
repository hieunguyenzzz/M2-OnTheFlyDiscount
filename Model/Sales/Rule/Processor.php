<?php

namespace Hieu\OnTheFlyDiscount\Model\Sales\Rule;

use Magento\Framework\DataObject;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Model\ResourceModel\Rule as RuleResource;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;

class Processor
{
    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var RuleResource
     */
    private $ruleResource;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * Processor constructor.
     *
     * @param RuleFactory $ruleFactory
     * @param RuleResource $ruleResource
     * @param RuleCollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        RuleFactory $ruleFactory,
        RuleResource $ruleResource,
        RuleCollectionFactory $ruleCollectionFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleResource = $ruleResource;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * Processes rule request
     *
     * @param $coupon
     * @param $discount
     * @param $websiteId
     * @param $groups
     */
    public function process($coupon, $discount, $websiteId, $groups)
    {
        $rule = $this->getRuleByName($coupon);

        if ($rule instanceof Rule && $rule->getSimpleAction() == RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT_FOR_CART) {
            $rule->setDiscountAmount($discount);
            $rule->setCouponCode($coupon);
            $this->ruleResource->save($rule);
        } else {
            $rule = $this->createRule($coupon, $discount, $websiteId, $groups);
            $this->ruleResource->save($rule);
        }
    }

    /**
     * Creates a cart rule
     *
     * @param $coupon
     * @param $discount
     * @param $websiteId
     * @param $groups
     *
     * @return Rule
     */
    protected function createRule($coupon, $discount, $websiteId, $groups)
    {
        $rule = $this->ruleFactory->create();

        $rule->setName($coupon)
            ->setWebsiteIds($websiteId)
            ->setCustomerGroupIds($groups)
            ->setFromDate(null)
            ->setToDate(null)
            ->setUsesPerCustomer(1)
            ->setUsesPerCoupon(1)
            ->setIsActive(1)
            ->setSimpleAction(RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT_FOR_CART)
            ->setDiscountAmount($discount)
            ->setCouponType(Rule::COUPON_TYPE_SPECIFIC)
            ->setCouponCode($coupon);
        return $rule;
    }

    /**
     * Searches for a rule with specified name
     *
     * @param $name
     *
     * @return DataObject|null
     */
    protected function getRuleByName($name) {
        $collection = $this->ruleCollectionFactory->create();

        $collection->addFieldToFilter('name', $name);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return null;
    }
}