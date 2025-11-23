import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, inject, signal } from '@angular/core';
import { CustomerService } from '../customer.service';
import { Router } from '@angular/router';
import { SharedModule } from '../../../shared/shared.module';

@Component({
  selector: 'app-list',
  imports: [SharedModule],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss'
})
export class ListComponent {

  icons = freeSet;

  customerService = inject(CustomerService);
  router  = inject(Router);
  toastr  = inject(ToastrService);

  customers:   any = signal<any[]>([]);
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
    this.listarCustomers();
  }

  refresh(){
    this.search = '';
    this.listarCustomers(1);  
  }

  verifData = 0;
  listarCustomers(page = 1){
    this.customerService.listCustomers(page, this.search, this.pageSize)
    .subscribe((resp:any) => {
      this.verifData = 0;
      if(resp.total == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      return this.customers.set(resp.customers.data) ;
    });
  }


  searchTo(){
    this.listarCustomers();
  }

  loadPage($event:any){
    this.listarCustomers($event);
  }
  
  abrirModal(id: number) {
    this.modalId.set(id);
  }

  cerrarModal() {
    this.modalId.set(null);
  }

  changeState(customer_id:any){
    this.customerService.changeState(customer_id)
    .subscribe((resp:any) => {
      this.cerrarModal();
      let state= false;
      if(resp[1] === 'Customer deactivate'){
        state = false;
        this.toastr.success('Exito', 'La customer se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'La customer se ha activado correctamente');
      }
      this.actualizarCustomer(customer_id, state);
    });
  }

  actualizarCustomer(id: number, state: boolean) {
    console.log(id, state);
    this.customers.update((lista:any) =>
      lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
    );
  }

  createCustomer(){
    this.router.navigateByUrl("/customer/create");
  }

  editCustomer(item:any){
    this.router.navigateByUrl("/customer/edit/"+item.id);
  }

  verCustomer(item:any){
    this.router.navigateByUrl("/customer/show/"+item.id);
  }

}
