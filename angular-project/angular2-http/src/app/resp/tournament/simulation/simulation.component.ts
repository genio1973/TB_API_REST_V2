import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../../shared/models/tournament";
import { ConfigSimul } from "../../../shared/plannings/ConfigSimul";
import { MatchsPlan } from "../../../shared/plannings/matchs-plan";
import { Group } from "../../../shared/models/group";
import { ActivatedRoute } from "@angular/router";
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { Team } from "../../../shared/models/team";


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
  configSimul: ConfigSimul = {tournoi_date: this.d, heure_debut_h: this.d.getHours(), heure_debut_min: this.d.getMinutes(), match_duree: 5, matchs_meme_terrain: true, nb_terrains:2};
  groupsPlan: MatchsPlan[] = [];
  groups: Group[] = []; 
  tournamentId: number;
  simulLaunched = false;

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
                            //console.log(`==>> ${this.groups[1].teams[1].nom_equipe}`);
                          });
  }

  simulPlanning(){
    this.simulLaunched = true;
    this.groupsPlan = [];
    
    // for each group create a matchs planification
    this.groups.forEach(group => { 
                          if(group.teams.length<3 || group.teams.length>8){
                            this.errorMessage = `Le nombre d'équipes par groupe doit être entre 3 et 8 !`;
                            this.simulLaunched = false;
                          }
                          this.groupsPlan.push (new MatchsPlan(group.teams));
                        });
    this.clearMessages();
  }

  toggleMatchs_meme_terrain(){
    this.configSimul.matchs_meme_terrain = !this.configSimul.matchs_meme_terrain; 
  }

  /**
   * Clear all messages after 5 sec
   */
  clearMessages(){
    setTimeout(() => {
      this.errorMessage = '';
      this.successMessage = '';  
    }, 5000);

  }
}




