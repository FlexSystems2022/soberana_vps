<?php

namespace App\Shared\Commands;

use App\Shared\Provider\RestClient;

abstract class CommandNexti extends Command
{
    /**
     * Request timeinterval in seconds
     * 
     * @var float
     */
    protected float $timeInterval = 1;

    /**
     * @var \App\Shared\Provider\RestClient
     */
    protected RestClient $client;

    /**
     * Create Client
     * 
     * @return \App\Shared\Provider\RestClient
     **/
    protected function client(): RestClient
    {
        if(!isset($this->client)) {
            $this->client = new RestClient('nexti', false);
            $this->client->withOAuthToken('client_credentials');
        }

        return $this->client;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if(method_exists($this, 'buscaRegistrosCriar')) {
            $registros = $this->buscaRegistrosCriar();

            $this->warn("Total de {$registros->count()} para Criar!");

            foreach($registros as $registro) {
                $this->criaRegistro($registro);

                sleep($this->timeInterval);
            }
        }

        if(method_exists($this, 'buscaRegistrosAtualizar')) {
            $registros = $this->buscaRegistrosAtualizar();

            $this->warn("Total de {$registros->count()} para Atualizar!");

            foreach($registros as $registro) {
                $this->atualizaRegistro($registro);

                sleep($this->timeInterval);
            }
        }

        if(method_exists($this, 'buscaRegistrosDeletar')) {
            $registros = $this->buscaRegistrosDeletar();

            $this->warn("Total de {$registros->count()}! para Deletar");

            foreach($registros as $registro) {
                $this->deletaRegistro($registro);

                sleep($this->timeInterval);
            }
        }

        return static::SUCCESS;
    }
}