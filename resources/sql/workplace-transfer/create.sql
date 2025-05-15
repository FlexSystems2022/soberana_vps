INSERT INTO nexti_troca_posto_alterdata(NUMEMP, TIPCOL, NUMCAD, INIATU, SEQHIS, POSTO, IDEXTERNO)
SELECT
	nexti_colaborador.NUMEMP,
	nexti_colaborador.TIPCOL,
	nexti_colaborador.NUMCAD,
	nexti_colaborador.DATADM AS INIATU,
	1 AS SEQHIS,
   nexti_posto.IDEXTERNO AS POSTO,
	CONCAT('TMP-', nexti_colaborador.NUMCAD, nexti_posto.IDEXTERNO) AS IDEXTERNO
FROM nexti_colaborador
JOIN nexti_posto
	ON(nexti_posto.POSTRA = nexti_colaborador.POSTO
		AND nexti_posto.NUMEMP = nexti_colaborador.NUMEMP
	)
WHERE NOT EXISTS(
	SELECT 
		1
	FROM nexti_troca_posto_alterdata
	WHERE nexti_troca_posto_alterdata.NUMEMP = nexti_colaborador.NUMEMP
	AND nexti_troca_posto_alterdata.TIPCOL = nexti_colaborador.TIPCOL
	AND nexti_troca_posto_alterdata.NUMCAD = nexti_colaborador.NUMCAD
)
AND nexti_colaborador.SITFUN = 1