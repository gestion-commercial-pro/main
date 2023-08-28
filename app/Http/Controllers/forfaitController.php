<?php

namespace App\Http\Controllers;

use App\Models\forfait;
use App\Models\operateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class forfaitController extends Controller
{


    public function __construct()
    {
        //$this->middleware("auth:commerciale");

    }


         //afficher forfait
         public function index(){

            $forfait=forfait::when(request()->has("filter_key"), function ($q) {
                $q->where("id",request("filter_key"))
                ->orWhere("designation","LIKE","%".request("filter_key")."%");
            })->paginate(8);

            $operateur=operateur::all();


            return view("forfait.liste_des_forfaits")->with([
            "forfait"=>$forfait
            ]);

        }

        public function nouveau_forfait()
        {
            $operateur=operateur::all();

            return view("forfait.nouveau_forfait")->with([
                "ops"=>$operateur
            ]);
        }

        //ajouter forfait
        public function ajouter_forfait(Request $request)
        {

            $this->validate($request,[
                "designation"=>"required|min:1|max:40",
                "prix"=>"required|numeric",
                "operateur_id"=>"required|exists:operateur,id",
            ]);



            $forfait=new forfait();
            $forfait->designation=$request->designation;
            $forfait->prix=$request->prix;
            $forfait->operateur_id=$request->operateur_id;
            if($forfait->save()) return redirect("forfait/liste_des_forfaits")->with("msg","Cette Operation est bien effectuer");

        }

        public function modifier_forfait($id)
        {
            $operateur=operateur::all();
            $forfait=forfait::where("id",$id)->first();
            return view("forfait.modifier_forfait")->with([
                "ops"=>$operateur,
                "forfait"=>$forfait
            ]);

        }


    //modifie admin
    public function edit_forfait(Request $request,$id)
    {
        $this->validate($request,[
            "designation"=>"required|min:1|max:40",
            "prix"=>"required|numeric",
            "operateur_id"=>"required|exists:operateur,id",
        ]);



        $forfait=forfait::where("id",$id)->first();
        $forfait->designation=$request->designation;
        $forfait->prix=$request->prix;
        $forfait->operateur_id=$request->operateur_id;

        if($forfait->update()) return redirect("forfait/liste_des_forfaits")->with("msg","Cette Operation est bien effectuer");

    }

    //supprimer admin
    public function delete_forfait($id)
    {
        $forfait=forfait::where("id",$id)->first();
        if($forfait->delete()) return redirect("forfait/liste_des_forfaits")->with("msg","Cette Operation est bien effectuer");

    }



}
