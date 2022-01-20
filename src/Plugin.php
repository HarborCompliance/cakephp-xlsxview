<?php

declare(strict_types=1);

namespace XlsxView;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\ServerRequest;

/**
 * Plugin for XlsxView
 */
class Plugin extends BasePlugin
{
    /**
     * Plugin name.
     *
     * @var string
     */
    protected $name = 'XlsxView';

    /**
     * Load routes or not
     *
     * @var bool
     */
    protected $routesEnabled = false;

    /**
     * Console middleware
     *
     * @var bool
     */
    protected $consoleEnabled = false;

    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        /**
         * Add CsvView to View class map through RequestHandler, if available, on Controller initialisation
         *
         * @link https://book.cakephp.org/4/en/controllers/components/request-handling.html#using-custom-viewclasses
         */
        EventManager::instance()->on('Controller.initialize', function (EventInterface $event) {
            $controller = $event->getSubject();
            if ($controller->components()->has('RequestHandler')) {
                $controller->RequestHandler->setConfig('viewClassMap.xlsx', 'XslxView.Xlsx');
            }
        });

        /**
         * Add a request detector named "csv" to check whether the request was for a CSV,
         * either through accept header or file extension
         *
         * @link https://book.cakephp.org/4/en/controllers/request-response.html#checking-request-conditions
         */
        ServerRequest::addDetector(
            'xlsx',
            [
                'accept' => ['text/xlsx'],
                'param' => '_ext',
                'value' => 'xlsx',
            ]
        );
    }
}
