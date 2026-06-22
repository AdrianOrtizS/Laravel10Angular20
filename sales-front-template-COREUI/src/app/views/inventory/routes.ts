import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '',
    data: {
      title: 'Inventory'
    },
    children: [
      {
        path: '',
        redirectTo: 'list-product',
        pathMatch: 'full'
      },
      {
        path: 'list-product',
        loadComponent: () => import('./product/list/list.component').then(m => m.ListComponent),
        data: {
          title: 'List product'
        }
      },
      {
        path: 'create-product',
        loadComponent: () => import('./product/create/create.component').then(m => m.CreateComponent),
        data: {
          title: 'Create product'
        }
      },
      {
        path: 'edit-product/:id',
        loadComponent: () => import('./product/edit/edit.component').then(m => m.EditComponent),
        data: {
          title: 'Edit product'
        }
      },
      {
        path: 'show-product/:id',
        loadComponent: () => import('./product/show/show.component').then(m => m.ShowComponent),
        data: {
          title: 'Show product'
        }
      },
      {
        path: 'list-categorie',
        loadComponent: () => import('./categorie/list/list.component').then(m => m.ListComponent),
        data: {
          title: 'List categorie'
        }
      },
      {
        path: 'create-categorie',
        loadComponent: () => import('./categorie/create/create.component').then(m => m.CreateComponent),
        data: {
          title: 'Create categorie'
        }
      },
      {
        path: 'edit-categorie/:id',
        loadComponent: () => import('./categorie/edit/edit.component').then(m => m.EditComponent),
        data: {
          title: 'Edit categorie'
        }
      },
      {
        path: 'show-categorie/:id',
        loadComponent: () => import('./categorie/show/show.component').then(m => m.ShowComponent),
        data: {
          title: 'Show categorie'
        }
      },
      ///
      {
        path: 'list-ice-tarifa',
        loadComponent: () => import('./iceTarifa/list/list.component').then(m => m.ListComponent),
        data: {
          title: 'List ice tarifa'
        }
      },
      {
        path: 'create-ice-tarifa',
        loadComponent: () => import('./iceTarifa/create/create.component').then(m => m.CreateComponent),
        data: {
          title: 'Create ice tarifa'
        }
      },
      {
        path: 'edit-ice-tarifa/:id',
        loadComponent: () => import('./iceTarifa/edit/edit.component').then(m => m.EditComponent),
        data: {
          title: 'Edit ice tarifa'
        }
      },
      {
        path: 'show-ice-tarifa/:id',
        loadComponent: () => import('./iceTarifa/show/show.component').then(m => m.ShowComponent),
        data: {
          title: 'Show ice tarifa'
        }
      },
    ]
  }
];


