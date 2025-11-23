import { ActivatedRoute } from '@angular/router';
import { Component, inject } from '@angular/core';
import { CategorieService } from '../categorie.service';
import { SharedModule } from './../../../shared/shared.module';
import { URL_BACKEND } from '../../../config/config';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Location } from '@angular/common';
import { freeSet } from '@coreui/icons';

@Component({
  selector: 'app-edit',
  imports: [ SharedModule ,  ],
  templateUrl: './edit.component.html',
  styleUrl: './edit.component.scss',
  host: {
    'class': 'example',
  },
})
export class EditComponent {

    public favoriteColor = '#26ab3c';
    icons = freeSet;
    location = inject(Location); 
    toastr  = inject(ToastrService);
    router = inject(Router);
    categorieService = inject(CategorieService);
    activatedRoute = inject(ActivatedRoute);
    CATEGORIE_ID:any;
    CATEGORIE:any;
    name:string = '';
    description:string = '';
    imagen:string = '';
    state:boolean = true;
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    file_imagen:any =null;
    
    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.CATEGORIE_ID = resp.id;
      });
      this.categorieService.showCategorie(this.CATEGORIE_ID)
      .subscribe((resp:any)=>{
        this.CATEGORIE = resp.categorie;
        this.name = this.CATEGORIE.name;
        this.description = this.CATEGORIE.description;
        if(this.CATEGORIE.imagen){
          let url = URL_BACKEND+'storage/'+this.CATEGORIE.imagen;
          this.imagen_previsualiza = url;
        }
        this.state = this.CATEGORIE.state;
      });
    }

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


    save(){
      if(!this.name || !this.description){
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }

      let formData = new FormData();
      formData.append('name', this.name);
      formData.append('description', this.description);

      if(this.file_imagen){
        formData.append('categorie', this.file_imagen);
      }

      this.categorieService.updateCategorie(this.CATEGORIE_ID, formData)
      .subscribe((resp:any) =>{
        if(resp.message == 403){
          this.toastr.error('Validacion', 'La categorie ya existe');
          return;
        }
        this.toastr.success('Exito', 'La categorie se actualizo correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/categorie/list");
    }
}
