<h1 align="center">Supervisor</h1>

This package provides *supervisor-like*, *async-like*, *child-process-like*, etc to build event-listener driven with little painless for your Laravel Project.

Getting Started
---

Installation :

```
$ composer require tripteki/laravelphp-supervisor
```

```
$ npm install pm2 && npm install --save-dev chokidar
```

```
$ pecl install swoole
```

How to use :

- Publish config file into your project's directory with running :

```
php artisan vendor:publish --tag=tripteki-laravelphp-supervisor
```

Usage
---

`php artisan supervisor:<option>`

Option
---

- `start ...` : Start the supervisor.
    - foreground *(default)*
    - background
- `reload` : Reload the background supervisor.
- `stop` : Stop the background supervisor / `ctrl + c` for foreground supervisor.
- `status` : Show the status of background supervisor.
- `startup` : Generate `ecosystem.json` supervisor startup configuration, do not forget to stop your supervisor process perproject, then see [this](https://pm2.keymetrics.io/docs/usage/startup) to know how to get started.

Snippet
---

```php
/** Use asynchronous? */

__async__(function () {

    Model::truncate();
});
```

```php
/** Use asynchronous await-like to get variable? */

[ $model, ] = __async__(fn () => Model::all());
```

```php
/** Use setInterval? */

__setInterval__(function () {

    Model::truncate();

}, 2000);
```

```php
/** Use setImmediate? */

__setImmediate__(function () {

    Model::truncate();

}, 2000);
```

```php
/** Use exec as replace temporary process? */

$os = __exec__("uname -a");
```

```php
/** Use spawn as one way communication child process? */

/** Stdin stream handler... */
$stdin = fopen("php://temporary", "w+");
fwrite($stdin, "Foo...");
fwrite($stdin, "Bar...");
fwrite($stdin, "Baz...");
fclose($stdin);

/** Stdout handler... */
$stdout = function ($isError, $data)
{
    if ($isError) {

        // $isError //
    }

    // $data //
};

__spawn__("python3 example.py", $environment = [], $stdout, $stdin);
```

Author
---

- Trip Teknologi ([@tripteki](https://linkedin.com/company/tripteki))
- Hasby Maulana ([@hsbmaulana](https://linkedin.com/in/hsbmaulana))
