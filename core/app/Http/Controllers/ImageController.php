<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Image;
use App\Models\Download;
use App\Constants\Status;
use App\Lib\DownloadFile;
use App\Models\ImageFile;
use App\Models\EarningLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ImageController extends Controller
{
    public function download($id)
    {
        
        $file = ImageFile::with('image')->findOrFail(decrypt($id));
        $user    = auth()->user();
        if ($file->is_free == Status::PREMIUM && @$user->id != $file->image->user_id) {
          
            $this->premiumDownloadProcess($file);
        }
        $this->downloadData($file, $user);
        return DownloadFile::download($file);
    }

    //save download data
    protected function downloadData($file, $user)
    {
        
        $general = gs();

        if ($file->image->user_id != @$user->id) {
            if ($user) {
                $download = Download::where('image_file_id', $file->id)->where('user_id', $user->id)->first();
                if (!$download) {
                    $download = new Download();
                    $download->user_id = $user->id;
                    $file->total_downloads += 1;
                }
            } else {
                $download = new Download();
                $file->total_downloads += 1;
            }

            $isDownloaded = Download::where('image_file_id', $file->id)->where('user_id', @$user->id)->exists();

            $download->image_file_id = $file->id;
            $download->contributor_id =  $file->image->user_id;
            $download->ip = request()->ip();
            $download->premium = $file->is_free == Status::PREMIUM;

            if (!$file->is_free && !$isDownloaded) {
               
                $amount = $file->price * $general->per_download / 100;
                
                $contributor = $file->image->user;
                $contributor->balance +=  $amount;
                $contributor->update();


                $earn                 = new EarningLog();
                $earn->contributor_id = $contributor->id;
                $earn->image_file_id       = $file->id;
                $earn->amount         = $amount;
                $earn->earning_date           = now()->format('Y-m-d');
                $earn->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $contributor->id;
                $transaction->amount       =  $amount;
                $transaction->post_balance = getAmount($contributor->balance);
                $transaction->charge       = 0;
                $transaction->trx_type     = '+';
                $transaction->details      = "Earnings generated from downloading the {$file->image->title}";
                $transaction->trx          = getTrx();
                $transaction->remark       = 'earning_log';
                $transaction->save();
            }
            $file->save();
            $download->save();
        }
    }

    private function premiumDownloadProcess($file)
    {
      
        $user = auth()->user();
        if (!$user) {
            throw ValidationException::withMessages(['user' => 'Please login to download this image']);
        }

        $alreadyDownload = Download::where('image_file_id', $file->id)->where('user_id', $user->id)->exists();
       
        
        if ($alreadyDownload) {
            return;
        }
       
        if ($user->purchasedPlan && $user->purchasedPlan->expired_at > Carbon::now()->format('Y-m-d')) {

            $this->purchaseProcessByPlan($file, $user);
            
        } else {
        

            $this->purchaseProcessByBalance($file, $user);
        }
    }
    
    private function purchaseProcessByPlan($file, $user)
    {
   
        $downloads       = Download::where('user_id', $user->id)->where('premium', Status::YES);
        
        $todayDownload   = (clone $downloads)->whereDate('created_at', Carbon::now())->count();
        $monthlyDownload = (clone $downloads)->whereMonth('created_at', Carbon::now()->month)->count();
        

        if ($user->purchasedPlan->daily_limit <= $todayDownload || $user->purchasedPlan->monthly_limit <= $monthlyDownload) {
 
            $this->purchaseProcessByBalance($file, $user);
           
        }
    }
    
    private function purchaseProcessByBalance($file, $user)
    {
    
        $price = $file->price;
        if ($user->balance < $price) {
            throw ValidationException::withMessages(['limit_over' => 'Inefficient Balance']);
        }
        $user->balance -= $price;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $price;
        $transaction->post_balance = getAmount($user->balance);
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = "Charge for download - {$file->image->title}";
        $transaction->trx          = getTrx();
        $transaction->remark       = 'download_charge';
        $transaction->save();

        $shortCodes = [
            'image_title'   => $file->image->title,
            'charge_amount' => showAmount($transaction->amount),
            'post_balance'  => showAmount($transaction->post_balance),
            'trx'           => $transaction->trx
        ];

        if (gs()->is_invoice_active) $shortCodes['invoice_link'] = url('invoice', ['image', $transaction->trx, $file->id]);
        notify($user, 'PURCHASE_CHARGE', $shortCodes);
    }
}
