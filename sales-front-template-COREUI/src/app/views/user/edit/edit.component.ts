import { UserService } from './../user.service';
import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
// import { Component, computed, inject, signal, effect } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../../shared/shared.module';
import { ActivatedRoute, Router } from '@angular/router';
import { FormSelectDirective } from '@coreui/angular';
import { Component, computed, inject, signal, effect } from '@angular/core';

interface UserI {
  name:   string;
  email:  string;
  imagen:  string;
  id_point_of_sale:number;
  role: string;
  id_branch: number,
  branch_name_estab: string;
  branch_num_estab: string;  
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

    // roles = Object.values(Role); // ['admin', 'user']
    // roles = Object.values(Role); // ['admin', 'user']
    roles: Role[] = Object.values(Role);
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';

    roleTouched = false;
    id_branchTouched = false;
    point_of_saleTouched =false;
    pointsOfSale = signal<any[]>([]);
    file_imagen:any =null;
    public favoriteColor = '#26ab3c';
    icons   = freeSet;
    router  = inject(Router);
    toastr  = inject(ToastrService);
    userService = inject(UserService);
    activatedRoute = inject(ActivatedRoute);
    
    branches:any =[];
    // id_branch:any=0;

    // pointsOfSale:any =[];
    // id_point_of_sale:any=0;

    USER = signal<UserI>({
      name: '',
      email:'',
      imagen:'',
      role:'',
      id_branch:0,
      id_point_of_sale:0,
      branch_name_estab:'',
      branch_num_estab:''
    });

    USER_ID:any  = null;
    // state:boolean = true;
    isEmpty:boolean = true;


    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.USER_ID = resp.id;
        this.userService.showUser(this.USER_ID)
          .subscribe((resp:any) => {
            this.USER.set(resp.User);
            // console.log(this.USER());
            this.isEmpty = false; // ✅ IMPORTANTE
            if (this.USER().id_branch) {
              this.getPointsOfSale();
            }
            if(this.USER().imagen){
              let url = this.USER().imagen;
              this.imagen_previsualiza = url;
              // console.log(this.imagen_previsualiza);
            }
          });
      });
      this.getBranches();
      // this.pointsOfSale();
    }

    getBranches(){
      this.userService.getBranches()
        .subscribe({
          next:(resp:any)=>{
            this.branches = resp.branches;
            // console.log(this.branches);
          },
          error:(err:any)=>{
            console.log(err);
          },
          complete:()=> {
            // this.getPointsOfSale();
          },
        });
    }



    getPointsOfSale(){
      const idBranch = this.USER().id_branch;

      if (!idBranch || idBranch === 0) {
        this.pointsOfSale.set([]);
        return;
      }

      this.userService.getPointsOfSale(idBranch)
        .subscribe((resp:any)=>{
          this.pointsOfSale.set(resp.point_of_sales);
          // console.log(this.pointsOfSale());
        });
    }

    clickInputFileHide(){
      const clickInputFile = document.getElementById('categorieImage');
      clickInputFile?.click();
    }

    processFile($event:any){
      if($event.target.files[0].type.indexOf('image') < 0){
        return;
      }
      this.file_imagen = $event.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(this.file_imagen);
      reader.onloadend = ()=> this.imagen_previsualiza = reader.result;
    }


    // Métodos para update cada campo (evita parser error)
    updateName(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, name: valor }));
    }
    updateImagen(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.USER.update(c => ({ ...c, imagen: valor }));
    }
    updateRole(value: Role) {
      this.USER.update(user => ({
        ...user,
        role: value
      }));
    }
    updateIdBranch(value: any) {
      this.USER.update(c => ({ 
        ...c, 
        id_branch: value,
        point_of_sale: 0 
      }));
      this.getPointsOfSale();
    }
    updateIdPointOfSale(value: any) {
      // console.log(value);
      this.USER.update(c => ({ ...c, id_point_of_sale: value }));
    }
    
    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.USER();
      return (
        c.name.trim().length > 0 &&
        c.role.trim().length > 0 &&
        c.branch_num_estab.trim().length > 0 
      );
    });



    save(){
 
      

      let formData = new FormData();
      formData.append('name', this.USER().name?.toString() ?? '');
      formData.append('role', this.USER().role?.toString() ?? '');
      formData.append('id_point_of_sale', this.USER().id_point_of_sale?.toString() ?? '');

      // console.log(this.file_imagen);
      if(this.file_imagen){
        formData.append('producto', this.file_imagen);
      }

      // console.log(this.USER());


      this.userService.updateUser(this.USER_ID, formData)
      .subscribe((resp:any) =>{
        // console.log(resp);
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
