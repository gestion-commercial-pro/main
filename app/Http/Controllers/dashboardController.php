<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\commerciale;
use App\Models\dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;

class dashboardController extends Controller
{

    public function __construct()
    {
       // $this->middleware("auth:super_admin");
    }


    public function index()
    {

        $dos=dossier::where("statu_id",2)->orWhere("statu_id",4)->get();
        $admin=Admin::all("id")->count();
        $commerciale=commerciale::all("id")->count();
        $dossier=$dos->count();
        $gains=$dos->sum("montant");



        for($i = 1; $i < 13; $i++) {
            $dos_date[$i]=dossier::whereMonth('date', Date::createFromFormat('!m', $i)->format('m') )->whereYear("date",date('Y'))->count();
        }


        return view('dashboard.dashboard')->with([
            "admin"=>$admin,
            "commerciale"=>$commerciale,
            "dossier"=>$dossier,
            "gains"=> $gains,
            "chart"=>$dos_date
        ]);
    }
}
