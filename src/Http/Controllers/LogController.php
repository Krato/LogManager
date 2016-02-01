<?php namespace Infinety\LogManager\Http\Controllers;

use starter\Http\Requests;
use starter\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App;
use Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Psr\Log\LogLevel;
use ReflectionClass;

class LogController extends Controller {


	/**
	 * @var string file
	 */
	private static $file;

	private static $levels_classes = [
		'debug' => 'info',
		'info' => 'info',
		'notice' => 'info',
		'warning' => 'warning',
		'error' => 'danger',
		'critical' => 'danger',
		'alert' => 'danger',
		'emergency' => 'danger',
	];

	private static $levels_imgs = [
		'debug' => 'info',
		'info' => 'info',
		'notice' => 'info',
		'warning' => 'warning',
		'error' => 'warning',
		'critical' => 'warning',
		'alert' => 'warning',
		'emergency' => 'warning',
	];

	const MAX_FILE_SIZE = 52428800;

	public function __construct()
	{
		$this->middleware('auth');

		// Check for the right roles to access these pages
		if (!\Entrust::can('view-logs')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permissions to see this page.');
	    }
	}

	public function index()
	{
		$disk = Storage::disk('storage');
		$files = $disk->files('logs');
		$this->data['logs'] = [];

		// make an array of log files, with their filesize and creation date
		foreach ($files as $k => $f) {
			// only take the zip files into account
			if (substr($f, -4) == '.log' && $disk->exists($f)) {
				$this->data['logs'][] = [
											'file_path' => $f,
											'file_name' => str_replace('logs/', '', $f),
											'file_size' => $disk->size($f),
											'last_modified' => $disk->lastModified($f),
											];
			}
		}

		// reverse the logs, so the newest one would be on top
		$this->data['logs'] = array_reverse($this->data['logs']);

		return view("logmanager::logs", $this->data);
	}

	/**
	 * Previews a log file.
	 *
	 * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
	 */
	public function preview($file_name)
	{
		if (!\Entrust::can('preview-logs')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permission to preview logs.');
	    }

		$disk = Storage::disk('storage');

		if ($disk->exists('logs/'.$file_name)) {

			self::$file = storage_path().'/logs/'.$file_name;

			$log = array();

			$log_levels = self::getLogLevels();

			$pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/';

			if (!self::$file) {
				$log_file = self::getFiles();
				if(!count($log_file)) {
					return [];
				}
				self::$file = $log_file[0];
			}

			if (File::size(self::$file) > self::MAX_FILE_SIZE) return null;

			$file = File::get(self::$file);

			preg_match_all($pattern, $file, $headings);



			if (!is_array($headings)) return $log;

			$log_data = preg_split($pattern, $file);

			if ($log_data[0] < 1) {
				array_shift($log_data);
			}

			foreach ($headings as $h) {
				for ($i=0, $j = count($h); $i < $j; $i++) {
					foreach ($log_levels as $level_key => $level_value) {
						if (strpos(strtolower($h[$i]), '.' . $level_value)) {

							preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?\.' . $level_key . ': (.*?)( in .*?:[0-9]+)?$/', $h[$i], $current);

							if (!isset($current[2])) continue;

							$log[] = array(
								'level' => $level_value,
								'level_class' => self::$levels_classes[$level_value],
								'level_img' => self::$levels_imgs[$level_value],
								'date' => $current[1],
								'text' => $current[2],
								'in_file' => isset($current[3]) ? $current[3] : null,
								'stack' => preg_replace("/^\n*/", '', $log_data[$i])
							);
						}
					}
				}
			}

			$log["data"]['file_path'] = 'logs/'.$file_name;
			$log["data"]['file_name'] = $file_name;
			$log["data"]['file_size'] = $disk->size('logs/'.$file_name);
			$log["data"]['last_modified'] = $disk->lastModified('logs/'.$file_name);


//			return array_reverse($log);
			return view("logmanager::log_item", ['logs' => array_reverse($log)]);

//			return array_reverse($log);
//


//			$this->data['log'] = [
//									'file_path' => 'logs/'.$file_name,
//									'file_name' => $file_name,
//									'file_size' => $disk->size('logs/'.$file_name),
//									'last_modified' => $disk->lastModified('logs/'.$file_name),
//									'content' => $disk->get('logs/'.$file_name),
//									];
//
//			return view("logmanager::log_item", $this->data);
		}
		else
		{
			abort(404, "The log file doesn't exist.");
		}
	}

	/**
	 * Downloads a log file.
	 *
	 * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
	 */
	public function download($file_name)
	{
		if (!\Entrust::can('download-logs')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permission to download logs.');
	    }

		$disk = Storage::disk('storage');

		if ($disk->exists('logs/'.$file_name)) {
			return response()->download(storage_path('logs/'.$file_name));
		}
		else
		{
			abort(404, "The log file doesn't exist.");
		}
	}

	/**
	 * Deletes a log file.
	 */
	public function delete($file_name)
	{
		if (!\Entrust::can('delete-logs')) {
	        abort(403, 'Unauthorized access - you do not have the necessary permission to delete logs.');
	    }

		$disk = Storage::disk('storage');

		if ($disk->exists('logs/'.$file_name)) {
			$disk->delete('logs/'.$file_name);

			return 'success';
		}
		else
		{
			abort(404, "The log file doesn't exist.");
		}
	}


	/**
	 * @return array
	 */
	private static function getLogLevels()
	{
		$class = new ReflectionClass(new LogLevel);
		return $class->getConstants();
	}
}
