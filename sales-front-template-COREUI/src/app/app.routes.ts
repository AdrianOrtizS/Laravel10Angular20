import { AuthGuard } from './views/services/auth.guard';
import { Routes } from '@angular/router';

export const routes: Routes = [
  { 
    path: '', redirectTo: 'dashboard', pathMatch: 'full',
  },
  { 
    path: '', loadComponent: () => import('./layout').then(m => m.DefaultLayoutComponent),
    data: { title: 'Home'},
    canActivate: [AuthGuard],
    children: [
      {
        path: 'dashboard', loadChildren: () => import('./views/dashboard/routes').then((m) => m.routes)
      },
      {
        path: 'theme', loadChildren: () => import('./views/theme/routes').then((m) => m.routes)
      },
      {
        path: 'base', loadChildren: () => import('./views/base/routes').then((m) => m.routes)
      },
      {
        path: 'buttons', loadChildren: () => import('./views/buttons/routes').then((m) => m.routes)
      },
      {
        path: 'forms', loadChildren: () => import('./views/forms/routes').then((m) => m.routes)
      },
      {
        path: 'icons', loadChildren: () => import('./views/icons/routes').then((m) => m.routes)
      },
      {
        path: 'notifications', loadChildren: () => import('./views/notifications/routes').then((m) => m.routes)
      },
      {
        path: 'widgets', loadChildren: () => import('./views/widgets/routes').then((m) => m.routes)
      },
      {
        path: 'charts', loadChildren: () => import('./views/charts/routes').then((m) => m.routes)
      },
      {
        path: 'pages', loadChildren: () => import('./views/pages/routes').then((m) => m.routes)
      },
      {
        path: 'categorie', loadChildren: () => import('./views/categorie/routes').then((m) => m.routes)
      },
      {
        path: 'product', loadChildren: () => import('./views/product/routes').then((m) => m.routes)
      },
      {
        path: 'branch', loadChildren: () => import('./views/branch/routes').then((m) => m.routes)
      },
      {
        path: 'customer', loadChildren: () => import('./views/customer/routes').then((m) => m.routes)
      },
      {
        path: 'supplier', loadChildren: () => import('./views/supplier/routes').then((m) => m.routes)
      },
      {
        path: 'configuration', loadChildren: () => import('./views/configuration/routes').then((m) => m.routes)
      },      
      {
        path: 'sale', loadChildren: () => import('./views/sale/routes').then((m) => m.routes)
      },
      {
        path: 'buy', loadChildren: () => import('./views/buy/routes').then((m) => m.routes)
      },      
      {
        path: 'pointsOfSale', loadChildren: () => import('./views/pointsOfSale/routes').then((m) => m.routes)
      },      
      {
        path: 'profile', loadChildren: () => import('./views/userAuth/routes').then((m) => m.routes)
      },      

    ]
  },
  {
    path: '404', loadComponent: () => import('./views/pages/page404/page404.component').then(m => m.Page404Component),
    data: { title: 'Page 404' }
  },
  {
    path: '500', loadComponent: () => import('./views/pages/page500/page500.component').then(m => m.Page500Component),
    data: { title: 'Page 500' }
  },
  {
    path: 'login', loadComponent: () => import('./views/pages/login/login.component').then(m => m.LoginComponent),
    data: { title: 'Login Page' }
  },
  {
    path: 'register', loadComponent: () => import('./views/pages/register/register.component').then(m => m.RegisterComponent),
    data: { title: 'Register Page' }
  },
  {
    path: 'recover-password', loadComponent: () => import('./views/pages/recover-password/recover-password.component').then(m => m.RecoverPasswordComponent),
    data: { title: 'Recover password' }
  },
  {
    path: 'update_password', loadComponent: () => import('./views/pages/recover-password/update-password/update-password.component').then(m => m.UpdatePasswordComponent),
    data: { title: 'Update Password' }
  },
  { path: '**', redirectTo: 'dashboard' }
];
