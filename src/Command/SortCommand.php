<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Synoa\Bundle\DataObjectSortIndexBundle\Maintenance\SortMaintenance;

class SortCommand extends AbstractCommand
{
    private $maintenance;

    public function __construct(SortMaintenance $maintenance)
    {
        $this->maintenance = $maintenance;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('synoa:data_object_sort_index:sort');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->maintenance->execute();

        return 0;
    }
}
