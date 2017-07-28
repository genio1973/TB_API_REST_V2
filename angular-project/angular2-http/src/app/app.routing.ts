import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuardService } from "./shared/guards/auth-guard.service";
import { NotFoundComponent } from "./not-found/not-found.component";
import { AuthAdminGuardService } from "./shared/guards/auth-admin-guard.service";
import { AccountComponent } from "./resp/account/account.component";

export const routes: Routes = [
        { path: '', redirectTo: 'public/tournaments', pathMatch: 'full' },   
        { path: 'account', component: AccountComponent, canActivate: [AuthGuardService] },                    
        { 
          path: 'admin',
          loadChildren: 'app/admin/admin.module#AdminModule'
          //loadChildren: 'app/admin/admin.module'
        },
        { path: 'responsible',
          loadChildren: 'app/resp/resp.module#RespModule'
          //loadChildren: 'app/resp/resp.module'
        },
        { path: 'public',
          loadChildren: 'app/public/public.module#PublicModule'
          //loadChildren: 'app/public/public.module'
          //component: PublicComponent,          
        },
        //{ path: '**', component: NotFoundComponent } 
    ];

export const routing: ModuleWithProviders = RouterModule.forRoot(routes);
