// import { NgModel } from '@angular/forms';
// import { SharedModule } from './../../../shared/shared.module';
import { CategorieService } from '../categorie.service';
import { Component, inject, signal } from '@angular/core';
// import { URL_BACKEND } from '../../../config/config';
import { Router } from '@angular/router';
import { freeSet  } from '@coreui/icons';
import { ToastrService } from 'ngx-toastr';
// import { SharedModule } from 'src/app/shared/shared.module';
import { SharedModule } from '../../../../shared/shared.module';
import { URL_BACKEND } from '../../../../config/config';
// import { URL_BACKEND } from 'src/app/config/config';

@Component({
  selector: 'app-list',
  imports: [  SharedModule ,],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss'
})
export class ListComponent {
  
  icons = freeSet;

  categorieService = inject(CategorieService);
  router = inject(Router);
  toastr  = inject(ToastrService);
  
  name: string='';
  description: string='';
  imagen: string='';
  state: boolean=true;

  categories: any = signal<any[]>([]);
  search:     string ='';
  totalPages: number =0;
  currentPage:number =1;
  pageSize:number = 5;

  itemForPage:any = [
    {'id':1,'val':1},
    {'id':2,'val':2},
    {'id':5,'val':5},
    {'id':10,'val':10},
    {'id':20,'val':20}];

  modalId = signal<number | null>(null);


  constructor(
  ){
  }

  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }

  ngOnInit(){
    this.listarCategories();
  }

  refresh(){
    this.search = '';
    this.listarCategories(1);  
  }

  verifData = 0;
  listarCategories(page = 1){
    this.categorieService.listCategories(page, this.search, this.pageSize)
    .subscribe((resp:any) => {
      // console.log(resp);
      this.verifData = 0;
      if(resp.total == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      return this.categories.set(resp.Categories.data) ;
    });
  }


  searchTo(){
    this.listarCategories();
  }

  loadPage($event:any){
    this.listarCategories($event);
  }
  
  getImageUrl(imagePath: string | null): string {
    return imagePath 
      ? `${URL_BACKEND}storage/${imagePath}` 
      : `${URL_BACKEND}uploads/sin_imagen.jpg`;
  }

  abrirModal(id: number) {
    this.modalId.set(id);
  }

  cerrarModal() {
    this.modalId.set(null);
  }

  changeState(categorie_id:any){
    this.categorieService.changeState(categorie_id)
    .subscribe((resp:any) => {
      this.cerrarModal();
      let state= false;
      if(resp[1] === 'Categorie deactivate'){
        state = false;
        this.toastr.success('Exito', 'La categorie se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'La categorie se ha activado correctamente');
      }
      this.actualizarUsuario(categorie_id, state);
    });
  }

  actualizarUsuario(id: number, state: boolean) {
    this.categories.update((lista:any) =>
      lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
    );
  }

  createCategorie(){
    this.router.navigateByUrl("/inventory/create-categorie");
  }

  editCategorie(item:any){
    this.router.navigateByUrl("/inventory/edit-categorie/"+item.id);
  }

  verCategorie(item:any){
    this.router.navigateByUrl("/inventory/show-categorie/"+item.id);
  }
  

}
