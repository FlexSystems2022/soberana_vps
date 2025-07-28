<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\Commands\CommandIntermediary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InsertAusencia extends Command
{
    protected $signature = 'InsertAusencia';
    protected $description = 'Insere ausências vindas do Senior nas tabelas da integração do Nexti';

    public function handle()
    {
        $tabelas = collect([
            '00001', '00002', '00003', '00004', '00005', '00006', '00007', '00008', '00009',
            '00014', '00017', '00020', '01000', '01001', '01002', '01003', '01004', '01005',
            '01006', '01007', '01008', '01009', '01010', '01011', '02021', '02022', '02023',
            '02024', '02025', '02026', '02027', '02028', '02029', '02030', '02031', '02032',
            '02033', '02034', '02035', '02036', '02037', '02038', '02039', '02040', '02041',
            '02042', '02043', '02044', '02045', '02046', '02047',
        ])->map(fn($sufixo) => [
            'a' => "a$sufixo",
            'f' => "f$sufixo",
            'numemp' => (int)ltrim($sufixo, '0'),
        ])->toArray();

        $ausencias = DB::table(
            DB::raw("({$this->geraUnions($tabelas)->toSql()}) as ausencias")
        )
        ->mergeBindings($this->geraUnions($tabelas))
        ->join('wdp.especial', function ($join) {
            $join->on('especial.numemp', '=', 'ausencias.numemp');
            $join->on('especial.matricula', '=', 'ausencias.matricula_esocial');
        })
        ->select(
            'ausencias.numemp',
            'ausencias.matricula_esocial',
            'ausencias.idafastamento',
            'ausencias.dtinicial',
            'ausencias.dtfinal',
            'ausencias.dsmotivo',
            'ausencias.cid',
            'wdp.especial.cdchamada AS idexternosituacao'
        )
        ->get();

        dd($ausencias);
    }

    private function geraUnions(array $pares)
    {
        if (empty($pares)) {
            throw new \InvalidArgumentException("Lista de pares de tabelas está vazia.");
        }

        $first = array_shift($pares);

        $base = DB::table("wdp.{$first['a']} as a")
            ->join("wdp.{$first['f']} as f", function ($join) use ($first) {
                $join->on('a.codcoligada', '=', 'f.codcoligada')
                     ->on('a.idmov', '=', 'f.idmov');
            })
            ->select([
                DB::raw("{$first['numemp']} as numemp"),
                'a.matricula_esocial',
                'f.idafastamento',
                'f.dtinicial',
                'f.dtfinal',
                'f.dsmotivo',
                'f.cid',
            ]);

        foreach ($pares as $par) {
            $union = DB::table("wdp.{$par['a']} as a")
                ->join("wdp.{$par['f']} as f", function ($join) use ($par) {
                    $join->on('a.codcoligada', '=', 'f.codcoligada')
                         ->on('a.idmov', '=', 'f.idmov');
                })
                ->select([
                    DB::raw("{$par['numemp']} as numemp"),
                    'a.matricula_esocial',
                    'f.idafastamento',
                    'f.dtinicial',
                    'f.dtfinal',
                    'f.dsmotivo',
                    'f.cid',
                ]);

            $base->unionAll($union);
        }

        return $base;
    }
}
