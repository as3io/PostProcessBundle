# PostProcessBundle
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/as3io/PostProcessBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/as3io/PostProcessBundle/?branch=master) [![Build Status](https://travis-ci.org/as3io/PostProcessBundle.svg?branch=master)](https://travis-ci.org/as3io/PostProcessBundle) [![Packagist](https://img.shields.io/packagist/dt/as3/post-process-bundle.svg)](https://packagist.org/packages/as3/post-process-bundle) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/ed50d7d9-c5d5-4c4d-be6f-e8882099785e/mini.png)](https://insight.sensiolabs.com/projects/ed50d7d9-c5d5-4c4d-be6f-e8882099785e)

Provides centralized support for executing callable code before Symfony framework termination

## Installation

### Install packages with Composer

To install this bundle via composer, perform the following command: `composer require as3/post-process-bundle ^1.0`.

### Register the Bundle

Once installed, register the bundle in your `AppKernel.php`:
```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new As3\Bundle\PostProcessBundle\As3PostProcessBundle(),
    );

    // ...
}
```

## Usage

To use the PostProcessBundle, you must first create a class adhering to the `Task\TaskInterface` or `Plugins\PluginInterface`. A task is a process that will be executed on the Symfony terminate event (after the response is sent,) whereas a Plugin is a process that is run before the response is sent (allowing you to modify it.)

### Tasks

A task can be used to execute logic after the response has been sent to the user, allowing you to trigger long-running processes that need to complete, but the user does not need to wait for them.

Example:

```php
use As3\Bundle\PostProcessBundle\TaskInterface;

class SleepTestTask implements TaskInterface
{
    /**
     * {@inhericDoc}
     */
    public function run()
    {
        // Some process that takes 5 minutes
        sleep(300);
    }
}
```

To register your task, call the `addTask` method against the task manager's service (`as3_post_process.task.manager`):
```php
    $manager = $this->get('as3_post_process.task.manager');
    $manager->addTask(new SleepTestTask(), 5);
```

Tasks can have a `priority` set when they are added to the manager -- by default new tasks are added with a priority of `0`. Tasks are executed in ascending order by their priority.

You can also register a service by using the tag `as3_post_process.task` if your task should be run on every request.

```yaml
# src\MyBundle\Resources\services.yml
services:
my_app.my_cool_task:
    class: MyCoolTask
    tags:
        - { name: as3_post_process.task, priority: 5 }
```

### Plugins

A plugin can be used to modify the response before it is returned to the user.

Example:

```php
use Symfony\Component\HttpFoundation\Response;

/**
 * Integration with New Relic End User Monitoring services
 */
class NewRelicInjector extends PluginInterface
{
    /**
     * Handles injection of NREUM Javascript
     */
    public function filterResponse(Response $response)
    {
        if (extension_loaded('newrelic')) {
            newrelic_disable_autorum();

            $content = $response->getContent();

            if (false != strpos($content, '</head>')) {
                $content = str_replace('</head>', sprintf("\n%s\n</head>", newrelic_get_browser_timing_header()), $content);
            }

            if (false != strpos($content, '</body>')) {
                $content = str_replace('</body>', sprintf("\n%s\n</body>", newrelic_get_browser_timing_footer()), $content);
            }

            $response->headers->set('X-NREUM', 'Enabled');

            // If we modified the content, set it on the response.
            if ($content !== $response->getContent()) {
                $response->setContent($content);
            }

            return $response;
        }
    }
}
```

This plugin will disable automatic injection of NewRelic end user monitoring javascript. To enable this for all requests, add the following service definition:

```yaml
    my_app.my_bundle.new_relic_injector:
        class: MyApp\MyBundle\NewRelicPlugin
        tags:
            - { name: as3_post_process.plugin }
```
