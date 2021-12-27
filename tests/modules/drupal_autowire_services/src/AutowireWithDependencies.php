<?php

namespace Drupal\drupal_autowire_services;

use Drupal\Core\Entity\EntityTypeManager;

/**
 * Given the constructor parameters, autowiring is possible. But because the dependencies are explicitly defined
 * in the services YAML file then autowiring should be skipped.
 */
class AutowireWithDependencies
{
    /** @var EntityTypeManager */
    private $entityTypeManager;

    public function __construct(EntityTypeManager $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }
}
