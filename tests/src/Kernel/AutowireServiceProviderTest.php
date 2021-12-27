<?php

namespace Drupal\Tests\drupal_autowire\Kernel;

use Drupal\drupal_autowire_services\AutowirableClassString;
use Drupal\KernelTests\KernelTestBase;

class AutowireServiceProviderTest extends KernelTestBase
{
    /** @var string[] */
    protected static $modules = [
        'system',
        'drupal_autowire_services',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->setSetting(
            'autowirable_directories',
            DRUPAL_ROOT . '/modules/custom/drupal_autowire/tests/modules'
        );
    }

    /** @test */
    public function autowires_services(): void
    {
        $serviceDefinition = $this->container->getDefinition('autowirable_service');

        $this->assertFalse($serviceDefinition->isAutowired());

        $this->enableModules([
            'drupal_autowire',
        ]);

        $serviceDefinition = $this->container->getDefinition('autowirable_service');

        $this->assertTrue($serviceDefinition->isAutowired());
    }

    /** @test */
    public function autowires_class_strings(): void
    {
        $this->markTestSkipped('Need to work on this one still');

        $serviceDefinition = $this->container->getDefinition(AutowirableClassString::class);

        $this->assertFalse($serviceDefinition->isAutowired());

        $this->enableModules([
            'drupal_autowire',
        ]);

        $serviceDefinition = $this->container->getDefinition(AutowirableClassString::class);

        $this->assertTrue($serviceDefinition->isAutowired());
    }

    /** @test */
    public function does_not_autowire_services_with_explicit_dependencies(): void
    {
        $serviceDefinition = $this->container->getDefinition('autowireable_with_explicit_dependencies');

        $this->assertFalse($serviceDefinition->isAutowired());

        $this->enableModules([
            'drupal_autowire',
        ]);

        $serviceDefinition = $this->container->getDefinition('autowireable_with_explicit_dependencies');

        $this->assertFalse($serviceDefinition->isAutowired());
    }

    /** @test */
    public function does_not_autowire_services_when_autowire_set_to_false(): void
    {
        $serviceDefinition = $this->container->getDefinition('autowireable_with_explicit_autowire_false');

        $this->assertFalse($serviceDefinition->isAutowired());

        $this->enableModules([
            'drupal_autowire',
        ]);

        $serviceDefinition = $this->container->getDefinition('autowireable_with_explicit_autowire_false');

        $this->assertFalse($serviceDefinition->isAutowired());
    }
}
