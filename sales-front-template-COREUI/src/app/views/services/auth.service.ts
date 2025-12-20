import { Router } from '@angular/router';
import { inject, Injectable, signal } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { URL_SERVICIOS } from '../../config/config';
import { catchError, map, of } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class AuthService {

  token: string ='';
  user:any = signal<any>(null);
  http    = inject(HttpClient);
  router  = inject(Router);

  constructor() { 
    this.initAuth();
  }

  initAuth(){
    if(localStorage.getItem('token')){
      this.user.set(localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user') ?? ''): null);
      this.token = JSON.stringify(localStorage.getItem('token'));
    }else{
      setTimeout(() => {
        this.router.navigateByUrl('/login');
      }, 500);
    }
  }

  updateUser(user: any) {
    this.user.set(user);
    localStorage.setItem('user', JSON.stringify(user));
  }

  updateUserLog(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.token)});
    let URL = URL_SERVICIOS+"/updateUserLog";
    return this.http.post(URL, data, {headers: headers})
            .pipe();
  }

  changePasswordUserLog(passwords:any){
    // console.log(passwords);
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.token)});
    let URL = URL_SERVICIOS+"/update_password_userLog";
    return this.http.put(URL, passwords, {headers: headers})
            .pipe();
  }

  // updateProduct(product_id:string, data:any){
  //   let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
  //   let URL = URL_SERVICIOS+'/products/'+product_id;
  //   return this.http.post(URL, data, {headers: headers})
  //           .pipe();
  // }

  login(email:string, password:string){
    let URL = URL_SERVICIOS+"/login";
    return this.http.post(URL, {email, password}).pipe(map((resp:any)=> {
      console.log(resp);
      if(resp.code == '402' || resp.code == '401'){
        return resp.message; 
      }
      const result = this.saveLocalStorage(resp);
      return result;

    }),catchError((err:any)=>{
      return of(err);
    }));
  }

  saveLocalStorage(resp:any){
    if(resp && resp.access_token){
      localStorage.setItem('token', resp.access_token);
      localStorage.setItem('user', JSON.stringify(resp.user));
      return true;
    }
    return false;
  }

  register(data:any){
    let URL = URL_SERVICIOS+"/register";
    return this.http.post(URL, data); 
  }

  verifiedAuth(data:any){
    let URL = URL_SERVICIOS+"/verified_auth";
    return this.http.post(URL, data);
  }

  verifiedEmail(data:any){
    let URL = URL_SERVICIOS+'/auth/verified_email';
    return this.http.post(URL, data);
  }

  verifiedCode(data:any){
    let URL = URL_SERVICIOS+'/auth/verified_code';
    return this.http.post(URL, data);
  }

  verifiedNewPassword(data:any){
    let URL = URL_SERVICIOS+'/auth/new_password';
    return this.http.post(URL, data);
  }



  getBranches(){
    let URL = URL_SERVICIOS+"/getBranches";
    return this.http.get(URL);
  }

  logout(){
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.user.set(null);
    this.token = '';
    setTimeout(() => {
      this.router.navigateByUrl('/login');
    }, 500);
  }
  
}
