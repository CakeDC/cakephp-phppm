<?php
/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\PHPPM\Bridges;

use App\Application;
use Cake\Core\PluginApplicationInterface;
use Cake\Error\Debugger;
use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Http\Response;
use Cake\Http\Runner;
use Cake\Http\Server;
use Cake\Http\ServerRequestFactory;
use PHPPM\Bridges\BridgeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cakephp implements BridgeInterface
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var BaseApplication $application
     */
    protected $application;

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
        require_once $root . '/config/requirements.php';
        require_once $root . '/vendor/autoload.php';
        $this->application = new Application($root . '/config');
        $this->application->bootstrap();
        if ($this->application instanceof \Cake\Core\PluginApplicationInterface) {
            $this->application->pluginBootstrap();
        }
        $this->server = new Server($this->application);
    }

    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (class_exists('\CakeDC\Api\Service\ServiceRegistry')) {
            \CakeDC\Api\Service\ServiceRegistry::getServiceLocator()->clear();
        }
        $request = ServerRequestFactory::fromGlobals();
        $middleware = $this->application->middleware(new MiddlewareQueue());
        if ($this->application instanceof PluginApplicationInterface) {
            $middleware = $this->application->pluginMiddleware($middleware);
        }

        if (!($middleware instanceof MiddlewareQueue)) {
            throw new \RuntimeException('The application `middleware` method did not return a middleware queue.');
        }
        $this->server->dispatchEvent('Server.buildMiddleware', ['middleware' => $middleware]);
        $middleware->add($this->application);
        $runner = new Runner();
        $response = $runner->run($middleware, $request);

        if (!($response instanceof ResponseInterface)) {
            throw new \RuntimeException(sprintf(
                'Application did not create a response. Got "%s" instead.',
                is_object($response) ? get_class($response) : $response
            ));
        }

        return $response;
    }
}
