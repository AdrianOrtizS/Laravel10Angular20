import { AuthService } from './auth.service';
import { inject, Injectable } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

@Injectable()
export class PermisionAuth {

  // authService = inject(AuthService);
  // router = inject(Router);
  constructor(
    public authService: AuthService,
    public router: Router
  ) {
    
  }

  canActive():boolean{
    // console.log(this.authService.user);
    // console.log(this.authService.token);
    if(!this.authService.user || !this.authService.token){
      this.router.navigateByUrl('/login');
      return false;
    }
    let token = this.authService.token;
    let expiration =  (JSON.parse(atob(token.split(".")[1]))).exp;
    if(Math.floor((new Date).getTime() / 1000) > expiration){
      this.authService.logout();
      return false;
    }
    return true;
  }

}
export const AuthGuard: CanActivateFn = (route, state) => {
  return inject(PermisionAuth).canActive();
};
