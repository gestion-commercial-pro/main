<?php

namespace App\Http\Controllers;

use App\Models\commerciale;
use App\Models\dossier;
use App\Models\dossier_details;
use App\Models\forfait;
use App\Models\operateur;
use App\Models\clients;
use App\Models\statu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class dossierController extends Controller
{

    public function __construct()
    {
        //$this->middleware("auth:commerciale");
    }

    //afficher admin
    public function index(){

        $status="";
        if (Auth::guard("admin")->check()) {
            $status=statu::where('id','!=',"4")->where('id','!=',"5")->get();
           // $dos=dossier::where("admin_id",Auth::guard("admin")->user()->id)->where("admin_id",null);
        }
         if(Auth::guard("commerciale")->check()){
            $dos=dossier::where("commerciale_id",Auth::guard("commerciale")->user()->id);
        }else  $dos=dossier::orderBy("date","asc");

        $dossier=$dos->when(request()->has("filter_statu"), function ($q) {
            $q->where("statu_id", request("filter_statu"));
        })->when(request()->has("filter_commerciale"), function ($q) {
            $q->where("commerciale_id", request("filter_commerciale"));
        })->when(request()->has("filter_operateur"), function ($q) {
            $q->where("operateur_id", request("filter_operateur"));
        })->when(request()->has("filter_date"), function ($q) {
            $q->where("date", request("filter_date"));
        })->paginate(8);

        //pour fillter
        $cl=clients::all();
        $commerciale=commerciale::all();
        $all_statu=statu::all();
        $operateur=operateur::all();


        return view("dossier.liste_des_dossiers")->with([
        "dossier"=>$dossier,
        "status"=>$status,
        "commerciale"=>$commerciale,
        "operateur"=>$operateur,
        "all_statu"=>$all_statu,
        "clients"=>$cl,
        ]);

    }

    public function nouveau_dossier_op()
    {
        $operateur=operateur::all();
        return view("dossier.ajouter_dossier")->with([
            "operateur"=>$operateur,
            ]);;
    }

    public function nouveau_dossier($id)
    {
        $forfait=forfait::where("operateur_id",$id)->get();
        $clients=clients::all();
        if($forfait){
            return view("dossier.nouveau_dossier")->with([
                "forfaits"=>$forfait,
                "operateur_id"=>$id,
                "clients"=>$clients,
            ]);
        }else return view("404");

    }

    //ajouter admin
    public function ajouter_dossier(Request $request)
    {

        $this->validate($request,[
            "clients_id"=>"required|exists:clients,id",
            "operateur_id"=>"required|exists:operateur,id",
            "qte"=>"required|min:1",
            "forfait"=>"required",

        ]);

        $dossier=new dossier();
        $code_dossier=0;

        do {
            $code_dossier = random_int(100000, 999999);
        } while (dossier::where("id", "=", $code_dossier)->first());
        $dossier->id=$code_dossier;
        $dossier->clients_id=$request->clients_id;
        $dossier->operateur_id=$request->operateur_id;
        $dossier->statu_id=1;
        $dossier->date=date("Y-m-d");
       // dd($request->file("recto"));
          //  dd($request->file("verso"));

  
            
        $dossier->commerciale_id=Auth::guard("commerciale")->user()->id;
        $dossier->save();
        $montant_dossier=0;
        $gains_commerciale=0;
        $comis=Auth::guard("commerciale")->user()->comis;

        if($request->forfait){
            foreach($request->forfait as $index => $forf){
                $dossier_details=new dossier_details();
                $dossier_details->dossier_id=$code_dossier;
                $dossier_details->forfait_id=$forf;
                //$dossier_details->remise=$request->remise[$index];
                $dossier_details->qte=$request->qte[$index];

                $prix_original=forfait::where("id",$forf)->get()->sum("prix");
                $dossier_details->prix_vente=$prix_original;
                $dossier_details->prix_final=$prix_original*(int)$dossier_details->qte;

                $montant_dossier+=$dossier_details->prix_final; /** sum pour prix final de la commande  */

                $dossier_details->save();
                //array_push($forf_total,forfait::select("prix")->where("id",$forf)->first());
            // $forf_total=(float)forfait::select("prix")->where("id",$forf)->first();
            // $montant_dossier+=$forf_total;

            //$prix_original=forfait::where("id",$forf)->get()->sum("prix");

           // $prix_forfait_remise=($prix_original*$request->remise[$index])/100;
            ///$prix_net=$prix_original; //-$prix_forfait_remise;
            
            }
        }
        /*
        for($i=0; $i<=count($forf_total); $i++){
            $montant_dossier+=$forf_total[$i];
        }*/
        //dd($montant_dossier);
        if($comis>0 && $comis!=null){
            $gains_commerciale=($comis*$montant_dossier)/100;
        }else $gains_commerciale=0;
        $dos_up_montant=dossier::where("id",$code_dossier)->first();
        $dos_up_montant->montant=$montant_dossier;
        $dos_up_montant->comission=$gains_commerciale;
        $dos_up_montant->update();
        return redirect("dossier/liste_des_dossiers")->with("msg","Cette Operation est bien effectuer");

    }


    public function modifier_dossier($id){
        $dossier=dossier::where("id",$id)->first();
        $clients=clients::all();
        $forfait=forfait::where("operateur_id",$dossier->operateur_id)->get();
        if($forfait){
            return view("dossier.modifier_dossier")->with([
                "forfaits"=>$forfait,
                "dossier"=>$dossier,
                "operateur_id"=>$id,
                "clients"=>$clients,
            ]);
        }else return view("404");

    }

    //modifie admin
    public function edit_dossier(Request $request,$id)
    {
        $this->validate($request,[
            "clients_id"=>"required|exists:clients,id",
            "qte"=>"required|min:1",
            "forfait"=>"required",
        ]);

        $dossier=dossier::where("id",$id)->first();
        $dossier->clients_id=$request->clients_id;
        $dossier->statu_id=5;
        $dossier->date=date("Y-m-d");

      

      

      //  $dossier->commerciale_id=Auth::guard("commerciale")->user()->id;
        $dossier->update();

        $dossier_dt=dossier_details::where("dossier_id",$dossier->id)->get();
        if(count($dossier_dt)){
            dossier_details::where("dossier_id",$dossier->id)->delete();
        }

        //déclaration des variables
        $montant_dossier=0;
        $gains_commerciale=0;
        $comis=Auth::guard("commerciale")->user()->comis;
        if($request->forfait){
            foreach($request->forfait as $index => $forf){
                $dossier_details=new dossier_details();
                $dossier_details->dossier_id=$dossier->id;
                $dossier_details->forfait_id=$forf;
                

                $dossier_details->qte=$request->qte[$index];

                $prix_original=forfait::where("id",$forf)->get()->sum("prix");
                $dossier_details->prix_vente=$prix_original;
                $dossier_details->prix_final=$prix_original*(int)$dossier_details->qte;

                $montant_dossier+=$dossier_details->prix_final; /** sum pour prix final de la commande  */
                
                $dossier_details->save();
            }
        }

        if($comis>0 || $comis==null){
            $gains_commerciale=($comis*$montant_dossier)/100;
        }else $gains_commerciale=0;
        $dos_up_montant=dossier::where("id",$id)->first();
        $dos_up_montant->montant=$montant_dossier;
        $dos_up_montant->comission=$gains_commerciale;
        $dos_up_montant->update();
        return redirect("dossier/liste_des_dossiers")->with("msg","Cette Operation est bien effectuer");

    }


    //modifier status par admin
    public function edit_status(Request $request,$id)
    {

        $this->validate($request,[
           "statu_id"=>"required|exists:statu,id"
        ]);

        $dossier=dossier::where("id",$id)->first();
        $dossier->statu_id=$request->statu_id;
        $dossier->admin_id=Auth::guard("admin")->user()->id;
        $dossier->date=date("Y-m-d");
        if($request->statu_remarque){
            $dossier->statu_remarque=$request->statu_remarque;
        }


        if($dossier->update()) return redirect("dossier/liste_des_dossiers")->with("msg","Cette Operation est bien effectuer");
        else return view("404");

    }

    //supprimer admin
    public function delete_dossier($id)
    {
        $dossier=dossier::where("id",$id)->first();
        if($dossier->delete()) return redirect("dossier/liste_des_dossier")->with("msg","Cette Operation est bien effectuer");

    }

}
