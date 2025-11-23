import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { SharedModule } from '../../../shared/shared.module';
import { ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CustomerService } from '../customer.service';

interface CustomerI {
  name:   string;
  email:  string;
  phone:  string;
  num_identificador:string;
  address: string
}

@Component({
  selector: 'app-edit',
  imports: [SharedModule, ReactiveFormsModule],
  templateUrl: './edit.component.html',
  styleUrl: './edit.component.scss',
  host: {
    'class': 'example',
  },
})
export class EditComponent {

    public favoriteColor = '#26ab3c';
    icons   = freeSet;
    router  = inject(Router);
    toastr  = inject(ToastrService);
    customerService = inject(CustomerService);
    activatedRoute = inject(ActivatedRoute);
    
    CUSTOMER = signal<CustomerI>({
      name: '',
      email:'',
      num_identificador:'',
      address:'',
      phone:''
    });

    CUSTOMER_ID:any  = null;

    state:boolean = true;
    isEmpty:boolean = true;

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.CUSTOMER_ID = resp.id;
        this.customerService.showCustomer(this.CUSTOMER_ID)
        .subscribe((resp:any)=>{
          this.isEmpty = Object.keys(this.CUSTOMER()).length === 0;
          this.CUSTOMER.set(resp.Customer);
        });
      });
    }

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
        this.isEmailValid()
      );
    });

    save(){
      // if(!this.CUSTOMER().name || !this.CUSTOMER().num_identificador 
      // || !this.CUSTOMER().email || !this.CUSTOMER().phone || !this.CUSTOMER().address
      // ){
      //   this.toastr.error('Validacion', 'Los campos con * son obligatorios');
      //   return;
      // }

      this.customerService.updateCustomer(this.CUSTOMER_ID, this.CUSTOMER())
      .subscribe((resp:any) =>{
        if(resp.code == 403){
          this.toastr.error('Validacion', 'El cliente ya existe');
          return;
        }
        this.toastr.success('Exito', 'La cliente se ha actualizado correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/customer/list");
    }

}
