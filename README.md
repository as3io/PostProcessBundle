# PostProcessBundle
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/as3io/PostProcessBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/as3io/PostProcessBundle/?branch=master) [![Build Status](https://travis-ci.org/as3io/PostProcessBundle.svg?branch=master)](https://travis-ci.org/as3io/PostProcessBundle) [![Packagist](https://img.shields.io/packagist/dt/as3/post-process-bundle.svg)](https://packagist.org/packages/as3/post-process-bundle) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/ed50d7d9-c5d5-4c4d-be6f-e8882099785e/mini.png)](https://insight.sensiolabs.com/projects/ed50d7d9-c5d5-4c4d-be6f-e8882099785e)

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
