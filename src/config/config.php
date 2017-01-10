<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The disk where to store the .zip backups, as defined in config/filesystems.php
    |
    | Default: 'local', which has its root in storage_path()
    |
    */

    'disk' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Middlware
    |--------------------------------------------------------------------------
    |
    | Web middleware to access to backup page
    |
    */
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | Route prefix to backups. 
    |
    | End route will be: route-prefix/backup.
    | For example: dashboard/backup
    |
    */
    'route-prefix' => 'dashboard',
];
