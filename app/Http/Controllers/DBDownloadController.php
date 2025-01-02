<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Spatie\Backup\Tasks\Backup\BackupJob;
use Spatie\DbDumper\Databases\MySql;

class DBDownloadController extends Controller
{
    public function DbDownload()
    {
        $app_name = env('APP_NAME');
        
        Artisan::call('backup:run --filename='.$app_name.'.zip --only-db');

        $file   = public_path(). "/uploads/Laravel/".$app_name.".zip";
        
        return Response::download($file, ''.$app_name.'.zip')->deleteFileAfterSend(true);
        
    }
}
