<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\Service;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

class AlphabeticSorter implements SorterInterface
{
    protected $connection;

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }


    public function sortFolder(AbstractObject $folder, array $config)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->select('o_id')
            ->from('objects')
            ->where('o_parentId = :parentId')
            ->andWhere('o_className = :className')
            ->orderBy('LOWER(o_key)', 'ASC')
            ->setParameter('parentId', $folder->getId())
            ->setParameter('className', $config['object_class']);

        $result = $this->connection->fetchAll($queryBuilder->getSQL(), $queryBuilder->getParameters());

        foreach ($result as $index => $entry) {
            $concrete = Concrete::getById($entry['o_id']);

            if (!$concrete) {
                continue;
            }

            $concrete->set($config['data_object_field'], ($index + 1) * 10);
            $concrete->save();
        }
    }
}
