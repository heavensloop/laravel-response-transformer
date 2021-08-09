<?php

namespace Heavensloop\DataTransformer\Transformers;

use Heavensloop\DataTransformer\AbstractTransformer as Transformer;
use Illuminate\Database\Eloquent\Builder;

class DefaultResponseTransformer extends Transformer
{
    protected $name = "default-response-transformer";
    protected $options = ['id', 'name'];

    public function transform($dataSource)
    {
        if ($dataSource instanceof Builder) {
            return $dataSource->get();
        }

        return $dataSource;
    }

    public function applies(string $format): bool
    {
        // Always applies cos it's a default
        return true;
    }
}
