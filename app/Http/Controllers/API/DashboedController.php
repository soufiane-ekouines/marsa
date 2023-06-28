<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Controle;
use App\Models\Demande;
use App\Models\Detail_Critaire;
use App\Models\Detail_Demande;
use App\Models\Detail_Enjin;
use App\Models\Enjin;
use App\Models\Entite;
use App\Models\Famille_Enjin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            'count_demandes' => $CntDemandes,
            'incompletes' => $incompletes,
            'cloturees' => $cloturees,
            'pesrantes' => $pesrantes,
            'last_demandes' => $lastDemandes,
            'detail_enjin' => $Engin
        ]);
    }

    function list_engin(Request $request)
    {
        if ($request->etat == "" && $request->famille == "") {
            $engin = Enjin::with("famille_enjin")->where('Nom_enjin', 'like', '' . $request->Nom . '%')->get();
        } else if ($request->etat == "" && $request->famille != "") {
            $engin = Enjin::with("famille_enjin")->where('Nom_enjin', 'like', '' . $request->Nom . '%')->where('famille_enjin_id', $request->famille)->get();
        } else if ($request->famille != "" && $request->etat != "") {
            $engin = Enjin::with("famille_enjin")->where('Nom_enjin', 'like', '' . $request->Nom . '%')->where('Etat', $request->etat)->get();
        } else {
            $engin = Enjin::with("famille_enjin")->where('Nom_enjin', 'like', '' . $request->Nom . '%')->where('Etat', $request->etat)->where('famille_enjin_id', $request->famille)->get();
        }

        $etat = Enjin::get('Etat');
        $famille = Famille_Enjin::get();
        return response()->json(['engin' => $engin,'Etat'=>$etat, 'famille'=> $famille]);
    }

    function Ajouter_demande(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'date_demande' => 'date',
            'Shift' => 'required|string',
            'Sortie_preveue' => 'required|string',
            'entite_id' => 'required|exists:entites,id',
            'user_id' => 'required|exists:users,id',
            'Commentaire' => 'nullable|string',
            'details' => 'required|array',
        ]);
        $validatedData['date_demande']=today();
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

        $id = $request->input('demande_id');
        $commentaire = $request->input('commentaire');
        $entite = $request->input('entite');
        $date_demande = $request->input('date_entrer');
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
            ->with('detailDemande.familleEnjin')->get();

        return response()->json(['demandes' => $demandes]);
    }

    function detail_demande(Request $request)
    {
        $detailDemande = detail_demande::with('demande.user', 'familleEnjin', 'detailEnjin')->where('demande_id', $request->demande_id)->first();

        return response()->json(['detail_demande' => $detailDemande]);
    }

    function Engin(Request $request)
    {

        $Engin = Enjin::with('famille_enjin')->where('id', $request->id)->first();

        $demande = Detail_Enjin::where('enjin_id', $Engin->id)->orderby('created_at')->first()?->demande;
        return response()->json(['Engin' => $Engin, 'demande' => $demande]);
    }


    public function getAllDemandes()
    {
        $demandes = Demande::all();

        return response()->json(['demandes' => $demandes]);
    }

    public function addDetailEnjin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'demande_id' => 'required',
            'famille_enjin_id' => 'required',
            'date_sortie' => 'required|date',
            'date_entrer' => 'required|date',
            'conduteur_id' => 'required|date'

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $detailEnjin = new Detail_Enjin();
        $detailEnjin->demande_id = $request->demande_id;
        $detailEnjin->enjin_id = $request->famille_enjin_id;
        $detailEnjin->date_sortie = $request->date_sortie;
        $detailEnjin->date_entrer = $request->date_entrer;
        $detailEnjin->user_id = $request->conduteur_id;

        $detailEnjin->save();

        return response()->json(['message' => 'Detail enjin added successfully', 'detail_enjin' => $detailEnjin]);
    }



    public function getDetailCritaires(Request $request)
    {
        $detailCritaires = Detail_Critaire::with("critaire")->where('famille_enjin_id', $request->id_famille)->get();



        return response()->json(['detail_critaires' => $detailCritaires]);
    }

    public function createControles(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'controls' => 'required|array',
            'controls.*.id_detail_critire' => 'required|exists:detail_critaires,id',
            'controls.*.id_detail_enjin' => 'required|exists:detail_enjins,id',
            'controls.*.confirmation' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $controlsData = $request->input('controls');

        $controls = [];
        foreach ($controlsData as $controlData) {
            $control = new Controle();
            $control->id_detail_critire = $controlData['id_detail_critire'];
            $control->id_detail_enjin = $controlData['id_detail_enjin'];
            $control->confirmation = $controlData['confirmation'];
            $control->save();

            $controls[] = $control;
        }

        return response()->json(['message' => 'Controls stored successfully', 'controls' => $controls]);
    }

    function details_affectation(Request $request) {
        $detailDemande = detail_demande::with('demande', 'familleEnjin', 'detailEnjin')->where('demande_id', $request->demande_id)->first();
        $Conducteur=$detailDemande?->detailEnjin?->Conducteur;
        return response()->json(['detail_demande' => $detailDemande,'Conducteur' => $Conducteur]);
    }


    function Historique_affectation() {
        $detailDemande = detail_demande::with('demande.user', 'familleEnjin', 'detailEnjin')->get();
        if(empty($detailDemande->detailEnjin))
        // $Conducteur=$detailDemande?->detailEnjin?->Conducteur;
        return response()->json(['detail_demande' => $detailDemande]);
    }

    function famille_engin() {
        $famille_enjin = Famille_Enjin::get();
        return response()->json(['Famille_Enjin' => $famille_enjin]);

    }

    
    public function users()
    {
        $User = User::all();

        return response()->json(['user' => $User]);
    }

    
    public function entites()
    {
        $Entite = Entite::all();

        return response()->json(['entite' => $Entite]);
    }
}
