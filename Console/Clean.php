<?php

namespace Hieu\OnTheFlyDiscount\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


class Clean extends Command
{
    /**
     * @var \Hieu\OnTheFlyDiscount\Cron\Clean
     */
    private $clean;

    public function __construct(\Hieu\OnTheFlyDiscount\Cron\Clean $clean, $name = null)
    {
        parent::__construct($name);
        $this->clean = $clean;
    }

    protected function configure()
    {
        $this->setName('hieu:clean_generated_coupon')
            ->setDescription('Clean Generated Coupon');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clean->execute();
    }
}