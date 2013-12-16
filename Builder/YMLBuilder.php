<?php
/**
 * Author: Paul Seleznev
 * Date: 4/08/2013
 */
namespace Frieser\BreadcrumbsBundle\Builder;

use Frieser\BreadcrumbsBundle\Exception\InvalidSchemaException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;

class YMLBuilder implements BuilderInterface
{
    /**
     * @var Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var
     */
    private $routeCollection;

    /**
     * @var string
     */
    private $schema;

    public function __construct(RouterInterface $router, $schema_enterprise_admin, $schema_distributor, $schema_super_admin)
    {
        $this->router = $router;
        $this->schema_distributor = $schema_distributor;
        $this->schema_enterprise_admin = $schema_enterprise_admin;
        $this->schema_super_admin = $schema_super_admin;
    }

    public function build()
    {
        $path = realpath($this->schema_distributor);
        if (!is_string($path) || 'yml' !== pathinfo($path, PATHINFO_EXTENSION)) {
            throw new InvalidSchemaException("The schema file provided isn't correct");
        }
        $structure = Yaml::parse($path);

        if (!$this->request) {
            throw new \Exception(sprintf('This builder(%s) should be used only in request scope', get_class($this)));
        }

        //echo serialize($structure);
        
        $this->routeCollection = $this->router->getRouteCollection();

        $breadcrumbs['distributor'] = $this->createBreadcrumbs($this->request, $structure);

        $path = realpath($this->schema_enterprise_admin);
        if (!is_string($path) || 'yml' !== pathinfo($path, PATHINFO_EXTENSION)) {
            throw new InvalidSchemaException("The schema file provided isn't correct");
        }
        $structure = Yaml::parse($path);

        if (!$this->request) {
            throw new \Exception(sprintf('This builder(%s) should be used only in request scope', get_class($this)));
        }

        //echo serialize($structure);
        
        $this->routeCollection = $this->router->getRouteCollection();

        $breadcrumbs['enterprise_admin'] = $this->createBreadcrumbs($this->request, $structure);

        $path = realpath($this->schema_super_admin);
        if (!is_string($path) || 'yml' !== pathinfo($path, PATHINFO_EXTENSION)) {
            throw new InvalidSchemaException("The schema file provided isn't correct");
        }
        $structure = Yaml::parse($path);

        if (!$this->request) {
            throw new \Exception(sprintf('This builder(%s) should be used only in request scope', get_class($this)));
        }

        //echo serialize($structure);
        
        $this->routeCollection = $this->router->getRouteCollection();

        $breadcrumbs['super_admin'] = $this->createBreadcrumbs($this->request, $structure);



        return $breadcrumbs;
    }

    public function createBreadcrumbs($request, $structure)
    {
        $breadcrumbs = array();
        if (!isset($structure['route'])) {
            return;
        }
        if ($structure['route'] === $request->get('_route')) {
            $breadcrumbs[] = array(
                'label' => $structure['label']
            );

            return $breadcrumbs;
        }
        if (!isset($structure['children'])) {
            return null;
        }

        foreach ($structure['children'] as $child) {
            $result = $this->createBreadcrumbs($request, $child);
            if ($result) {
                $route = $this->routeCollection->get($structure['route']);
                $paramVariables = $route->compile()->getVariables();
                if (!empty($paramVariables)) {
                    $parameters = array();
                    foreach ($paramVariables as $variable) {
                        $value = $request->attributes->get($variable);
                        if (isset($value)) {
                            $parameters[$variable] = $value;
                        }
                    }
                    $breadcrumbs[] = array(
                        'label' => $structure['label'],
                        'url' => $this->router->generate($structure['route'], $parameters)
                    );
                } else {
                    $breadcrumbs[] = array(
                        'label' => $structure['label'],
                        'url' => $this->router->generate($structure['route'])
                    );
                }

                return array_merge($breadcrumbs, $result);
            }
        }

        return $breadcrumbs;
    }



    public function setRequest(Request $request = null)
    {
        $this->request = $request;

        return $this;
    }
}
