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

import { UserService } from "./shared/services/user.service";
import { UsersComponent } from './admin/users/users.component';
import { UserListComponent } from './admin/users/user-list/user-list.component';
import { UserCreateComponent } from './admin/users/user-create/user-create.component';
import { UserEditComponent } from './admin/users/user-edit/user-edit.component';
import { UserSingleComponent } from './admin/users/user-single/user-single.component';
import { TournamentService } from "./shared/services/tournament.service";
import { TournamentsComponent } from "./public/tournaments/tournaments.component";
import { TournamentListComponent } from "./public/tournaments/tournament-list/tournament-list.component";
import { TournamentSingleComponent } from "./public/tournaments/tournament-single/tournament-single.component";
import { ResponsibleCreateComponent } from "./admin/responsibles/responsible-create/responsible-create.component";
import { ResponsibleEditComponent } from "./admin/responsibles/responsible-edit/responsible-edit.component";
import { ResponsibleListComponent } from "./admin/responsibles/responsible-list/responsible-list.component";
import { ResponsibleSingleComponent } from "./admin/responsibles/responsible-single/responsible-single.component";
import { ResponsiblesComponent } from "./admin/responsibles/responsibles.component";
import { ResponsibleService } from "./shared/services/responsible.service";
import { AdminComponent } from "./admin/admin.component";
import { RespComponent } from "./resp/resp.component";
import { PublicComponent } from "./public/public.component";
import { AccountComponent } from "./resp/account/account.component";
import { LoginComponent } from "./public/login/login.component";
import { AuthService } from "./shared/services/auth.service";
import { AuthGuardService } from "./shared/guards/auth-guard.service";
import { NotFoundComponent } from "./not-found/not-found.component";


@NgModule({
  declarations: [
    AppComponent,
    UsersComponent,
    UsersComponent,
    UserListComponent,
    UserCreateComponent,
    UserEditComponent,
    UserSingleComponent,
    TournamentsComponent,
    TournamentListComponent,
    TournamentSingleComponent,
    ResponsiblesComponent,
    ResponsibleCreateComponent,
    ResponsibleEditComponent,
    ResponsibleListComponent,
    ResponsibleSingleComponent,
    AdminComponent,
    RespComponent,
    PublicComponent,
    AccountComponent,
    LoginComponent,
    NotFoundComponent
  ],
  imports: [
    BrowserModule,
    HttpModule,
    FormsModule,
    routing
  ],
  providers: [UserService, TournamentService, ResponsibleService, AuthService, AuthGuardService],
  bootstrap: [AppComponent]
})
export class AppModule { }
