import { AfterContentInit, AfterViewInit, ChangeDetectorRef, Component, inject, OnInit, viewChild } from '@angular/core';
import { getStyle } from '@coreui/utils';
import { ChartjsComponent } from '@coreui/angular-chartjs';
import { RouterLink } from '@angular/router';
import { IconDirective } from '@coreui/icons-angular';
import { ButtonDirective,ColComponent,DropdownComponent,DropdownDividerDirective,DropdownItemDirective,DropdownMenuDirective,DropdownToggleDirective,RowComponent,TemplateIdDirective,WidgetStatAComponent} from '@coreui/angular';
import { WidgetsService } from '../widgets.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-widgets-dropdown',
  templateUrl: './widgets-dropdown.component.html',
  imports: [CommonModule,RowComponent, ColComponent, WidgetStatAComponent, TemplateIdDirective, IconDirective, DropdownComponent, ButtonDirective, DropdownToggleDirective, DropdownMenuDirective, DropdownItemDirective, RouterLink, DropdownDividerDirective, ChartjsComponent]
})
export class WidgetsDropdownComponent implements OnInit, AfterContentInit {
  
  widgetsService = inject(WidgetsService);
  
  private changeDetectorRef = inject(ChangeDetectorRef);

  data: any[] = [];
  options: any[] = [];

  labels = [
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre',
  ];
  optionsDefault: any = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false
      }
    },
    scales: {
      x: {
        display: false,
        grid: { display: false, drawBorder: false },
        ticks: { display: false }
      },
      y: {
        display: false,
        grid: { display: false },
        ticks: { display: false }
      }
    },
    elements: {
      line: {
        borderWidth: 1,
        tension: 0.4
      },
      point: {
        radius: 3,
        hitRadius: 10,
        hoverRadius: 5
      }
    }
  };

  ngOnInit(): void {
    this.reportsSalesMonthly();
    this.reportsSalesDaily();
    this.reportsProductsTop();
    this.reportsLowStock();
  }

  ngAfterContentInit(): void {
    this.changeDetectorRef.detectChanges();
  }

  datasets:any = [];
  setData() {
    this.datasets = [
      [{
        label: 'Total',
        backgroundColor: 'transparent',
        borderColor: 'rgba(255,255,255,.55)',
        pointBackgroundColor: getStyle('--cui-primary'),
        pointHoverBorderColor: getStyle('--cui-primary'),
        data: [...this.firstWidget]  // ✔️ ahora ya está inicializado
      }],
      [{
        label: 'Total',
        backgroundColor: 'transparent',
        borderColor: 'rgba(255,255,255,.55)',
        pointBackgroundColor: getStyle('--cui-info'),
        pointHoverBorderColor: getStyle('--cui-info'),
        data: [...this.secondWidget]
      }],
      [{
        label: 'Total',
        backgroundColor: 'rgba(255,255,255,.2)',
        borderColor: 'rgba(255,255,255,.55)',
        // pointBackgroundColor: getStyle('--cui-warning'),
        // pointHoverBorderColor: getStyle('--cui-warning'),
        data: [...this.thirdWidget],
        fill: false
      }],
      [{
        label: 'Total',
        backgroundColor: 'rgba(255,255,255,.2)',
        borderColor: 'rgba(255,255,255,.55)',
        data: [...this.fourWidget],
        barPercentage: 0.7
      }]
    ];

    // Ahora sí generas this.data
    for (let idx = 0; idx < 4; idx++) {
      const length = this.datasets[idx][0].data.length;
      if(idx == 0){
        this.data[idx] = {
          labels: this.labels.slice(0, length),
          datasets: this.datasets[idx]
        };
      }
      if(idx == 1){
        this.data[idx] = {
          labels: this.date_last_10days.slice(0, length),
          datasets: this.datasets[idx]
        };
      }

      if(idx == 2){
        this.data[idx] = {
          labels: this.low_stock_product.slice(0, length),
          datasets: this.datasets[idx]
        };
      }
      
      if(idx == 3){
        this.data[idx] = {
          labels: this.label_top_10_products.slice(0, length),
          datasets: this.datasets[idx]
        };
      }

    }
    this.setOptions();
  }


  setOptions() {
    this.options = [];
    for (let idx = 0; idx < 4; idx++) {
      const options = JSON.parse(JSON.stringify(this.optionsDefault));
      // === WIDGET 1 (Users) ===
      if (idx === 0) {
        const values = this.firstWidget;
        if (values.length) {
          const min = Math.min(...values);
          const max = Math.max(...values);
          options.scales.y.min = min - Math.abs(min * 0.2);
          options.scales.y.max = max + Math.abs(max * 0.2);
        }
      }

      // === WIDGET 2 ===
      else if (idx === 1) {
        options.elements.line.tension = 0;
      }
      // === WIDGET 3 ===
      else if (idx === 2) {
        options.elements.line.borderWidth = 2;
        options.elements.point.radius = 0;
        options.scales.x.display = false;
        options.scales.y.display = false;
      }
      // === WIDGET 4 (bar chart) ===
      else if (idx === 3) {
        options.scales.y.min = undefined;
        options.scales.y.max = undefined;
      }
      this.options.push(options);
    }
  }


  firstWidget:      any=[];
  total_current_year:any=0;
  total_last_year:   any=0;
  percent_difference:any=0;

  reportsSalesMonthly(){
    this.widgetsService.reportsSalesMonthly()
      .subscribe(
        {
          next:(resp:any)=>{
            this.total_current_year = resp.total_current_year;
            this.total_last_year    = resp.total_last_year;
            this.percent_difference = resp.percent_difference;
            
            let monthly = resp.monthly;
            this.firstWidget = monthly;
          },
          error:(err)=>{
            console.log(err);
          },
          complete:()=>{
            this.setData();
          }
        });    
  }

  secondWidget:any =[];
  date_last_10days:any =[];
  total_last_10days:any =0;

  reportsSalesDaily(){
    this.date_last_10days=[];
    this.widgetsService.reportsSalesDaily()
      .subscribe(
        {
          next:(resp:any) =>{
            let sale_daily = resp.last_10days;
            sale_daily.forEach((element:any) => {
              this.date_last_10days.push(element.date)
              this.secondWidget.push(element.total);
            });
            this.total_last_10days = resp.total_last_10days;
          },
          error:(err)=>{
            console.log(err);
          },
          complete:()=>{
            this.setData();
          }
        });    
  }


  thirdWidget:any =[];  
  low_stock_product:any =[];
  total_products_low_stock:any=0;
  
  reportsLowStock(){
    this.total_products_low_stock =0;
    this.low_stock_product =[];
    this.widgetsService.reportsLowStock()
      .subscribe(
        {
          next:(resp:any) =>{
            resp.low_stock_20.forEach((element:any) => {
              this.low_stock_product.push(element.name);
              this.thirdWidget.push(element.stock);
              this.total_products_low_stock += element.stock;
            });
          },
          error:(err)=>{
            console.log(err);
          },
          complete:()=>{
            this.setData();
          }
        });    
  }


  fourWidget:any =[];  
  label_top_10_products:any =[];
  total_products:any=0;
  
  reportsProductsTop(){
    this.label_top_10_products=[];
    this.widgetsService.reportsProductsTop()
      .subscribe(
        {
          next:(resp:any) =>{
            let sale_top = resp.top_10_products;
            sale_top.forEach((element:any) => {
              this.label_top_10_products.push(element.product_name)
              this.fourWidget.push(element.quantity);
            });
            this.total_products = resp.total_products;
            // console.log(this.label_top_10_products);
            // console.log(this.fourWidget);
            // console.log(this.total_products);
          },
          error:(err)=>{
            console.log(err);
          },
          complete:()=>{
            this.setData();
          }
        });    
  }

}

