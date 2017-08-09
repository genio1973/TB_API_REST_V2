import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { publicRouting } from './public.routing';
import { PublicComponent } from "./public.component";
import { LoginComponent } from "./login/login.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { FormsModule } from "@angular/forms";
import { TournamentComponent } from "./tournament/tournament.component";
import { RankingComponent } from "./tournament/rankings/rankings.component";
import { RankingListComponent } from "./tournament/rankings/ranking-list/ranking-list.component";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    publicRouting
  ],
  declarations: [
    PublicComponent,
    LoginComponent,
    TournamentsComponent,
    TournamentListComponent,
    TournamentComponent,
    RankingComponent,
    RankingListComponent,
  ],
  providers: [

  ]
})
export class PublicModule {}
