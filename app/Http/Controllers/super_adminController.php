<?php

namespace App\Http\Controllers;

use App\Models\super_admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class super_adminController extends Controller
{

    public function __construct()
    {
       // $this->middleware("auth:super_admin");
    }


     //afficher admin
     public function index(){

        $super_admins=super_admin::when(request()->has("filter_key"), function ($q) {
            $q->where("id",request("filter_key"))
            ->orWhere("nom","LIKE","%".request("filter_key")."%")
            ->orWhere("numero","LIKE","%".request("filter_key")."%")
            ->orWhere("mail","LIKE","%".request("filter_key")."%")
            ->orWhere("ville","LIKE","%".request("filter_key")."%");
        })->paginate(8);


        return view("admin.liste_des_admins")->with([
        "super_admins"=>$super_admins,
        ]);

    }

    //ajouter admin
    public function ajouter_super_admin(Request $request)
    {
        $super_admin=new super_admin();
        $super_admin->nom=$request->nom;
        $super_admin->nom=$request->mail;
        $super_admin->nom=$request->numero;
        $super_admin->date=date("Y-m-d");
        if ($request->file("super_admin_img")) {
           // $super_admin->super_admin_img=$request->file("super_admin_img")->store("/");

           $file=$request->file("super_admin_img");
           $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
           $destinationPath = public_path().'/uploads' ;
           $super_admin->super_admin_img=$fileName;
           $request->file("super_admin_img")->move($destinationPath,$fileName);
        }
        $super_admin->nom=Hash::make($request->motpass);
        if($super_admin->save()) return redirect("admin/nouveau_admin")->with("msg","Cette Operation est bien effectuer");

    }

    //modifie admin
    public function modifie_super_admin(Request $request)
    {
        $super_admin=super_admin::where("id",$request->id)->get();
        $super_admin->nom=$request->nom;
        $super_admin->nom=$request->mail;
        $super_admin->nom=$request->numero;
        if ($request->file("super_admin_img")) {
          //  $super_admin->super_admin_img=$request->file("super_admin_img")->store("/");

          $file=$request->file("super_admin_img");
          $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
          $destinationPath = public_path().'/uploads' ;
          $super_admin->super_admin_img=$fileName;
          $request->file("super_admin_img")->move($destinationPath,$fileName);
        }
        if (Hash::needsRehash($request->motpass)) {
            $super_admin->nom=Hash::make($request->motpass);
        }
        if($super_admin->update()) return redirect("admin/liste_des_admins")->with("msg","Cette Operation est bien effectuer");

    }

    //supprimer admin
    public function supperimer_super_admin(Request $request)
    {
        $super_admin=super_admin::where("id",$request->id)->get();
        if($super_admin->delete()) return redirect("admin/liste_des_admins")->with("msg","Cette Operation est bien effectuer");

    }


}
