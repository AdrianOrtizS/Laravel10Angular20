import { Component, inject, OnInit } from '@angular/core';
import { NgStyle } from '@angular/common';
import { IconDirective } from '@coreui/icons-angular';
import {
  ButtonDirective, CardBodyComponent, CardComponent, CardGroupComponent,
  ColComponent, ContainerComponent, FormControlDirective, FormDirective,
  InputGroupComponent, InputGroupTextDirective, RowComponent
} from '@coreui/angular';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from '../../services/auth.service';
import { SharedModule } from './../../../shared/shared.module';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  imports: [SharedModule, RouterModule, ContainerComponent, RowComponent, ColComponent, CardGroupComponent, CardComponent, CardBodyComponent, FormDirective, InputGroupComponent, InputGroupTextDirective, IconDirective, FormControlDirective, ButtonDirective, NgStyle]
})
export class LoginComponent implements OnInit {

  authService = inject(AuthService);
  toastr  = inject(ToastrService);
  router  = inject(Router);
  activatedRoute  = inject(ActivatedRoute);
  
  email:    string  ='';
  password: string  ='';
  uniqid:string  ='';

  loadingRegister:  boolean =false;
  disableBtn:       boolean=false;

  // password: string = '';
  showPassword: boolean = false;

  togglePassword() {
    this.showPassword = !this.showPassword;
  }
  ngOnInit(){
    // console.log('OnInit Login');
    if(this.authService.token && this.authService.user){
      setTimeout(() => {
        this.router.navigateByUrl('/');
      }, 100);
      return;
    }  
    this.activatedRoute.queryParams
    .subscribe((resp:any)=>{
      this.uniqid = resp.code;
    });
    if(this.uniqid){
      let data = {
        uniqid: this.uniqid
      }
      this.authService.verifiedAuth(data)
      .subscribe((resp:any)=>{
        // console.log(data);
        if(resp.message == 403){
          this.toastr.error('Validacion','El codigo no pertenece a ningun usuario');
        }
        if(resp.message == 200){
          this.toastr.success('Validacion','El correo ha sido verificado con exito');
          setTimeout(() => {
            this.router.navigateByUrl('/');
          }, 500);
        }
      });
    }
  }

  login(){
    this.disableBtn = true;
    this.loadingRegister = true;
    if(!this.email || !this.password){
      this.toastr.error('Validacion', 'Ingresa todos los campos');
      this.disableBtn = false;
      this.loadingRegister = false;
      return;
    }
    this.authService.login(this.email, this.password)
    .subscribe({
      next:(resp:any) =>{
          console.log(resp);

          if(resp != true){
            this.toastr.error('Validacion', resp);
            this.disableBtn = false;
            this.loadingRegister = false;
            return;
          }
          if(resp == true){
            this.toastr.success("Exito", "Bienvenido a la tienda");
            setTimeout(() => {
              this.disableBtn = false;
              this.loadingRegister = false;
              window.location.reload();
              this.router.navigateByUrl('/');
              return;
            }, 100);
          }
      },
      error:(err:any) =>{

      }
    });
      

   }

  // login(){
  //   this.disableBtn = true;
  //   this.loadingRegister = true;
  //   if(!this.email || !this.password){
  //     this.toastr.error('Validacion', 'Ingresa todos los campos');
  //     this.disableBtn = false;
  //     this.loadingRegister = false;
  //     return;
  //   }
  //   this.authService.login(this.email, this.password)
  //   .subscribe((resp:any)=>{
  //     console.log(resp);
  //     if(resp.error && resp.error.error == 'Unauthorized'){
  //       this.toastr.error('Validacion','Las credenciales son incorrectas');
  //       this.disableBtn = false;
  //       this.loadingRegister = false;
  //       return;
  //     }
  //     if(resp == true){
  //       this.toastr.success("Exito", "Bienvenido a la tienda");
  //       setTimeout(() => {
  //         this.disableBtn = false;
  //         this.loadingRegister = false;
  //         window.location.reload();
  //         this.router.navigateByUrl('/');
  //         return;
  //       }, 500);
  //     }
  //   },(error)=>{
  //     console.log(error);
  //   });
  // }

  goRecoverPassword(){
    this.router.navigateByUrl('/recover-password');    
  }

}
