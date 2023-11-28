<?php
declare(strict_types=1);

/**
 * Copyright 2010 - 2023, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2023, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\PHPPM\Bridges;

use App\Application;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Http\Runner;
use Cake\Http\Server;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use PHPPM\Bridges\BridgeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cakephp implements BridgeInterface
{
    protected Server $server;

    protected Application $application;

    /**
     * Bootstrap an application
     *
     * @param string|null $appBootstrap The environment your application will use to bootstrap (if any)
     * @param string $appenv
     * @param bool $debug If debug is enabled
     */
    public function bootstrap($appBootstrap, $appenv, $debug)
    {
        $root = dirname(__DIR__, 5);
        require_once $root . '/vendor/autoload.php';
        $this->application = new Application($root . '/config');
        $this->application->bootstrap();
        if (!Configure::read('App.base')) {
            Configure::write('App.base', '');
        }
        if ($this->application instanceof \Cake\Core\PluginApplicationInterface) {
            $this->application->pluginBootstrap();
        }
        $this->server = new Server($this->application);
        $this->runner = new Runner();
        $this->middleware = $this->application->middleware(new MiddlewareQueue());
        if ($this->application instanceof PluginApplicationInterface) {
            $this->middleware = $this->application->pluginMiddleware($this->middleware);
        }

        if (!($this->middleware instanceof MiddlewareQueue)) {
            throw new \RuntimeException('The application `middleware` method did not return a middleware queue.');
        }
        $this->server->dispatchEvent('Server.buildMiddleware', ['middleware' => $this->middleware]);
    }

    /**
     * Handle the request and return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->resetApiServiceRegistry();
        $request = ServerRequestFactory::fromGlobals();
        $response = $this->runner->run($this->middleware, $request, $this->application);
        if ($request instanceof ServerRequest) {
            $request->getSession()->close();
        }

        if (!($response instanceof ResponseInterface)) {
            throw new \RuntimeException(sprintf(
                'Application did not create a response. Got "%s" instead.',
                is_object($response) ? get_class($response) : $response
            ));
        }

        return $response;
    }

    protected function resetApiServiceRegistry()
    {
        $serviceRegistryClass = '\CakeDC\Api\Service\ServiceRegistry';
        if (class_exists($serviceRegistryClass)) {
            $locator = call_user_func($serviceRegistryClass . '::getServiceLocator');
            $locator->clear();
        }
    }
}
