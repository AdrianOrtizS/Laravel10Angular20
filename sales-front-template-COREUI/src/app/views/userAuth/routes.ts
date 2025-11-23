import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '',
    data: {
      title: 'Profile'
    },
    children: [
      {
        path: '',
        redirectTo: 'show',
        pathMatch: 'full'
      },
      {
        path: 'show',
        loadComponent: () => import('./show/show.component').then(m => m.ShowComponent),
        data: {
          title: 'Show'
        }
      },

    ]
  }
];

