import { UpdatePasswordComponent } from './recover-password/update-password/update-password.component';
import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '404', loadComponent: () => import('./page404/page404.component').then(m => m.Page404Component),
    data: { title: 'Page 404' }
  },
  {
    path: '500', loadComponent: () => import('./page500/page500.component').then(m => m.Page500Component),
    data: { title: 'Page 500' }
  },
  {
    path: 'login', loadComponent: () => import('./login/login.component').then(m => m.LoginComponent),
    data: { title: 'Login Page' }
  },
  {
    path: 'register', loadComponent: () => import('./register/register.component').then(m => m.RegisterComponent),
    data: { title: 'Register Page' }
  },
  {
    path: 'recover-password', loadComponent: () => import('./recover-password/recover-password.component').then(m => m.RecoverPasswordComponent),
    data: { title: 'Recover Password' }
  },
  {                                                                                   
    path: 'update_password', loadComponent: () => import('./recover-password/update-password/update-password.component').then(m => m.UpdatePasswordComponent),
    data: { title: 'Update Password' }
  },      
  // /update_password?code=691a9914391e8
];
