<?php

namespace App\Console\Commands\Intermediary\Insert;

use App\Shared\DBPG;
use App\Models\Absence\AbsenceAux;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use App\Shared\Commands\CommandIntermediary;
use Illuminate\Support\Facades\DB;

class InsertAusencia extends CommandIntermediary
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'InsertAusencia';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
    	try {
			AbsenceAux::truncate();

    		return parent::handle();
    	} catch(\Throwable $e) {
    		$this->error($e->getMessage());

    		return static::FAILURE;
    	}
    }

    /**
     * Dispatch Item
     **/
    protected function dispachItem(object $ausencia): string
    {
		$campos = [
			'IDAFASTAMENTO' => $ausencia->idafastamento,
			'NUMEMP' => intval($ausencia->numemp),
			'TIPCOL' => 1,
			'NUMCAD' => $ausencia->matricula_esocial,
			'ABSENCESITUATIONEXTERNALID' => $ausencia->idexternosituacao,
			'FINISHDATETIME' => $ausencia->dtfinal,
			'STARTDATETIME' => $ausencia->dtinicial,
			'IDEXTERNO' => $ausencia->idafastamento . '-' . $ausencia->numemp,
			'CIDCODE' => $ausencia->cid,
			'CIDDESCRICAO' => null,
			'CIDID' => null,
			'DOUTOR_CRM' => null,
			'DOUTOR_NOME' => null,
			'DOUTOR_ID' => null,
			'OBSAFA' => $ausencia->dsmotivo,
		];

		$found = AbsenceAux::where('IDEXTERNO', $campos['IDEXTERNO'])->first();
		if($found) {
			return "Ausência {$campos['IDEXTERNO']} ignorada!";
		}

		AbsenceAux::create($campos);

		return "Ausência {$campos['IDEXTERNO']} Criado!";
    }

    /**
     * Busca ausencias no banco do cliente
     **/
   protected function buscaRegistros(): Collection
{
    $query = DB::connection('pgsql')
        ->table('wdp.a00001')
        ->select([
            'wdp.f00001.matricula_esocial',
            'wdp.a00001.idafastamento',
            'wdp.a00001.idespecial',
            'wdp.a00001.dtinicial',
            'wdp.a00001.dtfinal',
            'wdp.a00001.dsmotivo',
            'wdp.a00001.cid',
        ])
        ->selectRaw("'a00001' as origem")
        ->selectRaw("'f00001' as origem_func")
        ->selectRaw("'a00001' as numemp")
        ->join('wdp.f00001', 'wdp.f00001.idfuncionario', '=', 'wdp.a00001.idfuncionario')
        ->whereNull('wdp.f00001.dtdemissao')

        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00002')
                ->select([
                    'wdp.f00002.matricula_esocial',
                    'wdp.a00002.idafastamento',
                    'wdp.a00002.idespecial',
                    'wdp.a00002.dtinicial',
                    'wdp.a00002.dtfinal',
                    'wdp.a00002.dsmotivo',
                    'wdp.a00002.cid',
                ])
                ->selectRaw("'a00002' as origem")
                ->selectRaw("'f00002' as origem_func")
                ->selectRaw("'a00002' as numemp")
                ->join('wdp.f00002', 'wdp.f00002.idfuncionario', '=', 'wdp.a00002.idfuncionario')
                ->whereNull('wdp.f00002.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00003')
                ->select([
                    'wdp.f00003.matricula_esocial',
                    'wdp.a00003.idafastamento',
                    'wdp.a00003.idespecial',
                    'wdp.a00003.dtinicial',
                    'wdp.a00003.dtfinal',
                    'wdp.a00003.dsmotivo',
                    'wdp.a00003.cid',
                ])
                ->selectRaw("'a00003' as origem")
                ->selectRaw("'f00003' as origem_func")
                ->selectRaw("'a00003' as numemp")
                ->join('wdp.f00003', 'wdp.f00003.idfuncionario', '=', 'wdp.a00003.idfuncionario')
                ->whereNull('wdp.f00003.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00004')
                ->select([
                    'wdp.f00004.matricula_esocial',
                    'wdp.a00004.idafastamento',
                    'wdp.a00004.idespecial',
                    'wdp.a00004.dtinicial',
                    'wdp.a00004.dtfinal',
                    'wdp.a00004.dsmotivo',
                    'wdp.a00004.cid',
                ])
                ->selectRaw("'a00004' as origem")
                ->selectRaw("'f00004' as origem_func")
                ->selectRaw("'a00004' as numemp")
                ->join('wdp.f00004', 'wdp.f00004.idfuncionario', '=', 'wdp.a00004.idfuncionario')
                ->whereNull('wdp.f00004.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00005')
                ->select([
                    'wdp.f00005.matricula_esocial',
                    'wdp.a00005.idafastamento',
                    'wdp.a00005.idespecial',
                    'wdp.a00005.dtinicial',
                    'wdp.a00005.dtfinal',
                    'wdp.a00005.dsmotivo',
                    'wdp.a00005.cid',
                ])
                ->selectRaw("'a00005' as origem")
                ->selectRaw("'f00005' as origem_func")
                ->selectRaw("'a00005' as numemp")
                ->join('wdp.f00005', 'wdp.f00005.idfuncionario', '=', 'wdp.a00005.idfuncionario')
                ->whereNull('wdp.f00005.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00006')
                ->select([
                    'wdp.f00006.matricula_esocial',
                    'wdp.a00006.idafastamento',
                    'wdp.a00006.idespecial',
                    'wdp.a00006.dtinicial',
                    'wdp.a00006.dtfinal',
                    'wdp.a00006.dsmotivo',
                    'wdp.a00006.cid',
                ])
                ->selectRaw("'a00006' as origem")
                ->selectRaw("'f00006' as origem_func")
                ->selectRaw("'a00006' as numemp")
                ->join('wdp.f00006', 'wdp.f00006.idfuncionario', '=', 'wdp.a00006.idfuncionario')
                ->whereNull('wdp.f00006.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00007')
                ->select([
                    'wdp.f00007.matricula_esocial',
                    'wdp.a00007.idafastamento',
                    'wdp.a00007.idespecial',
                    'wdp.a00007.dtinicial',
                    'wdp.a00007.dtfinal',
                    'wdp.a00007.dsmotivo',
                    'wdp.a00007.cid',
                ])
                ->selectRaw("'a00007' as origem")
                ->selectRaw("'f00007' as origem_func")
                ->selectRaw("'a00007' as numemp")
                ->join('wdp.f00007', 'wdp.f00007.idfuncionario', '=', 'wdp.a00007.idfuncionario')
                ->whereNull('wdp.f00007.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00008')
                ->select([
                    'wdp.f00008.matricula_esocial',
                    'wdp.a00008.idafastamento',
                    'wdp.a00008.idespecial',
                    'wdp.a00008.dtinicial',
                    'wdp.a00008.dtfinal',
                    'wdp.a00008.dsmotivo',
                    'wdp.a00008.cid',
                ])
                ->selectRaw("'a00008' as origem")
                ->selectRaw("'f00008' as origem_func")
                ->selectRaw("'a00008' as numemp")
                ->join('wdp.f00008', 'wdp.f00008.idfuncionario', '=', 'wdp.a00008.idfuncionario')
                ->whereNull('wdp.f00008.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00009')
                ->select([
                    'wdp.f00009.matricula_esocial',
                    'wdp.a00009.idafastamento',
                    'wdp.a00009.idespecial',
                    'wdp.a00009.dtinicial',
                    'wdp.a00009.dtfinal',
                    'wdp.a00009.dsmotivo',
                    'wdp.a00009.cid',
                ])
                ->selectRaw("'a00009' as origem")
                ->selectRaw("'f00009' as origem_func")
                ->selectRaw("'a00009' as numemp")
                ->join('wdp.f00009', 'wdp.f00009.idfuncionario', '=', 'wdp.a00009.idfuncionario')
                ->whereNull('wdp.f00009.dtdemissao')
        )
        // Agora as tabelas avulsas (não sequenciais)
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00014')
                ->select([
                    'wdp.f00014.matricula_esocial',
                    'wdp.a00014.idafastamento',
                    'wdp.a00014.idespecial',
                    'wdp.a00014.dtinicial',
                    'wdp.a00014.dtfinal',
                    'wdp.a00014.dsmotivo',
                    'wdp.a00014.cid',
                ])
                ->selectRaw("'a00014' as origem")
                ->selectRaw("'f0014' as origem_func")
                ->selectRaw("'a00014' as numemp")
                ->join('wdp.f0014', 'wdp.f0014.idfuncionario', '=', 'wdp.a00014.idfuncionario')
                ->whereNull('wdp.f0014.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a0017')
                ->select([
                    'wdp.f00017.matricula_esocial',
                    'wdp.a00017.idafastamento',
                    'wdp.a00017.idespecial',
                    'wdp.a00017.dtinicial',
                    'wdp.a00017.dtfinal',
                    'wdp.a00017.dsmotivo',
                    'wdp.a00017.cid',
                ])
                ->selectRaw("'a00017' as origem")
                ->selectRaw("'f00017' as origem_func")
                ->selectRaw("'a00017' as numemp")
                ->join('wdp.f00017', 'wdp.f00017.idfuncionario', '=', 'wdp.a00017.idfuncionario')
                ->whereNull('wdp.f00017.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a00020')
                ->select([
                    'wdp.f00020.matricula_esocial',
                    'wdp.a00020.idafastamento',
                    'wdp.a00020.idespecial',
                    'wdp.a00020.dtinicial',
                    'wdp.a00020.dtfinal',
                    'wdp.a00020.dsmotivo',
                    'wdp.a00020.cid',
                ])
                ->selectRaw("'a00020' as origem")
                ->selectRaw("'f00020' as origem_func")
                ->selectRaw("'a00020' as numemp")
                ->join('wdp.f00020', 'wdp.f00020.idfuncionario', '=', 'wdp.a00020.idfuncionario')
                ->whereNull('wdp.f00020.dtdemissao')
        )
        // Agora sequência a1000 até a01011
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01000')
                ->select([
                    'wdp.f01000.matricula_esocial',
                    'wdp.a01000.idafastamento',
                    'wdp.a01000.idespecial',
                    'wdp.a01000.dtinicial',
                    'wdp.a01000.dtfinal',
                    'wdp.a01000.dsmotivo',
                    'wdp.a01000.cid',
                ])
                ->selectRaw("'a01000' as origem")
                ->selectRaw("'f01000' as origem_func")
                ->selectRaw("'a01000' as numemp")
                ->join('wdp.f01000', 'wdp.f01000.idfuncionario', '=', 'wdp.a01000.idfuncionario')
                ->whereNull('wdp.f01000.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01001')
                ->select([
                    'wdp.f01001.matricula_esocial',
                    'wdp.a01001.idafastamento',
                    'wdp.a01001.idespecial',
                    'wdp.a01001.dtinicial',
                    'wdp.a01001.dtfinal',
                    'wdp.a01001.dsmotivo',
                    'wdp.a01001.cid',
                ])
                ->selectRaw("'a01001' as origem")
                ->selectRaw("'f01001' as origem_func")
                ->selectRaw("'a01001' as numemp")
                ->join('wdp.f01001', 'wdp.f01001.idfuncionario', '=', 'wdp.a01001.idfuncionario')
                ->whereNull('wdp.f01001.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01002')
                ->select([
                    'wdp.f01002.matricula_esocial',
                    'wdp.a01002.idafastamento',
                    'wdp.a01002.idespecial',
                    'wdp.a01002.dtinicial',
                    'wdp.a01002.dtfinal',
                    'wdp.a01002.dsmotivo',
                    'wdp.a01002.cid',
                ])
                ->selectRaw("'a01002' as origem")
                ->selectRaw("'f01002' as origem_func")
                ->selectRaw("'a01002' as numemp")
                ->join('wdp.f01002', 'wdp.f01002.idfuncionario', '=', 'wdp.a01002.idfuncionario')
                ->whereNull('wdp.f01002.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01003')
                ->select([
                    'wdp.f01003.matricula_esocial',
                    'wdp.a01003.idafastamento',
                    'wdp.a01003.idespecial',
                    'wdp.a01003.dtinicial',
                    'wdp.a01003.dtfinal',
                    'wdp.a01003.dsmotivo',
                    'wdp.a01003.cid',
                ])
                ->selectRaw("'a01003' as origem")
                ->selectRaw("'f01003' as origem_func")
                ->selectRaw("'a01003' as numemp")
                ->join('wdp.f01003', 'wdp.f01003.idfuncionario', '=', 'wdp.a01003.idfuncionario')
                ->whereNull('wdp.f01003.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01004')
                ->select([
                    'wdp.f01004.matricula_esocial',
                    'wdp.a01004.idafastamento',
                    'wdp.a01004.idespecial',
                    'wdp.a01004.dtinicial',
                    'wdp.a01004.dtfinal',
                    'wdp.a01004.dsmotivo',
                    'wdp.a01004.cid',
                ])
                ->selectRaw("'a01004' as origem")
                ->selectRaw("'f01004' as origem_func")
                ->selectRaw("'a01004' as numemp")
                ->join('wdp.f01004', 'wdp.f01004.idfuncionario', '=', 'wdp.a01004.idfuncionario')
                ->whereNull('wdp.f01004.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01005')
                ->select([
                    'wdp.f01005.matricula_esocial',
                    'wdp.a01005.idafastamento',
                    'wdp.a01005.idespecial',
                    'wdp.a01005.dtinicial',
                    'wdp.a01005.dtfinal',
                    'wdp.a01005.dsmotivo',
                    'wdp.a01005.cid',
                ])
                ->selectRaw("'a01005' as origem")
                ->selectRaw("'f01005' as origem_func")
                ->selectRaw("'a01005' as numemp")
                ->join('wdp.f01005', 'wdp.f01005.idfuncionario', '=', 'wdp.a01005.idfuncionario')
                ->whereNull('wdp.f01005.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01006')
                ->select([
                    'wdp.f01006.matricula_esocial',
                    'wdp.a01006.idafastamento',
                    'wdp.a01006.idespecial',
                    'wdp.a01006.dtinicial',
                    'wdp.a01006.dtfinal',
                    'wdp.a01006.dsmotivo',
                    'wdp.a01006.cid',
                ])
                ->selectRaw("'a01006' as origem")
                ->selectRaw("'f01006' as origem_func")
                ->selectRaw("'a01006' as numemp")
                ->join('wdp.f01006', 'wdp.f01006.idfuncionario', '=', 'wdp.a01006.idfuncionario')
                ->whereNull('wdp.f01006.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01007')
                ->select([
                    'wdp.f01007.matricula_esocial',
                    'wdp.a01007.idafastamento',
                    'wdp.a01007.idespecial',
                    'wdp.a01007.dtinicial',
                    'wdp.a01007.dtfinal',
                    'wdp.a01007.dsmotivo',
                    'wdp.a01007.cid',
                ])
                ->selectRaw("'a01007' as origem")
                ->selectRaw("'f01007' as origem_func")
                ->selectRaw("'a01007' as numemp")
                ->join('wdp.f01007', 'wdp.f01007.idfuncionario', '=', 'wdp.a01007.idfuncionario')
                ->whereNull('wdp.f01007.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01008')
                ->select([
                    'wdp.f01008.matricula_esocial',
                    'wdp.a01008.idafastamento',
                    'wdp.a01008.idespecial',
                    'wdp.a01008.dtinicial',
                    'wdp.a01008.dtfinal',
                    'wdp.a01008.dsmotivo',
                    'wdp.a01008.cid',
                ])
                ->selectRaw("'a01008' as origem")
                ->selectRaw("'f01008' as origem_func")
                ->selectRaw("'a01008' as numemp")
                ->join('wdp.f01008', 'wdp.f01008.idfuncionario', '=', 'wdp.a01008.idfuncionario')
                ->whereNull('wdp.f01008.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01009')
                ->select([
                    'wdp.f01009.matricula_esocial',
                    'wdp.a01009.idafastamento',
                    'wdp.a01009.idespecial',
                    'wdp.a01009.dtinicial',
                    'wdp.a01009.dtfinal',
                    'wdp.a01009.dsmotivo',
                    'wdp.a01009.cid',
                ])
                ->selectRaw("'a01009' as origem")
                ->selectRaw("'f01009' as origem_func")
                ->selectRaw("'a01009' as numemp")
                ->join('wdp.f01009', 'wdp.f01009.idfuncionario', '=', 'wdp.a01009.idfuncionario')
                ->whereNull('wdp.f01009.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01010')
                ->select([
                    'wdp.f01010.matricula_esocial',
                    'wdp.a01010.idafastamento',
                    'wdp.a01010.idespecial',
                    'wdp.a01010.dtinicial',
                    'wdp.a01010.dtfinal',
                    'wdp.a01010.dsmotivo',
                    'wdp.a01010.cid',
                ])
                ->selectRaw("'a01010' as origem")
                ->selectRaw("'f01010' as origem_func")
                ->selectRaw("'a01010' as numemp")
                ->join('wdp.f01010', 'wdp.f01010.idfuncionario', '=', 'wdp.a01010.idfuncionario')
                ->whereNull('wdp.f01010.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a01011')
                ->select([
                    'wdp.f01011.matricula_esocial',
                    'wdp.a01011.idafastamento',
                    'wdp.a01011.idespecial',
                    'wdp.a01011.dtinicial',
                    'wdp.a01011.dtfinal',
                    'wdp.a01011.dsmotivo',
                    'wdp.a01011.cid',
                ])
                ->selectRaw("'a01011' as origem")
                ->selectRaw("'f01011' as origem_func")
                ->selectRaw("'a01011' as numemp")
                ->join('wdp.f01011', 'wdp.f01011.idfuncionario', '=', 'wdp.a01011.idfuncionario')
                ->whereNull('wdp.f01011.dtdemissao')
        )
        // Agora a sequência final de a02021 até a02047 (assumindo sequenciais)
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02021')
                ->select([
                    'wdp.f02021.matricula_esocial',
                    'wdp.a02021.idafastamento',
                    'wdp.a02021.idespecial',
                    'wdp.a02021.dtinicial',
                    'wdp.a02021.dtfinal',
                    'wdp.a02021.dsmotivo',
                    'wdp.a02021.cid',
                ])
                ->selectRaw("'a02021' as origem")
                ->selectRaw("'f02021' as origem_func")
                ->selectRaw("'a02021' as numemp")
                ->join('wdp.f02021', 'wdp.f02021.idfuncionario', '=', 'wdp.a02021.idfuncionario')
                ->whereNull('wdp.f02021.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02022')
                ->select([
                    'wdp.f02022.matricula_esocial',
                    'wdp.a02022.idafastamento',
                    'wdp.a02022.idespecial',
                    'wdp.a02022.dtinicial',
                    'wdp.a02022.dtfinal',
                    'wdp.a02022.dsmotivo',
                    'wdp.a02022.cid',
                ])
                ->selectRaw("'a02022' as origem")
                ->selectRaw("'f02022' as origem_func")
                ->selectRaw("'a02022' as numemp")
                ->join('wdp.f02022', 'wdp.f02022.idfuncionario', '=', 'wdp.a02022.idfuncionario')
                ->whereNull('wdp.f02022.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02023')
                ->select([
                    'wdp.f02023.matricula_esocial',
                    'wdp.a02023.idafastamento',
                    'wdp.a02023.idespecial',
                    'wdp.a02023.dtinicial',
                    'wdp.a02023.dtfinal',
                    'wdp.a02023.dsmotivo',
                    'wdp.a02023.cid',
                ])
                ->selectRaw("'a02023' as origem")
                ->selectRaw("'f02023' as origem_func")
                ->selectRaw("'a02023' as numemp")
                ->join('wdp.f02023', 'wdp.f02023.idfuncionario', '=', 'wdp.a02023.idfuncionario')
                ->whereNull('wdp.f02023.dtdemissao')
        )
		->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02024')
                ->select([
                    'wdp.f02024.matricula_esocial',
                    'wdp.a02024.idafastamento',
                    'wdp.a02024.idespecial',
                    'wdp.a02024.dtinicial',
                    'wdp.a02024.dtfinal',
                    'wdp.a02024.dsmotivo',
                    'wdp.a02024.cid',
                ])
                ->selectRaw("'a02024' as origem")
                ->selectRaw("'f02024' as origem_func")
                ->selectRaw("'a02024' as numemp")
                ->join('wdp.f02024', 'wdp.f02024.idfuncionario', '=', 'wdp.a02024.idfuncionario')
                ->whereNull('wdp.f02024.dtdemissao')
        )
	    ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02025')
                ->select([
                    'wdp.f02025.matricula_esocial',
                    'wdp.a02025.idafastamento',
                    'wdp.a02025.idespecial',
                    'wdp.a02025.dtinicial',
                    'wdp.a02025.dtfinal',
                    'wdp.a02025.dsmotivo',
                    'wdp.a02025.cid',
                ])
                ->selectRaw("'a02025' as origem")
                ->selectRaw("'f02025' as origem_func")
                ->selectRaw("'a02025' as numemp")
                ->join('wdp.f02025', 'wdp.f02025.idfuncionario', '=', 'wdp.a02025.idfuncionario')
                ->whereNull('wdp.f02025.dtdemissao')
        )
	    ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02026')
                ->select([
                    'wdp.f02026.matricula_esocial',
                    'wdp.a02026.idafastamento',
                    'wdp.a02026.idespecial',
                    'wdp.a02026.dtinicial',
                    'wdp.a02026.dtfinal',
                    'wdp.a02026.dsmotivo',
                    'wdp.a02026.cid',
                ])
                ->selectRaw("'a02026' as origem")
                ->selectRaw("'f02026' as origem_func")
                ->selectRaw("'a02026' as numemp")
                ->join('wdp.f02026', 'wdp.f02026.idfuncionario', '=', 'wdp.a02026.idfuncionario')
                ->whereNull('wdp.f02026.dtdemissao')
        )
	    ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02027')
                ->select([
                    'wdp.f02027.matricula_esocial',
                    'wdp.a02027.idafastamento',
                    'wdp.a02027.idespecial',
                    'wdp.a02027.dtinicial',
                    'wdp.a02027.dtfinal',
                    'wdp.a02027.dsmotivo',
                    'wdp.a02027.cid',
                ])
                ->selectRaw("'a02027' as origem")
                ->selectRaw("'f02027' as origem_func")
                ->selectRaw("'a02027' as numemp")
                ->join('wdp.f02027', 'wdp.f02027.idfuncionario', '=', 'wdp.a02027.idfuncionario')
                ->whereNull('wdp.f02027.dtdemissao')
        )
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02028')
                ->select([
                    'wdp.f02028.matricula_esocial',
                    'wdp.a02028.idafastamento',
                    'wdp.a02028.idespecial',
                    'wdp.a02028.dtinicial',
                    'wdp.a02028.dtfinal',
                    'wdp.a02028.dsmotivo',
                    'wdp.a02028.cid',
                ])
                ->selectRaw("'a02028' as origem")
                ->selectRaw("'f02028' as origem_func")
                ->selectRaw("'a02028' as numemp")
                ->join('wdp.f02028', 'wdp.f02028.idfuncionario', '=', 'wdp.a02028.idfuncionario')
                ->whereNull('wdp.f02028.dtdemissao')
        )
		->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02029')
                ->select([
                    'wdp.f02029.matricula_esocial',
                    'wdp.a02029.idafastamento',
                    'wdp.a02029.idespecial',
                    'wdp.a02029.dtinicial',
                    'wdp.a02029.dtfinal',
                    'wdp.a02029.dsmotivo',
                    'wdp.a02029.cid',
                ])
                ->selectRaw("'a02029' as origem")
                ->selectRaw("'f02029' as origem_func")
                ->selectRaw("'a02029' as numemp")
                ->join('wdp.f02029', 'wdp.f02029.idfuncionario', '=', 'wdp.a02029.idfuncionario')
                ->whereNull('wdp.f02029.dtdemissao')
        )
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02030')
				->select([
					'wdp.f02030.matricula_esocial',
					'wdp.a02030.idafastamento',
					'wdp.a02030.idespecial',
					'wdp.a02030.dtinicial',
					'wdp.a02030.dtfinal',
					'wdp.a02030.dsmotivo',
					'wdp.a02030.cid',
				])
				->selectRaw("'a02030' as origem")
				->selectRaw("'f02030' as origem_func")
				->selectRaw("'a02030' as numemp")
				->join('wdp.f02030', 'wdp.f02030.idfuncionario', '=', 'wdp.a02030.idfuncionario')
				->whereNull('wdp.f02030.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02031')
				->select([
					'wdp.f02031.matricula_esocial',
					'wdp.a02031.idafastamento',
					'wdp.a02031.idespecial',
					'wdp.a02031.dtinicial',
					'wdp.a02031.dtfinal',
					'wdp.a02031.dsmotivo',
					'wdp.a02031.cid',
				])
				->selectRaw("'a02031' as origem")
				->selectRaw("'f02031' as origem_func")
				->selectRaw("'a02031' as numemp")
				->join('wdp.f02031', 'wdp.f02031.idfuncionario', '=', 'wdp.a02031.idfuncionario')
				->whereNull('wdp.f02031.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02032')
				->select([
					'wdp.f02032.matricula_esocial',
					'wdp.a02032.idafastamento',
					'wdp.a02032.idespecial',
					'wdp.a02032.dtinicial',
					'wdp.a02032.dtfinal',
					'wdp.a02032.dsmotivo',
					'wdp.a02032.cid',
				])
				->selectRaw("'a02032' as origem")
				->selectRaw("'f02032' as origem_func")
				->selectRaw("'a02032' as numemp")
				->join('wdp.f02032', 'wdp.f02032.idfuncionario', '=', 'wdp.a02032.idfuncionario')
				->whereNull('wdp.f02032.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02033')
				->select([
					'wdp.f02033.matricula_esocial',
					'wdp.a02033.idafastamento',
					'wdp.a02033.idespecial',
					'wdp.a02033.dtinicial',
					'wdp.a02033.dtfinal',
					'wdp.a02033.dsmotivo',
					'wdp.a02033.cid',
				])
				->selectRaw("'a02033' as origem")
				->selectRaw("'f02033' as origem_func")
				->selectRaw("'a02033' as numemp")
				->join('wdp.f02033', 'wdp.f02033.idfuncionario', '=', 'wdp.a02033.idfuncionario')
				->whereNull('wdp.f02033.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02034')
				->select([
					'wdp.f02034.matricula_esocial',
					'wdp.a02034.idafastamento',
					'wdp.a02034.idespecial',
					'wdp.a02034.dtinicial',
					'wdp.a02034.dtfinal',
					'wdp.a02034.dsmotivo',
					'wdp.a02034.cid',
				])
				->selectRaw("'a02034' as origem")
				->selectRaw("'f02034' as origem_func")
				->selectRaw("'a02034' as numemp")
				->join('wdp.f02034', 'wdp.f02034.idfuncionario', '=', 'wdp.a02034.idfuncionario')
				->whereNull('wdp.f02034.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02035')
				->select([
					'wdp.f02035.matricula_esocial',
					'wdp.a02035.idafastamento',
					'wdp.a02035.idespecial',
					'wdp.a02035.dtinicial',
					'wdp.a02035.dtfinal',
					'wdp.a02035.dsmotivo',
					'wdp.a02035.cid',
				])
				->selectRaw("'a02035' as origem")
				->selectRaw("'f02035' as origem_func")
				->selectRaw("'a02035' as numemp")
				->join('wdp.f02035', 'wdp.f02035.idfuncionario', '=', 'wdp.a02035.idfuncionario')
				->whereNull('wdp.f02035.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02036')
				->select([
					'wdp.f02036.matricula_esocial',
					'wdp.a02036.idafastamento',
					'wdp.a02036.idespecial',
					'wdp.a02036.dtinicial',
					'wdp.a02036.dtfinal',
					'wdp.a02036.dsmotivo',
					'wdp.a02036.cid',
				])
				->selectRaw("'a02036' as origem")
				->selectRaw("'f02036' as origem_func")
				->selectRaw("'a02036' as numemp")
				->join('wdp.f02036', 'wdp.f02036.idfuncionario', '=', 'wdp.a02036.idfuncionario')
				->whereNull('wdp.f02036.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02037')
				->select([
					'wdp.f02037.matricula_esocial',
					'wdp.a02037.idafastamento',
					'wdp.a02037.idespecial',
					'wdp.a02037.dtinicial',
					'wdp.a02037.dtfinal',
					'wdp.a02037.dsmotivo',
					'wdp.a02037.cid',
				])
				->selectRaw("'a02037' as origem")
				->selectRaw("'f02037' as origem_func")
				->selectRaw("'a02037' as numemp")
				->join('wdp.f02037', 'wdp.f02037.idfuncionario', '=', 'wdp.a02037.idfuncionario')
				->whereNull('wdp.f02037.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02038')
				->select([
					'wdp.f02038.matricula_esocial',
					'wdp.a02038.idafastamento',
					'wdp.a02038.idespecial',
					'wdp.a02038.dtinicial',
					'wdp.a02038.dtfinal',
					'wdp.a02038.dsmotivo',
					'wdp.a02038.cid',
				])
				->selectRaw("'a02038' as origem")
				->selectRaw("'f02038' as origem_func")
				->selectRaw("'a02038' as numemp")
				->join('wdp.f02038', 'wdp.f02038.idfuncionario', '=', 'wdp.a02038.idfuncionario')
				->whereNull('wdp.f02038.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02039')
				->select([
					'wdp.f02039.matricula_esocial',
					'wdp.a02039.idafastamento',
					'wdp.a02039.idespecial',
					'wdp.a02039.dtinicial',
					'wdp.a02039.dtfinal',
					'wdp.a02039.dsmotivo',
					'wdp.a02039.cid',
				])
				->selectRaw("'a02039' as origem")
				->selectRaw("'f02039' as origem_func")
				->selectRaw("'a02039' as numemp")
				->join('wdp.f02039', 'wdp.f02039.idfuncionario', '=', 'wdp.a02039.idfuncionario')
				->whereNull('wdp.f02039.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02040')
				->select([
					'wdp.f02040.matricula_esocial',
					'wdp.a02040.idafastamento',
					'wdp.a02040.idespecial',
					'wdp.a02040.dtinicial',
					'wdp.a02040.dtfinal',
					'wdp.a02040.dsmotivo',
					'wdp.a02040.cid',
				])
				->selectRaw("'a02040' as origem")
				->selectRaw("'f02040' as origem_func")
				->selectRaw("'a02040' as numemp")
				->join('wdp.f02040', 'wdp.f02040.idfuncionario', '=', 'wdp.a02040.idfuncionario')
				->whereNull('wdp.f02040.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02041')
				->select([
					'wdp.f02041.matricula_esocial',
					'wdp.a02041.idafastamento',
					'wdp.a02041.idespecial',
					'wdp.a02041.dtinicial',
					'wdp.a02041.dtfinal',
					'wdp.a02041.dsmotivo',
					'wdp.a02041.cid',
				])
				->selectRaw("'a02041' as origem")
				->selectRaw("'f02041' as origem_func")
				->selectRaw("'a02041' as numemp")
				->join('wdp.f02041', 'wdp.f02041.idfuncionario', '=', 'wdp.a02041.idfuncionario')
				->whereNull('wdp.f02041.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02042')
				->select([
					'wdp.f02042.matricula_esocial',
					'wdp.a02042.idafastamento',
					'wdp.a02042.idespecial',
					'wdp.a02042.dtinicial',
					'wdp.a02042.dtfinal',
					'wdp.a02042.dsmotivo',
					'wdp.a02042.cid',
				])
				->selectRaw("'a02042' as origem")
				->selectRaw("'f02042' as origem_func")
				->selectRaw("'a02042' as numemp")
				->join('wdp.f02042', 'wdp.f02042.idfuncionario', '=', 'wdp.a02042.idfuncionario')
				->whereNull('wdp.f02042.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02043')
				->select([
					'wdp.f02043.matricula_esocial',
					'wdp.a02043.idafastamento',
					'wdp.a02043.idespecial',
					'wdp.a02043.dtinicial',
					'wdp.a02043.dtfinal',
					'wdp.a02043.dsmotivo',
					'wdp.a02043.cid',
				])
				->selectRaw("'a02043' as origem")
				->selectRaw("'f02043' as origem_func")
				->selectRaw("'a02043' as numemp")
				->join('wdp.f02043', 'wdp.f02043.idfuncionario', '=', 'wdp.a02043.idfuncionario')
				->whereNull('wdp.f02043.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02044')
				->select([
					'wdp.f02044.matricula_esocial',
					'wdp.a02044.idafastamento',
					'wdp.a02044.idespecial',
					'wdp.a02044.dtinicial',
					'wdp.a02044.dtfinal',
					'wdp.a02044.dsmotivo',
					'wdp.a02044.cid',
				])
				->selectRaw("'a02044' as origem")
				->selectRaw("'f02044' as origem_func")
				->selectRaw("'a02044' as numemp")
				->join('wdp.f02044', 'wdp.f02044.idfuncionario', '=', 'wdp.a02044.idfuncionario')
				->whereNull('wdp.f02044.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02045')
				->select([
					'wdp.f02045.matricula_esocial',
					'wdp.a02045.idafastamento',
					'wdp.a02045.idespecial',
					'wdp.a02045.dtinicial',
					'wdp.a02045.dtfinal',
					'wdp.a02045.dsmotivo',
					'wdp.a02045.cid',
				])
				->selectRaw("'a02045' as origem")
				->selectRaw("'f02045' as origem_func")
				->selectRaw("'a02045' as numemp")
				->join('wdp.f02045', 'wdp.f02045.idfuncionario', '=', 'wdp.a02045.idfuncionario')
				->whereNull('wdp.f02045.dtdemissao')
		)
		->unionAll(fn(Builder $union) =>
			$union->from('wdp.a02046')
				->select([
					'wdp.f02046.matricula_esocial',
					'wdp.a02046.idafastamento',
					'wdp.a02046.idespecial',
					'wdp.a02046.dtinicial',
					'wdp.a02046.dtfinal',
					'wdp.a02046.dsmotivo',
					'wdp.a02046.cid',
				])
				->selectRaw("'a02046' as origem")
				->selectRaw("'f02046' as origem_func")
				->selectRaw("'a02046' as numemp")
				->join('wdp.f02046', 'wdp.f02046.idfuncionario', '=', 'wdp.a02046.idfuncionario')
				->whereNull('wdp.f02046.dtdemissao')
		)
        ->unionAll(fn(Builder $union) =>
            $union->from('wdp.a02047')
                ->select([
                    'wdp.f02047.matricula_esocial',
                    'wdp.a02047.idafastamento',
                    'wdp.a02047.idespecial',
                    'wdp.a02047.dtinicial',
                    'wdp.a02047.dtfinal',
                    'wdp.a02047.dsmotivo',
                    'wdp.a02047.cid',
                ])
                ->selectRaw("'a02047' as origem")
                ->selectRaw("'f02047' as origem_func")
                ->selectRaw("'a02047' as numemp")
                ->join('wdp.f02047', 'wdp.f02047.idfuncionario', '=', 'wdp.a02047.idfuncionario')
                ->whereNull('wdp.f02047.dtdemissao')
        );

    // Depois do union de tudo, você pode fazer o join para buscar a descrição da especial:
    $ausencias = DB::connection('pgsql')
        ->table(DB::raw("({$query->toSql()}) as ausencias"))
        ->mergeBindings($query)
        ->join('wdp.especial as esp', 'esp.idespecial', '=', 'ausencias.idespecial')
        ->select([
            'ausencias.numemp',
            'ausencias.matricula_esocial',
            'ausencias.idafastamento',
            'ausencias.dtinicial',
            'ausencias.dtfinal',
            'ausencias.dsmotivo',
            'ausencias.cid',
            'esp.cdchamada as idexternosituacao',
        ])
        ->get();

    return $ausencias;
}

    /**
     * After execute
     */
    protected function afterExecute(): void
    {
    	$this->createAbsences();
    	$this->updateAbsences();
    	$this->deleteAbsences();
    }

    /**
     * Create Absences
     */
    private function createAbsences(): void
    {
    	$query = "
    		INSERT INTO nexti_ausencias_alterdata (IDAFASTAMENTO, NUMEMP, TIPCOL, NUMCAD, ABSENCESITUATIONEXTERNALID, FINISHDATETIME, STARTDATETIME, TIPO, SITUACAO, ID, IDEXTERNO, CIDCODE, CIDDESCRICAO, CIDID, DOUTOR_CRM, DOUTOR_NOME, DOUTOR_ID, OBSAFA)
			SELECT 
			   nexti_ausencias_aux.IDAFASTAMENTO,
			   nexti_ausencias_aux.NUMEMP,
			   nexti_ausencias_aux.TIPCOL,
			   nexti_ausencias_aux.NUMCAD,
			   nexti_ausencias_aux.ABSENCESITUATIONEXTERNALID,
			   nexti_ausencias_aux.FINISHDATETIME,
			   nexti_ausencias_aux.STARTDATETIME,
			   0 AS TIPO,
			   0 AS SITUACAO,
			   0 AS ID,
			   nexti_ausencias_aux.IDEXTERNO,
			   nexti_ausencias_aux.CIDCODE,
			   nexti_ausencias_aux.CIDDESCRICAO,
			   nexti_ausencias_aux.CIDID,
			   nexti_ausencias_aux.DOUTOR_CRM,
			   nexti_ausencias_aux.DOUTOR_NOME,
			   nexti_ausencias_aux.DOUTOR_ID,
			   nexti_ausencias_aux.OBSAFA
			FROM nexti_ausencias_aux
			WHERE NOT EXISTS(
			    SELECT
				 	1
				FROM nexti_ausencias_alterdata
			    WHERE nexti_ausencias_alterdata.IDEXTERNO = nexti_ausencias_aux.IDEXTERNO
			)
    	";

        DB::statement($query);
    }

    /**
     * Update Absence
     */
    private function updateAbsences(): void
    {
    	$query = "
			UPDATE nexti_ausencias_alterdata
			JOIN nexti_ausencias_aux
				ON(nexti_ausencias_aux.IDEXTERNO = nexti_ausencias_alterdata.IDEXTERNO
					AND (nexti_ausencias_aux.ABSENCESITUATIONEXTERNALID <> nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID
						OR nexti_ausencias_aux.FINISHDATETIME <> nexti_ausencias_alterdata.FINISHDATETIME
						OR nexti_ausencias_aux.STARTDATETIME <> nexti_ausencias_alterdata.STARTDATETIME
					)
				)
				SET nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID = nexti_ausencias_aux.ABSENCESITUATIONEXTERNALID,
					nexti_ausencias_alterdata.FINISHDATETIME = nexti_ausencias_aux.FINISHDATETIME,
					nexti_ausencias_alterdata.STARTDATETIME = nexti_ausencias_aux.STARTDATETIME,
					nexti_ausencias_alterdata.SITUACAO = 0,
					nexti_ausencias_alterdata.TIPO = 1
			WHERE nexti_ausencias_alterdata.TIPO <> 3
			AND nexti_ausencias_alterdata.SITUACAO = 1
    	";

        DB::statement($query);
    }

    /**
     * Delete Absence
     */
    private function deleteAbsences(): void
    {
    	$query = "
			UPDATE nexti_ausencias_alterdata
			SET nexti_ausencias_alterdata.TIPO = 3,
				nexti_ausencias_alterdata.SITUACAO = 0
			WHERE NOT EXISTS(
				SELECT
					1
				FROM nexti_ausencias_aux
				WHERE nexti_ausencias_aux.IDEXTERNO = nexti_ausencias_alterdata.IDEXTERNO
			)
			AND EXISTS(
				SELECT 
					1
				FROM nexti_colaborador
				WHERE nexti_colaborador.NUMEMP = nexti_ausencias_alterdata.NUMEMP
				AND nexti_colaborador.TIPCOL = nexti_ausencias_alterdata.TIPCOL
				AND nexti_colaborador.NUMCAD = nexti_ausencias_alterdata.NUMCAD
				AND nexti_colaborador.DATADEM IS NULL
			)
			AND nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID <> '15671'
			AND nexti_ausencias_alterdata.TIPO <> 3
			AND nexti_ausencias_alterdata.ID IS NOT NULL
			AND nexti_ausencias_alterdata.SITUACAO = 1
    	";

        DB::statement($query);

    	$query = "
			UPDATE nexti_ausencias_alterdata
			SET nexti_ausencias_alterdata.TIPO = 3,
				nexti_ausencias_alterdata.SITUACAO = 1,
				nexti_ausencias_alterdata.OBSERVACAO = 'Exclusão via merge'
			WHERE NOT EXISTS(
				SELECT
					1
				FROM nexti_ausencias_aux
				WHERE nexti_ausencias_aux.IDEXTERNO = nexti_ausencias_alterdata.IDEXTERNO
			)
			AND EXISTS(
				SELECT 
					1
				FROM nexti_colaborador
				WHERE nexti_colaborador.NUMEMP = nexti_ausencias_alterdata.NUMEMP
				AND nexti_colaborador.TIPCOL = nexti_ausencias_alterdata.TIPCOL
				AND nexti_colaborador.NUMCAD = nexti_ausencias_alterdata.NUMCAD
				AND nexti_colaborador.DATADEM IS NULL
			)
			AND nexti_ausencias_alterdata.ABSENCESITUATIONEXTERNALID <> '15671'
			AND nexti_ausencias_alterdata.TIPO = 0
			AND nexti_ausencias_alterdata.SITUACAO = 0
    	";

        DB::statement($query);
    }
}