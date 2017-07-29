import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from "@angular/forms";
import { respRouting } from "./resp.routing";
import { RespComponent } from "./resp.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { TournamentCreateComponent } from "./tournaments/tournament-create/tournament-create.component";
import { TournamentEditComponent } from "./tournaments/tournament-edit/tournament-edit.component";
import { TournamentSingleComponent } from "./tournaments/tournament-single/tournament-single.component";
import { TournamentComponent } from "./tournament/tournament.component";
import { GroupsComponent } from "./tournament/groups/groups.component";
import { TeamsComponent } from "./tournament/teams/teams.component";
import { CoachsComponent } from "./tournament/teams/coachs/coachs.component";
import { MatchsComponent } from "./tournament/groups/matchs/matchs.component";
import { GroupSingleComponent } from "./tournament/groups/group-single/group-single.component";
import { GroupListComponent } from "./tournament/groups/group-list/group-list.component";
import { GroupCreateComponent } from "./tournament/groups/group-create/group-create.component";
import { GroupEditComponent } from "./tournament/groups/group-edit/group-edit.component";
import { TeamCreateComponent } from './tournament/teams/team-create/team-create.component';
import { TeamEditComponent } from './tournament/teams/team-edit/team-edit.component';
import { TeamSingleComponent } from './tournament/teams/team-single/team-single.component';
import { TeamListComponent } from './tournament/teams/team-list/team-list.component';
import { CoachSingleComponent } from "./tournament/teams/coachs/coach-single/coach-single.component";
import { CoachEditComponent } from "./tournament/teams/coachs/coach-edit/coach-edit.component";
import { CoachCreateComponent } from "./tournament/teams/coachs/coach-create/coach-create.component";
import { CoachListComponent } from "./tournament/teams/coachs/coach-list/coach-list.component";


@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    respRouting
  ],
  declarations: [
    RespComponent,
    TournamentsComponent,
    TournamentSingleComponent,
    TournamentListComponent,
    TournamentCreateComponent,
    TournamentEditComponent,
    TournamentComponent,
    GroupsComponent,
    TeamsComponent,
    CoachsComponent,
    MatchsComponent,
    GroupEditComponent,
    GroupCreateComponent,
    GroupListComponent,
    GroupSingleComponent,
    TeamCreateComponent,
    TeamEditComponent,
    TeamSingleComponent,
    TeamListComponent,
    CoachListComponent,
    CoachCreateComponent,
    CoachEditComponent,
    CoachSingleComponent,
  ],
  providers: [
  ]
})
export class RespModule {}

