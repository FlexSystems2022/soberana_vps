<?php

namespace App\Console\Commands;

use App\Models;
use App\Mail\MailLog;
use App\Shared\Commands\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class ProcessaLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessaLogs {--force} {--table}';

    /**
     * Execute the console command.
     * 
     * @return int
     **/
    public function handle(): int
    {
        if($this->option('table')) {
            $this->showTable();

            return static::SUCCESS;
        }

        if(!$this->option('force') && !Models\MailLog::checkSendLog()) {
            return static::SUCCESS;
        }

        Mail::to($this->getEmails())
            ->send(new MailLog($this->errors()));

        Models\MailLog::updateDatetime();

        $this->info('E-mail enviado!');

		return static::SUCCESS;
    }

    /**
     * Busca Emails
     * 
     * @return array<string>
     **/
    private function getEmails(): array
    {
        return array_values(array_filter([
            env('EMAIL_LOG'),
            'parcerias@flexsystems.com.br'
        ]));
    }

    /**
     * Busca Erros no banco
     * 
     * @return array
     **/
    private function errors(): array
    {
        return [
            'Empresas'          => Models\Company::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Cargos'            => Models\Career::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Horários'          => Models\Shift::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Clientes'          => Models\Client::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Postos'            => Models\Workplace::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Sindicatos'        => Models\Union::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Colaboradores'     => Models\People::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Troca de Posto'    => Models\Workplace\WorkplaceTransfer::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Troca de Escala'   => Models\Schedule\ScheduleTransfer::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Situações'         => Models\Absence\Situation::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Ausências'         => Models\Absence::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
            'Competência Contra Cheque' => Models\Paycheck\Competence::getToLog(
                                                fn(Builder $db) => $db->isPendent()
                                            ),
            'Contra Cheque'     => Models\Paycheck::getToLog(
                                    fn(Builder $db) => $db->isPendent()
                                ),
        ];
    }

    /**
     * Show Data Error to table
     * 
     * @return void
     **/
    private function showTable(): void
    {
        foreach($this->errors() as $name => $errors) {
            $this->info($name);

            if($errors) {
                $this->table(array_keys($errors[0]), $errors);
            }
        }
    }
}