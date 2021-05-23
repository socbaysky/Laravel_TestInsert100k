## Demo insert 100k row into MySQL with paginate array

Follow these files:

-   main process: database/seeders/TestInsert.php
-   table for test: database/migrations/2021_05_23_041830_create_tests_table.php

Create Database:

-   php artisan migrate

Flow:

-   At first run **"php artisan db:seed --class=TestInsert"** : Create large array with 100k item and json_encode this large array and store to Cache.
-   Next run **"db:seed --class=TestInsert"** : Pull from cache and json_decode to convert to array again, use paginate function to split large array to many small array, using DB Transaction and bulk insert to insert into MySQL for optimize.
