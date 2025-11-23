import { AuthService } from './../../../services/auth.service';
import { FormsModule } from '@angular/forms';
import { CommonModule, NgStyle } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { ButtonDirective, CardBodyComponent, CardComponent, CardGroupComponent, ColComponent, ContainerComponent, FormControlDirective, FormDirective, InputGroupComponent, InputGroupTextDirective, RowComponent, SharedModule, SpinnerModule } from '@coreui/angular';
import { IconDirective } from '@coreui/icons-angular';
import { ToastrService } from 'ngx-toastr';
import { Component, inject, OnInit } from '@angular/core';
import { NoAuthService } from '../../../services/no-auth.service';

@Component({
  selector: 'app-update-password',
  imports: [SharedModule,CommonModule, SpinnerModule, FormsModule, RouterModule, ContainerComponent, RowComponent, ColComponent, CardGroupComponent, CardComponent, CardBodyComponent, FormDirective, InputGroupComponent, InputGroupTextDirective, IconDirective, FormControlDirective, ButtonDirective, NgStyle],
  templateUrl: './update-password.component.html',
  styleUrl: './update-password.component.scss'
})
export class UpdatePasswordComponent {

  activatedRoute  = inject(ActivatedRoute);
  no_authService = inject(NoAuthService);
  showPassword:any = false;
  showConfirmPassword:any = false;
  code:any;
  toastr  = inject(ToastrService);
  router  = inject(Router);
  
  password:any ='';
  confirm_password:any ='';
  loadingRegister:  boolean =false;
  disableBtn:       boolean=false;

  ngOnInit(){
    this.activatedRoute.queryParams
      .subscribe({
        next:(resp:any)=>{
          this.code = resp.code ?? '';
        },
        error:(error:any)=>{
          console.log(error);
        },
        complete:()=>{
        }
      });
  }

  updatePassword(){
    if(this.password != this.confirm_password){
      this.toastr.error('Error','Contraseñas no coinciden');
      return;
    }
    let data = {
      code_verified: this.code,
      new_password: this.password
    };
    console.log(data);
    this.no_authService.updatePasswordForCode(data)
    .subscribe({
      next:()=>{
        // console.log('BIIIEEENNNNN');
        this.toastr.success('Exito','Se ha actualizado la contraseña');
        this.showPassword = false;
        this.password ='';
        this.confirm_password ='';
      },
      error:()=>{
        console.log('MMAAAALLL');
        this.showPassword = false;
        // this.password ='';
        // this.confirm_password ='';
        this.toastr.error('Error','No se pudo actualizar la contraseña');
      }
    });
  }

  sendLogin(){
    this.router.navigateByUrl('/login');
  }

}
