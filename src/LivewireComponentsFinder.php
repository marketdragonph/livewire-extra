<?php

namespace MarketDragon\LivewireExtra;

use Exception;
use ReflectionClass;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Livewire\Component;

class LivewireComponentsFinder
{
    protected $paths;
    protected $files;
    protected $manifest;
    protected $manifestPath;

    public function __construct(Filesystem $files, $manifestPath, $paths)
    {
        $this->files = $files;
        $this->paths = $paths;
        $this->manifestPath = $manifestPath;
    }

    public function find($alias)
    {
        $manifest = $this->getManifest();

        return $manifest[$alias] ?? $manifest["{$alias}.index"] ?? null;
    }

    public function getManifest()
    {
        if (! is_null($this->manifest)) {
            return $this->manifest;
        }

        if (! file_exists($this->manifestPath)) {
            $this->build();
        }

        return $this->manifest = $this->files->getRequire($this->manifestPath);
    }

    public function build()
    {
        $this->manifest = $this->getClassNames()
            ->mapWithKeys(function ($class) {
                return [$class::getName() => $class];
            })->toArray();

        $this->write($this->manifest);

        return $this;
    }

    protected function write(array $manifest)
    {
        if (! is_writable(dirname($this->manifestPath))) {
            throw new Exception('The '.dirname($this->manifestPath).' directory must be present and writable.');
        }

        $this->files->put($this->manifestPath, '<?php return '.var_export($manifest, true).';', true);
    }

    public function getClassNames()
    {
        $files = [];

        foreach($this->paths as $path)
        {
            if (is_dir($path)) {
                foreach($this->files->allFiles($path) as $file) {
                    $files[] = $file;
                }
            }
        }

        return collect($files)
            ->map(function (SplFileInfo $file) {

                if (preg_match('/app/', $file->getPath())) {
                    return app()->getNamespace() . str($file->getPathname())
                        ->after(app_path().'/')
                        ->replace(['/', '.php'], ['\\', ''])
                        ->__toString();
                }
                $filename = str_replace('.php', '', $file->getFilename());
                $packageParentName = str_replace(' ', '', config('livewire-extra.package_parent_name')) . '\\';
                $folder = str_replace('/src/Http/Livewire', '', $file->getPath());
                $foldername = ucfirst(str_replace(config('livewire-extra.vendor_dir'), '', $folder));
                return $packageParentName . $foldername . '\\Http\\Livewire\\'. $filename;

            })->filter(function (string $class) {
                return is_subclass_of($class, Component::class) &&
                    ! (new ReflectionClass($class))->isAbstract();
            });

    }
}
