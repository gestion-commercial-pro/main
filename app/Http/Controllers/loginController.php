<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\commerciale;
use App\Models\sesion;
use App\Models\super_admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class loginController extends Controller
{

    public function index(Request $request)
    {

        if (Auth::guard("commerciale")->check()) {
            return redirect("/home");
        }else{

            $this->validate($request, [
            "mail"=>"required|max:79",
            "motpass"=>"required|max:30",
        ]);

            $commerciale=commerciale::where("mail", $request->mail)->first();
            $admin=Admin::where("mail", $request->mail)->first();
            $super_admin=super_admin::where("mail", $request->mail)->first();


            if ($super_admin) {
                if ($request->motpass==$super_admin->motpass) {
                    Auth::guard("super_admin")->loginUsingId($super_admin->id);
                    return redirect("/dashboard");
                }else {
                    return redirect("/login")->with("error", "cette utilisateur n'existe pas");
                }
            }else if ($admin) {
                if (Hash::check($request->motpass,$admin->motpass)) {
                    Auth::guard("admin")->loginUsingId($admin->id);
                    return redirect("/dossier/liste_des_dossiers");
                }else {
                    return redirect("/login")->with("error", "cette utilisateur n'existe pas");
                }
            }else if ($commerciale) {
                if (Hash::check($request->motpass,$commerciale->motpass)) {
                    //Auth::loginUsingId($commerciale->id);
                    Auth::guard("commerciale")->loginUsingId($commerciale->id);
                    $sesion=new sesion();
                    $sesion->commerciale_id=Auth::guard("commerciale")->user()->id;
                    $sesion->action="login";
                    $sesion->adresse=$request->adresse;
                    $sesion->lat=$request->lat;
                    $sesion->longt=$request->longt;
                    $sesion->save();
                    //online
                    $commerciale->online=1;
                    $commerciale->update();
                    return redirect("commerciale/dashboard");

                }else {
                    return redirect("/login")->with("error", "cette utilisateur n'existe pas");
                }
            }else {
                return redirect("/login")->with("msg", "cette utilisateur n'existe pas");
            }

       }


    }

    public function logout(Request $request)
    {
        if (Auth::guard("commerciale")->check()) {
                $sesion=new sesion();
                $sesion->commerciale_id=Auth::guard("commerciale")->user()->id;
                $sesion->action="logout";
                $sesion->adresse=$request->adresse;
                $sesion->lat=$request->lat;
                $sesion->longt=$request->longt;
                $sesion->save();

                //offline
                $commerciale=commerciale::where("id",Auth::guard("commerciale")->user()->id)->first();
                $commerciale->online=0;
                $commerciale->update();
        }


        Auth::logout();
        //$request->session()->flush();
       // $request->session()->regenerate();
        //$request->session()->invalidate();
        $sesion=new sesion();


        return redirect("/login");
    }

}
