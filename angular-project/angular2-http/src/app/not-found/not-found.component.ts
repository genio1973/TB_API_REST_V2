import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-not-found',
  template: `
    <div class="jumbotron text-center">
      <h3>404 </h3>
      <p> Oups ! Page non trouvée...</p>
    </div>
  `,
  styleUrls: ['./not-found.component.css']
})
export class NotFoundComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
