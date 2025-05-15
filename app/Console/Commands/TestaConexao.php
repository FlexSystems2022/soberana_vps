<?php

namespace App\Console\Commands;

use App\Shared\DBPG;
use Illuminate\Console\Command;

class TestaConexao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TestaConexao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $query = 
        "
            SELECT * FROM wdp.agencia
        ";

        $dados = DBPG::initialize()->select($query);

        if($dados){
            foreach($dados as $dado){
                dd($dado);
            }
        }
    }
}