<?php
namespace Radar\Adr;

use Aura\Router\Map as AuraMap;

class Map extends AuraMap
{
    public function route($name, $path, $domain = null)
    {
        $route = clone $this->protoRoute;

        $route->name($name);
        $route->path($path);

        if ($domain) {
            $route->domain($domain);
            $this->setRouteInput($route);
        }

        $this->setRouteResponder($route);

        $this->addRoute($route);
        return $route;
    }

    protected function setRouteInput($route)
    {
        $input = $route->name . '\\Input';
        if (class_exists($input)) {
            $route->input($input);
        }
    }

    protected function setRouteResponder($route)
    {
        $responder = $route->name . '\\Responder';
        if (class_exists($responder)) {
            $route->responder($responder);
        }
    }
}
