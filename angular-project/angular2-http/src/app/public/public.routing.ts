import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PublicComponent } from "./public.component";
import { LoginComponent } from "./login/login.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { NotFoundComponent } from "../not-found/not-found.component";
import { TeamsComponent } from "../resp/tournament/teams/teams.component";
import { TeamListComponent } from "../resp/tournament/teams/team-list/team-list.component";
import { TournamentComponent } from "./tournament/tournament.component";
import { GroupsComponent } from "../resp/tournament/groups/groups.component";
import { GroupListComponent } from "../resp/tournament/groups/group-list/group-list.component";
import { ResultsComponent } from "../resp/tournament/results/results.component";
import { ResultListComponent } from "../resp/tournament/results/result-list/result-list.component";
import { RankingComponent } from "./tournament/rankings/rankings.component";
import { RankingListComponent } from "./tournament/rankings/ranking-list/ranking-list.component";
import { RankingSingleComponent } from "./tournament/rankings/ranking-single/ranking-single.component";

export const publicRoutes: Routes = [
        { path: 'public',
          component: PublicComponent,
          children: [
              { path: '', redirectTo: 'tournaments', pathMatch: 'full' },
              { path: 'login', component: LoginComponent },
              {
                path: 'tournaments',
                component: TournamentsComponent,
                children:[
                            { path: '', redirectTo: 'list', pathMatch: 'full' },
                            { path: 'list',    component: TournamentListComponent },
                      ]
                },
                { 
                        path: 'tournament/:idtournoi',
                        component: TournamentComponent,
                        children: [
                                    { path: '', redirectTo: 'teams', pathMatch: 'full' },
                                    {
                                        path: 'results',
                                        component: ResultsComponent,
                                        children: [
                                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                                    { path: 'list',          component: ResultListComponent },
                                        ]
                                    },
                                    {
                                        path: 'groups',
                                        component: GroupsComponent,
                                        children: [
                                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                                    { path: 'list',          component: GroupListComponent },
                                        ]
                                    }, 
                                    {
                                        path: 'teams',
                                        component: TeamsComponent,
                                        children: [
                                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                                    { path: 'list',                     component: TeamListComponent },
                                        ]
                                    }, 
                                    {
                                        path: 'rankings',
                                        component: RankingComponent,
                                        children: [
                                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                                    { path: 'list',          component: RankingListComponent },
                                                    { path: ':idgroup',      component: RankingSingleComponent},
                                        ]
                                    }                                      
                        ]
                                
                    },     
              ]
        } 
    ];

export const publicRouting: ModuleWithProviders = RouterModule.forChild(publicRoutes);
