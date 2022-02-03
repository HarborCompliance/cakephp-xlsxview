# XlsxView plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```sh
composer require harborcompliance/cakephp-xlsxview
```

### Enable plugin

Load the plugin by running command

```sh
bin/cake plugin load XlsxView
```

## Usage

A basic example writing an array to the xlsx file.

```php
public function export()
{
    $data = [
        ['id' => 1, 'name' => 'Alex', 'email' => 'alex@example.com'],
        ['id' => 2, 'name' => 'Jason', 'email' => 'jason@example.com'],
    ];

    $this->set(compact('data'));
    $this->viewBuilder()
        ->setClassName('XlsxView.Xlsx')
        ->setOption('serialize', 'data');
}
```

You may specify an optional header that will be the first row in your xlsx file.

```php
public function export()
{
    $data = [
        ['id' => 1, 'name' => 'Alex', 'email' => 'alex@example.com'],
        ['id' => 2, 'name' => 'Jason', 'email' => 'jason@example.com'],
    ];

    $header = ['ID', 'Name', 'Email'];

    $this->set(compact('data'));
    $this->viewBuilder()
        ->setClassName('XlsxView.Xlsx')
        ->setOptions([
            'serialize' => 'data',
            'header' => $header,
        ]);
}
```

### Automatic View Class Switching

You can use the router's extension parsing feature and the `RequestHandlerComponent` to automatically use the XlsxView class.

Enable `xlsx` extension parsing for all routes using `Router::extensions('xlsx')` in your app's `routes.php` or using `$routes->addExtensions()` within the required scope.

```php
// UsersController.php

// In your controller's initialize() method:
$this->loadComponent('RequestHandler');

// Controller action
public function index()
{
    $users = $this->Users->find();
    $this->set(compact('users'));

    if ($this->request->is('xlsx')) {
        $this->viewBuilder()->setOptions([
            'serialize' => 'users',
            'header' => $header,
        ]);
    }
}
```

With the above controller you can now access `/users.xlsx` or use the `Accept` header `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` to get the data as an xlsx file and use `/users` to get normal HTML page.

### Setting the Downloaded File Name

By default, the downloaded file will be named after the final portion of the url. To explicitly set the filename use the `Response::withDownload()` method.

```php
public function export()
{
    // ...

    $this->response = $this->response->withDownload('users.xlsx');
}
```
