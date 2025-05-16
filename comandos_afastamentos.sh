#!/bin/bash
cd /var/www/soberana_vps

/usr/bin/php artisan InsertAusencia
/usr/bin/php artisan nexti-sync absence
/usr/bin/php artisan ProcessaAusencia
/usr/bin/php artisan BuscaAusencia
/usr/bin/php artisan MergeAusencia
