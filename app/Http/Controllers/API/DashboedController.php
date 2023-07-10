<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Controle;
use App\Models\Critaire;
use App\Models\Demande;
use App\Models\Detail_Critaire;
use App\Models\Detail_Demande;
use App\Models\Detail_Enjin;
use App\Models\Enjin;
use App\Models\Entite;
use App\Models\Entrer;
use App\Models\Famille_Enjin;
use App\Models\Sortie;
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
        return response()->json(['engin' => $engin, 'Etat' => $etat, 'famille' => $famille]);
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
        $validatedData['date_demande'] = today();
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
            ->with('detailDemande.familleEnjin', 'entite')->get();

        return response()->json(['demandes' => $demandes]);
    }

    function detail_demande(Request $request)
    {
        // $detailDemande = detail_demande::with('demande.user', 'familleEnjin', 'detailEnjin')->where('demande_id', $request->demande_id)->first();

        $detailDemande = Demande::with('user', 'detailDemandes.familleEnjin.critaire', 'detailDemandes.detailEnjin', 'entite')->where('id', $request->demande_id)->first();


        return response()->json(['demande' => $detailDemande]);
    }

    function Engin(Request $request)
    {

        $Engin = Enjin::with('famille_enjin')->where('id', $request->id)->first();

        $demande = Detail_Enjin::where('enjin_id', $Engin->id)->with("enjin.famille_enjin", "demande", "Conducteur")->orderby('created_at')->first()?->demande;
        return response()->json(['Engin' => $Engin, 'demande' => $demande]);
    }


    public function getAllDemandes()
    {
        $demandes = Demande::all();

        return response()->json(['demandes' => $demandes]);
    }

    public function addDetailEnjin(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'demande_id' => 'nulable',
        //     'famille_enjin_id' => '*',
        //     'date_sortie' => 'date',
        //     'date_entrer' => 'date',
        //     'conduteur_id' => 'date'

        // ]);





        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 400);
        // }



        foreach ($request->famille_enjin_id as  $value) {

            $detailEnjin = new Detail_Enjin();
            $detailEnjin->demande_id = $request->demande_id;
            $detailEnjin->enjin_id = $value;
            $detailEnjin->date_sortie = now();
            $detailEnjin->date_entrer = now();
            $detailEnjin->save();
        }
        //  $detailEnjin->date_sortie = $request->date_sortie;
        //  $detailEnjin->date_entrer = $request->date_entrer;
        //  $detailEnjin->user_id = $request->conduteur_id;


        return response()->json(['message' => 'Detail enjin added successfully'], 201);
    }



    public function getDetailCritaires(Request $request)
    {
        $detailCritaires = Detail_Critaire::with("critaire")->where('famille_enjin_id', $request->id_famille)->get();



        return response()->json(['detail_critaires' => $detailCritaires]);
    }

    public function createControles(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Klaxon'=> 'boolean',
            'Essuie_glase'=> 'boolean',
            'Frein'=> 'boolean',
            'Pneu'=> 'boolean',
            'Pare_Brise'=> 'boolean',
            "commentaireK" =>'string',
            "commentairee" =>'string',
            "commentairef" =>'string',
            "commentairepn" =>'string',
            "commentairepa" =>'string',
            'detail_enjin_id'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $critic = Critaire::create([
            'Klaxon' => $request->input('Klaxon', false),
            'Essuie_glase' => $request->input('Essuie_glase', false),
            'Frein' => $request->input('Frein', false),
            'Pneu' => $request->input('Pneu', false),
            'Pare_Brise' => $request->input('Pare_Brise', false),
            'commentaireK' => $request->input('commentaireK', ''),
            'commentairee' => $request->input('commentairee', ''),
            'commentairef' => $request->input('commentairef', ''),
            'commentairepn' => $request->input('commentairepn', ''),
            'commentairepa' => $request->input('commentairepa', ''),
            'detail_enjin_id' => $request->input('detail_enjin_id')
        ]);
        

        return response()->json(['message' => 'Controls stored successfully', 'controls' => $critic]);
    }

    function details_affectation(Request $request)
    {
        $Detail_Enjin = Detail_Enjin::with('demande.entite', 'enjin.sortie','enjin.entrer', 'Conducteur','Critaire')->where('id', $request->id)->first();

        return response()->json(['details_affectation' => $Detail_Enjin]);
    }


    function Historique_affectation()
    {


        // $detailDemande = detail_demande::with('demande.user', 'familleEnjin', 'detailEnjin.enjin')->get();

        $detailengin = Detail_Enjin::with('demande.user', 'enjin', 'Conducteur')->get();


        // if(empty($detailDemande->detailEnjin))
        // $Conducteur=$detailDemande?->detailEnjin?->Conducteur;
        return response()->json(['detail_demande' => $detailengin]);
    }

    function famille_engin()
    {
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

    function delete_demande(Request $request)
    {
        Demande::where('id', $request->id)->delete();
        return response()->json(['message' => "deleted"]);
    }

    function delete_dettail_demande(Request $request)
    {
        detail_demande::where('id', $request->id)->delete();
        return response()->json(['message' => "deleted"]);
    }

    function delete_dettail_engin(Request $request)
    {
        Detail_Enjin::where('id', $request->id)->delete();
        return response()->json(['message' => "deleted"]);
    }

    function affectation(Request $request)
    {
        $detailDemande = detail_demande::where('id', $request->id)->update(['effect' => true]);

        return response()->json(['affectation' => $detailDemande]);
    }

    function search_affectation(Request $request)
    {

        $engin_id = Enjin::where('Nom_enjin', $request->search)->pluck('id');
        $dettail_engin_id = Detail_Enjin::whereIn("enjin_id", $engin_id)->pluck('demande_id');
        $detailDemande = detail_demande::whereIn('demande_id', $dettail_engin_id)->get();

        return response()->json(['affectation' =>  $detailDemande]);
    }

    function sortie(Request $request)  {

            $validatedData = $request->validate([
                'matricule' => 'required',
                'societe' => 'required',
                'nom' => 'required',
                'prenom' => 'required',
                'compteur' => 'required|integer',
                'engin_id' => 'required',
            ]);
    
            $Sortie = Sortie::create($validatedData);
    
            return response()->json([
                'message' => 'Sortie created successfully',
                'Sortie' => $Sortie
            ], 201);
         
    }

    function entrer(Request $request)  {
        $validatedData = $request->validate([
            'matricle' => 'required',
            'societe' => 'required',
            'nom' => 'required',
            'prenom' => 'required',
            'compteur' => 'required|integer',
            'observation' => 'nullable',
            'engin_id' => 'required',
            'Critaire_id' => 'nullable|integer'
        ]);

        $entrer = Entrer::create($validatedData);

        return response()->json([
            'message' => 'Entrer created successfully',
            'entrer' => $entrer
        ], 201);
    
    }
}
