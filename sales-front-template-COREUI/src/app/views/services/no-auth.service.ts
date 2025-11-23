import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class NoAuthService {

  http    = inject(HttpClient);
  router  = inject(Router);

  constructor() { 

  }

  recoverPassword(email:any){
    let URL = URL_SERVICIOS+'/recover_password_email';
    return this.http.post(URL, {email});
  }

  updatePasswordForCode(data:any){
    let URL = URL_SERVICIOS+"/update_password_for_code";
    return this.http.post(URL, data); 
  }

  
}
