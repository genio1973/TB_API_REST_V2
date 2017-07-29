import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuardService } from "../shared/guards/auth-guard.service";
import { RespComponent } from "./resp.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { TournamentCreateComponent } from "./tournaments/tournament-create/tournament-create.component";
import { TournamentEditComponent } from "./tournaments/tournament-edit/tournament-edit.component";
import { TournamentSingleComponent } from "./tournaments/tournament-single/tournament-single.component";
import { TournamentComponent } from "./tournament/tournament.component";
import { GroupsComponent } from "./tournament/groups/groups.component";
import { GroupListComponent } from "./tournament/groups/group-list/group-list.component";
import { NotFoundComponent } from "../not-found/not-found.component";
import { GroupSingleComponent } from "./tournament/groups/group-single/group-single.component";
import { GroupEditComponent } from "./tournament/groups/group-edit/group-edit.component";
import { GroupCreateComponent } from "./tournament/groups/group-create/group-create.component";
import { CoachsComponent } from "./tournament/teams/coachs/coachs.component";
import { TeamEditComponent } from "./tournament/teams/team-edit/team-edit.component";
import { TeamSingleComponent } from "./tournament/teams/team-single/team-single.component";
import { TeamCreateComponent } from "./tournament/teams/team-create/team-create.component";
import { TeamListComponent } from "./tournament/teams/team-list/team-list.component";
import { TeamsComponent } from "./tournament/teams/teams.component";


export const respRoutes: Routes = [
    { 
        path: 'responsible',
        component: RespComponent,
        children: [
                    { path: '', redirectTo: '/account', pathMatch: 'full' },
                    { 
                        path: 'tournaments',
                        component: TournamentsComponent,
                        canActivateChild: [AuthGuardService],
                        children: [
                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                    { path: 'list',     component: TournamentListComponent },
                                    { path: 'create',   component: TournamentCreateComponent },
                                    { path: ':id',      component: TournamentSingleComponent},
                                    { path: ':id/edit', component: TournamentEditComponent},                         
                        ]
                                
                    },
                    { 
                        path: 'tournament/:idtournoi',
                        component: TournamentComponent,
                        canActivateChild: [AuthGuardService],
                        children: [
                                    { path: '', redirectTo: 'groups', pathMatch: 'full' },
                                    {
                                        path: 'groups',
                                        component: GroupsComponent,
                                        canActivateChild: [AuthGuardService],
                                        children: [
                                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                                    { path: 'list',          component: GroupListComponent },
                                                    { path: 'create',        component: GroupCreateComponent },
                                                    { path: ':idgroup',      component: GroupSingleComponent},
                                                    { path: ':idgroup/edit', component: GroupEditComponent},                         
                                        ]
                                    },                     
                                    {
                                        path: 'teams',
                                        component: TeamsComponent,
                                        canActivateChild: [AuthGuardService],
                                        children: [
                                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                                    { path: 'list',          component: TeamListComponent },
                                                    { path: 'create',        component: TeamCreateComponent },
                                                    { path: ':idteam',      component: TeamSingleComponent},
                                                    { path: ':idteam/edit', component: TeamEditComponent},   
                                                    { path: ':idteam/coachs', component: CoachsComponent},                      
                                        ]
                                    }   
                        ]
                                
                    },     
                                 
                  ]
    },
   // { path: '**', component: NotFoundComponent } 
];

export const respRouting: ModuleWithProviders = RouterModule.forChild(respRoutes);
