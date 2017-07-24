import { Routes } from "@angular/router";
import { RespComponent } from "./resp.component";


const respRoutes: Routes = [
  {
    path: 'resp-center',
    component: RespComponent,
    children: [
      {
        path: '',
        component: RespListComponent,
        children: [
          {
            path: ':id',
            component: RespDetailComponent
          },
          {
            path: '',
            component: MyAccountComponent
          }
        ]
      }
    ]
  }
];