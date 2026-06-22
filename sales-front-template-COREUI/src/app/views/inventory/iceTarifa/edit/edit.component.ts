import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { ToastrService } from 'ngx-toastr';
import { IceTarifaService } from '../ice-tarifa.service';
import { SharedModule } from '../../../../shared/shared.module';
import { FormCheckComponent, FormCheckInputDirective, FormCheckLabelDirective, FormSelectDirective } from '@coreui/angular';
import { ReactiveFormsModule } from '@angular/forms';

export enum Tipo_tar {
  Porcentaje = 'PORCENTAJE',
  Especifico = 'ESPECIFICO',
  Mixto = 'MIXTO'
}

@Component({
  selector: 'app-edit',
  imports: [SharedModule, FormSelectDirective, ReactiveFormsModule, FormCheckComponent,FormCheckInputDirective,FormCheckLabelDirective],
  templateUrl: './edit.component.html',
  styleUrl: './edit.component.scss',
  host: {
    'class': 'example',
  },
})
export class EditComponent {

    public favoriteColor = '#26ab3c';
    icons = freeSet;
    toastr  = inject(ToastrService);
    router = inject(Router);

    iceTarifaService = inject(IceTarifaService);
    activatedRoute = inject(ActivatedRoute);
    ICE_TARIFA_ID:any;
    ICE_TARIFA:any;
    
    codigo:string = '';
    codigo_porcentaje:string = '';
    descripcion:string = '';
    Tipo_tar = Tipo_tar;
    tipo: Tipo_tar = Tipo_tar.Porcentaje;
    tarifa:any;
    unidad:string='';
    estado:boolean = true;

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        // console.log(resp);
        this.ICE_TARIFA_ID = resp.id;
      });
      this.iceTarifaService.showIceTarifa(this.ICE_TARIFA_ID)
      .subscribe((resp:any)=>{
        this.ICE_TARIFA = resp.iceTarifa;

        this.codigo = this.ICE_TARIFA.codigo;
        this.codigo_porcentaje = this.ICE_TARIFA.codigo_porcentaje;
        this.descripcion = this.ICE_TARIFA.descripcion;
        this.tipo = this.ICE_TARIFA.tipo;
        this.tarifa = this.ICE_TARIFA.tarifa;
        // console.log(this.tarifa);
        this.unidad =this.ICE_TARIFA.unidad;
        this.estado = this.ICE_TARIFA.estado;
    
      });
    }



    save(){
      if(!this.codigo || !this.codigo_porcentaje || !this.descripcion || !this.tipo || !this.tarifa){
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }

      let tarifa_ice = {
        ice_tarifa_id : this.ICE_TARIFA_ID,
        codigo : this.codigo,
        codigo_porcentaje : this.codigo_porcentaje,
        descripcion : this.descripcion,
        tipo : this.tipo,
        tarifa : this.tarifa,
        unidad :this.unidad,
        estado : this.estado,
      }

      this.iceTarifaService.updateIceTarifa(tarifa_ice.ice_tarifa_id, tarifa_ice)
      .subscribe((resp:any) =>{
        if(resp.message == 403){
          this.toastr.error('Validacion', 'Taria ice ya existe');
          return;
        }
        this.toastr.success('Exito', 'Tarifa ice se actualizo correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/inventory/list-ice-tarifa");
    }
}
