export class Responsible{
    id: number;
    email: string;
    password?: string;
    token_expire?: Date;
    nom_user: string;
    prenom_user: string;
    status: number;
    id_role: number;
    droits?: string;
}