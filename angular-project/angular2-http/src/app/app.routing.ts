import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { UsersComponent } from "./admin/users/users.component";
import { UserListComponent } from "./admin/users/user-list/user-list.component";
import { UserCreateComponent } from "./admin/users/user-create/user-create.component";
import { UserSingleComponent } from "./admin/users/user-single/user-single.component";
import { UserEditComponent } from "./admin/users/user-edit/user-edit.component";
import { TournamentListComponent } from "./public/tournaments/tournament-list/tournament-list.component";
import { TournamentsComponent } from "./public/tournaments/tournaments.component";
import { TournamentSingleComponent } from "./public/tournaments/tournament-single/tournament-single.component";
import { ResponsibleListComponent } from "./admin/responsibles/responsible-list/responsible-list.component";
import { ResponsibleEditComponent } from "./admin/responsibles/responsible-edit/responsible-edit.component";
import { ResponsibleSingleComponent } from "./admin/responsibles/responsible-single/responsible-single.component";
import { ResponsibleCreateComponent } from "./admin/responsibles/responsible-create/responsible-create.component";
import { AdminComponent } from "./admin/admin.component";
import { ResponsiblesComponent } from "./admin/responsibles/responsibles.component";
import { PublicComponent } from "./public/public.component";
import { LoginComponent } from "./public/login/login.component";
import { AuthGuardService } from "./shared/guards/auth-guard.service";
import { NotFoundComponent } from "./not-found/not-found.component";
import { AuthAdminGuardService } from "./shared/guards/auth-admin-guard.service";
import { RespComponent } from "./resp/resp.component";
import { AccountComponent } from "./resp/account/account.component";

export const routes: Routes = [
        { path: '', redirectTo: 'public/tournaments', pathMatch: 'full' },                       
        { 
          path: 'admin',
          component: AdminComponent,
          canActivateChild: [AuthAdminGuardService],          
          children: [
            { path: '', redirectTo: 'resp', pathMatch: 'full' },
            { 
              path: 'resp',
              component: ResponsiblesComponent,
              canActivateChild: [AuthAdminGuardService],
              children: [
                            { path: '', redirectTo: 'list', pathMatch: 'full' },
                            { path: 'list',     component: ResponsibleListComponent },
                            { path: 'create',   component: ResponsibleCreateComponent },
                            { path: ':id',      component: ResponsibleSingleComponent },
                            { path: ':id/edit', component: ResponsibleEditComponent }
                        ]
            },            
            { 
              path: 'users',
              component: UsersComponent,
              canActivateChild: [AuthAdminGuardService],
              children: [
                            { path: '', redirectTo: 'list', pathMatch: 'full' },
                            { path: 'list',     component: UserListComponent },
                            { path: 'create',   component: UserCreateComponent },
                            { path: ':id',      component: UserSingleComponent },
                            { path: ':id/edit', component: UserEditComponent }
                        ]
            },
            ]
        },
        { path: 'responsible',
          component: RespComponent,
          canActivateChild: [AuthGuardService],
          children: [
                        { path: '', redirectTo: 'account', pathMatch: 'full' },
                        { path: 'account',        component: AccountComponent },
                        { path: 'tournaments',    component: TournamentListComponent },
                    ]
            },
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
                            { path: ':id',     component: TournamentSingleComponent },

                      ] }
              ]
        },
        { path: '**', component: NotFoundComponent } 
    ];

export const routing: ModuleWithProviders = RouterModule.forRoot(routes);
