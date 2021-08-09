<?php

namespace Heavensloop\DataTransformer;

use Illuminate\Container\RewindableGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ResponseTransformer
{
    private $transformers;
    private $loadedTransformer;
    private $request;

    const PARAMETER_TYPE_ROUTE = 'route-parameter';
    const PARAMETER_TYPE_QUERY = 'query-parameter';

    public function __construct(RewindableGenerator $transformers, Request $request)
    {
        $this->request = $request;

        $this->transformers = $transformers;
    }

    public function getTransformer(): TransformerInterface
    {
        return $this->loadedTransformer ?: $this->loadTransformer();
    }

    public function apply($dataSource, array $options = [])
    {
        // Load the transformer
        $transformer = $this->loadTransformer();

        return $transformer
            ->setOptions($options)
            ->getTransformed($dataSource);
    }

    protected function loadTransformer(): TransformerInterface
    {
        $transformationName = $this->getTransformationName();

        return $this->loadedTransformer = collect($this->transformers)
            ->filter(function (TransformerInterface $transformer) use ($transformationName) {
                return $transformer->applies($transformationName);
            })->first();
    }

    private function getTransformationName(): string
    {
        $parameterName = config('dto.parameter_name', 'transformation');
        $inputType = config('dto.parameter_type', 'query');
        $name = "";

        switch ($inputType) {
            case (self::PARAMETER_TYPE_ROUTE):
                $name = $this->request->route($parameterName, "");
            default:
                $name = $this->request->get($parameterName, "");
        }

        return $name;
    }

    private function defaultTransform($dataSource)
    {
        return $dataSource instanceof Builder ? $dataSource->get() : $dataSource;
    }
}
