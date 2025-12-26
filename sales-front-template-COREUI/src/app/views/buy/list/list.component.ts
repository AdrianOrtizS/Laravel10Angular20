import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { SharedModule } from '../../../shared/shared.module';
import { BuyService } from '../buy.service';
import { FormCheckComponent, FormCheckInputDirective, FormCheckLabelDirective } from '@coreui/angular';

@Component({
  selector: 'app-list',
  imports: [SharedModule,FormCheckComponent, FormCheckInputDirective, FormCheckLabelDirective],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss'
})
export class ListComponent {

    icons = freeSet;
  
    buyService = inject(BuyService);
    router  = inject(Router);
    toastr  = inject(ToastrService);
      
    buys:   any = signal<any[]>([]);
    search:     string ='';
    totalPages: number = 0;
    currentPage:number = 1;
    pageSize:   number = 5;
    
    seeFilter:boolean = false;
    filterType: string = '';
    filterFrom: string = '';
    filterTo: string = '';
    selectedTypePay: string = ''; 

    itemForPage:any = [
      {'id':  1,'val':  1},
      {'id':  2,'val':  2},
      {'id':  5,'val':  5},
      {'id': 10,'val': 10},
      {'id': 20,'val': 20}];
  
    modalId = signal<number | null>(null);
    modalIdDelete = signal<any | null>(null);
  
    constructor(
    ){
    }
  
    onTypePayChange() {
      console.log('Tipo de pago seleccionado:', this.selectedTypePay);
    }
    changePage(page: number): void {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    }
  
    ngOnInit(){
      this.listarBuys();
    }
  
    refresh(){
      this.search     = '';
      this.filterFrom = '';
      this.filterTo   = '';  
      this.seeFilter  = false;
      this.filterType = '';
      this.selectedTypePay = '';
      this.listarBuys(1);
    }
  
    verifData = 0;
    listarBuys(page = 1){
      this.buyService.listBuys(page, this.search, this.pageSize, this.filterFrom, this.filterTo, this.selectedTypePay)
      .subscribe((resp:any) => {
        this.verifData = 0;
        if(resp.Buys.data.length == 0 && this.verifData == 0){
          this.verifData ++;
          this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
          return this.buys.set([]) ;
        }
        this.totalPages = resp.total;
        this.currentPage = page;
        return this.buys.set(resp.Buys.data) ;
      });
    }

    clearFilters(){

    }

    mostrarFiltros(){
      console.log('mostrar filtros');
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
      this.selectedTypePay = '';
      this.refresh();
    }

    searchTo(){
      this.listarBuys();
    }
  
    loadPage($event:any){
      this.listarBuys($event);
    }
    
    abrirModal(id: number) {
      this.modalId.set(id);
    }
    abrirModalDelete(id: any) {
      console.log(id);
      this.modalIdDelete.set(id);
    }
  
    num_comprobante_abono:any;
    valor_abono:any
    imagen:string = '';
    state:boolean = true;
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    file_imagen:any =null;
    clickInputFileHide(){
      const clickInputFile = document.getElementById('categorieImage');
      clickInputFile?.click();
    }

    processFile($event:any){
      if($event.target.files[0].type.indexOf('image') < 0){
        return;
      }
      this.file_imagen = $event.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(this.file_imagen);
      reader.onloadend = ()=> this.imagen_previsualiza = reader.result;
    }

    
    disabledPay(item:any){
      if(!this.num_comprobante_abono || (this.valor_abono > item.saldo || !this.valor_abono)){
        return true;
      }else{
        return false;
      }
    }
    

    removeBuy(id: any): void {
      this.buyService.removeBuy(id).subscribe({
        next: (resp: any) => {
          if (resp.code === 400) {
            this.toastr.warning('Aviso', 'No se puede eliminar, tiene pagos asociados');
            return;
          }
          if (resp.code === 200) {
            this.cerrarModalDelete();
            this.buys.update((buys: any[]) => buys.filter((b: any) => b.id !== id));
            this.toastr.success('Éxito', 'El comprobante se eliminó correctamente');
            this.listarBuys();
            return;
          }
        },
        error: (err: any) => {
          this.toastr.error('Error', err.error.message);
        }
      });
    }

    savePay(id:any, buy:any){
      let formData = new FormData();
      formData.append('id_buy', id);
      formData.append('num_comprobante_abono', this.num_comprobante_abono);
      formData.append('valor_abono', this.valor_abono);

      if(this.file_imagen){
        formData.append('pay_img', this.file_imagen);
      }

      this.buyService.createPago(formData)
      .subscribe((resp:any) =>{
        if(resp.code == 403){
          this.toastr.error('Validacion', 'El comprobante de pago ya existe');
          return;
        }
        setTimeout(() => {
          this.cerrarModal();
          this.updateBuyAbono(id, this.valor_abono)

          this.num_comprobante_abono = '';
          this.valor_abono = '';
          this.file_imagen = null;
          this.imagen_previsualiza = '../../../../assets/images/sin_imagen.jpg';        
          this.toastr.success('Exito', 'El pago se ha creado correctamente');
          
        }, 1000);
      });
    }

    updateBuyAbono(id: number, total_abonos: any) {
      this.buys.update((currentBuys:any) => 
        currentBuys.map((buy:any) => 
          buy.id === id ? { ...buy, saldo: Number(buy.saldo - total_abonos).toFixed(2) } : buy
        )
      );
    }


    cerrarModal() {
      this.modalId.set(null);
    }
    cerrarModalDelete() {
      this.modalIdDelete.set(null);
    }
  
    changeState(buy_id:any){
      this.buyService.changeState(buy_id)
      .subscribe((resp:any) => {
        this.cerrarModal();
        let state= false;
        if(resp[1] === 'Buy annulled'){
          state = false;
          this.toastr.success('Exito', 'La compra se ha anulado correctamente');
        }
        this.actualizarBuy(buy_id, state);
      });
    }
  
    actualizarBuy(id: number, state: boolean) {
      this.buys.update((lista:any) =>
        lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
      );
    }
  
    createBuy(){
      this.router.navigateByUrl("/buy/create");
    }
    
    verBuy(id:any){
      // console.log(id);
      this.router.navigateByUrl("/buy/show/"+id);
    }
  
}
