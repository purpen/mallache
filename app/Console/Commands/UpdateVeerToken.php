<?php

namespace App\Console\Commands;

use App\Models\Veer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateVeerToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Update:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = "http://api-v1.vcg.com/api/oauth2/access_token";
        $headers = 'Content-Type: application/x-www-form-urlencoded';
        $client_id = config('veer.client_id');
        $client_secret = config('veer.client_secret');
        $grant_type = config('veer.grant_type');
        $username = config('veer.username');
        $password = config('veer.password');
        $post_data = 'client_id='.$client_id.'&client_secret='.$client_secret.'&grant_type=authorization_code&username='.$username.'&password='.$password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        $output_array = json_decode($output,true);

        $access_token = $output_array['access_token'];
        $refresh_token = $output_array['refresh_token'];
        $expires_in = $output_array['expires_in'];
        //token有效期是7200秒，小于1000秒重新获取token值
        if($expires_in < 1000){
            $refresh_url = 'http://api-v1.vcg.com/api/oauth2/refresh_token';
            $post_data = 'client_id='.$client_id.'&client_secret='.$client_secret.'&grant_type=refresh_token&refresh_token='.$refresh_token;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $refresh_url);
            curl_setopt($ch, CURLOPT_HEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $output = curl_exec($ch);
            curl_close($ch);
            //打印获得的数据
            $output_array = json_decode($output,true);

            $veer= Veer::where('client_id', $client_id)->first();
            if($veer){
                $veer->access_token = $output_array['access_token'];
                $veer->refresh_token = $output_array['refresh_token'];
                $veer->save();
            }else{
                $veers = new Veer();
                $veers->client_id = $client_id;
                $veers->access_token = $output_array['access_token'];
                $veers->refresh_token = $output_array['refresh_token'];
                $veers->save();
            }

        }else{
            $veer= Veer::where('client_id', $client_id)->first();
            if($veer){
                return;
            }else{
                $veers = new Veer();
                $veers->client_id = $client_id;
                $veers->access_token = $access_token;
                $veers->refresh_token = $refresh_token;
                $veers->save();
            }

        }

    }
}
