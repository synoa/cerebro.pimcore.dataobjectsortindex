<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\Service;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class Connection
{
    private $driverConnection;
    private $schemaSynchronizer;

    public function __construct(DBALConnection $driverConnection)
    {
        $this->driverConnection = $driverConnection;
        $this->schemaSynchronizer = new SingleDatabaseSynchronizer($this->driverConnection);
    }

    public function send(int $folderId, array $config): string
    {
        $queryBuilder = $this->driverConnection->createQueryBuilder()
            ->select('*')
            ->from('synoa_sort_message')
            ->where('folder = :folder')
            ->setParameter('folder', $folderId);

        $res = $this->executeQuery($queryBuilder->getSQL(), $queryBuilder->getParameters());
        $res = $res->fetchAll();

        if (count($res) === 0) {
            $queryBuilder = $this->driverConnection->createQueryBuilder()
                ->insert('synoa_sort_message')
                ->values([
                    'folder' => '?',
                    'config' => '?'
                ]);

            $this->executeQuery($queryBuilder->getSQL(), [$folderId, $config], [null, Types::ARRAY]);

            return $this->driverConnection->lastInsertId();
        }

        return $res[0]['id'];
    }

    public function ack(string $id): bool
    {
        return $this->driverConnection->delete('synoa_sort_message', ['id' => $id]) > 0;
    }

    public function get(): ?array
    {
        $queryBuilder = $this->driverConnection->createQueryBuilder()
            ->select('*')
            ->from('synoa_sort_message');

        $stmt = $this->executeQuery(
            $queryBuilder->getSQL()
        );

        $data = $stmt->fetchAll();

        foreach ($data as &$entry) {
            $entry['config'] = $this->driverConnection->convertToPHPValue($entry['config'], Types::ARRAY);
        }

        return $data;
    }

    public function setup(): void
    {
        $configuration = $this->driverConnection->getConfiguration();
        // Since Doctrine 2.9 the getFilterSchemaAssetsExpression is deprecated
        $hasFilterCallback = method_exists($configuration, 'getSchemaAssetsFilter');

        if ($hasFilterCallback) {
            $assetFilter = $this->driverConnection->getConfiguration()->getSchemaAssetsFilter();
            $this->driverConnection->getConfiguration()->setSchemaAssetsFilter(null);
        } else {
            $assetFilter = $this->driverConnection->getConfiguration()->getFilterSchemaAssetsExpression();
            $this->driverConnection->getConfiguration()->setFilterSchemaAssetsExpression(null);
        }

        $this->schemaSynchronizer->updateSchema($this->getSchema(), true);

        if ($hasFilterCallback) {
            $this->driverConnection->getConfiguration()->setSchemaAssetsFilter($assetFilter);
        } else {
            $this->driverConnection->getConfiguration()->setFilterSchemaAssetsExpression($assetFilter);
        }
    }

    private function executeQuery(string $sql, array $parameters = [], array $types = [])
    {
        try {
            $stmt = $this->driverConnection->executeQuery($sql, $parameters, $types);
        } catch (TableNotFoundException $e) {
            if ($this->driverConnection->isTransactionActive()) {
                throw $e;
            }

            // create table
            $this->setup();

            $stmt = $this->driverConnection->executeQuery($sql, $parameters, $types);
        }

        return $stmt;
    }

    private function getSchema(): Schema
    {
        $schema = new Schema([], [], $this->driverConnection->getSchemaManager()->createSchemaConfig());
        $table = $schema->createTable('synoa_sort_message');
        $table->addColumn('id', Types::BIGINT)
            ->setAutoincrement(true)
            ->setNotnull(true);
        $table->addColumn('folder', Types::BIGINT)
            ->setNotnull(true);
        $table->addColumn('config', Types::ARRAY)
            ->setNotnull(true);

        $table->setPrimaryKey(['id']);

        return $schema;
    }
}
