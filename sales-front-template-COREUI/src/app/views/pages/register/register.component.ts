import { PointsOfSaleService } from './../../pointsOfSale/points_of_sale.service';
import { AuthService } from '../../services/auth.service';
import { inject } from '@angular/core';
import { Component, OnInit } from '@angular/core';
import { SharedModule } from './../../../shared/shared.module';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { FormSelectDirective } from '@coreui/angular';

@Component({
  selector: 'app-register',
  
  templateUrl: './register.component.html',
  imports: [SharedModule,FormSelectDirective, ]
})
export class RegisterComponent {

  authService    = inject(AuthService);
  pointsOfSaleService = inject(PointsOfSaleService);

  router    = inject(Router);
  toastr    = inject(ToastrService);

  type:   string    ='';
  message:  string  ='';
  name:   string    ='';
  email:  string    ='';
  password: string  ='';
  password_confirmation:  string ='';
  loadingRegister:  boolean =false;
  disableBtn:       boolean=false;
  error:any;
  Branches :any = null;
  id_branch:any = 0;
  PointsOfSale: any[] = [];
  id_point_of_sale: number = 0;

  ngOnInit(){
      this.authService.getBranches()
        .subscribe((resp:any)=>{
          this.Branches = resp.Branches;
      });

      // this.pointsOfSaleService.getBranches()
      // .subscribe((resp: any) => {
      //   console.log(resp);
      //   this.Branches = resp.Branches;
      // });
  }


  onBranchChange(branchId: number) {
    this.id_branch = branchId;
    this.id_point_of_sale = 0; // reset selección

    if (branchId == 0) {
      this.PointsOfSale = [];
      return;
    }

    // Llamar al backend para traer puntos de venta de la sucursal
    this.pointsOfSaleService.getPointsByBranch(branchId).subscribe({
      next: (resp: any) => {
        console.log(resp);
        this.PointsOfSale = resp.pointsOfSale;
      },
      error: () => {
        this.toastr.error('Error', 'No se pudieron cargar los puntos de venta');
        this.PointsOfSale = [];
      },
    });
  }

  register(){
    this.disableBtn = true;
    this.loadingRegister = true;
    
    if( !this.name || !this.email || !this.id_branch || 
        !this.password || !this.password_confirmation){
      this.toastr.error('Validacion', 'Necesitas ingresar todos los campos');
      this.disableBtn = false;
      this.loadingRegister = false;
      return;  
    }
    if(this.password != this.password_confirmation){
        this.toastr.error('Error', 'Contraseñas deben ser iguales');
        this.disableBtn = false;
        this.loadingRegister = false;
        return;
    }
    let data =  {
      name:   this.name,
      email:  this.email,
      password: this.password,
      password_confirmation: this.password_confirmation,
      id_point_of_sale: this.id_point_of_sale
    };

    this.authService.register(data)
    .subscribe(
      {
        next: (resp:any) => {
          this.toastr.success('Exito','Verifica tu correo para continuar con el registro');
          setTimeout(() => {
            this.disableBtn = false;
            this.loadingRegister = false;
                  this.name = '';
                  this.email = '';
                  this.password = '';
                  this.password_confirmation = '';
                  this.id_branch = 0;
                  this.id_point_of_sale = 0;
          },500);
        },
        error:(error:any) =>{
          this.disableBtn = false;
          this.loadingRegister = false;
          this.error = Object.values(error.error);
          this.toastr.error('Errors', (this.error)  );
        }
      });
      this.loadingRegister = false;
  }

  goBack(){
    this.router.navigateByUrl('/Login');
  }

}
