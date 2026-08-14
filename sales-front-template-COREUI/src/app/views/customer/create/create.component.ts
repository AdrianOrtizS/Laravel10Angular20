import { CustomerService } from './../customer.service';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../../shared/shared.module';
import { FormSelectDirective } from '@coreui/angular';

interface CustomerI {
  name: string,
  email: string,
  phone: string,
  num_identificador:string,
  address: string,
  tipo_identificador:string
}

@Component({
  selector: 'app-create',
  imports: [SharedModule, ReactiveFormsModule, FormSelectDirective],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss',
  host: {
    'class': 'example',
  },
})
export class CreateComponent {

    public favoriteColor = '#26ab3c';
    icons   = freeSet;
    router  = inject(Router);
    toastr  = inject(ToastrService);
    customerService = inject(CustomerService);
    
    CUSTOMER = signal<CustomerI>({
      name:   '',
      email:  '',
      num_identificador:'',
      address:'',
      phone:  '',
      tipo_identificador: ''
    });

    // Métodos para update cada campo (evita parser error)
    updateName(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.CUSTOMER.update(c => ({ ...c, name: valor }));
    }

    updateEmail(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.CUSTOMER.update(c => ({ ...c, email: valor }));
    }

    updateNumIdentificador(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.CUSTOMER.update(c => ({ ...c, num_identificador: valor }));
    }

    updatePhone(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.CUSTOMER.update(c => ({ ...c, phone: valor }));
    }

    updateAddress(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.CUSTOMER.update(c => ({ ...c, address: valor }));
    }  

    nombreTouched = false;
    updateTipo_identificador(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.CUSTOMER.update(c => ({ ...c, tipo_identificador: valor }));
    }

    // Validación de email reactiva
    isEmailValid = computed(() => {
      const email = this.CUSTOMER().email.trim();
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
    });
    
    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.CUSTOMER();
      return (
        c.name.trim().length > 0 &&
        c.num_identificador.trim().length > 0 &&
        c.phone.trim().length > 0 &&
        c.address.trim().length > 0 &&
        c.tipo_identificador.trim().length > 0 &&
        this.isEmailValid()
      );
    });

    save(){
      this.customerService.createCustomer(this.CUSTOMER())
      .subscribe((resp:any) =>{
        if(resp.code == 403){
          let errors = Object.values(resp.message);
          errors.forEach((err:any) => {
            setTimeout(() => {
              this.toastr.error('Validacion', err);
            }, 800);
          });
          return;
        }
        this.limpiarFormulario()
        this.toastr.success('Exito', 'El cliente se ha creado correctamente');
      });
    }

    // Limpiar formulario
    limpiarFormulario() {
      this.CUSTOMER.set({ 
        name:   '', 
        num_identificador: '',
        email:  '', 
        phone:  '',
        address:'',
        tipo_identificador:''
      });
    }

    goList(){
      this.limpiarFormulario()
      this.router.navigateByUrl("/customer/list");
    }

}