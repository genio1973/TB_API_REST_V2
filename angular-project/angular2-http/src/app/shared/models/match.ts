import { Team } from "../models/team";
import { Responsible } from "../models/responsible";

export class Match {
    id_match?: number;
    data_match?: Date;
    heure?: Date;
    statut?: string;
    id_user_dirige?: number;
    id_equipe_home: number;
    id_equipe_visiteur: number;
    id_equipe_arbitre?: number;
}