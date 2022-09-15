<?php

declare(strict_types=1);

namespace Optime\SimpleReport\Bundle\DependencyInjection;

use Optime\SimpleReport\Bundle\Service\ReportInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class OptimeSimpleReportExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yaml');
        $container->registerForAutoconfiguration(ReportInterface::class)->addTag('optime_simple_report.report');

    }
}