import { Directive, Input } from '@angular/core';
import { AbstractControl, ValidationErrors, Validator, NG_VALIDATORS } from "@angular/forms";

@Directive({
  selector: '[min]',
  providers: [{provide: NG_VALIDATORS, useExisting: MinValidatorDirective, multi: true}]
})

export class MinValidatorDirective implements Validator {
  @Input() min: number;
  constructor() { }

  
  validate(control: AbstractControl): ValidationErrors {
    const currentValue = control.value;
    const isValid = currentValue >= this.min;

    // return errors as an object
    return isValid ? null : {
      min: {
        valid: false
      }
    };
  }
}
