import { SharedModule } from './../../../shared/shared.module';
import { ConfigurationService } from '../configuration.service';
import { Component, inject, signal } from '@angular/core';
import { URL_BACKEND } from '../../../config/config';
import { Router } from '@angular/router';
import { freeSet  } from '@coreui/icons';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-list',
  imports: [ SharedModule ,],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss'
})
export class ListComponent {

  icons = freeSet;

  configurationService = inject(ConfigurationService);
  router = inject(Router);
  toastr  = inject(ToastrService);
  
  name: string='';
  value: string='';
  state: boolean=true;

  configurations: any = signal<any[]>([]);
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
    this.listarConfigurations();
  }

  refresh(){
    this.search = '';
    this.listarConfigurations(1);  
  }

  verifData = 0;
  listarConfigurations(page = 1){
    this.configurationService.listConfigurations(page, this.search, this.pageSize)
    .subscribe((resp:any) => {
      console.log(resp);
      this.verifData = 0;
      if(resp.configurations.length == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      console.log(resp.configurations);
      return this.configurations.set(resp.configurations) ;
    });
  }

  searchTo(){
    this.listarConfigurations();
  }

  loadPage($event:any){
    this.listarConfigurations($event);
  }
  

  abrirModal(id: number) {
    this.modalId.set(id);
  }

  cerrarModal() {
    this.modalId.set(null);
  }

  changeState(configuration_id:any){
    this.configurationService.changeState(configuration_id)
    .subscribe((resp:any) => {
      this.cerrarModal();
      let state= false;
      if(resp[1] === 'Configuration deactivate'){
        state = false;
        this.toastr.success('Exito', 'La configuration se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'La configuration se ha activado correctamente');
      }
      this.actualizarUsuario(configuration_id, state);
    });
  }

  actualizarUsuario(id: number, state: boolean) {
    this.configurations.update((lista:any) =>
      lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
    );
  }

  createConfiguration(){
    this.router.navigateByUrl("/configuration/create");
  }

  editConfiguration(item:any){
    this.router.navigateByUrl("/configuration/edit/"+item.id);
  }

  verConfiguration(item:any){
    this.router.navigateByUrl("/configuration/show/"+item.id);
  }

}
