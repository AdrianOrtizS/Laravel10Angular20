import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../../shared/shared.module';
import { SupplierService } from '../supplier.service';

interface SupplierI {
  name: string,
  email: string,
  phone: string,
  num_identificador:string,
  address: string
}

@Component({
  selector: 'app-create',
  imports: [SharedModule, ReactiveFormsModule],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss'
})
export class CreateComponent {

    public favoriteColor = '#26ab3c';
    icons   = freeSet;
    router  = inject(Router);
    toastr  = inject(ToastrService);
    supplierService = inject(SupplierService);
    
    SUPPLIER = signal<SupplierI>({
      name:   '',
      email:  '',
      num_identificador:'',
      address:'',
      phone:  ''
    });

    // Métodos para update cada campo (evita parser error)
    updateName(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.SUPPLIER.update(c => ({ ...c, name: valor }));
    }

    updateEmail(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.SUPPLIER.update(c => ({ ...c, email: valor }));
    }

    updateNumIdentificador(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.SUPPLIER.update(c => ({ ...c, num_identificador: valor }));
    }

    updatePhone(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.SUPPLIER.update(c => ({ ...c, phone: valor }));
    }

    updateAddress(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.SUPPLIER.update(c => ({ ...c, address: valor }));
    }  

    // Validación de email reactiva
    isEmailValid = computed(() => {
      const email = this.SUPPLIER().email.trim();
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
    });
    
    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.SUPPLIER();
      return (
        c.name.trim().length > 0 &&
        c.num_identificador.trim().length > 0 &&
        c.phone.trim().length > 0 &&
        c.address.trim().length > 0 &&
        this.isEmailValid()
      );
    });

    save(){
      this.supplierService.createSupplier(this.SUPPLIER())
      .subscribe((resp:any) =>{
        if(resp.code == 403){
          this.toastr.error('Validacion', 'El cliente ya existe');
          return;
        }
        this.limpiarFormulario()
        this.toastr.success('Exito', 'El cliente se ha creado correctamente');
      });
    }

    // Limpiar formulario
    limpiarFormulario() {
      this.SUPPLIER.set({ 
        name:   '', 
        num_identificador: '',
        email:  '', 
        phone:  '',
        address:''});
    }

    goList(){
      this.limpiarFormulario()
      this.router.navigateByUrl("/supplier/list");
    }

}
