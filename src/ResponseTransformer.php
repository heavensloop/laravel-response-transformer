<?php

namespace Heavensloop\DataTransformer;

use Facade\Ignition\Support\ComposerClassMap;
use Illuminate\Container\RewindableGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResponseTransformer
{
    private $transformers;
    private $transformType;

    public function __construct(RewindableGenerator $transformerGenerator, Request $request)
    {
        $this->transformType = $request->route('transformation');
        $transformerIterator = $transformerGenerator->getIterator();

        foreach ($transformerIterator as $key => $transformerClass) {
            $this->transformers[] = $transformerClass;
        }
    }

    public function apply($dataSource, array $options = [])
    {
        if (!$this->transformType) {
            return $this->defaultTransform($dataSource);
        }

        $transformer = collect($this->transformers)->filter(function (TransformerInterface $transformer) {
            return $transformer->applies($this->transformType);
        })->first();

        if (!$transformer) {
            return $this->defaultTransform($dataSource);;
        }

        return $transformer
            ->setOptions($options)
            ->getTransform($dataSource);
    }

    private function defaultTransform($dataSource)
    {
        return $dataSource instanceof Builder ? $dataSource->get() : $dataSource;
    }
}
