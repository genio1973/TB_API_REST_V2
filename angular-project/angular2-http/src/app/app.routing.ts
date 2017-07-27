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
        { path: 'account', component: AccountComponent, canActivate: [AuthGuardService] },                    
        { 
          path: 'admin',
          loadChildren: 'app/admin/admin.module#adminModule'
        },
        { path: 'responsible',
          loadChildren: 'app/resp/resp.module#respModule'
        },
        { path: 'public',
          loadChildren: 'app/public/public.module#PublicModule'
          //component: PublicComponent,          
        },
        { path: '**', component: NotFoundComponent } 
    ];

export const routing: ModuleWithProviders = RouterModule.forRoot(routes);
