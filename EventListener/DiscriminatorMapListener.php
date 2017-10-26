<?php

namespace KunicMarko\SonataConfigurationPanelBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use KunicMarko\SonataConfigurationPanelBundle\Entity\AbstractConfiguration;
use Doctrine\ORM\Mapping\DiscriminatorMap;

class DiscriminatorMapListener
{
    /**
     * @var array
     */
    protected $additionalTypes;

    public function __construct(array $additionalTypes)
    {
        $this->additionalTypes = $additionalTypes;
    }

    /**
     * Updates discrimantor map with new types
     *
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $metadata = $event->getClassMetadata();
        $class = $metadata->getReflectionClass();

        if ($class->getName() !== AbstractConfiguration::class) {
            return;
        }

        $reader = new AnnotationReader;

        if (null !== $discriminatorMapAnnotation =
                $reader->getClassAnnotation($class, DiscriminatorMap::class)) {
            $discriminatorMap = $discriminatorMapAnnotation->value;
        }

        $newDiscriminatorMap = array_merge($discriminatorMap, $this->additionalTypes);

        $metadata->setDiscriminatorMap($newDiscriminatorMap);
    }
}
