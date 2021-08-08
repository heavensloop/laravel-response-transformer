<?php

namespace Heavensloop\DataTransformer\Commands;

use Illuminate\Console\Command;
use Heavensloop\DataTransformer\TemplateGenerator;

class GenerateResponseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:response {className}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a response file';
    protected $templateGenerator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TemplateGenerator $templateGenerator)
    {
        parent::__construct();

        $this->templateGenerator = $templateGenerator;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Validate the

        // Get the input
        $className = $this->argument('className');

        try {
            // Load the template..
            $this->templateGenerator->generate($className);
            $successMessage = "The Response - {$className} was created successfully";
            $this->output->block($successMessage, 'SUCCESS', 'fg=green', ' ', true);
        } catch( \ErrorException $ex) {
            $this->output->block($ex->getMessage(), 'INFO', 'fg=yellow', false, true);
        }

        return 0;
    }
}
