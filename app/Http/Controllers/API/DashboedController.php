<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Detail_Enjin;
use App\Models\Enjin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboedController extends Controller
{
    function dashboard()
    {
        $CntDemandes = Auth()->user()->Demandes?->count();
        $incompletes = Auth()->user()->Demandes->where('Etat', 'complete')?->count();
        $cloturees = Auth()->user()->Demandes->where('Etat', 'traitee')?->count();
        $pesrantes = Auth()->user()->Demandes->where('Etat', 'restant')?->count();
        $lastDemandes = Demande::with('user')->take(5)->get();
        $Engin = Detail_Enjin::take(5)->get();
        return response()->json([
            'count demandes' => $CntDemandes,
            'incompletes' => $incompletes,
            'cloturees' => $cloturees,
            'pesrantes' => $pesrantes,
            'last demandes' => $lastDemandes,
            'detail enjin' => $Engin
        ]);
    }

    function list_engin(Request $request)
    {
        if ($request->Etat == "" && $request->famille_id == "") {
            $engin = Enjin::where('Nom_enjin', 'like', '' . $request->Nom . '%')->get();
        } else if ($request->Etat == "" && $request->famille_id != "") {
            $engin = Enjin::where('Nom_enjin', 'like', '' . $request->Nom . '%')->where('famille_enjin_id', $request->famille_id)->get();
        } else if ($request->famille_id != "" && $request->Etat != "") {
            $engin = Enjin::where('Nom_enjin', 'like', '' . $request->Nom . '%')->where('Etat', $request->Etat)->get();
        } else {
            $engin = Enjin::where('Nom_enjin', 'like', '' . $request->Nom . '%')->where('Etat', $request->Etat)->where('famille_enjin_id', $request->famille_id)->get();
        }
        return response()->json(['engin' => $engin]);
    }
}
