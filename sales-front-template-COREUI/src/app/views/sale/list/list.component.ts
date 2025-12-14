import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, inject, signal } from '@angular/core';
import { SaleService } from '../sale.service';
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
  
    saleService = inject(SaleService);
    router  = inject(Router);
    toastr  = inject(ToastrService);
    
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
  
    sales:   any = signal<any[]>([]);
    search:     string ='';
    totalPages: number =0;
    currentPage:number =1;
    pageSize:   number = 5;
  
    seeFilter:boolean = false;
    filterType: string = '';
    filterFrom: string = '';
    filterTo: string = '';

    itemForPage:any = [
      {'id':  1,'val':  1},
      {'id':  2,'val':  2},
      {'id':  5,'val':  5},
      {'id': 10,'val': 10},
      {'id': 20,'val': 20}];
  
    modalId = signal<number | null>(null);
  
    num_comprobante_abono:any;
    // num_comprobante_documento:any;
    valor_abono:any;
    observacion:any;


    constructor(
    ){
    }
  
    changePage(page: number): void {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    }
  
    ngOnInit(){
      this.listarSales();
    }
  
    refresh(){
      this.search = '';
      this.filterFrom = '';
      this.filterTo   = '';  
      this.seeFilter  = false;
      this.filterType = '';
      // this.selectedTypePay = '';
      this.listarSales(1); 
    }

    disabledReceived(item:any){
      if((this.valor_abono > item.saldo || !this.valor_abono)){
        return true;
      }else{
        return false;
      }
    }

    saveReceived(id:any, sale:any){
      let received = {
        'id_sale':id,
        'valor_abono':this.valor_abono,
        'observacion': this.observacion
      };
      
      this.saleService.createCobro(received)
      .subscribe((resp:any) =>{
        if(resp.code == 403){
          this.toastr.error('Validacion', 'El comprobante de pago ya existe');
          return;
        }
        setTimeout(() => {
          this.cerrarModal();
          this.updateSaleAbono(id, this.valor_abono)

          this.num_comprobante_abono = '';
          this.valor_abono = '';
          this.observacion = '';
          this.toastr.success('Exito', 'El cobro se ha creado correctamente');
          // console.log(resp.receivable.id);
          this.printReceivable(resp.receivable.id)
        }, 1000);
      });
    }

    printReceivable(id_receivable:any){
      this.saleService.getReceivablePDF(id_receivable).subscribe((pdfBlob: Blob) => {
          const url = window.URL.createObjectURL(pdfBlob);
          const newWindow = window.open(url, '_blank');
          if (newWindow) {
            newWindow.print(); // abre el diálogo de impresión directamente
          }
      });
    }
    

    updateSaleAbono(id: number, total_abonos: any) {
      this.sales.update((currentSales:any) => 
        currentSales.map((sale:any) => 
          sale.idx === id ? { ...sale, saldo: Number(sale.saldo - total_abonos).toFixed(2) } : sale
        )
      );
    }

    verifData = 0;
    listarSales(page = 1){
      this.saleService.listSales(page, this.search, this.pageSize, this.filterFrom, this.filterTo)
      .subscribe((resp:any) => {
        this.verifData = 0;
        if(resp.Sales.data.length == 0 && this.verifData == 0){
          this.verifData ++;
          this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
          return this.sales.set([]);
        }
        this.totalPages = resp.total;
        this.currentPage = page;
        // console.log(resp.Sales.data);
        return this.sales.set(resp.Sales.data) ;
      });
    }
  
    mostrarFiltros(){
      // console.log('mostrar filtros');
      this.seeFilter = true;
      if(this.seeFilter){
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        this.filterFrom = firstDay.toISOString().split('T')[0];
        this.filterTo = today.toISOString().split('T')[0];
      }
    }
    ocultarFiltros(){
      this.seeFilter = false;
      // this.selectedTypePay = '';
      this.refresh();
    }

    getSecuencial(num: number): string {
      return num.toString().padStart(9, '0');
    }
  
    searchTo(){
      this.listarSales();
    }
  
    loadPage($event:any){
      this.listarSales($event);
    }
    
    abrirModal(id: number) {
      this.modalId.set(id);
    }
  
    cerrarModal() {
      this.modalId.set(null);
    }
  
    changeState(sale_id:any){
      this.saleService.changeState(sale_id)
      .subscribe((resp:any) => {
        this.cerrarModal();
        let state= false;
        if(resp[1] === 'Sale annulled'){
          state = false;
          this.toastr.success('Exito', 'La venta se ha anulado correctamente');
        }
        this.actualizarSale(sale_id, state);
      });
    }
  
    actualizarSale(id: number, state: boolean) {
      this.sales.update((lista:any) =>
        lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
      );
    }
  
    createSale(){
      this.router.navigateByUrl("/sale/create");
    }
    
    verSale(item:any){
      this.router.navigateByUrl("/sale/show/"+item.idx);
    }
  
}
