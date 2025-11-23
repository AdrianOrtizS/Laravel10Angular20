import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '',
    data: {
      title: 'Sale'
    },
    children: [
      {
        path: '',
        redirectTo: 'list',
        pathMatch: 'full'
      },
      {
        path: 'list',
        loadComponent: () => import('./list/list.component').then(m => m.ListComponent),
        data: {
          title: 'List'
        }
      },

      {
        path: 'create',
        loadComponent: () => import('./create/create.component').then(m => m.CreateComponent),
        data: {
          title: 'Create'
        }
      },
      {
        path: 'show/:id',
        loadComponent: () => import('./show/show.component').then(m => m.ShowComponent),
        data: {
          title: 'Show'
        }
      },


    ]
  }
];

