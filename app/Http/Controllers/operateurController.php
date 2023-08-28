<?php

namespace App\Http\Controllers;

use App\Models\operateur;
use Illuminate\Http\Request;

class operateurController extends Controller
{

    public function __construct()
    {
      //  $this->middleware("auth:super_admin");
    }

    //afficher forfait
    public function index(){

        $operateur=operateur::when(request()->has("key"), function ($q) {
            $q->where("id", request("key"));
        })->paginate(50);


        return view("operateur.liste_des_operateurs")->with([
        "operateur"=>$operateur,
        ]);

    }


    //ajouter forfait
    public function ajouter_operateur(Request $request)
    {

        $this->validate($request,[
            "designation"=>"required|min:1|max:40",
        ]);



        $operateur=new operateur();
        $operateur->designation=$request->designation;
        if($request->file("operateur_img")){
           // $operateur->logo=$request->file("operateur_img")->store("/");
           $file=$request->file("operateur_img");
           $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
           $destinationPath = public_path().'/uploads' ;
           $operateur->logo=$fileName;
           $request->file("operateur_img")->move($destinationPath,$fileName);
        }
        if($operateur->save()) return redirect("operateur/liste_des_operateurs")->with("msg","Cette Operation est bien effectuer");

    }




//modifie admin
public function edit_operateur(Request $request,$id)
{
    $this->validate($request,[
        "designation"=>"required|min:1|max:40",
    ]);



    $operateur=operateur::where("id",$id)->first();
    $operateur->designation=$request->designation;
    if($request->file("operateur_img")){

        //$operateur->logo=$request->file("operateur_img")->store("/");
        //dd($request->logo);
        $file=$request->file("operateur_img");
        $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
        $destinationPath = public_path().'/uploads' ;
        $operateur->logo=$fileName;
        $request->file("operateur_img")->move($destinationPath,$fileName);
    }
    if($operateur->update()) return redirect("operateur/liste_des_operateurs")->with("msg","Cette Operation est bien effectuer");

}

//supprimer admin
public function delete_operateur($id)
{
    $operateur=operateur::where("id",$id)->first();
    if($operateur->delete()) return redirect("operateur/liste_des_operateurs")->with("msg","Cette Operation est bien effectuer");

}

}
