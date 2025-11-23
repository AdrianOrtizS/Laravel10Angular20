import { SupplierService } from './../supplier.service';
import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { SharedModule } from '../../../shared/shared.module';
import { ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';

interface SupplierI {
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
    supplerService = inject(SupplierService);
    activatedRoute = inject(ActivatedRoute);
    
    SUPPLIER = signal<SupplierI>({
      name: '',
      email:'',
      num_identificador:'',
      address:'',
      phone:''
    });

    SUPPLIER_ID:any  = null;

    state:boolean = true;
    isEmpty:boolean = true;

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.SUPPLIER_ID = resp.id;
        this.supplerService.showSupplier(this.SUPPLIER_ID)
        .subscribe((resp:any)=>{
          console.log(resp);
          this.isEmpty = Object.keys(this.SUPPLIER()).length === 0;
          this.SUPPLIER.set(resp.supplier);
        });
      });
    }

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

      this.supplerService.updateSupplier(this.SUPPLIER_ID, this.SUPPLIER())
      .subscribe((resp:any) =>{
        if(resp.code == 403){
          this.toastr.error('Validacion', 'El cliente ya existe');
          return;
        }
        this.toastr.success('Exito', 'La cliente se ha actualizado correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/supplier/list");
    }

}
