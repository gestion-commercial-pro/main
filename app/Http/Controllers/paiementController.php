<?php

namespace App\Http\Controllers;

use App\Models\commerciale;
use App\Models\dossier;
use App\Models\paiement;
use App\Models\paiement_details;
use App\Models\statu;
use App\Models\super_admin;
//use Barryvdh\DomPDF\PDF;
//use Barryvdh\DomPDF\Facade as PDF;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class paiementController extends Controller
{

    public function __construct()
    {
     //   $this->middleware("auth:super_admin");
    }



    //afficher admin
    public function index(){

        if(Auth::guard("commerciale")->check()){
            $pai=paiement::where("commerciale_id",Auth::guard("commerciale")->user()->id);
        }else $pai=new paiement();
        $paiement=$pai->when(request()->has("filter_commerciale"), function ($q) {
            $q->where("commerciale_id", request("filter_commerciale"));
        })->when(request()->has("filter_date"), function ($q) {
            $q->where("date", request("filter_date"));

        })->paginate(8);

        $commerciale=commerciale::all();
        return view("paiement.liste_des_paiements")->with([
        "paiement"=>$paiement,
        "commerciale"=>$commerciale,
        ]);

    }


    public function liste_des_commerciales()
    {
        $commerciale=commerciale::paginate(8);
        return view("paiement.liste_des_commerciales")->with([
            "commerciale"=>$commerciale
        ]);
    }

    public function liste_des_dossiers($id)
    {
        $dossier=dossier::where("commerciale_id",$id)->where("statu_id" ,2)->paginate(8);
        return view("paiement.liste_des_dossiers")->with([
            "dossier"=>$dossier,
            "com_id"=>$id,
        ]);
    }



    //ajouter admin
    public function ajouter_paiement(Request $request,$com_id)
    {
        $paiement=new paiement();
        $code_paiement=0;

        do {
            $code_paiement = random_int(100000, 999999);
        } while (paiement::where("id", "=", $code_paiement)->first());
        $paiement->id=$code_paiement;
        $paiement->commerciale_id=$com_id;
        $paiement->date=date("Y-m-d");
        $paiement->save();
        $total_prix_dos=0;
        if($request->dos_pay){
            foreach($request->dos_pay as $dossier){
                $paiement_details=new paiement_details();
                $paiement_details->paiement_id=$code_paiement;
                $paiement_details->dossier_id=$dossier;
                $paiement_details->save();

                $dos=dossier::where("id",$dossier)->first();
                $dos->statu_id=4;
                $total_prix_dos+=$dos->comission;
                $dos->update();
            }
        }

        //update montant payé
        $pai_up=paiement::where("id",$code_paiement)->first();
        $pai_up->montant_paye=$total_prix_dos;
        $pai_up->update();


        return redirect("paiement/liste_des_paiements")->with("msg","Cette Operation est bien effectuer");

    }

    //modifie admin
    public function modifie_paiement(Request $request)
    {
        $paiement=paiement::where("id",$request->id)->get();
        $paiement->nom=$request->nom;
        if($request->statu_id){
            $paiement->statu_id=$request->statu_id;
            $paiement->admin_id=$request->admin_id;
        }
        $paiement->update();

        foreach($request->dossiers as $dossier){
            $paiement_details=new paiement_details();
            $paiement_details->paiement_id=$$request->id;
            $paiement_details->dossier_id=$dossier->id;
            $paiement_details->save();
        }



        return redirect("paiement/liste_des_paiements")->with("msg","Cette Operation est bien effectuer");

    }


    public function invoice($id)
    {
        $paiement=paiement::where("id",$id)->first();
        $super_admin=super_admin::first();
        if($paiement){

            $data = [
                'paiement' => $paiement,
                'super_admin'=>$super_admin

            ];

            $pdf=app()->make(PDF::class);
            $pdf = PDF::loadView("paiement/facture", $data);

            return $pdf->stream('facture.pdf');
           // return PDF::loadView('/paiement/facture/'.$id, $data)->stream();

        } return view("dashboard.dashboard");

    }

    //supprimer admin
    public function delete_paiement($id)
    {
        $paiement=paiement::where("id",$id)->get();
        if($paiement->delete()) return redirect("paiement/liste_des_paiements")->with("msg","Cette Operation est bien effectuer");

    }

}
