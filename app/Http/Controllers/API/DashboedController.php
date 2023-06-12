<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Detail_Demande;
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

    function Ajouter_demande(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'date_demande' => 'required|date',
            'Shift' => 'required|string',
            'Sortie_preveue' => 'required|string',
            'entite_id' => 'required|exists:entites,id',
            'user_id' => 'required|exists:users,id',
            'Commentaire' => 'nullable|string',
            'details' => 'required|array',
        ]);

        // Create the demande
        $demande = Demande::create($validatedData);

        // Create the detail demande entries
        $details = $validatedData['details'];
        foreach ($details as $detail) {
            Detail_Demande::create([
                'famille_enjin_id' => $detail['famille_enjin_id'],
                'demande_id' => $demande->id,
                'Description' => $detail['Description'],
                'qte' => $detail['qte'],
            ]);
        }

        return response()->json(['message' => 'Demande added successfully'], 201);
    }

    function consulter_demandes(Request $request)
    {

        $id = $request->input('id');
        $commentaire = $request->input('commentaire');
        $entite = $request->input('entite');
        $date_demande = $request->input('date_demande');
        $shift = $request->input('shift');
        $sortie_prevue = $request->input('sortie_prevue');

        $demandes = Demande::query()
            ->when($id, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when($commentaire, function ($query) use ($commentaire) {
                $query->where('Commentaire', 'LIKE', "%$commentaire%");
            })
            ->when($entite, function ($query) use ($entite) {
                $query->whereHas('entite', function ($subquery) use ($entite) {
                    $subquery->where('Nom_Entite', 'LIKE', "%$entite%");
                });
            })
            ->when($date_demande, function ($query) use ($date_demande) {
                $query->whereDate('date_demande', $date_demande);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('Shift', $shift);
            })
            ->when($sortie_prevue, function ($query) use ($sortie_prevue) {
                $query->where('Sortie_preveue', $sortie_prevue);
            })
            ->get();

        return response()->json(['demandes' => $demandes]);
    }

    function detail_demande(Request $request)  {
        $detailDemande = detail_demande::with('demande', 'familleEnjin', 'detailEnjin')->where('demande_id',$request->demande_id)->first();

        return response()->json(['detail_demande' => $detailDemande]);
    }
}
