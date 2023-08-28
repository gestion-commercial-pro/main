<?php

namespace App\Http\Controllers;

use App\Models\commerciale;
use App\Models\sesion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class commercialeController extends Controller
{



    public function __construct()
    {
        //$this->middleware("auth:super_admin");
    }


     //afficher commerciale
     public function index(){



        $commerciale=commerciale::when(request()->has("filter_key"), function ($q) {
            $q->where("id",request("filter_key"))
            ->orWhere("nom","LIKE","%".request("filter_key")."%")
            ->orWhere("numero","LIKE","%".request("filter_key")."%")
            ->orWhere("mail","LIKE","%".request("filter_key")."%")
            ->orWhere("ville","LIKE","%".request("filter_key")."%");
        })->paginate(8);


        return view("commerciale.liste_des_commerciales")->with([
        "commerciale"=>$commerciale,
        ]);

    }

    //session track
    public function session(Request $request){
        try{
            $session=sesion::when(request()->has("filter_commerciale"), function ($q) {
                $q->where("commerciale_id", request("filter_commerciale"));
            })->when(request()->has("filter_key"), function ($q) {
                $q->where("adresse","LIKE", "%".request("filter_key")."%");
            })->when(request()->has("filter_date"), function ($q) {
                $q->whereDate("date", request("filter_date")  );
            })->orderBy("date","DESC")->paginate(8);

        }catch (Exception $ex) {
            return view("errors.404");
        }

        $com=commerciale::all();

        return view("commerciale.liste_des_sessions")->with([
            "sessions"=>$session,
            "commerciale"=>$com,
        ]);
    }

    public function nouveau_commerciale()
    {
        return view("commerciale.nouveau_commerciale");
    }

    //ajouter admin
    public function ajouter_commerciale(Request $request)
    {

        $this->validate($request,[
            "mail"=>"required|min:5|max:79|email",
            "motpass"=>"required|min:6|max:99",
            "numero"=>"required|numeric",
            "nom"=>"required|min:3|max:50",
            "ville"=>"required|min:2|max:29",
            "adresse"=>"required|min:6|max:199",
            "comis"=>"required",

        ]);



        $commerciale=new commerciale();
        $commerciale->nom=$request->nom;
        $commerciale->mail=$request->mail;
        $commerciale->numero=$request->numero;
        $commerciale->ville=$request->ville;
        $commerciale->adresse=$request->adresse;
        $commerciale->comis=$request->comis;
        $commerciale->date=date("Y-m-d");

        if($request->file("commerciale_img")){
            //$commerciale->commerciale_img=$request->file("commerciale_img")->store("/");


           $file=$request->file("commerciale_img");
           $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
           $destinationPath = public_path().'/uploads' ;
           $commerciale->commerciale_img=$fileName;
           $request->file("commerciale_img")->move($destinationPath,$fileName);
        }
        $commerciale->motpass=Hash::make($request->motpass);
        if($commerciale->save()) return redirect("commerciale/liste_des_commerciales")->with("msg","Cette Operation est bien effectuer");

    }

    public function modifier_commerciale($id)
    {
        $commerciale=commerciale::where("id",$id)->first();
        return view("commerciale.modifier_commerciale")->with([
            "commerciale"=>$commerciale
        ]);

    }

    //modifie admin
    public function edit_commerciale(Request $request,$id)
    {
        $this->validate($request,[
            "mail"=>"required|min:5|max:79|email",
            "motpass"=>"required|min:6|max:99",
            "numero"=>"required",
            "nom"=>"required|min:3|max:50",
            "ville"=>"required|min:2|max:29",
            "adresse"=>"required|min:6|max:199",
            "comis"=>"required",
        ]);



        $commerciale=commerciale::where("id",$id)->first();
        $commerciale->nom=$request->nom;
        $commerciale->mail=$request->mail;
        $commerciale->numero=$request->numero;
        $commerciale->ville=$request->ville;
        $commerciale->adresse=$request->adresse;
        $commerciale->comis=$request->comis;
        if($request->file("commerciale_img")){
           // $commerciale->commerciale_img=$request->file("commerciale_img")->store("/");

           $file=$request->file("commerciale_img");
           $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
           $destinationPath = public_path().'/uploads' ;
           $commerciale->commerciale_img=$fileName;
           $request->file("commerciale_img")->move($destinationPath,$fileName);
        }

        if (Hash::needsRehash($request->motpass)){
            $commerciale->motpass=Hash::make($request->motpass);
        }
        if($commerciale->update()) return redirect("commerciale/liste_des_commerciales")->with("msg","Cette Operation est bien effectuer");

    }

    //supprimer admin
    public function delete_commerciale($id)
    {
        $commerciale=commerciale::where("id",$id)->first();
        if($commerciale->delete()) return redirect("commerciale/liste_des_commerciales")->with("msg","Cette Operation est bien effectuer");

    }


}
