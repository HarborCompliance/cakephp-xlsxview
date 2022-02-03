<?php

declare(strict_types=1);

namespace XlsxView;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;

class Plugin extends BasePlugin
{
    /**
     * The name of this plugin
     *
     * @var string
     */
    protected $name = 'XlsxView';

    /**
     * Console middleware
     *
     * @var bool
     */
    protected $consoleEnabled = false;

    /**
     * Enable middleware
     *
     * @var bool
     */
    protected $middlewareEnabled = false;

    /**
     * Register container services
     *
     * @var bool
     */
    protected $servicesEnabled = false;

    /**
     * Load routes or not
     *
     * @var bool
     */
    protected $routesEnabled = false;

    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        /**
         * Add XlsxView to View class map through RequestHandler, if available, on Controller initialisation
         *
         * @link https://book.cakephp.org/4/en/controllers/components/request-handling.html#using-custom-viewclasses
         */
        EventManager::instance()->on('Controller.initialize', function (EventInterface $event) {
            $controller = $event->getSubject();
            if ($controller->components()->has('RequestHandler')) {
                $controller->RequestHandler->setConfig('viewClassMap.xlsx', 'XlsxView.Xlsx');
            }
        });

        /**
         * Add a request detector named "xlsx" to check whether the request was for an XLSX file,
         * either through accept header or file extension
         *
         * @link https://book.cakephp.org/4/en/controllers/request-response.html#checking-request-conditions
         */
        ServerRequest::addDetector(
            'xlsx',
            [
                'accept' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'param' => '_ext',
                'value' => 'xlsx',
            ]
        );
    }
}
