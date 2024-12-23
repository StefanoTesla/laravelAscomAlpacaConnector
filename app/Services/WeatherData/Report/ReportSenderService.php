<?php

namespace App\Services\WeatherData\Report;

use App\Enums\SyncStatusEnum;
use App\Http\Resources\ShortTimeReportResurces;
use App\Models\WeatherData\Report\ShortTimeReport;
use App\Services\ServerLoginService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportSenderService{

    public function main(){

    Log::channel('wt_short_report_sender')->info("Start sending data...");
    $count =ShortTimeReport::where('sync',0)->count();

    if($count == 0){
        Log::channel('wt_short_report_sender')->info("nothing to sync with the server");
    }

    $reportsToSync = ShortTimeReport::where('sync',0)
                        ->orderBy('interval','asc')
                        ->get();
    $chunkedRepors = $reportsToSync->chunk(100);
    foreach($chunkedRepors as $records) {
            $data = ShortTimeReportResurces::collection($records);
            $data = $data->toArray(request());

            $response = Http::accept('application/json')
            ->withToken(ServerLoginService::getToken())
            ->post(env("REMOTE_DOMAIN").'/api/weatherdata/short/store',$data);
            $data = $response->json();

            Log::channel('wt_short_report_sender')->info($response->status());

            if($response->successful()){
                $this->updateSyncValue($data['valid_rows'],SyncStatusEnum::COMPLETED);
                Log::channel('wt_short_report_sender')->info("Dati inviati correttamente");
            } else {

                if($response->status() == 422){
                    Log::channel('wt_short_report_sender')->info("Alcuni dati contengono errori");
                    if(!empty($data['valid_rows'])){
                        $this->updateSyncValue($data['valid_rows'],SyncStatusEnum::COMPLETED);
                        Log::channel('wt_short_report_sender')->info("Aggiorno i dati validi");
                    }
                    if(!empty($data['invalid_rows'])){
                        Log::channel('wt_short_report_sender')->info("Aggiorno i dati con errori");
                        $intervals =[];
                        foreach($data['invalid_rows'] as $unvalid){
                            if(isset($unvalid['interval'])){
                                $intervals[] = $unvalid['interval'];
                                Log::channel('wt_short_report_sender')->error("Nella data :".$unvalid['interval']." ci sono errori.");
                            } else {
                                Log::channel('wt_short_report_sender')->error("Campo interval mancante.");
                            }
                            Log::channel('wt_short_report_sender')->error($unvalid['errors']);
                        }
                        $this->updateSyncValue($intervals,SyncStatusEnum::CANCELED);
                    }
                } else {
                    $status = $response->status();
                    Log::channel('wt_short_report_sender')->error("Remote server response {$status}, abort");
                    return;
                };
            }
            $intervals = [];

    };

    Log::channel('wt_short_report_sender')->info("Sended all pending data...");
    }


private function updateSyncValue(array $intervals,SyncStatusEnum $status){
    $formattedIntervals = array_map(function($interval) {
        return Carbon::parse($interval)->format('Y-m-d H:i:s');
    }, $intervals);
    ShortTimeReport::whereIn('interval', $formattedIntervals)->update(['sync' => $status]);
}

}
