INSERT INTO nexti_troca_escala_alterdata(NUMEMP, TIPCOL, NUMCAD, DATALT, ESCALA, TURMA, IDEXTERNO)
SELECT
	nexti_colaborador.NUMEMP,
	nexti_colaborador.TIPCOL,
	nexti_colaborador.NUMCAD,
	nexti_colaborador.DATADM AS DATALT,
	224132 AS ESCALA,
	1 AS TURMA,
	CONCAT('TMP-', nexti_colaborador.NUMEMP, '-', nexti_colaborador.NUMCAD, '-', 224132) AS IDEXTERNO
FROM nexti_colaborador
WHERE NOT EXISTS(
	SELECT 
		1
	FROM nexti_troca_escala_alterdata
	WHERE nexti_troca_escala_alterdata.NUMEMP = nexti_colaborador.NUMEMP
	AND nexti_troca_escala_alterdata.TIPCOL = nexti_colaborador.TIPCOL
	AND nexti_troca_escala_alterdata.NUMCAD = nexti_colaborador.NUMCAD
)
AND nexti_colaborador.SITFUN = 1