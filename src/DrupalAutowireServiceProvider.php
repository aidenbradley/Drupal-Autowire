<?php

namespace Drupal\drupal_autowire;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DrupalAutowireServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $container): void
    {
        foreach ($this->autowirableDirectories() as $directory) {
            foreach ($this->modulesInDirectory($directory) as $module) {

                $moduleName = $module->getBasename();

                try {
                    $modulePath = drupal_get_path('module', $moduleName) . '/' . $moduleName . '.services.yml';
                } catch (\Throwable $throwable) {
                    continue;
                }

                try {
                    $serviceFile = Yaml::decode(file_get_contents($modulePath));
                } catch (\Throwable $throwable) {
                    continue;
                }

                if (isset($serviceFile['services']) === false) {
                    continue;
                }

                foreach ($serviceFile['services'] as $serviceName => $serviceInfo) {
                    if (isset($serviceInfo['class']) === false) {
                        continue;
                    }

                    if (isset($serviceInfo['dependencies'])) {
                        continue;
                    }

                    if (isset($serviceInfo['autowire']) && $serviceInfo['autowire'] === false) {
                        continue;
                    }

                    if ($container->hasDefinition($serviceName) === false) {
                        continue;
                    }

                    $definition = new Definition($serviceName);

                    $definition->setAutowired(true);

                    $container->setDefinition($serviceName, $definition);
                }
            }
        }
    }

    private function modulesInDirectory(string $directory): Finder
    {
        return Finder::create()->directories()->depth(0)->in($directory);
    }

    /** Return an array so multiple directories can be targeted */
    private function autowirableDirectories(): array
    {
        $autowirableDirectories = Settings::get('autowirable_directories');

        if ($autowirableDirectories !== null) {
            return (array)$autowirableDirectories;
        }

        return [
            DRUPAL_ROOT . '/modules/custom'
        ];
    }
}
