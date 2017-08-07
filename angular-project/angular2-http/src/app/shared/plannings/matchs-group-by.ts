
import { Team } from "../models/team";
import { Match } from "../models/match";
import { MatchDetails } from "../models/match-details";

export class MatchsGroupBy {
    planning: MatchDetails[];
    groupId: number;

    constructor(matchs: MatchDetails[]) {
        this.planning = matchs; 
        this.groupId = matchs[0].equipe_home.id_groupe;
    }
}

