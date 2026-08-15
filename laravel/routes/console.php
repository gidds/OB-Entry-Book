<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('ob:status', function () {
    $this->info('OB Entry Book Laravel rebuild OK');
})->purpose('Check the Laravel rebuild bootstrap');
