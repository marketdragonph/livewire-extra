## Livewire Extra

A re-written LivewireComponentsFinder version to make Route based Livewire component to work on packages.

This add package components in the livewire cache to avoid page expired message in the browser.

### Installation

`composer require marketdragon/livewire-extra`

Add a livewire component in your `<package-folder>/src/Http/Livewire`.

Add these to your .env

```
MD_PACKAGE_PARENT_NAME=<your package parent name>
MD_PACKAGE_VENDOR_DIR=<vendor folder location>
```

In your livewire component render method.

```
    public function render()
    {
        return view('md::livewire.admin');
    }
```

Add this to your preferred service provider

```
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__. '/../resources/views', 'md');
    }

```


`php artisan marketdragon:livewire_discover`

### Customise the configuration

`php artisan vendor:publish --provider="MarketDragon\LivewireExtra\LivewireExtraServiceProvider"`

```
<?php

return [
    // Your core package name
    'package_parent_name' => env('MD_PACKAGE_PARENT_NAME', 'MarketDragon'),
    // Your package folder location
    'vendor_dir' => env('MD_PACKAGE_VENDOR_DIR', './vendor/marketdragon/'),
];

```
