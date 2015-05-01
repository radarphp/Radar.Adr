<?php
namespace Radar\Adr\Exception;

use Radar\Adr\Exception;

class RoutingFailed extends Exception
{
    protected $failedRoute;

    public function setFailedRoute($failedRoute)
    {
        $this->failedRoute = $failedRoute;
    }

    public function getFailedRoute()
    {
        return $this->failedRoute;
    }
}
