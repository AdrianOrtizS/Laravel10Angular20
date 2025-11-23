import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { SharedModule } from '../../../shared/shared.module';
import { SupplierService } from '../supplier.service';

@Component({
  selector: 'app-list',
  imports: [SharedModule],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss'
})
export class ListComponent {

  icons = freeSet;

  supplierService = inject(SupplierService);
  router  = inject(Router);
  toastr  = inject(ToastrService);

  suppliers:   any = signal<any[]>([]);
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

  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }

  ngOnInit(){
    this.listarSuppliers();
  }

  refresh(){
    this.search = '';
    this.listarSuppliers(1);  
  }

  verifData = 0;
  listarSuppliers(page = 1){
    this.supplierService.listSuppliers(page, this.search, this.pageSize)
    .subscribe((resp:any) => {
      this.verifData = 0;
      if(resp.total == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      return this.suppliers.set(resp.Suppliers.data) ;
    });
  }


  searchTo(){
    this.listarSuppliers();
  }

  loadPage($event:any){
    this.listarSuppliers($event);
  }
  
  abrirModal(id: number) {
    this.modalId.set(id);
  }

  cerrarModal() {
    this.modalId.set(null);
  }

  changeState(supplier_id:any){
    this.supplierService.changeState(supplier_id)
    .subscribe((resp:any) => {
      this.cerrarModal();
      let state= false;
      if(resp[1] === 'supplier deactivate'){
        state = false;
        this.toastr.success('Exito', 'Proveedor se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'Proveedor se ha activado correctamente');
      }
      this.actualizarSupplier(supplier_id, state);
    });
  }

  actualizarSupplier(id: number, state: boolean) {
    console.log(id, state);
    this.suppliers.update((lista:any) =>
      lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
    );
  }

  createSupplier(){
    this.router.navigateByUrl("/supplier/create");
  }

  editSupplier(item:any){
    this.router.navigateByUrl("/supplier/edit/"+item.id);
  }

  verSupplier(item:any){
    this.router.navigateByUrl("/supplier/show/"+item.id);
  }

}
