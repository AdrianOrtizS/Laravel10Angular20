import { SharedModule } from './../../../shared/shared.module';
import { ToastrService } from 'ngx-toastr';
import { PointsOfSaleService } from './../points_of_sale.service';
import { Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { FormSelectDirective } from '@coreui/angular';

// interface ProductI {
//   cod_pro:string,
//   name:   string,
//   description: string,
//   price:  string,
//   stock:  string,
//   stock_min:  string,
//   imagen: string,
//   id_categorie: number,
//   // state: any
// }
        
@Component({
  selector: 'app-create',
  imports: [  SharedModule, FormSelectDirective, ReactiveFormsModule  ],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss'
})
export class CreateComponent {
  
    favoriteColor = '#26ab3c';
    icons = freeSet;
    router = inject(Router);
    toastr  = inject(ToastrService);
    pointsOfSaleService = inject(PointsOfSaleService);
    
    POINTSOFSALE:any = signal<any>({
      id_branch: 0,
      codigo_punto_emision:  '',
      secuencial_actual:  '',
      secuencial_actual_receivable:  '',
      descripcion:     '',
    });

    Branches:any  = [];

    ngOnInit(){
      this.pointsOfSaleService.getBranches()
      .subscribe((resp:any)=>{
        console.log(resp);
        this.Branches = resp.branches;
      });
    }

    // Métodos para update cada campo (evita parser error)
    updateId_branch(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, id_branch: valor }));
    }

    updateCodigoPuntoEmision(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, codigo_punto_emision: valor }));
    }

    updateSecuencialActual(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, secuencial_actual: valor }));
    }

    updateSecuencialActualReceivable(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, secuencial_actual_receivable: valor }));
    }

    updateDescripcion(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, descripcion: valor }));
    }
  
    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.POINTSOFSALE();
      return (
        c.codigo_punto_emision.trim().length > 0 &&
        c.secuencial_actual.trim().length > 0 &&
        c.secuencial_actual_receivable.trim().length > 0 &&
        c.descripcion.trim().length > 0 &&
        c.id_branch > 0
      );
    });

    save(){
      if(this.POINTSOFSALE.id_branch == 0){
        this.toastr.error('Validacion', 'Seleccione sucursal');
        return;
      }

      let pointsOfSale = {
        'codigo_punto_emision': this.POINTSOFSALE().codigo_punto_emision,
        'secuencial_actual':    this.POINTSOFSALE().secuencial_actual,
        'secuencial_actual_receivable':    this.POINTSOFSALE().secuencial_actual_receivable,
        'descripcion':          this.POINTSOFSALE().descripcion,
        'id_branch':            this.POINTSOFSALE().id_branch
      };

      this.pointsOfSaleService.createPointsOfSael(pointsOfSale)
      .subscribe((resp:any) =>{
        console.log(resp);
        if(resp.code == 403){
          this.toastr.error('Validacion', 'El punto de venta ya existe');
          return;
        }
        this.limpiarFormulario();
        this.toastr.success('Exito', 'El punto de venta se ha creado correctamente');
      });
    }

    // Limpiar formulario
    limpiarFormulario() {
      this.POINTSOFSALE.set({ 
        codigo_punto_emision:'',
        secuencial_actual:'',
        secuencial_actual_receivable:'',
        descripcion:'',
        id_branch:0
      });
    }

    goList(){
      this.router.navigateByUrl("/pointsOfSale/list");
    }

}
