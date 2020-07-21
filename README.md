PowerHTML/PowerHTML
===

[![Stable version](https://poser.pugx.org/mykehowells/PowerHTML/v/stable.svg)](https://packagist.org/packages/mykehowells/powerhtml)
[![Unstable version](https://poser.pugx.org/mykehowells/PowerHTML/v/unstable.svg)](https://packagist.org/packages/mykehowells/powerhtml)

Create HTML template with .pwr.html extension and make use in emails or whatever else!

Installation
---

```composer require mykehowells/powerhtml```

Usage
---

Include the composer autoloader

```require __DIR__ . '/../vendor/autoload.php'```


Getting started with a basic PowerHTML setup. The below code would output the processed PowerHTML template to the browser.

```php
$powerHtml = new \PowerHTML\PowerHTML;

$template = ( $_SERVER[ 'REQUEST_URI'] == '/' ) ? null : ltrim( $_SERVER[ 'REQUEST_URI'], '/' );

$powerHtml->with( 'hello', 'hello world' )
    ->parse( $template );

$powerHtml->render();

```


If you're looking to store the parsed html into a variable, use ```store()``` rather than ```render()```.

Options
--

### Change Template Directory

**Default:** ```/resources/templates```

```php
$options = [
    'template_dir' => __DIR__ . '/public/templates'
];

$powerHtml = new \PowerHTML\PowerHTML( $options );
```

### Stop PHP Evaluation

Stopping PHP evaluation will stop the curly braces from being parsed in your templates. For example: {{$my_var}} would be output as {{$my_var}}.

```php
$options = [
    'allow_php_eval' => false
];

$powerHtml = new \PowerHTML\PowerHTML( $options );
```