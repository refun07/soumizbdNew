<?php

namespace App\Http\Controllers\Backend\Admin\Configurations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseBackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:general_settings']);
    }

    /**
     * Get the private disk instance for backups.
     */
    private function getDisk()
    {
        return Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/backups'),
        ]);
    }

    public function index()
    {
        $backups = [];
        $disk = $this->getDisk();
        
        // Ensure backups directory exists
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $files = $disk->files('/');
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'name' => basename($file),
                    'size' => $disk->size($file),
                    'created_at' => Carbon::createFromTimestamp($disk->lastModified($file)),
                ];
            }
        }
        
        // Sort by latest
        usort($backups, function ($a, $b) {
            return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
        });

        return view('backend.admin.configurations.database-backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = config('database.connections.mysql.database');
            $key = 'Tables_in_' . $dbName;
            
            $sql = "-- Raisa Mart Database Backup\n";
            $sql .= "-- Generated: " . now()->toDateTimeString() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$key;
                
                // Get Create Table statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                // Get table data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "INSERT INTO `{$tableName}` VALUES \n";
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes($val) . "'";
                            }
                        }
                        $values[] = "(" . implode(',', $rowValues) . ")";
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $filename = 'backup_' . $dbName . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $this->getDisk()->put($filename, $sql);

            flash(translate('Database backup created successfully'))->success();
        } catch (\Exception $e) {
            flash(translate('Failed to create backup: ') . $e->getMessage())->error();
        }

        return redirect()->route('admin.database-backups.index');
    }

    public function download($filename)
    {
        $disk = $this->getDisk();
        if ($disk->exists($filename)) {
            return $disk->download($filename);
        }
        
        flash(translate('Backup file not found'))->error();
        return redirect()->route('admin.database-backups.index');
    }

    public function delete($filename)
    {
        $disk = $this->getDisk();
        if ($disk->exists($filename)) {
            $disk->delete($filename);
            flash(translate('Backup file deleted successfully'))->success();
        } else {
            flash(translate('Backup file not found'))->error();
        }
        
        return redirect()->route('admin.database-backups.index');
    }
}
