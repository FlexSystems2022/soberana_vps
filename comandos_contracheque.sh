#!/bin/bash
cd /var/www/soberana_vps

/usr/bin/php artisan InsertContraChequeCompetence
/usr/bin/php artisan InsertContraCheque
/usr/bin/php artisan InsertContraChequeEvento
/usr/bin/php artisan nexti-sync paycheck

/usr/bin/php artisan ProcessaContraChequeCompetencias
/usr/bin/php artisan ProcessaContraCheque
