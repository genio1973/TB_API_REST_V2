import { Team } from "../models/team";
import { Responsible } from "../models/responsible";

export class MatchDetails {
    id_match?: number;
    date_match?: Date;
    heure?: Date;
    statut?: string;
    user_dirige?: Responsible;
    equipe_home: Team;
    equipe_visiteur: Team;
    equipe_arbitre?: Team;
    id_terrain?: number;
    isConflict?: boolean=false;
}