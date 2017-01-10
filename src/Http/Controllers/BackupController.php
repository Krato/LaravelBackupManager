<?php

namespace Infinety\BackupManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Storage;
use Artisan;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(config('backupmanager.middleware'));
    }

    public function index()
    {
        $disk = Storage::disk(config('backupmanager.disk'));
        $files = $disk->files('backups');
        $this->data['backups'] = [];

        foreach (config('laravel-backup.backup.destination.disks') as $disk_name) {
            $disk = Storage::disk($disk_name);
            $adapter = $disk->getDriver()->getAdapter();
            $files = $disk->allFiles();
            // make an array of backup files, with their filesize and creation date
            foreach ($files as $k => $f) {
                // only take the zip files into account
                if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                    $this->data['backups'][] = [
                        'file_path' => $f,
                        'file_name' => str_replace('backups/', '', $f),
                        'file_size' => $disk->size($f),
                        'last_modified' => $disk->lastModified($f),
                        'disk' => $disk_name,
                        'download' => ($adapter instanceof \League\Flysystem\Adapter\Local) ? true : false,
                        ];
                }
            }
        }
        // reverse the backups, so the newest one would be on top
        $this->data['backups'] = array_reverse($this->data['backups']);
        $this->data['title'] = 'Backups';

        return $this->firstViewThatExists('vendor/infinety/backupmanager/backup', 'backupmanager::backup', $this->data);
    }

    public function create()
    {
        try {
            ini_set('max_execution_time', 300);
            // start the backup process
            Artisan::call('backup:run');
            $output = Artisan::output();
            // return the results as a response to the ajax call
            echo $output;
        } catch (Exception $e) {
            Response::make($e->getMessage(), 500);
        }

        // return 'success';
    }

    /**
     * Downloads a backup zip file.
     *
     * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
     */
    public function download()
    {
        $disk = Storage::disk(\Request::input('disk'));
        $file_name = \Request::get('file_name');
        $adapter = $disk->getDriver()->getAdapter();

        if ($adapter instanceof \League\Flysystem\Adapter\Local) {
            $storage_path = $disk->getDriver()->getAdapter()->getPathPrefix();
            if ($disk->exists($file_name)) {
                return response()->download($storage_path.$file_name);
            } else {
                abort(404, trans('backpack::backup.backup_doesnt_exist'));
            }
        } else {
            abort(404, trans('backpack::backup.only_local_downloads_supported'));
        }
    }

    /**
     * Deletes a backup file.
     */
    public function delete()
    {
        $disk = Storage::disk(\Request::input('disk'));
        $file_name = \Request::get('file_name');
        $adapter = $disk->getDriver()->getAdapter();

        if ($adapter instanceof \League\Flysystem\Adapter\Local) {
            $storage_path = $disk->getDriver()->getAdapter()->getPathPrefix();
            if ($disk->exists($file_name)) {
                $disk->delete($file_name);

                return 'success';
            } else {
                abort(404, trans('backpack::backup.backup_doesnt_exist'));
            }
        } else {
            abort(404, trans('backpack::backup.only_local_downloads_supported'));
        }
    }

    /**
     * Allow replace the default views by placing a view with the same name.
     * If no such view exists, load the one from the package.
     *
     * @param $first_view
     * @param $second_view
     * @param array $information
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function firstViewThatExists($first_view, $second_view, $information = [])
    {
        // load the first view if it exists, otherwise load the second one
        if (view()->exists($first_view)) {
            return view($first_view, $information);
        } else {
            return view($second_view, $information);
        }
    }
}
