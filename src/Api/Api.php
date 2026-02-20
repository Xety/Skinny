<?php
namespace Skinny\Api;

use Skinny\Core\Configure;

/**
 * The base class used to call a service.
 */
class Api
{
    /**
     * Create a new service.
     *
     * @param string $service The service name.
     * @param mixed $arguments The argument to pass to the service.
     *
     * @return object The newest created service.
     *
     * @throws \Exception
     */
    public function __call($service, $arguments)
    {
        $serviceClass = Configure::read('App.namespace') . '\\Api\\Services\\' . ucfirst($service);

        if (class_exists($serviceClass)) {
            return new $serviceClass(...$arguments);
        }

        throw new \Exception('Service ' . $service . ' does not defined.');
    }
}