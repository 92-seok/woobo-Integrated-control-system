import 'chart.js/auto';
import { Bar, Line } from 'react-chartjs-2';
import type { ChartOptions } from 'chart.js';
import { formatValue, getSensorColor } from '@/lib/data-utils';
import { SENSOR_CONFIG } from '@/types/data';
import type { SensorType, WaterUnit } from '@/types/data';

// 차트 색상 팔레트
const CHART_COLORS = [
  'rgba(59, 130, 246, 0.85)', // blue
  'rgba(239, 68, 68, 0.75)', // red
  'rgba(34, 197, 94, 0.75)', // green
  'rgba(249, 115, 22, 0.75)', // orange
  'rgba(139, 92, 246, 0.75)', // violet
  'rgba(20, 184, 166, 0.75)', // teal
  'rgba(236, 72, 153, 0.75)', // pink
  'rgba(100, 116, 139, 0.75)', // slate
  'rgba(202, 138, 4, 0.75)', // amber
  'rgba(8, 145, 178, 0.75)', // cyan
  'rgba(162, 28, 175, 0.75)', // fuchsia
  'rgba(101, 163, 13, 0.75)', // lime
];

export interface DatasetItem {
  label: string;
  data: (number | null)[];
}

interface DataChartProps {
  sensorType: SensorType;
  labels: (string | number | null)[];
  datasets: DatasetItem[];
  waterUnit?: WaterUnit;
}

export function DataChart({ sensorType, labels, datasets, waterUnit }: DataChartProps) {
  const isBar = sensorType === 'rain';
  const themeColor = getSensorColor(sensorType);
  const unit = SENSOR_CONFIG[sensorType].unit;

  // 데이터셋 변환: 원시값 -> 표시값
  const chartDatasets = datasets.map((ds, idx) => {
    const color = datasets.length === 1 ? themeColor : CHART_COLORS[idx % CHART_COLORS.length];

    const converted = ds.data.map((v) => (v !== null ? Number(formatValue(v, sensorType, waterUnit)) : null));

    return {
      label: ds.label,
      data: converted,
      backgroundColor: isBar ? color : 'transparent',
      borderColor: color,
      borderWidth: isBar ? 1.5 : 2,
      pointRadius: isBar ? 0 : 2.5,
      pointHoverRadius: isBar ? 0 : 5,
      fill: false,
      tension: 0.3,
      spanGaps: true,
    };
  });

  const chartData = {
    labels,
    datasets: chartDatasets,
  };

  // Y축 최대값 계산
  const allValues = chartDatasets.flatMap((ds) => ds.data.filter((v): v is number => v !== null));
  const maxVal = allValues.length > 0 ? Math.max(...allValues) : 1;

  const options: ChartOptions<'bar' | 'line'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: datasets.length > 1,
        position: 'top',
        labels: {
          font: { size: 11 },
          boxWidth: 12,
          padding: 12,
        },
      },
      tooltip: {
        enabled: true,
        callbacks: {
          label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y} ${unit}`,
        },
      },
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { font: { size: 10 } },
      },
      y: {
        min: 0,
        max: maxVal + maxVal * 0.15 + 1,
        grid: { color: 'rgba(0,0,0,0.06)' },
        ticks: { font: { size: 10 } },
      },
    },
  };

  // rain -> 막대, 나머지 -> 꺾은선
  return (
    <div className="h-105 w-full">
      {isBar ? (
        <Bar data={chartData} options={options as ChartOptions<'bar'>} />
      ) : (
        <Line data={chartData} options={options as ChartOptions<'line'>} />
      )}
    </div>
  );
}
