import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, inject, signal } from '@angular/core';
import { PointsOfSaleService } from './../points_of_sale.service';
import { Router } from '@angular/router';
import { URL_BACKEND } from '../../../config/config';
import { SharedModule } from '../../../shared/shared.module';

@Component({
  selector: 'app-list',
  imports: [ SharedModule ],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss'
})

export class ListComponent {

  icons = freeSet;

  pointOfSaleService = inject(PointsOfSaleService);
  router  = inject(Router);
  toastr  = inject(ToastrService);
  
  id_branch: number = 0;
  codigo_punto_emision: string  = '';
  secuencial_actual: string = '';
  descripcion: boolean = true;

  pointsOfSale: any = signal<any[]>([]);
  search:     string ='';
  totalPages: number =0;
  currentPage:number =1;
  pageSize:   number = 5;

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
    this.listarPointsOfSale();
  }

  refresh(){
    this.search = '';
    this.listarPointsOfSale(1);  
  }

  verifData = 0;
  listarPointsOfSale(page = 1){
    this.pointOfSaleService.listPointsOfSale(page, this.search, this.id_branch, this.pageSize)
    .subscribe((resp:any) => {
      this.verifData = 0;
      if(resp.total == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      console.log(resp);
      return this.pointsOfSale.set(resp.PointsOfSale.data) ;
    });
  }


  searchTo(){
    this.listarPointsOfSale();
  }

  loadPage($event:any){
    this.listarPointsOfSale($event);
  }
  
  openModalState(id: number) {
    this.modalId.set(id);
  }

  closerModalState() {
    this.modalId.set(null);
  }

  changeState(PointsOfSale_id:any){
    this.pointOfSaleService.changeState(PointsOfSale_id)
    .subscribe((resp:any) => {
      this.closerModalState();
      let state= false;
      if(resp[1] === 'pointsOfSaledeactivate'){
        state = false;
        this.toastr.success('Exito', 'La Product se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'La Product se ha activado correctamente');
      }
      // this.updateProduct(PointsOfSale_id, state);
    });
  }

  // this.updateProduct(Product_id, state);
  // updateProduct(id: number, state: boolean) {
  //   this.products.update((lista:any) =>
  //     lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
  //   );
  // }

  createPointsOfSale(){
    this.router.navigateByUrl("/pointsOfSale/create");
  }

  editPointsOfSale(item:any){
    this.router.navigateByUrl("/pointsOfSale/edit/"+item.id);
  }

  viewPointsOfSale(item:any){
    this.router.navigateByUrl("/pointsOfSale/show/"+item.id);
  }

}
