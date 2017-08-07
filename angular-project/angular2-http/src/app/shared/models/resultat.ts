import { ScoreSet } from "./score-set";

export class Resultat {
    id_match?: number;
    id_groupe?:number;
    nom_groupe?: string;
    date_match?: string;
    heure: string;
    statut?: string;
    id_equipe_home: number;
    nom_equipe_home: string;
    id_equipe_visiteur: number;
    nom_equipe_visiteur: string;
    id_equipe_arbitre?: number;
    nom_equipe_arbitre?: string; 
    id_user_dirige?: number;
    nom_user_dirige?: string; 
    nom_terrain?: string;
    id_terrain?: number;
    score_match?: string;
    //score_sets?: string[];
    score_sets?: ScoreSet[];
    set_home_gagne?:number;
    set_visiteur_gagne?:number;

}
