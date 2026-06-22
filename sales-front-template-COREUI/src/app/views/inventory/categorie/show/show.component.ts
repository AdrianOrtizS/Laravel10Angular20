import { SharedModule } from './../../../../shared/shared.module';
import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router, } from '@angular/router';
import { CategorieService } from '../categorie.service';
// import { URL_BACKEND } from '../../../config/config';
import moment  from 'moment';
// import 'moment/locale/es';
// import { SharedModule } from '../../../shared/shared.module';
import { freeSet } from '@coreui/icons';
import { Location } from '@angular/common';
import { URL_BACKEND } from '../../../../config/config';
// import { SharedModule } from 'src/app/shared/shared.module';
// import { URL_BACKEND } from 'src/app/config/config';

@Component({
  selector: 'app-show',
  imports: [ SharedModule ],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss'
})
export class ShowComponent {
   
    icons = freeSet;

    favoriteColor = '#26ab3c';

    location = inject(Location);
    router = inject(Router);
    categorieService = inject(CategorieService);
    activatedRoute = inject(ActivatedRoute);
    CATEGORIE_ID:any;
    CATEGORIE:any;
    name:string = '';
    description:string = '';
    imagen:string = '';
    tiempo_creacion:any;
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
        this.tiempo_creacion = moment(this.CATEGORIE.created_at).fromNow();

        if(this.CATEGORIE.imagen){
          let url = URL_BACKEND+'storage/'+this.CATEGORIE.imagen;
          this.imagen_previsualiza = url;
        }
        this.state = this.CATEGORIE.state;
      });
    }

    goBack(){
      this.router.navigateByUrl("/inventory/list-categorie");
    }

    goUpdateCategorie(){
      this.router.navigate(['/inventory/edit-categorie/'+this.CATEGORIE_ID]);
    }

    goList(){
      this.router.navigateByUrl("/inventory/list-categorie");
    }

}
