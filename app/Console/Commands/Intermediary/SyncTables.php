<?php

namespace App\Console\Commands\Intermediary;

use App\Shared\Commands\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;

class SyncTables extends Command
{
    /**
     * @var bool
     */
    protected bool $log = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexti-sync {folder?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Nexti Tables';

    /**
     * Execute Command.
     * 
     * @return int
     */
    public function handle(): int
    {
        $folder = $this->argument('folder');

        $this->comment("Processing command [{$this->name}]");

        $files = (new Filesystem)->allFiles(resource_path('sql/' . $folder));

        foreach ($files as $file) {
            $this->info("Executando {$file->getFilename()}");

            $string = $file->getContents();

            collect(explode(';', $string))
                    ->map(fn(string $sql) => trim($sql))
                    ->filter()
                    ->each(function(string $sql) {
                        $result = $this->executeSql($sql);
                        if(!$result['success']) {
                            throw new \Exception($result['message']);
                        }
                    });
        }

        return static::SUCCESS;
    }

    /**
     * Execute Sql
     * 
     * @param string $sql
     * @return array
     */
    protected function executeSql(string $sql): array
    {
        try {    
            DB::select($sql);

            return [
                'success' => true
            ];
        } catch(\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}