<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use \Artisan;
class InstallController extends Controller
{
    public function index(Request $request){

        if ($request->session()->get('lang') == NULL){
            $request->session()->put('lang', 'en');
        }

        try {
        DB::connection()->getPdo();
            } catch (\Exception $e) {              
                return view('install');
                die("Could not connect to the database.  Please check your configuration.");
            }
        return redirect()->route('admin.login');
        }

    public function store(Request $request){

        
        $myfile = fopen(base_path('.env'), "a+");
        $str=file_get_contents(base_path('.env'));
        $array=[];
        while(!feof($myfile)) {
            $array[]=fgets($myfile);
        }

        $array1=[];
        foreach($array as $ar){
            if(substr_count($ar,'=') == 1){
            $array1[] = explode('=', $ar);
            }else{
            $array1[] = explode('=', $ar,2);
            }

        }

        $array2=array();

        for($i=0;$i<count($array1);$i++){
            if($i != count($array1))
                {
                if(count($array1[$i]) == 2){
                    $x=$array1[$i][0];
                    $array2[$x] = $array1[$i][1];
                }
            }   
        }
        
        file_put_contents(base_path('.env'), "");

        $array2['DB_DATABASE']=$request->db_name." \n";
        $array2['DB_USERNAME']=$request->db_user." \n";
        $array2['DB_PASSWORD']=$request->user_pass." \n";
        $array2['DB_HOST']=$request->db_host." \n";
        $array2['DB_CONNECTION']=$request->db_type." \n";


        foreach($array2 as $key => $value){
            fwrite($myfile, $key.'='.$value);
        }

        fclose($myfile);
                
        $currentUrl = url()->current();
        $url=str_replace('install','',$currentUrl);
        Artisan::call('config:cache');
        Artisan::call('migrate');

        \DB::table('admins')->insert([
                    'first_name'=>$request->first_name,
                    'last_name'=>$request->last_name,
                    'email'=>$request->email,
                    'password'=>bcrypt($request->password)
                    ]);

        return redirect($url.'/admin/login');

    }
}
