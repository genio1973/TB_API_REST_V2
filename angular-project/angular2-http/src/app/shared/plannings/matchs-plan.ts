
import { Team } from "../models/team";
import { Match } from "../models/match";
import { MatchDetails } from "../models/match-details";

export class MatchsPlan {
    planning: MatchDetails[] = [];
    groupId: number;
    nameBlock?: string = '';
    private auto_arbitrage:boolean = false;

    constructor(teams: Team[], autoArbitrage:boolean, nameBlock?:string ) {
        this.groupId = teams[0].id_groupe;
        this.nameBlock = nameBlock;
        this.auto_arbitrage = autoArbitrage;
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

    private plan3Teams(teams: Team[]): MatchDetails[]{
        if(!this.auto_arbitrage){
            return [
                    {equipe_home: teams[0], equipe_visiteur: teams[1]},
                    {equipe_home: teams[2], equipe_visiteur: teams[0]}, 
                    {equipe_home: teams[1], equipe_visiteur: teams[2]}   
            ];
        }

        
        return [
                {equipe_home: teams[0], equipe_visiteur: teams[1], equipe_arbitre: teams[2]},
                {equipe_home: teams[2], equipe_visiteur: teams[0], equipe_arbitre: teams[1]}, 
                {equipe_home: teams[1], equipe_visiteur: teams[2], equipe_arbitre: teams[0]}   
        ];
            
    }

    private plan4Teams(teams: Team[]): MatchDetails[]{
        if(!this.auto_arbitrage){
            return [
                    {equipe_home: teams[2], equipe_visiteur: teams[3]},
                    {equipe_home: teams[0], equipe_visiteur: teams[2]},
                    {equipe_home: teams[1], equipe_visiteur: teams[3]},
                    {equipe_home: teams[0], equipe_visiteur: teams[1]},
                    {equipe_home: teams[3], equipe_visiteur: teams[0]},
                    {equipe_home: teams[1], equipe_visiteur: teams[2]}
            ];       
        }
        return [
                {equipe_home: teams[2], equipe_visiteur: teams[3], equipe_arbitre: teams[0]},
                {equipe_home: teams[0], equipe_visiteur: teams[2], equipe_arbitre: teams[3]},
                {equipe_home: teams[1], equipe_visiteur: teams[3], equipe_arbitre: teams[2]},
                {equipe_home: teams[0], equipe_visiteur: teams[1], equipe_arbitre: teams[3]},
                {equipe_home: teams[3], equipe_visiteur: teams[0], equipe_arbitre: teams[1]},
                {equipe_home: teams[1], equipe_visiteur: teams[2], equipe_arbitre: teams[0]}
        ];
    
    }    

    private plan5Teams(teams: Team[]): MatchDetails[]{
        if(!this.auto_arbitrage){
            return [
                    {equipe_home: teams[1], equipe_visiteur: teams[4]},
                    {equipe_home: teams[2], equipe_visiteur: teams[3]},
                    {equipe_home: teams[0], equipe_visiteur: teams[2]},
                    {equipe_home: teams[3], equipe_visiteur: teams[4]},
                    {equipe_home: teams[1], equipe_visiteur: teams[3]},
                    {equipe_home: teams[4], equipe_visiteur: teams[0]},
                    {equipe_home: teams[0], equipe_visiteur: teams[1]},
                    {equipe_home: teams[2], equipe_visiteur: teams[4]},
                    {equipe_home: teams[3], equipe_visiteur: teams[0]},
                    {equipe_home: teams[1], equipe_visiteur: teams[2]}
            ];
        }
        return [
                {equipe_home: teams[1], equipe_visiteur: teams[4], equipe_arbitre: teams[2]},
                {equipe_home: teams[2], equipe_visiteur: teams[3], equipe_arbitre: teams[4]},
                {equipe_home: teams[0], equipe_visiteur: teams[2], equipe_arbitre: teams[3]},
                {equipe_home: teams[3], equipe_visiteur: teams[4], equipe_arbitre: teams[0]},
                {equipe_home: teams[1], equipe_visiteur: teams[3], equipe_arbitre: teams[4]},
                {equipe_home: teams[4], equipe_visiteur: teams[0], equipe_arbitre: teams[1]},
                {equipe_home: teams[0], equipe_visiteur: teams[1], equipe_arbitre: teams[4]},
                {equipe_home: teams[2], equipe_visiteur: teams[4], equipe_arbitre: teams[1]},
                {equipe_home: teams[3], equipe_visiteur: teams[0], equipe_arbitre: teams[2]},
                {equipe_home: teams[1], equipe_visiteur: teams[2], equipe_arbitre: teams[0]}
        ];
    
    } 

    private plan6Teams(teams: Team[]): MatchDetails[]{
        if(!this.auto_arbitrage){
         return [
                {equipe_home: teams[5], equipe_visiteur: teams[0]},
                {equipe_home: teams[4], equipe_visiteur: teams[1]},
                {equipe_home: teams[2], equipe_visiteur: teams[3]},
                {equipe_home: teams[1], equipe_visiteur: teams[5]},
                {equipe_home: teams[0], equipe_visiteur: teams[2]},
                {equipe_home: teams[3], equipe_visiteur: teams[4]},
                {equipe_home: teams[5], equipe_visiteur: teams[2]},
                {equipe_home: teams[1], equipe_visiteur: teams[3]},
                {equipe_home: teams[4], equipe_visiteur: teams[0]},
                {equipe_home: teams[3], equipe_visiteur: teams[5]},
                {equipe_home: teams[2], equipe_visiteur: teams[4]},
                {equipe_home: teams[0], equipe_visiteur: teams[1]},
                {equipe_home: teams[4], equipe_visiteur: teams[5]},
                {equipe_home: teams[3], equipe_visiteur: teams[0]},
                {equipe_home: teams[1], equipe_visiteur: teams[2]}
        ];       }
        return [
                {equipe_home: teams[5], equipe_visiteur: teams[0], equipe_arbitre: teams[1]},
                {equipe_home: teams[4], equipe_visiteur: teams[1], equipe_arbitre: teams[0]},
                {equipe_home: teams[2], equipe_visiteur: teams[3], equipe_arbitre: teams[4]},
                {equipe_home: teams[1], equipe_visiteur: teams[5], equipe_arbitre: teams[3]},
                {equipe_home: teams[0], equipe_visiteur: teams[2], equipe_arbitre: teams[5]},
                {equipe_home: teams[3], equipe_visiteur: teams[4], equipe_arbitre: teams[0]},
                {equipe_home: teams[5], equipe_visiteur: teams[2], equipe_arbitre: teams[3]},
                {equipe_home: teams[1], equipe_visiteur: teams[3], equipe_arbitre: teams[2]},
                {equipe_home: teams[4], equipe_visiteur: teams[0], equipe_arbitre: teams[1]},
                {equipe_home: teams[3], equipe_visiteur: teams[5], equipe_arbitre: teams[4]},
                {equipe_home: teams[2], equipe_visiteur: teams[4], equipe_arbitre: teams[5]},
                {equipe_home: teams[0], equipe_visiteur: teams[1], equipe_arbitre: teams[2]},
                {equipe_home: teams[4], equipe_visiteur: teams[5], equipe_arbitre: teams[1]},
                {equipe_home: teams[3], equipe_visiteur: teams[0], equipe_arbitre: teams[5]},
                {equipe_home: teams[1], equipe_visiteur: teams[2], equipe_arbitre: teams[3]}
        ];
    }

    private plan7Teams(teams: Team[]): MatchDetails[]{
        if(!this.auto_arbitrage){
            return [
                    {equipe_home: teams[6], equipe_visiteur: teams[1]},
                    {equipe_home: teams[2], equipe_visiteur: teams[5]},
                    {equipe_home: teams[3], equipe_visiteur: teams[4]},
                    {equipe_home: teams[0], equipe_visiteur: teams[6]},
                    {equipe_home: teams[1], equipe_visiteur: teams[4]},
                    {equipe_home: teams[2], equipe_visiteur: teams[3]},
                    {equipe_home: teams[0], equipe_visiteur: teams[5]},
                    {equipe_home: teams[6], equipe_visiteur: teams[4]},
                    {equipe_home: teams[1], equipe_visiteur: teams[2]},
                    {equipe_home: teams[4], equipe_visiteur: teams[0]},
                    {equipe_home: teams[5], equipe_visiteur: teams[3]},
                    {equipe_home: teams[6], equipe_visiteur: teams[2]},
                    {equipe_home: teams[3], equipe_visiteur: teams[0]},
                    {equipe_home: teams[4], equipe_visiteur: teams[2]},
                    {equipe_home: teams[5], equipe_visiteur: teams[1]},
                    {equipe_home: teams[2], equipe_visiteur: teams[0]},
                    {equipe_home: teams[1], equipe_visiteur: teams[3]},
                    {equipe_home: teams[5], equipe_visiteur: teams[6]},
                    {equipe_home: teams[0], equipe_visiteur: teams[1]},
                    {equipe_home: teams[3], equipe_visiteur: teams[6]},
                    {equipe_home: teams[4], equipe_visiteur: teams[5]}
        ];
        }
        return [
                {equipe_home: teams[6], equipe_visiteur: teams[1], equipe_arbitre: teams[2]},
                {equipe_home: teams[2], equipe_visiteur: teams[5], equipe_arbitre: teams[1]},
                {equipe_home: teams[3], equipe_visiteur: teams[4], equipe_arbitre: teams[5]},
                {equipe_home: teams[0], equipe_visiteur: teams[6], equipe_arbitre: teams[3]},
                {equipe_home: teams[1], equipe_visiteur: teams[4], equipe_arbitre: teams[6]},
                {equipe_home: teams[2], equipe_visiteur: teams[3], equipe_arbitre: teams[4]},
                {equipe_home: teams[0], equipe_visiteur: teams[5], equipe_arbitre: teams[2]},
                {equipe_home: teams[6], equipe_visiteur: teams[4], equipe_arbitre: teams[0]},
                {equipe_home: teams[1], equipe_visiteur: teams[2], equipe_arbitre: teams[4]},
                {equipe_home: teams[4], equipe_visiteur: teams[0], equipe_arbitre: teams[2]},
                {equipe_home: teams[5], equipe_visiteur: teams[3], equipe_arbitre: teams[4]},
                {equipe_home: teams[6], equipe_visiteur: teams[2], equipe_arbitre: teams[3]},
                {equipe_home: teams[3], equipe_visiteur: teams[0], equipe_arbitre: teams[6]},
                {equipe_home: teams[4], equipe_visiteur: teams[2], equipe_arbitre: teams[0]},
                {equipe_home: teams[5], equipe_visiteur: teams[1], equipe_arbitre: teams[2]},
                {equipe_home: teams[2], equipe_visiteur: teams[0], equipe_arbitre: teams[5]},
                {equipe_home: teams[1], equipe_visiteur: teams[3], equipe_arbitre: teams[0]},
                {equipe_home: teams[5], equipe_visiteur: teams[6], equipe_arbitre: teams[3]},
                {equipe_home: teams[0], equipe_visiteur: teams[1], equipe_arbitre: teams[5]},
                {equipe_home: teams[3], equipe_visiteur: teams[6], equipe_arbitre: teams[1]},
                {equipe_home: teams[4], equipe_visiteur: teams[5], equipe_arbitre: teams[6]}
        ];
    }

    private plan8Teams(teams: Team[]): MatchDetails[]{
        if(!this.auto_arbitrage){
            return [
                    {equipe_home: teams[0], equipe_visiteur: teams[7] },
                    {equipe_home: teams[6], equipe_visiteur: teams[1]},
                    {equipe_home: teams[2], equipe_visiteur: teams[5]},
                    {equipe_home: teams[4], equipe_visiteur: teams[3]},
                    {equipe_home: teams[6], equipe_visiteur: teams[0]},
                    {equipe_home: teams[5], equipe_visiteur: teams[7]},
                    {equipe_home: teams[1], equipe_visiteur: teams[4]},
                    {equipe_home: teams[3], equipe_visiteur: teams[2]},
                    {equipe_home: teams[0], equipe_visiteur: teams[5]},
                    {equipe_home: teams[4], equipe_visiteur: teams[6]},
                    {equipe_home: teams[7], equipe_visiteur: teams[3]},
                    {equipe_home: teams[2], equipe_visiteur: teams[1]},
                    {equipe_home: teams[4], equipe_visiteur: teams[0]},
                    {equipe_home: teams[3], equipe_visiteur: teams[5]},
                    {equipe_home: teams[6], equipe_visiteur: teams[2]},
                    {equipe_home: teams[1], equipe_visiteur: teams[7]},
                    {equipe_home: teams[0], equipe_visiteur: teams[3]},
                    {equipe_home: teams[2], equipe_visiteur: teams[4]},
                    {equipe_home: teams[5], equipe_visiteur: teams[1]},
                    {equipe_home: teams[7], equipe_visiteur: teams[6]},
                    {equipe_home: teams[2], equipe_visiteur: teams[0]},
                    {equipe_home: teams[1], equipe_visiteur: teams[3]},
                    {equipe_home: teams[4], equipe_visiteur: teams[7]},
                    {equipe_home: teams[6], equipe_visiteur: teams[5]},
                    {equipe_home: teams[0], equipe_visiteur: teams[1]},
                    {equipe_home: teams[7], equipe_visiteur: teams[2]},
                    {equipe_home: teams[3], equipe_visiteur: teams[6]},
                    {equipe_home: teams[5], equipe_visiteur: teams[4]}
            ];
        }
        return [
                {equipe_home: teams[0], equipe_visiteur: teams[7], equipe_arbitre: teams[6]},
                {equipe_home: teams[6], equipe_visiteur: teams[1], equipe_arbitre: teams[0]},
                {equipe_home: teams[2], equipe_visiteur: teams[5], equipe_arbitre: teams[1]},
                {equipe_home: teams[4], equipe_visiteur: teams[3], equipe_arbitre: teams[2]},
                {equipe_home: teams[6], equipe_visiteur: teams[0], equipe_arbitre: teams[3]},
                {equipe_home: teams[5], equipe_visiteur: teams[7], equipe_arbitre: teams[6]},
                {equipe_home: teams[1], equipe_visiteur: teams[4], equipe_arbitre: teams[7]},
                {equipe_home: teams[3], equipe_visiteur: teams[2], equipe_arbitre: teams[4]},
                {equipe_home: teams[0], equipe_visiteur: teams[5], equipe_arbitre: teams[3]},
                {equipe_home: teams[4], equipe_visiteur: teams[6], equipe_arbitre: teams[5]},
                {equipe_home: teams[7], equipe_visiteur: teams[3], equipe_arbitre: teams[0]},
                {equipe_home: teams[2], equipe_visiteur: teams[1], equipe_arbitre: teams[7]},
                {equipe_home: teams[4], equipe_visiteur: teams[0], equipe_arbitre: teams[2]},
                {equipe_home: teams[3], equipe_visiteur: teams[5], equipe_arbitre: teams[4]},
                {equipe_home: teams[6], equipe_visiteur: teams[2], equipe_arbitre: teams[5]},
                {equipe_home: teams[1], equipe_visiteur: teams[7], equipe_arbitre: teams[2]},
                {equipe_home: teams[0], equipe_visiteur: teams[3], equipe_arbitre: teams[1]},
                {equipe_home: teams[2], equipe_visiteur: teams[4], equipe_arbitre: teams[0]},
                {equipe_home: teams[5], equipe_visiteur: teams[1], equipe_arbitre: teams[4]},
                {equipe_home: teams[7], equipe_visiteur: teams[6], equipe_arbitre: teams[5]},
                {equipe_home: teams[2], equipe_visiteur: teams[0], equipe_arbitre: teams[6]},
                {equipe_home: teams[1], equipe_visiteur: teams[3], equipe_arbitre: teams[2]},
                {equipe_home: teams[4], equipe_visiteur: teams[7], equipe_arbitre: teams[3]},
                {equipe_home: teams[6], equipe_visiteur: teams[5], equipe_arbitre: teams[4]},
                {equipe_home: teams[0], equipe_visiteur: teams[1], equipe_arbitre: teams[5]},
                {equipe_home: teams[7], equipe_visiteur: teams[2], equipe_arbitre: teams[1]},
                {equipe_home: teams[3], equipe_visiteur: teams[6], equipe_arbitre: teams[7]},
                {equipe_home: teams[5], equipe_visiteur: teams[4], equipe_arbitre: teams[3]}
    ];
    }    
                  
}


