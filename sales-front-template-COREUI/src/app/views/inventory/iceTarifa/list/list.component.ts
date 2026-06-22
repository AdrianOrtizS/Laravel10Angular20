import { Component, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { ToastrService } from 'ngx-toastr';
import { IceTarifaService } from '../ice-tarifa.service';
import { SharedModule } from '../../../../shared/shared.module';

export enum Tipo_tar {
  Porcentaje = 'PORCENTAJE',
  Especifico = 'ESPECIFICO',
  Mixto = 'MIXTO'
}

@Component({
  selector: 'app-list',
  imports: [SharedModule ],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss',
})
export class ListComponent {

  icons = freeSet;

  iceTarifaService = inject(IceTarifaService);
  router = inject(Router);
  toastr  = inject(ToastrService);
  
  codigo:string = '';
  codigo_porcentaje:string = '';
  descripcion:string = '';

  Tipo_tar = Tipo_tar;
  tipo: Tipo_tar = Tipo_tar.Porcentaje;

  tarifa:any;
  unidad:string='';
  estado:boolean = true;


  iceTarifas: any = signal<any[]>([]);
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

  // modalId = signal<number | null>(null);
  selectedIceTarifa = signal<any | null>(null);


  constructor(
  ){
  }

  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }

  ngOnInit(){
    this.listarIceTarifas();
  }

  refresh(){
    this.search = '';
    this.listarIceTarifas(1);  
  }

  verifData = 0;
  listarIceTarifas(page = 1){
    this.iceTarifaService.listIceTarifas(page, this.search, this.pageSize)
    .subscribe((resp:any) => {
      this.verifData = 0;
      if(resp.total == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      // console.log(resp.iceTarifas.data);
      return this.iceTarifas.set(resp.iceTarifas.data) ;
    });
  }


  searchTo(){
    this.listarIceTarifas();
  }

  loadPage($event:any){
    this.listarIceTarifas($event);
  }
  
  abrirModal(id: number) {
    this.selectedIceTarifa.set(id);
  }

  cerrarModal() {
    this.selectedIceTarifa.set(null);
  }

  changeState(id_ice_tarifa:any){
    this.iceTarifaService.changeState(id_ice_tarifa)
    .subscribe((resp:any) => {
      this.cerrarModal();
      let state= false;
      if(resp[1] === 'Ice Tarifa deactivate'){
        state = false;
        this.toastr.success('Exito', 'La tarifa ice se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'La tarifa ice se ha activado correctamente');
      }
      this.actualizarIceTarifa(id_ice_tarifa, state);
    });
  }

  actualizarIceTarifa(id: number, estado: boolean) {
    this.iceTarifas.update((lista:any) =>
      lista.map((u:any) => u.id === id ? { ...u, estado:estado } : u)
    );
  }

  createIceTarifa(){
    this.router.navigateByUrl("/inventory/create-ice-tarifa");
  }

  editIceTarifa(item:any){
    this.router.navigateByUrl("/inventory/edit-ice-tarifa/"+item.id);
  }

  verIceTarifa(item:any){
    this.router.navigateByUrl("/inventory/show-ice-tarifa/"+item.id);
  }
  

}
