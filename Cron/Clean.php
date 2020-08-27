<?php

namespace Hieu\OnTheFlyDiscount\Cron;

use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollection;

class Clean
{
    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    public function __construct(RuleCollectionFactory $ruleCollectionFactory)
    {

        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function execute()
    {
        /**
         * @var $collection RuleCollection
         */
        $collection = $this->ruleCollectionFactory->create();

        $collection->addFieldToFilter('name', ['like' => 'MOB%']);
        foreach ($collection as $item) {
            $item->delete();
        }
    }
}