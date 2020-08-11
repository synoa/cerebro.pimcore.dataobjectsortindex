<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\Maintenance;

use Pimcore\Maintenance\TaskInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Synoa\Bundle\DataObjectSortIndexBundle\Service\Connection;

class SortMaintenance implements TaskInterface
{
    protected $connection;
    protected $locator;

    public function __construct(Connection $connection, ServiceLocator $locator)
    {
        $this->connection = $connection;
        $this->locator = $locator;
    }


    public function execute()
    {
        $foldersToSort = $this->connection->get();

        foreach ($foldersToSort as $folder) {
            $folderId = $folder['folder'];
            $config = $folder['config'];
            $folderObject = Concrete::getById($folderId);

            if ($folderObject) {
                $this->locator->get($config['type'])->sortFolder($folderObject, $config);
            }

            $this->connection->ack($folder['id']);
        }
    }
}
