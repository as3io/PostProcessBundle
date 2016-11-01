# PostProcessBundle
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/as3io/As3ModlrBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/as3io/As3ModlrBundle/?branch=master) [![Build Status](https://travis-ci.org/as3io/As3ModlrBundle.svg?branch=master)](https://travis-ci.org/as3io/As3ModlrBundle) [![Packagist](https://img.shields.io/packagist/dt/as3/modlr-bundle.svg)](https://packagist.org/packages/as3/modlr-bundle) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/6d7d530c-f405-4815-847a-4f7ff82960c5/mini.png)](https://insight.sensiolabs.com/projects/6d7d530c-f405-4815-847a-4f7ff82960c5)

Provides centralized support for executing callable code before Symfony framework termination

## Installation

### Install packages with Composer

To install this bundle via composer, perform the following command: `composer require as3/post-process-bundle ^1.0`.

### Register the Bundle

Once installed, register the bundle in your `AppKernel.php`:
```
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

## Configuration
