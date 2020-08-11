<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\Service;

use Pimcore\Model\DataObject\AbstractObject;

interface SorterInterface
{
    public function sortFolder(AbstractObject $folder, array $config);
}
