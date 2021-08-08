<?php

namespace Heavensloop\DataTransformer;

use Facade\Ignition\Support\ComposerClassMap;
use Heavensloop\DataTransformer\Commands\GenerateResponseCommand;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class DataTransformServiceProvider extends ServiceProvider
{
    const TAG = 'dto';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $tag = self::TAG;

        $this->app->tag($this->getTransformations(), $tag);

        $this->app->bind(ResponseTransformer::class, function ($app) use ($tag) {
            $request = $app->get(Request::class);
            return new ResponseTransformer($app->tagged($tag), $request);
        });
    }

    private function getTransformations(): array
    {
        $transformerType = config('dto.location');
        $namespaces = array_keys((new ComposerClassMap)->listClasses());

        return collect($namespaces)->filter(function ($item) use ($transformerType) {
            $classPath = str_replace("/", "\\", ucwords($transformerType));
            return Str::contains($item, "{$classPath}\\") && Str::endsWith($item, "Transformer");
        })->values()->toArray();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            GenerateResponseCommand::class
        ]);
    }
}
