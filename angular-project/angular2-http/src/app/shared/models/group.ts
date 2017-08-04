import { Team } from "./team";

export class Group {
    id_groupe?: number;
    nom_groupe: string;
    id_tournoi: number;
    teams?: Team[];
}