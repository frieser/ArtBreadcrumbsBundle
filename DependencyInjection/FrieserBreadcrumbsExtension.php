<?php
/**
 * Author: Paul Seleznev
 * Date: 1/07/2013
 */
namespace Frieser\BreadcrumbsBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Frieser\BreadcrumbsBundle\Factory\BuilderFactory;

class FrieserBreadcrumbsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('breadcrumbs.xml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('frieser_breadcrumbs.template', $config['template']);
        $container->setAlias('frieser_breadcrumbs.builder', $config['builder_service']);
        $container->setParameter('frieser_breadcrumbs.separator', $config['separator']);
        $container->setParameter('frieser_breadcrumbs.schema', $config['schema']);
        $container->setParameter('frieser_breadcrumbs.dev_mode', $config['dev_mode']);
    }

    public function getAlias()
    {
        return 'frieser_breadcrumbs';
    }
}