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
  selector: 'app-create',
  imports: [SharedModule , FormSelectDirective, ReactiveFormsModule, FormCheckComponent,FormCheckInputDirective,FormCheckLabelDirective ],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss',
  host: {
    'class': 'example',
  },
})
export class CreateComponent {

      public favoriteColor = '#26ab3c';
      icons = freeSet;
      router = inject(Router);
      toastr  = inject(ToastrService);
      iceTarifaService = inject(IceTarifaService);
      activatedRoute = inject(ActivatedRoute);
      
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
        // console.log(this.tipo);
      }
  

      save(){
        if(!this.codigo || !this.codigo_porcentaje || !this.descripcion
          || !this.tipo || !this.tarifa 
        ){
          this.toastr.error('Validacion', 'Los campos con * son obligatorios');
          return;
        }
  
        let iceTarifa = {
          'codigo':this.codigo,
          'codigo_porcentaje':this.codigo_porcentaje,
          'descripcion':this.descripcion,
          'tipo' : this.tipo,
          'tarifa':this.tarifa,
          'unidad':this.unidad

        };
        // console.log(iceTarifa);
        // return;

        this.iceTarifaService.createIceTarifa(iceTarifa)
        .subscribe((resp:any) =>{
          if(resp.code == 403){
            this.toastr.error('Validacion', 'La tarifa ice ya existe');
            return;
          }
          this.codigo ='';
          this.codigo_porcentaje =''; 
          this.descripcion ='';
          this.tipo =Tipo_tar.Porcentaje;
          this.tarifa ='';
          this.unidad ='';
          this.toastr.success('Exito', 'La tarifa ice se ha creado correctamente');
        });
      }
  
      goList(){
        this.router.navigateByUrl("/inventory/list-ice-tarifa");
      }
  
  
}
