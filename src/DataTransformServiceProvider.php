<?php

namespace Heavensloop\DataTransformer;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Facade\Ignition\Support\ComposerClassMap;
use Illuminate\Contracts\Foundation\Application;
use Heavensloop\DataTransformer\Commands\GenerateResponseCommand;
use Heavensloop\DataTransformer\Transformers\DefaultResponseTransformer;

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

        // dd($this->app->tagged($tag));

        $this->app->bind(ResponseTransformer::class, function (Application $app) use ($tag) {
            $requestInstance = $app->get(Request::class);
            return new ResponseTransformer($app->tagged($tag), $requestInstance);
        });
    }

    private function getTransformations(): array
    {
        $transformerLocation = config('dto.location');
        $namespaces = array_keys((new ComposerClassMap)->listClasses());

        return collect($namespaces)->filter(function ($item) use ($transformerLocation) {
            $classPath = str_replace("/", "\\", ucwords($transformerLocation));
            return Str::contains($item, "{$classPath}\\") && Str::endsWith($item, "Transformer");
        })
            ->values()
            // Add the default response transformer
            ->push(DefaultResponseTransformer::class)
            ->toArray();
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
