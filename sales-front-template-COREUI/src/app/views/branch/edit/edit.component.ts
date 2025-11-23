import { Component, inject } from '@angular/core';
import { SharedModule } from '../../../shared/shared.module';
import { freeSet } from '@coreui/icons';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute, Router } from '@angular/router';
import { BranchService } from '../branch.service';

@Component({
  selector: 'app-edit',
  imports: [SharedModule],
  templateUrl: './edit.component.html',
  styleUrl: './edit.component.scss',
  host: {
    'class': 'example',
  },
})
export class EditComponent {
    public favoriteColor = '#26ab3c';
    icons = freeSet;
    // location = inject(Location); 
    toastr  = inject(ToastrService);
    router = inject(Router);
    branchService = inject(BranchService);
    activatedRoute = inject(ActivatedRoute);
    BRANCH_ID:any;
    BRANCH:any;
    name:string = '';
    address:string = '';
    phone:string = '';
    state:boolean = true;
    
    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any) =>{
        this.BRANCH_ID = resp.id;
      });
      this.branchService.showBranch(this.BRANCH_ID)
      .subscribe((resp:any)=>{
        this.BRANCH = resp.branch;
        this.name = this.BRANCH.name;
        this.address = this.BRANCH.address;
        this.phone = this.BRANCH.phone;
        
        this.state = this.BRANCH.state;
      });
    }

    save(){
      if(!this.name || !this.address || !this.phone){
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }

      let branch = {
        'name': this.name,
        'address': this.address,
        'phone':this.phone
      };

      this.branchService.updateBranch(this.BRANCH_ID, branch)
      .subscribe((resp:any) =>{
        if(resp.message == 403){
          this.toastr.error('Validacion', 'La sucursal ya existe');
          return;
        }
        this.toastr.success('Exito', 'La sucursal se actualizo correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/branch/list");
    }
}
