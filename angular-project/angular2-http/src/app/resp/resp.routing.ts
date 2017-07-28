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
                                    }                        
                        ]
                                
                    },     
                                 
                  ]
    },
   // { path: '**', component: NotFoundComponent } 
];

export const respRouting: ModuleWithProviders = RouterModule.forChild(respRoutes);
