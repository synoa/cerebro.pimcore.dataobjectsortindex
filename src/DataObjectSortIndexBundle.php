<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class DataObjectSortIndexBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * @inheritDoc
     */
    protected function getComposerPackageName(): string
    {
        return 'synoa/apidataobjectsort';
    }
}
