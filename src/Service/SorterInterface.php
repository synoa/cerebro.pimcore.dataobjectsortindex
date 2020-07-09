<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\Service;

use Pimcore\Model\DataObject\Folder;

interface SorterInterface
{
    public function sortFolder(Folder $folder, array $config);
}
