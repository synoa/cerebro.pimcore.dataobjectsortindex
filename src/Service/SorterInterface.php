<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\Service;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;

interface SorterInterface
{
    public function sortFolder(DataObject $folder, array $config);
}
