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
  providedIn: 'root'
})
export class DashboardChartsData {
  

  public mainChart: IChartProps = { type: 'line' };

  buildChart(monthly: number[], last: number[], year: number) {
    this.sales_monthly = monthly;
    this.sales_monthly_last = last;
    this.current_year = year;
    this.mainChart.current_year = year;

    this.initMainChart();
  }

  sales_monthly: any  =[];
  sales_monthly_last: any=[];
  current_year:any;


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
      this.mainChart['Data1'].push(this.sales_monthly[i] ?? 0);
      this.mainChart['Data2'].push(this.sales_monthly_last[i] ?? 0);
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


    const colors = [
      {
        backgroundColor: 'transparent',
        borderColor: brandInfo,
        pointHoverBackgroundColor: brandInfo,
        borderWidth: 2,
        fill: false
      },
      {
        backgroundColor: 'transparent',
        borderColor: brandSuccess || '#4dbd74',
        pointHoverBackgroundColor: '#fff',
        fill: false
      }
    ];



    const datasets: ChartDataset<'line'>[] = [
      {
        data: this.mainChart['Data1'],
        label: 'Actual',
        borderWidth: 2,
        fill: false
      },
      {
        data: this.mainChart['Data2'],
        label: 'Anterior',
        borderWidth: 2,
        fill: false
      }
    ];


    const plugins: DeepPartial<PluginOptionsByType<any>> = {
      legend: { display: false },
      // filler: { propagate: false }, // 🔥 FIX REAL
      tooltip: {
        callbacks: {
          labelColor: (context) => ({
            backgroundColor: context.dataset.borderColor
          }) as TooltipLabelStyle
        }
      }
    };

    // const scales = this.getScales();

    const options: ChartOptions = {
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: this.getScales(),
      elements: {
        line: { tension: 0.4 },
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
