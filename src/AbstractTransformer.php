<?php

namespace Heavensloop\DataTransformer;

use Heavensloop\DataTransformer\TransformerInterface;

abstract class AbstractTransformer implements TransformerInterface
{
    protected $name = "";
    protected $options = [];
    protected $preTransforms = [];
    protected $postTransforms = [];

    public function applies(string $format): bool
    {
        if (!$this->name || $this->name === 'transformer-name') {
            throw new \ErrorException('Transformer name not defined');
        }

        return $format === $this->name;
    }

    protected function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(?array $options): TransformerInterface
    {
        if ($options) {
            $this->options = $options;
        }

        return $this;
    }

    public function getTransformed($dataSource)
    {
        // Apply any defined pre transformation
        $dataSource = $this->applyTransforms($dataSource, $this->preTransforms);

        // Apply the base transformation
        $dataSource = $this->transform($dataSource);

        // Apply any defined post transformation
        $dataSource = $this->applyTransforms($dataSource, $this->postTransforms);

        return $dataSource;
    }

    protected function applyTransforms($dataSource, array $transformers=[])
    {
        if (empty($transformers)) {
            return $dataSource;
        }

        foreach($transformers as $transformType) {
            dd($transformType);
            $transformer = resolve($transformType);

            if ($transformer instanceof TransformerInterface) {
                $dataSource = $transformer->transform($dataSource);
            }
        }

        return $dataSource;
    }

    abstract public function transform($dataSource);
}
