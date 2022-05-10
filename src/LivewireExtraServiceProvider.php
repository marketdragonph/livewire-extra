<?php

namespace MarketDragon\LivewireExtra;

use Illuminate\Support\ServiceProvider;
use Livewire\Commands\ComponentParser;
use Illuminate\Filesystem\Filesystem;
use MarketDragon\LivewireExtra\Commands\DiscoverLivewire;

class LivewireExtraServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DiscoverLivewire::class
            ]);
        }

        $this->mergeConfigFrom(
            __DIR__ . '/../config/livewire-extra.php', 'livewire-extra'
        );

        $this->publishes([
            __DIR__ . '/../config/livewire-extra.php' => config_path('livewire-extra.php'),
        ]);

        $this->registerComponentAutoDiscovery();

    }

    protected function registerComponentAutoDiscovery()
    {
        // Rather than forcing users to register each individual component,
        // we will auto-detect the component's class based on its kebab-cased
        // alias. For instance: 'examples.foo' => App\Http\Livewire\Examples\Foo

        // We will generate a manifest file so we don't have to do the lookup every time.
        $defaultManifestPath = $this->app['livewire']->isRunningServerless()
            ? '/tmp/storage/bootstrap/cache/livewire-components.php'
            : app()->bootstrapPath('cache/livewire-components.php');

        $this->app->singleton(LivewireComponentsFinder::class, function () use ($defaultManifestPath) {
            $namespaces = config('md-admin.namespaces');
            $packages = (new Filesystem)
                ->directories(config('livewire-extra.vendor_dir'));

            $livewireDefault = ComponentParser::generatePathFromNameSpace(config('livewire.class_namespace'));

            $livewirePackages = array_map(function($package) {
                return $package . '/src/' . 'Http/Livewire/';
            }, $packages);

            $components = array_merge([$livewireDefault], $livewirePackages);

            return new LivewireComponentsFinder(
                new Filesystem,
                config('livewire.manifest_path') ?: $defaultManifestPath,
                $components
            );
        });
    }

}
