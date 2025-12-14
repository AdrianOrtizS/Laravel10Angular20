
import { ActivatedRoute, Router } from '@angular/router';
import { Component, inject } from '@angular/core';
import { ConfigurationService } from '../configuration.service';
import { SharedModule } from './../../../shared/shared.module';
import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';

@Component({
  selector: 'app-create',
  imports: [ SharedModule ],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss'
})
export class CreateComponent {

    public favoriteColor = '#26ab3c';
    icons = freeSet;
    router = inject(Router);
    toastr  = inject(ToastrService);
    configurationService = inject(ConfigurationService);
    // activatedRoute = inject(ActivatedRoute);
    // CATEGORIE_ID:any;
    
    CONFIGURATION:any = {

    };
    
    name:string = '';
    value:string= '';
    
    ngOnInit(){
    
    }

    save(){
      this.CONFIGURATION.name = this.name;
      this.CONFIGURATION.value = this.value;

      if(!this.name || !this.value){
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }
      // console.log(this.CONFIGURATION);
      this.configurationService.createConfiguration(this.CONFIGURATION)
      .subscribe((resp:any) =>{
        // console.log(resp);
        if(resp.code == 403){
          this.toastr.error('Validacion', 'La configuracion ya existe');
          return;
        }
        this.name = '';
        this.value = '';
        this.toastr.success('Exito', 'La configuracion se ha creado correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/configuration/list");
    }

}
