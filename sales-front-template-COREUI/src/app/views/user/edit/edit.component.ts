import { UserService } from './../user.service';
import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../../shared/shared.module';
import { ActivatedRoute, Router } from '@angular/router';
import { FormSelectDirective } from '@coreui/angular';

interface UserI {
  name:   string;
  email:  string;
  imagen:  string;
  point_of_sale:string;
  role: string;
  sucursal_name_estab: string;
  sucursal_num_estab: string;  
}

export enum Role {
  admin = 'admin',
  user  = 'user',
}

@Component({
  selector: 'app-edit',
  imports: [SharedModule, ReactiveFormsModule, FormSelectDirective],
  templateUrl: './edit.component.html',
  styleUrl: './edit.component.scss',
  host: {
    'class': 'example',
  },
})
export class EditComponent {

    roles = Object.values(Role); // ['admin', 'user']
    
    roleTouched = false;
    id_branchTouched = false;
    point_of_saleTouched =false;

    public favoriteColor = '#26ab3c';
    icons   = freeSet;
    router  = inject(Router);
    toastr  = inject(ToastrService);
    userService = inject(UserService);
    activatedRoute = inject(ActivatedRoute);
    
    branches:any =[];
    id_branch:any;

    USER = signal<UserI>({
      name: '',
      email:'',
      imagen:'',
      role:'',
      point_of_sale:'',
      sucursal_name_estab:'',
      sucursal_num_estab:''
    });

    USER_ID:any  = null;
    state:boolean = true;
    isEmpty:boolean = true;

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.USER_ID = resp.id;
        this.userService.showUser(this.USER_ID)
        .subscribe((resp:any)=>{
          this.isEmpty = Object.keys(this.USER()).length === 0;
          this.USER.set(resp.User);
        });
      });
      this.getBranches();
    }

    getBranches(){
      this.userService.getBranches()
        .subscribe((resp:any)=>{
          this.branches = resp.branches;
          console.log(this.branches);
        });
    }




    // Métodos para update cada campo (evita parser error)
    updateName(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, name: valor }));
    }
    updateEmail(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, email: valor }));
    }
    updateImagen(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, imagen: valor }));
    }
    updateRole(value: string) {
      this.USER.update(u => ({ ...u, role: value }));
    }
    updatePointOfSale(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, point_of_sale: valor }));
    }
    updateIdBranch(value: any) {
      // const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, sucursal_name_estab: value }));
    }

    updateIdPointOfSale(value: any) {
      // const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, point_of_sale: value }));
    }
    updateSucursalNameEstab(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, sucursal_name_estab: valor }));
    }
    updateSucursalNumEstab(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, sucursal_num_estab: valor }));
    }


    // Validación de email reactiva
    isEmailValid = computed(() => {
      const email = this.USER().email.trim();
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
    });
    
    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.USER();
      return (
        c.name.trim().length > 0 &&
        c.role.trim().length > 0 &&
        // c.sucursal_name_estab.trim().length > 0 &&
        c.sucursal_num_estab.trim().length > 0 &&
        c.point_of_sale.trim().length > 0 &&
        this.isEmailValid()
      );
    });

    save(){
      this.userService.updateUser(this.USER_ID, this.USER())
      .subscribe((resp:any) =>{
        if(resp.code == 403){
          this.toastr.error('Validacion', 'El usuario ya existe');
          return;
        }
        this.toastr.success('Exito', 'La usuario se ha actualizado correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/user/list");
    }

}
