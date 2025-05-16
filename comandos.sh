#!/bin/bash
cd /var/www/soberana_vps

/usr/bin/php artisan ProcessaIntermediario
/usr/bin/php artisan nexti-sync schedule-transfer
/usr/bin/php artisan nexti-sync workplace-transfer

/usr/bin/php artisan ProcessaEmpresa
/usr/bin/php artisan ProcessaCargo
/usr/bin/php artisan ProcessaCliente

/usr/bin/php artisan BuscaPosto
/usr/bin/php artisan MergePosto
/usr/bin/php artisan ProcessaPosto

/usr/bin/php artisan ProcessaSindicato
/usr/bin/php artisan ProcessaSituacao
/usr/bin/php artisan ProcessaColaborador

/usr/bin/php artisan ProcessaTrocaPosto
/usr/bin/php artisan BuscaTrocaPosto
/usr/bin/php artisan MergeTrocaPosto

/usr/bin/php artisan ProcessaTrocaEscala
/usr/bin/php artisan BuscaTrocaEscala
/usr/bin/php artisan MergeTrocaEscala

/usr/bin/php artisan ProcessaLogs
