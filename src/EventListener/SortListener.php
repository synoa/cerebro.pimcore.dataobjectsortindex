<?php

namespace Synoa\Bundle\DataObjectSortIndexBundle\EventListener;

use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Synoa\Bundle\DataObjectSortIndexBundle\Service\Connection;

class SortListener implements EventSubscriberInterface
{
    protected $configMap;
    protected $connection;

    public function __construct(array $configMap, Connection $connection)
    {
        $this->configMap = $configMap;
        $this->connection = $connection;
    }

    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::POST_UPDATE => 'checkIfSortIsNeeded'
        ];
    }

    public function checkIfSortIsNeeded(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if (!$object instanceof Concrete) {
            return;
        }

        foreach ($this->configMap as $name => $config) {
            if ($config['object_class'] !== $object->getClassName()) {
                continue;
            }

            if ($object->getParent()->getFullPath() === $config['folder']) {
                $this->connection->send($object->getParentId(), $config);
            }

            if ($config['recursive'] && stripos($object->getParent()->getFullPath(), $config['folder']) !== false) {
                $this->connection->send($object->getParentId(), $config);
            }
        }
    }
}
