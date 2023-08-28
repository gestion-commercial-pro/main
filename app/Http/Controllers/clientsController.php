<?php

namespace App\Http\Controllers;

use App\Models\commerciale;
use App\Models\clients;
use App\Models\dossier_details;
use App\Models\forfait;
use App\Models\operateur;
use App\Models\statu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class clientsController extends Controller
{

    public function __construct()
    {
        //$this->middleware("auth:commerciale");
    }

    //afficher admin
    public function index(){


        $client=clients::when(request()->has("filter_query"), function ($q) {
            $q->where("nom", request("filter_query"));
        })->orderBy("CREATED_AT","DESC")->paginate(8);

        //pour fillter
      
        return view("client.liste_des_clients")->with([
        "client"=>$client,
        ]);

    }

    public function nouveau_client()
    {
        return view("client.nouveau_client");
    }


    //ajouter admin
    public function ajouter_client(Request $request)
    {

        $this->validate($request,[
            "nom"=>"required|min:1|max:40",
            "numero"=>"required",
            "mail"=>"email|max:79",
            "ville"=>"max:25",
            "adresse"=>"required|max:199",
            "longtt"=>"required",
            "latt"=>"required",
        ]);

      
        $client=new clients();
        $client->id = Str::uuid();
        $client->nom=$request->nom;
        $client->mail=$request->mail;
        $client->numero=$request->numero;
        $client->ville=$request->ville;
        $client->adresse=$request->adresse;
        $client->longtt=$request->longtt;
        $client->latt=$request->latt;

       // dd($request->file("recto"));
          //  dd($request->file("verso"));

        if($request->file("papier_1")){
           // $client->cin_recto=$request->file("recto")->store("/");

           $file=$request->file("papier_1");
           $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
           $destinationPath = public_path().'/uploads' ;
           $client->papier_1=$fileName;
           $request->file("papier_1")->move($destinationPath,$fileName);
        }

        if($request->file("papier_2")){
          //  $client->cin_verso=$request->file("verso")->store("/");

          $file=$request->file("papier_2");
          $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
          $destinationPath = public_path().'/uploads' ;
          $client->papier_2=$fileName;
          $request->file("papier_2")->move($destinationPath,$fileName);
        }

        if($request->file("papier_3")){
            //  $client->cin_verso=$request->file("verso")->store("/");
  
            $file=$request->file("papier_3");
            $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
            $destinationPath = public_path().'/uploads' ;
            $client->papier_3=$fileName;
            $request->file("papier_3")->move($destinationPath,$fileName);
          }

          if($request->file("papier_4")){
            //  $client->cin_verso=$request->file("verso")->store("/");
  
            $file=$request->file("papier_4");
            $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
            $destinationPath = public_path().'/uploads' ;
            $client->papier_4=$fileName;
            $request->file("papier_4")->move($destinationPath,$fileName);
          }


          if($request->file("papier_5")){
            //  $client->cin_verso=$request->file("verso")->store("/");
  
            $file=$request->file("papier_5");
            $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
            $destinationPath = public_path().'/uploads' ;
            $client->papier_5=$fileName;
            $request->file("papier_5")->move($destinationPath,$fileName);
          }

          if($request->file("papier_6")){
            //  $client->cin_verso=$request->file("verso")->store("/");
  
            $file=$request->file("papier_6");
            $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
            $destinationPath = public_path().'/uploads' ;
            $client->papier_6=$fileName;
            $request->file("papier_6")->move($destinationPath,$fileName);
          }


        $client->save();

        return redirect()->back()->with("msg","Cette Operation est bien effectuer");

    }


    public function modifier_client($id){
        $client=clients::where("id",$id)->first(); 
        return view('client.modifier_client')->with('client', $client);
    }

    //modifie admin
    public function edit_client(Request $request,$id)
    {
        $this->validate($request,[
            "nom"=>"required|min:1|max:40",
            "numero"=>"required",
            "mail"=>"email|max:79",
            "ville"=>"max:25",
            "adresse"=>"required|max:199",
            "longtt"=>"required",
            "latt"=>"required",
        ]);

        $client=clients::where("id",$id)->first();
        if($client){

        }else return redirect("404");
        $client->nom=$request->nom;
        $client->mail=$request->mail;
        $client->numero=$request->numero;
        $client->ville=$request->ville;
        $client->adresse=$request->adresse;
        $client->longtt=$request->longtt;
        $client->latt=$request->latt;

        if($request->file("papier_1")){
            // $client->cin_recto=$request->file("recto")->store("/");
 
            $file=$request->file("papier_1");
            $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
            $destinationPath = public_path().'/uploads' ;
            $client->papier_1=$fileName;
            $request->file("papier_1")->move($destinationPath,$fileName);
         }
 
         if($request->file("papier_2")){
           //  $client->cin_verso=$request->file("verso")->store("/");
 
           $file=$request->file("papier_2");
           $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
           $destinationPath = public_path().'/uploads' ;
           $client->papier_2=$fileName;
           $request->file("papier_2")->move($destinationPath,$fileName);
         }
 
         if($request->file("papier_3")){
             //  $client->cin_verso=$request->file("verso")->store("/");
   
             $file=$request->file("papier_3");
             $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
             $destinationPath = public_path().'/uploads' ;
             $client->papier_3=$fileName;
             $request->file("papier_3")->move($destinationPath,$fileName);
           }
 
           if($request->file("papier_4")){
             //  $client->cin_verso=$request->file("verso")->store("/");
   
             $file=$request->file("papier_4");
             $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
             $destinationPath = public_path().'/uploads' ;
             $client->papier_4=$fileName;
             $request->file("papier_4")->move($destinationPath,$fileName);
           }
 
 
           if($request->file("papier_5")){
             //  $client->cin_verso=$request->file("verso")->store("/");
   
             $file=$request->file("papier_5");
             $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
             $destinationPath = public_path().'/uploads' ;
             $client->papier_5=$fileName;
             $request->file("papier_5")->move($destinationPath,$fileName);
           }
 
           if($request->file("papier_6")){
             //  $client->cin_verso=$request->file("verso")->store("/");
   
             $file=$request->file("papier_6");
             $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
             $destinationPath = public_path().'/uploads' ;
             $client->papier_6=$fileName;
             $request->file("papier_6")->move($destinationPath,$fileName);
           }

        $client->update();

      


        return redirect("client/liste_des_clients")->with("msg","Cette Operation est bien effectuer");

    }



    //supprimer admin
    public function delete_client($id)
    {
        $client=clients::where("id",$id)->first();
        if($client->delete()) return redirect("client/liste_des_clients")->with("msg","Cette Operation est bien effectuer");

    }

}
