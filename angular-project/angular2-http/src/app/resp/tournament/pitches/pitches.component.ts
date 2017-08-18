import { Component, OnInit } from '@angular/core';
import { Pitch } from "../../../shared/models/pitch";
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { Router, ActivatedRoute } from "@angular/router";

@Component({
  selector: 'my-pitches',
  templateUrl: './pitches.component.html',

  styleUrls: ['./pitches.component.css']
})
export class PitchesComponent implements OnInit {
  successMessage = '';
  errorMessage = '';
  pitches: Pitch[];
  tournamentId: number;
  
  constructor( private service: PublicTournamentService,
               private respService: RespTournamentService,
               private router: Router, 
               private route: ActivatedRoute ){}

  ngOnInit(): void {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => { this.tournamentId = params['idtournoi']; });

    // get every teams in groups in this tournament
    this.service
      .getTournamentPitches(this.tournamentId)
      .subscribe(pitches => { this.pitches = pitches });
    
  }

  /**
   * Udpate pitches
   */
  updatePitches(){
    this.pitches.forEach( p => 
      this.respService.updatePitch(p)
        .subscribe(
          user => {
            this.successMessage = 'Terrain(s) mis à jour.';
          },
          err => {
            this.errorMessage = err;
          })
      );
    //this.router.navigate([`/responsible/tournament/${this.tournamentId}`]);
  }
    
}
