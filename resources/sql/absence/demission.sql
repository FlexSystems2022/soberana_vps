INSERT INTO nexti_ausencias_alterdata(NUMEMP, TIPCOL, NUMCAD, ABSENCESITUATIONEXTERNALID, FINISHDATETIME, STARTDATETIME, IDEXTERNO, TIPO, SITUACAO)
SELECT
    nexti_colaborador.NUMEMP,
    nexti_colaborador.TIPCOL,
    nexti_colaborador.NUMCAD,
    15671 AS ABSENCESITUATIONEXTERNALID,
    NULL as FINISHDATETIME,
    nexti_colaborador.DATADEM AS STARTDATETIME,
    CONCAT(
        'DM-', nexti_colaborador.NUMEMP, '-', nexti_colaborador.NUMCAD, '-',
        DATE_FORMAT(nexti_colaborador.DATADEM, '%Y-%m-%d')
    ) AS IDEXTERNO,
    0 AS TIPO,
    0 AS SITUACAO
FROM nexti_colaborador
WHERE nexti_colaborador.DATADEM IS NOT NULL
AND nexti_colaborador.SITFUN = 3
AND nexti_colaborador.TIPO <> 3
AND NOT EXISTS(
    SELECT
        1
    FROM nexti_ausencias_alterdata
    WHERE IDEXTERNO = CONCAT(
        'DM-',
        nexti_colaborador.NUMEMP, '-', nexti_colaborador.NUMCAD, '-',
        DATE_FORMAT(nexti_colaborador.DATADEM, '%Y-%m-%d')
    )
)