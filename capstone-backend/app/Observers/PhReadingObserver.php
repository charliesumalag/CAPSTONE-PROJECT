<?php

namespace App\Observers;

use App\Models\Data;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PhReadingObserver
{
    /**
     * Handle the PhReading "created" event.
     */
    public function created(Data $data): void
    {
        Log::info('DataObserver created fired for pH: ' . $data->ph);
        if ($data->ph < 6.5 || $data->ph > 8.5) {
            $message = "⚠️ ALERT: pH level is {$data->ph} at {$data->created_at->format('Y-m-d H:i')}.";

            $numbers = explode(',', config('sms.alert_phone'));
            $apiKey = config('sms.semaphore_key');
            $sender = config('sms.semaphore_sender');


            Log::info('Config ALERT_PHONE:', ['value' => config('sms.alert_phone')]);
            Log::info('Config SEMAPHORE_API_KEY:', ['value' => config('sms.semaphore_key')]);

            foreach ($numbers as $number) {
                $response = Http::post('https://api.semaphore.co/api/v4/messages', [
                    'apikey' => $apiKey,
                    'number' => trim($number),
                    'message' => $message,
                    'sendername' => $sender,
                ]);
                Log::info('Semaphore SMS sent', [
                    'number' => trim($number),
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        }
    }

    /**
     * Handle the PhReading "updated" event.
     */
    public function updated(Data $phReading): void
    {
        //
    }

    /**
     * Handle the PhReading "deleted" event.
     */
    public function deleted(Data $phReading): void
    {
        //
    }

    /**
     * Handle the PhReading "restored" event.
     */
    public function restored(Data $phReading): void
    {
        //
    }

    /**
     * Handle the PhReading "force deleted" event.
     */
    public function forceDeleted(Data $phReading): void
    {
        //
    }
}
