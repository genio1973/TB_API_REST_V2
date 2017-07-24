import { Component } from '@angular/core';
import { PeopleService } from "./people.service";


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers: [PeopleService]
})
export class AppComponent {
  title = 'Star Wars PPlz!!!';
}
