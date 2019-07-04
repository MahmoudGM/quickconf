<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Zipper;
use File;
class DownloadController extends Controller
{
    public function excel($model,$confid,$type){

        if(($type != 'pdf')and($type != 'xlsx'))
            return back();
        $model = ucfirst($model);

        $mod = 'App\\' .$model;

        $conference = \App\Conference::find($confid);

   
    	$export=$mod::all();	
        /*
        $pdf = PDF::loadView('conferences.topics.index',array('conference' => $conference));
        return $pdf->download('invoice.pdf');*/
        /*
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML('<h1>Test</h1>');
        $pdf->save($model);
        return $pdf->stream();
        */

    	\Excel::create($model, function($excel) use ($export){
    		$excel->sheet('Sheet1',function($sheet) use ($export){
    			$sheet->fromArray($export);
    		});
    		
    	})->export($type);
    }

    public function zip(Request $request){
        //return $request->all();
        //var_dump(public_path(config('app.fileDestinationPath')));
        //return glob(public_path(config('app.fileDestinationPath')).'/1');
        $ids = $request->inputZip;
        $ids = str_replace('[','',$ids);
        $ids = str_replace(']','',$ids);
        $array = explode(',',$ids);
        $n = count($array);
        $exist = File::exists(public_path('mydir/papers.zip'));
        
        if($exist == 1){
            File::delete(public_path('mydir/papers.zip'));
            for($i=0;$i<$n;$i++){
                $files = glob(public_path('papers/').$array[$i] );
                //return $files;
                Zipper::make('mydir/papers.zip')->add($files)->close();
            }

        }else{
            
            for($i=0;$i<$n;$i++){
                $files = glob(public_path('papers/').$array[$i] );
                //return $files;
                Zipper::make('mydir/papers.zip')->add($files)->close();
            }
            
            
        }
       

        

    
        return response()->download(public_path('mydir/papers.zip'));
    }


}
