UPDATE nexti_contra_cheque
SET nexti_contra_cheque.tipo = 1,
	nexti_contra_cheque.situacao = 0
WHERE EXISTS(
	SELECT
		1
	FROM nexti_contra_cheque_eventos
	WHERE nexti_contra_cheque_eventos.ID_CONTRA_CHEQUE = nexti_contra_cheque.IDEXTERNO
	AND nexti_contra_cheque_eventos.SITUACAO = 0
)
AND nexti_contra_cheque.tipo IN(0, 1)
AND nexti_contra_cheque.situacao = 1;