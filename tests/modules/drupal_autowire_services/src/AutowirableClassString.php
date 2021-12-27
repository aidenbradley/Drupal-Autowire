<?php

namespace Drupal\drupal_autowire_services;

use Drupal\Core\Entity\EntityTypeManager;

class AutowirableClassString
{
    /** @var EntityTypeManager */
    private $entityTypeManager;

    public function __construct(EntityTypeManager $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }
}
