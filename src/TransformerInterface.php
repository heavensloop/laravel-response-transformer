<?php

namespace Heavensloop\DataTransformer;

interface TransformerInterface
{
    protected $preTransforms = [];
    protected $postTransforms = [];

    /**
     * Checks if a transform format applies to the transformer
     *
     * @param string $format
     * @return boolean
     */
    public function applies(string $format): bool;

    /**
     * Transform the data or query
     *
     * @param [type] $dataOrQuery
     * @return boolean
     */
    public function transform($dataOrQuery);

    public function setOptions(array $options=[]): TransformerInterface;
}
