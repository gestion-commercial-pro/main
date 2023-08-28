<?php

namespace App\Http\Controllers;

use App\Models\dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class commerciale_dashboardController extends Controller
{

    public function __construct()
    {
       // $this->middleware("auth:commerciale");
    }


    public function index()
    {

        $confirme=dossier::where("commerciale_id",Auth::guard("commerciale")->user()->id)->where("statu_id",2)->get()->count();
        $refuse=dossier::where("commerciale_id",Auth::guard("commerciale")->user()->id)->where("statu_id",3)->get()->count();
        $paye=dossier::where("commerciale_id",Auth::guard("commerciale")->user()->id)->where("statu_id",4)->get()->sum("comission");
        $gains=dossier::where("commerciale_id",Auth::guard("commerciale")->user()->id)->where("statu_id",2)->get()->sum("comission");


        for($i = 1; $i < 13; $i++) {
            $dos_date[$i]=dossier::where("commerciale_id",Auth::guard("commerciale")->user()->id)->whereMonth('date', Date::createFromFormat('!m', $i)->format('m') )->whereYear("date",date('Y'))->count();
        }


        return view('commerciale_dashboard.dashboard')->with([
            "confirme"=>$confirme,
            "refuse"=>$refuse,
            "paye"=>$paye,
            "gains"=> $gains,
            "chart"=>$dos_date
        ]);
    }
}
