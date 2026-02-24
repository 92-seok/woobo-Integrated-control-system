import { useState, useCallback } from 'react';
import { ChevronDown, RefreshCw, Headphones } from 'lucide-react';
import { cn } from '@/lib/utils';

interface DataRow {
  name: string;
  value: string;
  status: '정상' | '오류' | 'AS접수됨';
}

interface DataSection {
  key: string;
  label: string;
  color: string;
  headers: string[];
  rows: DataRow[];
}

const SECTIONS: DataSection[] = [
  {
    key: 'water',
    label: '수위',
    color: '#7c3aed',
    headers: ['장비명', '현재값', '상태', 'A/S'],
    rows: [
      { name: '데모름 K수위', value: '-', status: '오류' },
      { name: '데모름 C수위', value: '-', status: '오류' },
      { name: '데모름 침수', value: '-', status: '오류' },
    ],
  },
  {
    key: 'dplace',
    label: '변위',
    color: '#7c3aed',
    headers: ['장비명', '현재값', '상태', 'A/S'],
    rows: [],
  },
  {
    key: 'flood',
    label: '침수',
    color: '#e91e8f',
    headers: ['장비명', '침수상태(침수수위)', '상태', 'A/S'],
    rows: [{ name: '데모름', value: '-(-)', status: '오류' }],
  },
];

export function DataTab() {
  const [openSections, setOpenSections] = useState<Record<string, boolean>>(
    Object.fromEntries(SECTIONS.map((s) => [s.key, true]))
  );

  const toggle = useCallback((key: string) => {
    setOpenSections((p) => ({ ...p, [key]: !p[key] }));
  }, []);

  return (
    <div className="flex flex-col gap-3 text-[12px]">
      {SECTIONS.map((section) => (
        <div key={section.key}>
          {/* 섹션 헤더 */}
          <div
            className="flex items-center justify-between"
            style={{
              borderBottom: `2px solid
  ${section.color}`,
            }}
          >
            <button
              type="button"
              onClick={() => toggle(section.key)}
              className="flex items-center gap-1 py-2 text-left text-[13px] font-bold"
              style={{ color: section.color }}
            >
              <ChevronDown
                className={cn(
                  'h-3.5 w-3.5 transition-transform duration-200',
                  !openSections[section.key] && '-rotate-90'
                )}
              />
              {section.label}
            </button>
            <RefreshCw className="h-4 w-4 cursor-pointer text-gray-400 transition-colors hover:text-gray-600" />
          </div>

          {/* 테이블 (transition) */}
          <div
            className={cn(
              'grid transition-[grid-template-rows] duration-200 ease-out',
              openSections[section.key] ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
            )}
          >
            <div className="overflow-hidden">
              <table className="mt-1 w-full">
                <thead>
                  <tr className="border-b border-gray-100">
                    {section.headers.map((h, i) => (
                      <th
                        key={h}
                        className={cn(
                          'py-2 text-[11px] font-medium whitespace-nowrap',
                          i === 0 ? 'pl-2 text-left' : 'text-center'
                        )}
                        style={{ color: section.color }}
                      >
                        {h}
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {section.rows.length === 0 ? (
                    <tr>
                      <td colSpan={section.headers.length} className="py-4 text-center text-[11px] text-gray-300">
                        데이터 없음
                      </td>
                    </tr>
                  ) : (
                    section.rows.map((row, i) => (
                      <tr key={i} className="border-b border-gray-50 transition-colors hover:bg-gray-50/50">
                        <td className="py-2.5 pl-2 text-left font-medium text-slate-700">{row.name}</td>
                        <td className="text-center text-slate-500">{row.value}</td>
                        <td className="text-center">
                          <span className={row.status === '정상' ? 'text-blue-500' : 'text-red-500'}>{row.status}</span>
                        </td>
                        <td className="text-center">
                          <Headphones className="mx-auto h-4 w-4 cursor-pointer text-gray-400 transition-colors hover:text-violet-500" />
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}

export default DataTab;
