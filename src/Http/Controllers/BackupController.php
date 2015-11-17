<?php namespace Dick\BackupManager\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App;
use Storage;
use Carbon\Carbon;
use Artisan;

class BackupController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');

		// Check for the right roles to access these pages
		if (!\Entrust::can('view-backups')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permissions to see this page.');
	    }
	}

	public function index()
	{
		$disk = Storage::disk(config('dick.backupmanager.disk'));
		$files = $disk->files('backups');
		$this->data['backups'] = [];

		// make an array of backup files, with their filesize and creation date
		foreach ($files as $k => $f) {
			// only take the zip files into account
			if (substr($f, -4) == '.zip' && $disk->exists($f)) {
				$this->data['backups'][] = [
											'file_path' => $f,
											'file_name' => str_replace('backups/', '', $f),
											'file_size' => $disk->size($f),
											'last_modified' => $disk->lastModified($f),
											];
			}
		}

		// reverse the backups, so the newest one would be on top
		$this->data['backups'] = array_reverse($this->data['backups']);

		return view("backupmanager::backup", $this->data);
	}

	public function create()
	{
		if (!\Entrust::can('make-backups')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permission to make backups.');
	    }

	    try {
	      Artisan::call('backup:run');
	      echo 'done backup:run';
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
	public function download($file_name)
	{
		if (!\Entrust::can('download-backups')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permission to download backups.');
	    }

		$disk = Storage::disk(config('dick.backupmanager.disk'));

		if ($disk->exists('backups/'.$file_name)) {
			return response()->download(storage_path('backups/'.$file_name));
		}
		else
		{
			abort(404, "The backup file doesn't exist.");
		}
	}

	/**
	 * Deletes a backup file.
	 */
	public function delete($file_name)
	{
		if (!\Entrust::can('delete-backups')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permission to delete backups.');
	    }

		$disk = Storage::disk(config('dick.backupmanager.disk'));

		if ($disk->exists('backups/'.$file_name)) {
			$disk->delete('backups/'.$file_name);

			return 'success';
		}
		else
		{
			abort(404, "The backup file doesn't exist.");
		}
	}
}
