<?php

namespace App\Shared\Commands\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait LoggerCommand
{
    /**
     * @var bool
     */
    protected bool $log = false;

    /**
     * @var \Illuminate\Support\Carbon
     */
    protected Carbon $init_time;

    /**
     * @var string
     */
    protected string $finish_time;

    /**
     * @var string
     */
    protected string $key_command;

    /**
     * Run the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->generateKey();

        $this->init_time = Carbon::now();
        if($this->log) {
            Log::info("Processing command [{$this->signature}]", [
                'key' => $this->getKeyCommand()
            ]);
        }

        $result = parent::run($input, $output);

        $this->comment("Processed in {$this->getTimeFinish()}");

        if($this->log) {
            Log::info("Processed command [{$this->signature}] in {$this->getTimeFinish()}", [
                'key' => $this->getKeyCommand()
            ]);
        }

        return $result;
    }

    /**
     * Get Time Finish Command
     * 
     * @return string
     */
    protected function getTimeFinish(): string
    {
        if(!isset($this->finish_time)) {
            $this->finish_time = $this->init_time->diffForHumans(
                other: Carbon::now(),
                syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                short: true
            );
        }

        return $this->finish_time;
    }

    /**
     * Generate Key Command
     * 
     * @return $this
     */
    protected function generateKey(): self
    {
        $this->key_command = date('YmdHis');

        return $this;
    }

    /**
     * Get Key Command
     * 
     * @return string
     */
    protected function getKeyCommand(): string
    {
        return $this->key_command;
    }

    /**
     * Create Log
     * 
     * @param string $action
     * @param object|array|string $message
     * @param array $context
     * 
     * @return void
     */
    protected function log(string $action, object|array|string $message, array $context=[]): void
    {
        if($message instanceof Model) {
            $message = $message->toArray();
        } elseif(is_array($message) || is_object($message)) {
            $message = json_encode((array) $message);
        }

        $message = "Command [{$this->signature}] - " . $message;
        
        forward_static_call([Log::class, $action], $message, array_merge($context, [
            'key' => $this->getKeyCommand()
        ]));
    }
}