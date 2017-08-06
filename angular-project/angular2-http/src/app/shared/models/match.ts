import { Team } from "../models/team";
import { Responsible } from "../models/responsible";

export class Match {
    id_match?: number;
    date_match?: string;
    heure?: string;
    statut?: string;
    id_user_dirige?: number;
    id_equipe_home: number;
    id_equipe_visiteur: number;
    id_equipe_arbitre?: number;
    id_terrain?: number;
}