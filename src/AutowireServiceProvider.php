<?php

namespace Drupal\autowire;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AutowireServiceProvider implements ServiceProviderInterface
{

  private $serviceFinder = null;

  public function register(ContainerBuilder $container): void
  {
    foreach ($this->modules() as $module) {

      $services = $this->servicesFor($module);

      if($services === null) {
        continue;
      }

      foreach ($services as $service) {
        dump($service);
        $namespace = $this->getNamespace($module, $service);
        dump($namespace);
        if ($container->hasDefinition($namespace)) {
          continue;
        }

        $definition = new Definition($namespace);

        $definition->setAutowired(true);

        $container->setDefinition($namespace, $definition);
      }

    }

  }

  private function modules(): Finder
  {
    return (new Finder())->directories()
      ->depth(0)
      ->in(DRUPAL_ROOT . '/modules/custom');
  }

  private function servicesFor(SplFileInfo $module): ?Finder
  {
    try {
      return $this->serviceFinder()->files()
        ->in($module->getRealPath() . '/src/Services')
        ->name('*.php');
    } catch (DirectoryNotFoundException $exception) {
      return null;
    }

  }

  private function getNamespace(SplFileInfo $module, SplFileInfo $service): string
  {
    $partialNamespace = strstr($service->getRealPath(), 'src/');
    $removeSrcDir = str_replace('src/', '', $partialNamespace);

    $removeFileExtension = substr($removeSrcDir, 0, -4);
    $namespace = str_replace('/', '\\', $removeFileExtension);

    return 'Drupal\\' . $module->getFilename() . '\\' . $namespace;
  }

  public function serviceFinder(): Finder
  {
    if(isset($this->servicesFinder)) {
      return $this->servicesFinder;
    }

    $serviceFinder = new Finder();
    $serviceFinder->name('*.php')->files();

    $this->serviceFinder = $serviceFinder;

    return $this->serviceFinder;
  }

}
