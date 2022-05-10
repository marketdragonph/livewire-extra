## Livewire Extra

A re-written LivewireComponentsFinder version to make Route based Livewire component to work on packages.

This add package components in the livewire cache to avoid page expired message in the browser.

### Installation

`composer require marketdragon/livewire-extra`

`php artisan marketdragon:livewire_discover`

### Customise the configuration

`php artisan vendor:publish --provider=MarketDragon\LivewireExtra\LivewireExtraServiceProvider`

`
<?php

return [
    // Your core package name
    'package_parent_name' => 'MarketDragon',
    // Your package folder location
    'vendor_dir' => './vendor/marketdragon/',
];
`
