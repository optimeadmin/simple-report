<?php

declare(strict_types=1);

namespace Optime\SimpleReport\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('optime_simple_report');
        $rootNode    = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}