import { CommonModule, NgStyle } from '@angular/common';
import { Component, DestroyRef, DOCUMENT, effect, inject, OnInit, Renderer2, signal, WritableSignal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ChartOptions } from 'chart.js';
import { AvatarComponent,ButtonDirective,ButtonGroupComponent,CardBodyComponent,CardComponent,CardFooterComponent,CardHeaderComponent,
         ColComponent,FormCheckLabelDirective,GutterDirective,ProgressComponent,RowComponent,TableDirective} from '@coreui/angular';
import { ChartjsComponent } from '@coreui/angular-chartjs';
import { IconDirective } from '@coreui/icons-angular';

import { WidgetsBrandComponent } from '../widgets/widgets-brand/widgets-brand.component';
import { WidgetsDropdownComponent } from '../widgets/widgets-dropdown/widgets-dropdown.component';
import { DashboardChartsData, IChartProps } from './dashboard-charts-data';
import { ToastrService } from 'ngx-toastr';
import { DashboardService } from './dashboard.service';

@Component({
  templateUrl:  'dashboard.component.html',
  styleUrls:  [ 'dashboard.component.scss'],
  imports:    [ CommonModule, WidgetsDropdownComponent, CardComponent, CardBodyComponent, RowComponent, ColComponent, ButtonDirective, IconDirective, ReactiveFormsModule, ButtonGroupComponent, FormCheckLabelDirective, ChartjsComponent, NgStyle, CardFooterComponent, GutterDirective, ProgressComponent, WidgetsBrandComponent, CardHeaderComponent, TableDirective, AvatarComponent]
})
export class DashboardComponent implements OnInit {

  dashboardService = inject(DashboardService);
  toastr      = inject(ToastrService);
  
  readonly #destroyRef: DestroyRef = inject(DestroyRef);
  readonly #document:   Document = inject(DOCUMENT);
  readonly #renderer:   Renderer2 = inject(Renderer2);
  readonly #chartsData: DashboardChartsData = inject(DashboardChartsData);

  public mainChart:     IChartProps = { type: 'line' };
  public mainChartRef:  WritableSignal<any> = signal(undefined);
  #mainChartRefEffect = effect(() => {
    if (this.mainChartRef()) {
      this.setChartStyles();
    }
  });
  public chart: Array<IChartProps> = [];
  public trafficRadioGroup = new FormGroup({
    trafficRadio: new FormControl('Month')
  });

  ngOnInit(): void {
    this.dashboardService.reportsSalesMonthly()
    .subscribe({
      next:(resp:any) =>  {
        this.#chartsData.buildChart(
          resp.monthly,
          resp.monthly_last,
          resp.current_year
        );
        
      },
      error:(err:any) =>  {
        
      },
      complete:() =>{
        this.mainChart = this.#chartsData.mainChart;
      },
    });
    this.updateChartOnColorModeChange();
  }


  handleChartRef($chartRef: any) {
    if ($chartRef) {
      this.mainChartRef.set($chartRef);
    }
  }

  updateChartOnColorModeChange() {
    const unListen = this.#renderer.listen(this.#document.documentElement, 'ColorSchemeChange', () => {
      this.setChartStyles();
    });

    this.#destroyRef.onDestroy(() => {
      unListen();
    });
  }

  // setChartStyles() {
  //   if (this.mainChartRef()) {
  //     // setTimeout(() => {
  //       const options: ChartOptions = { ...this.mainChart.options };
  //       const scales = this.#chartsData.getScales();
  //       this.mainChartRef().options.scales = { ...options.scales, ...scales };
  //       this.mainChartRef().update();
  //     // });
  //   }
  // }

  setChartStyles() {
    const chart = this.mainChartRef();
    if (!chart) return;
    if (!chart.data?.datasets?.length) return;
    requestAnimationFrame(() => {
      chart.options.scales = {
        ...chart.options.scales,
        ...this.#chartsData.getScales()
      };
      chart.update('none'); // 👈 IMPORTANTE
    });
  }


}
