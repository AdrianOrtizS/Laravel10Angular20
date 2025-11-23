import { Component, inject } from '@angular/core';
import { SharedModule } from '../../../shared/shared.module';
import { freeSet } from '@coreui/icons';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { BranchService } from '../branch.service';

@Component({
  selector: 'app-create',
  imports: [SharedModule],
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
    branchService = inject(BranchService);
    activatedRoute = inject(ActivatedRoute);
  
    BRANCH:any;
    name:string = '';
    address:string = '';
    phone:string = '';
    state:boolean = true;
    
    ngOnInit(){
    
    }

    
    save(){
      if(!this.name || !this.address || !this.phone){
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }

      let branch = {
        'name': this.name,
        'address': this.address,
        'phone': this.phone
      };


      this.branchService.createBranch(branch)
      .subscribe((resp:any) =>{
        // console.log(resp);
        if(resp.code == 403){
          this.toastr.error('Validacion', 'La sucursal ya existe');
          return;
        }
        this.name = '';
        this.address = '';
        this.phone = '';
        this.toastr.success('Exito', 'La sucursal se ha creado correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/branch/list");
    }
}
