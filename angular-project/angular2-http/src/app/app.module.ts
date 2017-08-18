import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';
import { routing } from './app.routing';

import { AppComponent } from './app.component';

import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/observable/throw';
import 'rxjs/add/operator/find';
import 'rxjs/add/operator/do';
import 'rxjs/add/operator/toPromise';

import { AdminService } from "./shared/services/admin.service";
import { RespComponent } from "./resp/resp.component";
import { LoginComponent } from "./public/login/login.component";
import { AuthService } from "./shared/services/auth.service";
import { AuthGuardService } from "./shared/guards/auth-guard.service";
import { NotFoundComponent } from "./not-found/not-found.component";
import { AuthAdminGuardService } from "./shared/guards/auth-admin-guard.service";
import { AccountComponent } from "./resp/account/account.component";
import { PublicModule } from "./public/public.module";
import { AdminModule } from "./admin/admin.module";
import { RespModule } from "./resp/resp.module";
import { PublicTournamentService } from "./shared/services/public-tournament.service";
import { RespTournamentService } from "./shared/services/resp.tournament.service";
import { KeysPipe } from "./shared/tools/keys-pipe";


@NgModule({
  declarations: [
    AppComponent,
    AccountComponent,
    KeysPipe,
    NotFoundComponent
  ],
  imports: [
    BrowserModule,
    HttpModule,
    FormsModule,
    PublicModule,
    RespModule,
    AdminModule,
    routing
  ],
  providers: [PublicTournamentService, 
    RespTournamentService, 
    AdminService,
    AuthService, 
    AuthGuardService, 
    AuthAdminGuardService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
