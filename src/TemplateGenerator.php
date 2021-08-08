<?php

namespace Heavensloop\DataTransformer;

class TemplateGenerator
{
    private $transformerLocation;
    private $fileContent;

    function __construct()
    {
        $this->transformerLocation = config('dto.location');
    }

    private function loadTemplate()
    {
        $filePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Template.tpl';

        return file_get_contents($filePath);
    }

    protected function publish(string $transformerClassName)
    {
        $outputPath = base_path($this->transformerLocation . DIRECTORY_SEPARATOR . $transformerClassName . '.php');

        if (file_exists($outputPath)) {
            throw new \ErrorException("The Transformer \"{$transformerClassName}\" already exists!");
        }

        file_put_contents($outputPath, $this->fileContent);
    }

    public function generate($transformerClassName)
    {
        $this->transformerLocation = config('dto.location');
        $classPath = str_replace("/", "\\", ucwords($this->transformerLocation));
        $namespace = $classPath . "\\" .  $transformerClassName;
        $this->fileContent = $this->loadTemplate();
        $parameters = [
            'ClassName' => $transformerClassName,
            'Namespace' => $namespace
        ];

        foreach ($parameters as $key => $value) {
            $this->fileContent = str_replace("{" . $key . "}", $value, $this->fileContent);
        }

        $this->publish($transformerClassName);
    }
}
