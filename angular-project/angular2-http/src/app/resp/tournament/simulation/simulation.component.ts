import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../../shared/models/tournament";
import { MatchsPlan } from "../../../shared/plannings/matchs-plan";
import { Group } from "../../../shared/models/group";
import { ActivatedRoute } from "@angular/router";
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { Team } from "../../../shared/models/team";
import { MatchDetails } from "../../../shared/models/match-details";
import { ConfigSimul } from "../../../shared/plannings/config-simul";


@Component({
  selector: 'my-simulation',
  templateUrl: './simulation.component.html',
  styleUrls: ['./simulation.component.css']
})
export class SimulationComponent implements OnInit {
  successMessage ='';
  errorMessage = '';
  tournament: Tournament; 
  //d: Date = new Date("2017-08-17T08:30:00+0100");
  d: Date = new Date("2017-08-17T08:30:00");
  configSimul: ConfigSimul = {tournoi_date: this.d, heure_debut_h: this.d.getHours(),
                               heure_debut_min: this.d.getMinutes(), match_duree: 5, matchs_meme_terrain: true,
                               auto_arbitrage: false, nb_terrains:2};
  groupsPlan: MatchsPlan[] = [];
  groups: Group[] = []; 
  tournamentId: number;
  simulLaunched = false;
  matchs: MatchDetails[] = [];
  //terrains: MatchDetails[] = [];

  constructor( private service: PublicTournamentService,
               private route: ActivatedRoute ){}

  ngOnInit(): void {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });

    this.service
      .getTournament(this.tournamentId)
      .subscribe(t => { this.configSimul.tournoi_date  = t.date_debut;
                        this.tournament = t;
                        });

    // get every teams in groups in this tournament
    this.service
      .getGroupsAndTeams(this.tournamentId)
      .subscribe(groups => { this.groups = groups;    
                             this.configSimul.nb_terrains = groups.length;                      
                          });
  }

  simulPlanning(){
    this.errorMessage = '';
    this.simulLaunched = true;
    this.groupsPlan = [];
    
    // for each group create a matchs planification
    this.groups.forEach(group => { 
                          if(group.teams.length<3 || group.teams.length>8){
                            this.errorMessage = `Le nombre d'équipes par groupe doit être entre 3 et 8 !`;
                            this.simulLaunched = false;
                          }else{
                          this.groupsPlan.push (new MatchsPlan(group.teams, this.configSimul.auto_arbitrage));
                          }
                        });
    
    this.matchs = []; //Delete old simulation if existing
    
    // Si pas d'erreur detectée...
    if(!this.errorMessage){
      if(this.configSimul.matchs_meme_terrain){
            this.setMatch(); // All group's matchs played on the same pitch
          }
          else{
            this.setMatchDifferentPitches();
          }

          if(this.checkConflict()) {
            //this.errorMessage = 'Il y a des conflits, vérifiez les horraires de chaque équipe.'
          }
    }
  }


  /**
   * Check if a team plays once per playtime 
   */
  private checkConflict():boolean{
    this.groupsPlan.map
    // Récupère tous le match en une seule liste
    this.matchs = [];
    this.groupsPlan.map( g =>{ g.planning.map(m => { this.matchs.push(m)})});

    
    // récupère toutes les datesHeure différentes des matchs, sans duplication
    let heuresMatchs = [];
    this.matchs.map(m => { if (heuresMatchs.indexOf(m.date_match.getTime()) == -1) heuresMatchs.push(m.date_match.getTime())});

    // pour chaque tranche horaire, vérifie q'un équipe n'est pas présente plus d'une fois
    let teamsInConflict: Team[] = [];
    heuresMatchs
      .map( h => {
        let matchsPerHour = [];
        matchsPerHour.push( this.matchs.filter(m => m.date_match.getTime() == h));

        // Récupère les équipes de la tranche horaire en cours
        let teams: Team[] = [];
        matchsPerHour.map(matchs => matchs.map( m => { teams.push(m.equipe_home);
                                                       teams.push(m.equipe_visiteur)
                                                       if(m.equipe_arbitre){teams.push(m.equipe_arbitre)}}));

        // Répertorie les équipes en conflits d'horaire
        teams.map( t => { 
          if(teams.filter( x => x.id_equipe === t.id_equipe).length > 1) {
            if(teamsInConflict.indexOf(t) == -1){
              teamsInConflict.push(t);
            }
          }})

        });

        // Y-a-t-il des équipes en conflit ?
        this.errorMessage ='';
        if(teamsInConflict.length > 0){
          this.errorMessage = `Equipe(s): `
          teamsInConflict.map(t => this.errorMessage += `${t.nom_equipe}. ` );
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
   * 
   */
   private setMatch(){
      // Si les matchs se jouent sur le même terrain       
      let terrainId:number = 0;
      //selon la configuration reçue, préparer les infos de chaque match
      this.groupsPlan.map( g =>{ let date_heure : Date = new Date(this.dateTimeStart());
                                  terrainId++;
                                  g.planning.map(m => {
                                      m.date_match = date_heure;
                                      m.statut = 'non jouée';
                                      m.id_terrain = terrainId;
                                      //this.matchs.push(m);
                                      date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000));
                                    })
                                });
  }  

  /**
   * Met à jour les données des matchs qui se jouent indifféremnt sur les terrains à disposition
   */
  private setMatchDifferentPitches(){
      // Récupère tous le match en une seule liste
      this.groupsPlan.map( g =>{ g.planning.map(m => { this.matchs.push(m)})});

      // Si les matchs ne se jouent sur le même terrain
      let nbGroups: number = this.groupsPlan.length;

      // nombre total des matchs (pour tous les groupes)
      let nbMatchTotal: number = 0;
      this.groupsPlan.map(g => nbMatchTotal += g.planning.length);
      
      // nombre de terrrains à disposition
      let terrainIds:number[]=[];
      for(let i=0; i < this.configSimul.nb_terrains; i++){ 
        terrainIds[i]=i+1;
      }

      let indexTerrain = 0;
      // mélange aléatoire des tous les matchs
      this.matchs = this.shuffle(this.matchs);

      // attribution des terrains
      let date_heure : Date = new Date(this.dateTimeStart());
      this.matchs.forEach( m => {
        m.id_terrain = terrainIds[indexTerrain++];
        indexTerrain %= terrainIds.length;
      });

      // Mise en place de l'heure
      terrainIds.forEach(id => { date_heure = new Date(this.dateTimeStart());
          this.matchs
            .filter(m => m.id_terrain == id)
            .map(m=>{
                      m.date_match = date_heure;
                      m.statut = 'non jouée';
                      date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000));
            });
      })

      // filter matchs on their group
      this.groupsPlan.map(g =>{ g.planning = this.matchs.filter(m => m.equipe_home.id_groupe == g.groupId); })
  }
    

  /**
   * Formatagde de l'heure
   */
  private dateTimeStart(): string{
    let hh = this.configSimul.heure_debut_h < 10 ? `0${this.configSimul.heure_debut_h}`:  this.configSimul.heure_debut_h;
    let min = this.configSimul.heure_debut_min < 10 ? `0${this.configSimul.heure_debut_min}`:  this.configSimul.heure_debut_min;
    return `${this.configSimul.tournoi_date}T${hh}:${min}:00`;
  }

  /**
   * Mélange le tableau reçu en paramètre
   * @param data 
   */
    private shuffle(data) {
			var m = data.length, t, i;

      while(m) {
			    i = Math.floor(Math.random() * m--);
			    t = data[m];
			    data[m] = data[i];
			    data[i] = t;
			};

	  		return data;
  };
      


}




