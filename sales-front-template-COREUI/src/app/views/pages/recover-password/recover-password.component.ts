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
import { NoAuthService } from '../../services/no-auth.service';

@Component({
  selector: 'app-recover-password',
  imports: [SharedModule, RouterModule, ContainerComponent, RowComponent, ColComponent, CardGroupComponent, CardComponent, CardBodyComponent, FormDirective, InputGroupComponent, InputGroupTextDirective, IconDirective, FormControlDirective, ButtonDirective, NgStyle],
  templateUrl: './recover-password.component.html',
  styleUrl: './recover-password.component.scss'
})
export class RecoverPasswordComponent {

  authService = inject(AuthService);
  no_authService = inject(NoAuthService);
  toastr  = inject(ToastrService);
  router  = inject(Router);
  activatedRoute  = inject(ActivatedRoute);
  
  email:    string  ='';
  password: string  ='';
  uniqid:string  ='';

  loadingRegister:  boolean =false;
  disableBtn:       boolean=false;

  ngOnInit(){
    if(this.authService.token && this.authService.user){
      setTimeout(() => {
        this.router.navigateByUrl('/');
      }, 100);
      return;
    }  
  }

  sendEmail(){
    this.disableBtn = true;
    this.loadingRegister = true;
    this.no_authService.recoverPassword(this.email)
      .subscribe({
        next:(resp) =>{
          this.disableBtn = false;
          this.loadingRegister = false;
          console.log(resp);
        },
        error:(err) =>{
          this.disableBtn = false;
          this.loadingRegister = false;
          this.toastr.error('Error','Sucedio un error verifique datos');
          console.log(err);
        },
        complete:() => {
          this.disableBtn = false;
          this.loadingRegister = false;
          this.toastr.success('Exito','Verifica tu correo');
        }
      });
  }

  sendLogin(){
    this.router.navigateByUrl('/login');
  }


}
