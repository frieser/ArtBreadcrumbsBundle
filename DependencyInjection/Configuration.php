<?php
/**
 * Author: Paul Seleznev
 * Date: 1/07/2013
 */
namespace Frieser\BreadcrumbsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('frieser_breadcrumbs');

        $rootNode
            ->children()
                ->scalarNode('template')
                    ->cannotBeEmpty()
                    ->defaultValue('FrieserBreadcrumbsBundle::frieser_breadcrumbs.html.twig')
                ->end()
                ->scalarNode('schema_distributor')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.root_dir%/config/breadcrumbs_distributor.yml')
                ->end()
                ->scalarNode('schema_enterprise_admin')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.root_dir%/config/breadcrumbs_enterprise_admin.yml')
                ->end()
                ->scalarNode('schema_super_admin')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.root_dir%/config/breadcrumbs_super_admin.yml')
                ->end()
                ->scalarNode('builder_service')
                    ->defaultValue('frieser_breadcrumbs.yml_builder')
                ->end()
                ->booleanNode('dev_mode')
                    ->defaultFalse()
                ->end()
                ->scalarNode('separator')
                    ->defaultValue('/')
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}