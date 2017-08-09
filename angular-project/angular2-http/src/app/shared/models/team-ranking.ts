import { OpponentInRanking } from "./opponent-in-ranking";

export class TeamRanking {

    id_equipe: number;
    nom_equipe: string;
    points_actuels?: number;
    matchs_joues?: number;
    matchs_gagnes_3pts?: number;
    matchs_gagnes_2pts?: number;
    matchs_perdus_1pt?: number;
    matchs_perdus_0pt?: number;
    sets_joues?: number;
    sets_gagnes?: string;
    sets_perdus?:number;
    sets_ratio?: number;
    points_sets_realises?: number;
    points_sets_encaisses?: number;
    points_sets_ratio?: number;
    
    adversersaires_joues?: OpponentInRanking[];
}                