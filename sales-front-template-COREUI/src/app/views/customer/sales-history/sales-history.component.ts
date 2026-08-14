import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { SharedModule } from '../../../shared/shared.module';
import { ButtonDirective } from '@coreui/angular';
import { IconDirective } from '@coreui/icons-angular';
import { CustomerService } from '../customer.service';

@Component({
  selector: 'app-sales-history',
  imports: [SharedModule, IconDirective,ButtonDirective],
  templateUrl: './sales-history.component.html',
  styleUrl: './sales-history.component.scss',
})
export class SalesHistoryComponent {

    icons = freeSet;
    customService = inject(CustomerService);
    router  = inject(Router);
    toastr  = inject(ToastrService);
    
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
  
    sales:   any = signal<any[]>([]);
    search:     string ='';
    
    total_autorizadas:any =0;
    total_autor_no_autor:any =0;
    total_ventas:any =0;
    total:any =0;
    
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
    modalIdErr = signal<number | null>(null);
  
    num_comprobante_abono:any;
    valor_abono:any;
    observacion:any;
    activatedRoute = inject(ActivatedRoute);
    CUSTOMER_ID:any;
    CUSTOMER:any;
    col:number = 2.5;

    constructor(
    ){
    }
  
    changePage(page: number): void {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    }
  
    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.CUSTOMER_ID = resp.id;
        this.listarSales();
      });
    }
  
    refresh(){
      this.search = '';
      this.filterFrom = '';
      this.filterTo   = '';  
      this.seeFilter  = false;
      this.filterType = '';
      this.listarSales(1); 
    }

    disabledReceived(item:any){
      if((this.valor_abono > item.saldo || !this.valor_abono)){
        return true;
      }else{
        return false;
      }
    }

    goBack(){
      this.router.navigateByUrl("/customer/show/"+this.CUSTOMER_ID);
    }

    // saveReceived(id:any, sale:any){
    //   let received = {
    //     'id_sale':id,
    //     'valor_abono':this.valor_abono,
    //     'observacion': this.observacion
    //   };
      
    //   this.saleService.createCobro(received)
    //   .subscribe((resp:any) =>{
    //     if(resp.code == 403){
    //       this.toastr.error('Validacion', 'El comprobante de pago ya existe');
    //       return;
    //     }
    //     setTimeout(() => {
    //       this.cerrarModal();
    //       this.updateSaleAbono(id, this.valor_abono)

    //       this.num_comprobante_abono = '';
    //       this.valor_abono = '';
    //       this.observacion = '';
    //       this.toastr.success('Exito', 'El cobro se ha creado correctamente');
    //       // console.log(resp.receivable.id);
    //       this.printReceivable(resp.receivable.id)
    //     }, 1000);
    //   });
    // }

    // printReceivable(id_receivable:any){
    //   this.customService.getReceivablePDF(id_receivable).subscribe((pdfBlob: Blob) => {
    //       const url = window.URL.createObjectURL(pdfBlob);
    //       const newWindow = window.open(url, '_blank');
    //       if (newWindow) {
    //         newWindow.print(); // abre el diálogo de impresión directamente
    //       }
    //   });
    // }
    

    updateSaleAbono(id: number, total_abonos: any) {
      this.sales.update((currentSales:any) => 
        currentSales.map((sale:any) => 
          sale.idx === id ? { ...sale, saldo: Number(sale.saldo - total_abonos).toFixed(2) } : sale
        )
      );
    }

    selectedItem: any = null;
    abrirModalErrores(item: any) {
      this.selectedItem = item;
      this.modalIdErr.set(item.idx);
    }

    cerrarModalErrores() {
      this.modalIdErr.set(null);
      this.selectedItem = null;
    }

    // 🔥 Getter PRO (evita problemas string/array)
    get errores() {
      if (!this.selectedItem) return [];

      const data = this.selectedItem.error_no_autorizada;

      try {
        return typeof data === 'string' ? JSON.parse(data) : data;
      } catch (e) {
        return [];
      }
    }

    parseErrores(data: any) {
      if (!data) return [];
      return typeof data === 'string' ? JSON.parse(data) : data;
    }

    verifData = 0;
    listarSales(page = 1){
      this.customService.listSalesCustomer(this.CUSTOMER_ID, page, this.search, this.pageSize, this.filterFrom, this.filterTo)
      .subscribe((resp:any) => {
        this.verifData = 0;
        if(resp.Sales.data.length == 0 && this.verifData == 0){
          this.verifData ++;
          this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
          return this.sales.set([]);
        }
        this.CUSTOMER = resp.Sales.data[0].customer;
        this.totalPages = resp.total;
        this.currentPage = page;
        this.total_autorizadas = resp.total_autorizado;
        this.total_autor_no_autor = resp.total_autor_no_autor;
        this.total = resp.total;
        this.total_ventas = Number(this.total_autorizadas) + Number(this.total_autor_no_autor);
        return this.sales.set(resp.Sales.data) ;

      });
    }


    listSalesExcel(){
      // this.customService.listSalesExcel(this.filterFrom, this.filterTo)
      // .subscribe({
      //   next: (blob: Blob) => {
          
      //     const file = new Blob([blob], {
      //       type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
      //     });
      //     const url = window.URL.createObjectURL(file);
      //     const link = document.createElement('a');
      //     link.href = url;
      //     link.download = 'ventas.xlsx';
      //     document.body.appendChild(link);
      //     link.click();
      //     document.body.removeChild(link);
      //     window.URL.revokeObjectURL(url);
      //   },
      //   error: (err) => {
      //     console.error(err);
      //   }
      // });
    }

    listSalesPdf(){
    //   this.saleService.listSalesPdf(this.filterFrom, this.filterTo)
    //   .subscribe({
    //      next: (response: Blob) => {

    //       const file = new Blob([response], {
    //         type: 'application/pdf'
    //       });

    //       const fileURL = window.URL.createObjectURL(file);

    //       // Descargar automáticamente
    //       const link = document.createElement('a');
    //       link.href = fileURL;
    //       link.download = 'ventas.pdf';
    //       link.click();

    //       window.URL.revokeObjectURL(fileURL);  
    //     },
    //     error: (err) => {
    //       console.error(err);
    //     }
    //   });
    }



    // reconsultarSri(item:any){
    //   // console.log(item);
    //   this.customsaleService.reconsultarSri(item.idx)
    //   .subscribe((resp:any)=>{
    //     if(resp.estado == 'AUTORIZADO'){
    //       this.sales.update((currentSales:any) => 
    //         currentSales.map((sale:any) => 
    //           sale.idx === item.idx 
    //             ? { ...sale, estado_sri: 'AUTORIZADO' } 
    //             : sale // 👈 IMPORTANTE
    //         )
    //       );
    //       this.toastr.success('Exito', 'Factura: '+item.numero_factura+' fue autorizada');
    //     }
    //   })
    // }
  

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
  
    // changeState(sale_id:any){
    //   this.saleService.changeState(sale_id)
    //   .subscribe((resp:any) => {
    //     this.cerrarModal();
    //     let state= false;
    //     if(resp[1] === 'Sale annulled'){
    //       state = false;
    //       this.toastr.success('Exito', 'La venta se ha anulado correctamente');
    //     }
    //     this.actualizarSale(sale_id, state);
    //   });
    // }
  
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
