
import { Team } from "../models/team";
import { Match } from "../models/match";

export class MatchsPlan {
    planning: Match[];

    constructor(teams: Team[]) {
        this.initializePlan(teams);
    }


    initializePlan(teams: Team[]): any {
        switch(teams.length){
            case 3: this.planning = this.plan3Teams(teams); break;
            case 4: this.planning = this.plan4Teams(teams); break;
            case 5: this.planning = this.plan5Teams(teams); break;
            case 6: this.planning = this.plan6Teams(teams); break;
            case 7: this.planning = this.plan7Teams(teams); break;
            case 8: this.planning = this.plan8Teams(teams); break;
            default: this.planning = null; break;            
        }
    }   




    /* Match array strutcure : 
    * [ homeTeam, VisitorTeam, refereeTeam,
    *   homeTeam, VisitorTeam, refereeTeam,
    *   homeTeam, VisitorTeam, refereeTeam
    * ]
    */ 

    private plan3Teams(teams: Team[]): Match[]{
        return [
                {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[2].id_equipe}, 
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[1].id_equipe}   
        ];
    }

    private plan4Teams(teams: Team[]): Match[]{
        return [
                {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[3].id_equipe}
        ];
    
    }    

    private plan5Teams(teams: Team[]): Match[]{
        return [
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[4].id_equipe}
        ];
    
    } 

    private plan6Teams(teams: Team[]): Match[]{
        return [
                {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[4].id_equipe}
        ];
    }

    private plan7Teams(teams: Team[]): Match[]{
            return [
                    {id_equipe_home: teams[7].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                    {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                    {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[7].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                    {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[7].id_equipe},
                    {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                    {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[7].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                    {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                    {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                    {id_equipe_home: teams[7].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                    {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[7].id_equipe},
                    {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                    {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                    {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                    {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[7].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                    {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                    {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[7].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                    {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[7].id_equipe}
        ];
    }

    private plan8Teams(teams: Team[]): Match[]{
            return [
                    {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[8].id_equipe, id_equipe_arbitre: teams[7].id_equipe},
                    {id_equipe_home: teams[7].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                    {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                    {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[7].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                    {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[8].id_equipe, id_equipe_arbitre: teams[7].id_equipe},
                    {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[8].id_equipe},
                    {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                    {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                    {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[7].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                    {id_equipe_home: teams[8].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                    {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[8].id_equipe},
                    {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                    {id_equipe_home: teams[7].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                    {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[8].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                    {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[1].id_equipe},
                    {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                    {id_equipe_home: teams[8].id_equipe, id_equipe_visiteur: teams[7].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                    {id_equipe_home: teams[3].id_equipe, id_equipe_visiteur: teams[1].id_equipe, id_equipe_arbitre: teams[7].id_equipe},
                    {id_equipe_home: teams[2].id_equipe, id_equipe_visiteur: teams[4].id_equipe, id_equipe_arbitre: teams[3].id_equipe},
                    {id_equipe_home: teams[5].id_equipe, id_equipe_visiteur: teams[8].id_equipe, id_equipe_arbitre: teams[4].id_equipe},
                    {id_equipe_home: teams[7].id_equipe, id_equipe_visiteur: teams[6].id_equipe, id_equipe_arbitre: teams[5].id_equipe},
                    {id_equipe_home: teams[1].id_equipe, id_equipe_visiteur: teams[2].id_equipe, id_equipe_arbitre: teams[6].id_equipe},
                    {id_equipe_home: teams[8].id_equipe, id_equipe_visiteur: teams[3].id_equipe, id_equipe_arbitre: teams[2].id_equipe},
                    {id_equipe_home: teams[4].id_equipe, id_equipe_visiteur: teams[7].id_equipe, id_equipe_arbitre: teams[8].id_equipe},
                    {id_equipe_home: teams[6].id_equipe, id_equipe_visiteur: teams[5].id_equipe, id_equipe_arbitre: teams[4].id_equipe}
        ];
    }    
                  
}


