import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../../shared/models/tournament";
import { MatchsPlan } from "../../../shared/plannings/matchs-plan";
import { Group } from "../../../shared/models/group";
import { ActivatedRoute, Router } from "@angular/router";
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { Team } from "../../../shared/models/team";
import { MatchDetails } from "../../../shared/models/match-details";
import { ConfigSimul } from "../../../shared/plannings/config-simul";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { Pitch } from "../../../shared/models/pitch";
import { ApiResponse } from "../../../shared/models/api-response";
import { Match } from "../../../shared/models/match";
import { MatchsGroupBy } from "../../../shared/plannings/matchs-group-by";


@Component({
  selector: 'my-simulation',
  templateUrl: './simulation.component.html',
  styleUrls: ['./simulation.component.css']
})
export class SimulationComponent implements OnInit {
  successMessage ='';
  errorMessage = '';
  tournoiNouveau: boolean = false;
  tournament: Tournament; 
  d: Date = new Date("2017-08-17T08:30:00");
  d_pause: Date; // = new Date("2017-08-17T12:30:00");
  configSimul: ConfigSimul = { tournoi_date: this.d, heure_debut_h: this.d.getHours(),
                               heure_debut_min: this.d.getMinutes(), match_duree: 12, matchs_meme_terrain: true,
                               auto_arbitrage: false, nb_terrains:2, pausePresence: false,
                               pause_debut_h:12, pause_debut_min:10, pause_duree: 60};

  groupsPlan: MatchsPlan[] = [];
  groups: Group[] = []; 
  tournamentId: number;
  simulLaunched = false;
  readyToDbPush: boolean = false;

  constructor( private service: PublicTournamentService,
               private respService: RespTournamentService,
               private router: Router, 
               private route: ActivatedRoute ){}

  ngOnInit(): void {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });

    this.respService
      .getTournament(this.tournamentId)
      .subscribe(t => { this.configSimul.tournoi_date  = t.date_debut;
                        this.tournament = t;
                        if(t.id_statut == 1){
                          this.tournoiNouveau = true;
                        }
                        });

    // get every teams in groups in this tournament
    this.service
      .getGroupsAndTeams(this.tournamentId)
      .subscribe(groups => { this.groups = groups;    
                             this.configSimul.nb_terrains = groups.length;
                          });
  }

  /**
   * Build a planning matchs with the user's settings
   */
  simulPlanning(){
    this.errorMessage = '';
    this.simulLaunched = true;
    this.groupsPlan = [];

    //définir l'heure de la pause
    if(this.configSimul.pausePresence){
      this.d_pause = new Date(this.configSimul.tournoi_date);
      this.d_pause.setHours(this.configSimul.pause_debut_h);
      this.d_pause.setMinutes(this.configSimul.pause_debut_min);
    }


    // for each group build the matchs planification
    this.groups.map(group => { 
                          if(group.teams.length<3 || group.teams.length>8){
                            this.errorMessage = `Le nombre d'équipes par groupe doit être entre 3 et 8 !`;
                            this.simulLaunched = false;
                          }else{
                            this.groupsPlan.push (new MatchsPlan(group.teams, this.configSimul.auto_arbitrage, group.nom_groupe));
                          }
                        });


    // si tous les groupes sont prêts (nb équipes min, max ok)
    if(this.simulLaunched){
      if(this.configSimul.matchs_meme_terrain){
        this.setMatch(); // Each group : matchs played on the same pitch
      }
      else{
        this.setMatchDifferentPitches();
      }
    
      // Contrôle s'il existe des conflits (equipe impliquée sur plusieurs front en même temps)
      this.readyToDbPush = true;
      if(this.checkConflict()) {
          this.readyToDbPush = false;
      }
    }

  }


  /**
   * Check if a team plays once per playtime 
   * Return false if no conflict is present
   */
  private checkConflict():boolean{
    let matchs: MatchDetails[] = [];
    // Récupère tous le match en une seule liste
    matchs = [];
    this.groupsPlan.map( g =>{ g.planning.map(m => { matchs.push(m)})});

    
    // récupère toutes les datesHeure différentes des matchs, sans duplication
    let heuresMatchs = [];
    matchs.map(m => { if (heuresMatchs.indexOf(m.date_match.getTime()) == -1) heuresMatchs.push(m.date_match.getTime())});

    // pour chaque tranche horaire, vérifie q'un équipe n'est pas présente plus d'une fois
    let teamsInConflict: Team[] = [];
    heuresMatchs
      .map( h => {
        let matchsPerHour = [];
        matchsPerHour.push( matchs.filter(m => m.date_match.getTime() == h));

        // Récupère les équipes de la tranche horaire en cours
        let teams: Team[] = [];
        matchsPerHour.map(matchs => matchs.map( m => {  
                                                       teams.push(m.equipe_home);
                                                       teams.push(m.equipe_visiteur);
                                                       if(m.equipe_arbitre){teams.push(m.equipe_arbitre)}}));

        // Répertorie les équipes en conflits d'horaire
        teams.map( t => { 
          if(teams.filter( x => x.id_equipe === t.id_equipe).length > 1) {
            if(teamsInConflict.indexOf(t) == -1){
              t.date_debut = h;
              teamsInConflict.push(t);
            }
          }})
        });

        // Y-a-t-il des équipes en conflit ?
        this.errorMessage ='';
        if(teamsInConflict.length > 0){
          this.errorMessage = `Equipe(s): `
          teamsInConflict.map(t => this.errorMessage += ` --${t.nom_equipe}@${this.msToTime(t.date_debut)}--` );
          return true;
        }
    return false;
  }

  /**
   * Clear all messages after 5 sec
   */
  private clearMessages(){
    setTimeout(() => {
      this.errorMessage = '';
      this.successMessage = '';  
    }, 5000);
  }

  /**
   * Converte time ms in time (hh:mm:ss)
   * @param duration 
   */
  private msToTime(duration) {
        let minutes:number = Math.trunc((duration/(1000*60))%60);
        let hours:number = Math.trunc((duration/(1000*60*60))%24);

        hours += 2; //UTC +2 !!
        let shours: string = (hours < 10) ? "0" + hours.toString() : hours.toString();
        let sminutes: string = (minutes < 10) ? "0" + minutes.toString() : minutes.toString();

        return shours + ":" + sminutes;
    }


  /**
   * Build the pitchPlanning with the number of pitches availaibles.
   * The group's matches are planigied on same pitch
   */
   private setMatch(){
      // Si les matchs se jouent sur le même terrain       
      let terrainId:number = 0;
      let pauseToDo: boolean;

      //selon la configuration reçue, préparer les infos de chaque match
      this.groupsPlan.map( g =>{ let date_heure : Date = new Date(this.dateTimeStart());
        terrainId++;
        pauseToDo = this.configSimul.pausePresence;
        g.planning.map(m => {
          m.date_match = date_heure;
          m.statut = null;
          m.id_terrain = terrainId;

          //Définir la nouvelle heure pour les prochains matchs
          date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000));

          //Si c'est dans l'heure de la pause alors décaler de la durée de la pause
          if(pauseToDo && date_heure.getTime() >= this.d_pause.getTime()){
            date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000) + (this.configSimul.pause_duree*60*1000)); 
            pauseToDo = false;
          }
        })
      });
  }  

  /**
   * Build the pitchPlanning with the number of pitches availaibles.
   * The group's matches can be played on different pitches
   */
  private setMatchDifferentPitches(){
   // Get the tot matchs 
    let nbPitches = this.configSimul.nb_terrains;
    let nbMatchTotal: number = 0;
    let pauseToDo: boolean = this.configSimul.pausePresence;
    this.groupsPlan.map(group => nbMatchTotal+= group.planning.length);

    // Build the pitchPlanning with the number of pitches availaibles
    let pitchesPlanning: {terrain:number, date_match: Date}[] = [];
    let date_heure : Date = new Date(this.dateTimeStart());
    for(let i=0; i<nbMatchTotal; i++){      
      pitchesPlanning.push({terrain: i%nbPitches+1, date_match: date_heure});
      if(i%nbPitches==nbPitches-1){
        date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000))
          //Définir la nouvelle heure pour les prochains matchs
          date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000));

          //Si c'est dans l'heure de la pause alors décaler de la durée de la pause
          if(pauseToDo && date_heure.getTime() >= this.d_pause.getTime()){
            date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000) + (this.configSimul.pause_duree*60*1000)); 
            pauseToDo = false;
          }
      }
    }


    // Build general planning. Put for each match the pitch number
    let numberGroups: number = this.groupsPlan.length -1 ; // 0 to length-1
    let planning: MatchDetails[] = [];
    let match: MatchDetails;
    let cptPitch: number = 0;

    // Build the planning
    for(let i=0; i<nbMatchTotal; i++){
      // If the planning of a group contains more than 2 matches of the little planning group the take out 2 matches of the greather
      // Else take out just one match from the bigger group
      // first of all sort the groups (number of maths)
      this.groupsPlan.sort((a: MatchsPlan, b: MatchsPlan) => {
        if (a.planning.length < b.planning.length) {
          return 1;
        } else if (a.planning.length > b.planning.length) {
          return -1;
        } else {
          return 0;
        }});

      // take out one match
      match = this.groupsPlan[0].planning.pop();
      match.id_terrain = pitchesPlanning[cptPitch].terrain;
      match.statut = null;
      match.date_match = pitchesPlanning[cptPitch++].date_match;
      planning.push(match);

      // the greather goups was containing 2 matchs more than the little group ?
      if(this.groupsPlan[0].planning.length > this.groupsPlan[numberGroups].planning.length){
        match = this.groupsPlan[0].planning.pop();
        match.id_terrain = pitchesPlanning[cptPitch].terrain;
        match.statut = null;
        match.date_match = pitchesPlanning[cptPitch++].date_match;
        planning.push(match);
        i++;
      }
    }


    // replace les matchs dans les plannings de chaque groupe
    // mémoriser les numéro des groupes
    let memGroup: number[] = [];
    this.groups.map(g => memGroup.push(g.id_groupe));

    planning.map(m => {
      let index = memGroup.indexOf(m.equipe_home.id_groupe);  //retrieve index
      this.groupsPlan[index].planning.push(m);
    })

    // Do we want an auto-referee by ohters teams ?
    if(this.configSimul.auto_arbitrage){
      this.selfRefereeTeam();
    }
  }
    
  /**
   * Define the self-referee
   * A referee is a team on the previous played match on the same pitch
   */
  private selfRefereeTeam(){  
    
    // Numerate the pitches from 1 to number of pitches
    let terrainIds:number[]=[];
    for(let i=0; i < this.configSimul.nb_terrains; i++){ 
      terrainIds[i]=i+1;
    }
  
    // Get all match in a array
    let myPlanning: MatchDetails[] = [];
    //console.log( this.groupsPlan[0].planning[0]);
    this.groupsPlan.map(g => g.planning.map( m => myPlanning.push(m) ));
    

    // Group match by pitch
    let matchsGroupBy: MatchsGroupBy[] = [];
    terrainIds.map(numTerrain => {
      let plan = myPlanning.filter(m=> m.id_terrain == numTerrain);
          // trie par heure
          plan.sort((a: any, b: any) => {
            if (a.date_match < b.date_match) {
              return -1;
            } else if (a.date_match > b.date_match) {
              return 1;
            } else {
              return 0;
            }});
      matchsGroupBy.push(new MatchsGroupBy(plan, `Terrrain ${numTerrain}`));
    })

    // Define teams referee for each matchs
    // fist match : the referee team is one of the second match
    // next match : the referee teams in one of previous match
    matchsGroupBy.map( g => {
      let referee: Team = g.planning[1].equipe_home;
      g.planning.map( m => {
        m.equipe_arbitre = referee;
        referee = m.equipe_visiteur;
      })
    })

    //console.log(matchsGroupBy);
  }

  /**
   * Formatage de l'heure
   */
  private dateTimeStart(): string{
    let hh = this.configSimul.heure_debut_h < 10 ? `0${this.configSimul.heure_debut_h}`:  this.configSimul.heure_debut_h;
    let min = this.configSimul.heure_debut_min < 10 ? `0${this.configSimul.heure_debut_min}`:  this.configSimul.heure_debut_min;
    return `${this.configSimul.tournoi_date}T${hh}:${min}:00`;
  }

  /**
   * En cas de changement de l'attribut check : les matchs se jouent ou pas sur le même terrain
   */
  playSamePitch(){
    this.configSimul.matchs_meme_terrain = !this.configSimul.matchs_meme_terrain;

    if(this.configSimul.nb_terrains < this.groups.length){
      this.configSimul.matchs_meme_terrain = true;
    }
    if (!this.configSimul.matchs_meme_terrain){
      this.configSimul.auto_arbitrage = false;
    }
  }

  matchs: MatchDetails[] = [];
  
  /**
   * Insert the planning data (with tournament) in database
   */
  InsertDataMatchsToDB(){

    // Récupère tous le match en une seule liste
    this.groupsPlan.map( g =>{ g.planning.map(m => { this.matchs.push(m)})});

    //Insert les nouveau terrains    
    this.insertPitchesDB();
    
    // Insert les matchs
    this.insertMatchsDB();
 
  }


  /**
   * Insert les nouveauy terrains  
   */
  private insertPitchesDB(){
    let id_first_pitch_inserted: number = 0;
    let pitches: Pitch[] = [];

    // crée autant de terrain qu'il est configuré par l'utilistateur
    for(let i=0; i<this.configSimul.nb_terrains;i++){
      let ter: Pitch = {id_terrain:0, nom_terrain:'A'};
      ter.nom_terrain = 'Ter_' + (i+1);
      pitches.push(ter);
    }

    // insert in the db all the necessary pitches
    this.respService.createPitches(pitches)
              .subscribe(
                res => {
                  let apiResp : ApiResponse = res;
                  id_first_pitch_inserted = apiResp.result.id_premier_insert;
                  pitches.map(t => t.id_terrain = id_first_pitch_inserted++);

                  // update in planning all the id pitches (from inserted id)
                  this.matchs.map(m=> {
                    m.id_terrain = pitches[m.id_terrain-1].id_terrain;
                  });

                },
                err => {
                  this.errorMessage = err;
                });
  }

  /**
   * Insert les matchs dans la bd, avec un délai avant de créer 
   */
  private insertMatchsDB(){
    
    let matchsToPost: Match[] = [];
    
    setTimeout(() => {
    this.matchs.map(m => { matchsToPost.push(this.toMatchFieldCreation(m))});
      this.respService.createMatchs(matchsToPost)
      .subscribe(
        res => {
          this.successMessage = "Création terminée";
          // Le tournoi a un nouveau statut : OUVERT 
          this.tournament.id_statut = 2;
          this.respService.updateTournament(this.tournament)
              .subscribe(
                res => {
                  this.successMessage += "Le tournoi passe en mode ouvert";
                }
              )
          this.router.navigate(['/responsible/tournaments/list']);
        },
        err => {
          this.errorMessage = err;
        });
    }, 2000);
  }

  /**
   * Format match to insert in db
   * @param m
   */
  private toMatchFieldCreation(m: MatchDetails): Match {
    let match: Match = {
              date_match: `${m.date_match.getFullYear()}-${m.date_match.getMonth()}-${m.date_match.getDay()}`,
              heure: `${m.date_match.getHours()}:${m.date_match.getMinutes()}:00`,
              id_equipe_home: m.equipe_home.id_equipe,
              id_equipe_visiteur: m.equipe_visiteur.id_equipe,
              id_terrain: m.id_terrain,
              statut: m.statut,
      }

    if(m.equipe_arbitre){
      match.id_equipe_arbitre = m.equipe_arbitre.id_equipe;
    }
    return match;
  }

}




