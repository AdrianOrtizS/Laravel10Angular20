import { inject, Injectable, OnInit } from '@angular/core';
import { ChartData, ChartDataset, ChartOptions, ChartType, PluginOptionsByType, ScaleOptions, TooltipLabelStyle } from 'chart.js';
import { DeepPartial } from './utils';
import { getStyle } from '@coreui/utils';
import { DashboardService } from './dashboard.service';

export interface IChartProps {
  data?:    ChartData;
  labels?:  any;
  options?: ChartOptions;
  colors?:  any;
  type?:     ChartType;
  legend?:  any;

  [propName: string]: any;
  current_year?:any;
}

@Injectable({
  providedIn: 'any'
})
export class DashboardChartsData {
  
  dashboardService = inject(DashboardService);

  constructor() {
    this.reportsSalesMonthly();
  }

  public mainChart: IChartProps = { type: 'line' };

  public random(min: number, max: number) {
    return Math.floor(Math.random() * (max - min + 1) + min);
  }

  sales_monthly: any  =[];
  sales_monthly_last: any=[];
  current_year:any;

  reportsSalesMonthly(){
    this.dashboardService.reportsSalesMonthly()
      .subscribe({
        next:(resp:any) =>{
          this.sales_monthly = resp.monthly;
          this.sales_monthly_last = resp.monthly_last;
          this.current_year = resp.current_year;

          this.mainChart.current_year = this.current_year;
          this.initMainChart();

        },
        error:(err:any) =>{
          console.log(err);
        },
        complete:() =>{
          // console.log('jajaja'); 
        }
      });
  }


  initMainChart(period: string = 'Month') {
    const brandSuccess  = getStyle('--cui-success') ?? '#4dbd74';
    const brandInfo     = getStyle('--cui-info') ?? '#20a8d8';
    const brandInfoBg   = `rgba(${getStyle('--cui-info-rgb')}, .1)`
    // const brandDanger   = getStyle('--cui-danger') ?? '#f86c6b';

    // mainChart
    this.mainChart['elements'] = period === 'Month' ? 12 : 27;
    this.mainChart['Data1'] = [];
    this.mainChart['Data2'] = [];
    // this.mainChart['Data3'] = [];

    // generate random values for mainChart
    for (let i = 0; i < this.mainChart['elements']; i++) {
      this.mainChart['Data1'].push(this.sales_monthly[i]);
      this.mainChart['Data2'].push(this.sales_monthly_last[i]);
      // this.mainChart['Data3'].push(65);
    }

    let labels: string[] = [];
    if (period === 'Month') {
      labels = [
        'Enero',
        'Febrero',
        'Marzo',
        'Abril',
        'Mayo',
        'Junio',
        'Julio',
        'Agosto',
        'Septiember',
        'Octubre',
        'Noviembre',
        'Diciembre'
      ];
    } 
    // else {
    //   /* tslint:disable:max-line-length */
    //   const week = [
    //     'Monday',
    //     'Tuesday',
    //     'Wednesday',
    //     'Thursday',
    //     'Friday',
    //     'Saturday',
    //     'Sunday'
    //   ];
    //   labels = week.concat(week, week, week);
    // }

    const colors = [
      {
        // brandInfo
        backgroundColor: brandInfoBg,
        borderColor: brandInfo,
        pointHoverBackgroundColor: brandInfo,
        borderWidth: 2,
        fill: true
      },
      {
        // brandSuccess
        backgroundColor: 'transparent',
        borderColor: brandSuccess || '#4dbd74',
        pointHoverBackgroundColor: '#fff'
      },
      // {
      //   // brandDanger
      //   backgroundColor: 'transparent',
      //   borderColor: brandDanger || '#f86c6b',
      //   pointHoverBackgroundColor: brandDanger,
      //   borderWidth: 1,
      //   borderDash: [8, 5]
      // }
    ];

    const datasets: ChartDataset[] = [
      {
        data: this.mainChart['Data1'],
        label: 'Actual',
        ...colors[0]
      },
      {
        data: this.mainChart['Data2'],
        label: 'Anterior',
        ...colors[1]
      },
      // {
      //   data: this.mainChart['Data3'],
      //   label: 'BEP',
      //   ...colors[2]
      // }
    ];

    const plugins: DeepPartial<PluginOptionsByType<any>> = {
      legend: {
        display: false
      },
      tooltip: {
        callbacks: {
          labelColor: (context) => ({ backgroundColor: context.dataset.borderColor } as TooltipLabelStyle)
        }
      }
    };

    const scales = this.getScales();

    const options: ChartOptions = {
      maintainAspectRatio: false,
      plugins,
      scales,
      elements: {
        line: {
          tension: 0.4
        },
        point: {
          radius: 0,
          hitRadius: 10,
          hoverRadius: 4,
          hoverBorderWidth: 3
        }
      }
    };

    this.mainChart.type = 'line';
    this.mainChart.options = options;
    this.mainChart.data = {
      datasets,
      labels
    };
  }


  getScales() {
    const colorBorderTranslucent = getStyle('--cui-border-color-translucent');
    const colorBody = getStyle('--cui-body-color');

    const scales: ScaleOptions<any> = {
      x: {
        grid: {
          color: colorBorderTranslucent,
          drawOnChartArea: false
        },
        ticks: {
          color: colorBody
        }
      },

      y: {
        border: {
          color: colorBorderTranslucent
        },
        grid: {
          color: colorBorderTranslucent
        },
        // max: 1000,
        beginAtZero: true,
        ticks: {
          color: colorBody,
          // maxTicksLimit: 5,
          // stepSize: Math.ceil(250 / 5)
        }
      }
    };
    return scales;
  }

}
