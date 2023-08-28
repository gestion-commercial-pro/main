<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class adminController extends Controller
{

    public function __construct()
    {
        //$this->middleware("auth:super_admin");
    }

    //afficher admin
    public function index(){

        $admins=Admin::when(request()->has("filter_key"), function ($q) {
            $q->where("id",request("filter_key"))
            ->orWhere("nom","LIKE","%".request("filter_key")."%")
            ->orWhere("numero","LIKE","%".request("filter_key")."%")
            ->orWhere("mail","LIKE","%".request("filter_key")."%")
            ->orWhere("ville","LIKE","%".request("filter_key")."%");
        })->paginate(8);


        return view("admin.liste_des_admins")->with([
        "admins"=>$admins,
        ]);

    }

    public function nouveau_admin()
    {
        return view("admin.nouveau_admin");
    }

    //ajouter admin
    public function ajouter_admin(Request $request)
    {

        $this->validate($request,[
            "mail"=>"required|min:5|max:79|email",
            "motpass"=>"required|min:6|max:99",
            "numero"=>"required|numeric",
            "nom"=>"required|min:3|max:50",
            "ville"=>"required|min:2|max:29",
            "adresse"=>"required|min:6|max:199",

        ]);



        $admin=new Admin();
        $admin->nom=$request->nom;
        $admin->mail=$request->mail;
        $admin->numero=$request->numero;
        $admin->ville=$request->ville;
        $admin->adresse=$request->adresse;
        $admin->date=date("Y-m-d");
        if ($request->file("admin_img")) {
           // $admin->admin_img=$request->file("admin_img")->store("/");

           $file=$request->file("admin_img");
           $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
           $destinationPath = public_path().'/uploads' ;
           $admin->admin_img=$fileName;
           $request->file("admin_img")->move($destinationPath,$fileName);
        }
        $admin->motpass=Hash::make($request->motpass);
        if($admin->save()) return redirect("admin/liste_des_admins")->with("msg","Cette Operation est bien effectuer");

    }

    public function modifier_admin($id)
    {
        $admin=Admin::where("id",$id)->first();
        return view("admin.modifier_admin")->with([
            "admin"=>$admin
        ]);

    }

    //modifie admin
    public function edit_admin(Request $request,$id)
    {
        $this->validate($request,[
            "mail"=>"required|min:5|max:79|email",
            "motpass"=>"required|min:6|max:99",
            "numero"=>"required",
            "nom"=>"required|min:3|max:50",
            "ville"=>"required|min:2|max:29",
            "adresse"=>"required|min:6|max:199",

        ]);



        $admin=Admin::where("id",$id)->first();
        $admin->nom=$request->nom;
        $admin->mail=$request->mail;
        $admin->numero=$request->numero;
        $admin->ville=$request->ville;
        $admin->adresse=$request->adresse;
        if ($request->file("admin_img")) {
           // $admin->admin_img=$request->file("admin_img")->store("/");

            $file=$request->file("admin_img");
            $fileName = time()."_".rand(1,100)."_".$file->getClientOriginalName();
            $destinationPath = public_path().'/uploads' ;
            $admin->admin_img=$fileName;
            $request->file("admin_img")->move($destinationPath,$fileName);
        }
        if (Hash::needsRehash($request->motpass)) {
            $admin->motpass=Hash::make($request->motpass);
        }
        if($admin->update()) return redirect("admin/liste_des_admins")->with("msg","Cette Operation est bien effectuer");

    }

    //supprimer admin
    public function delete_admin($id)
    {
        $admin=Admin::where("id",$id)->first();
        if($admin->delete()) return redirect("admin/liste_des_admins")->with("msg","Cette Operation est bien effectuer");

    }


}
