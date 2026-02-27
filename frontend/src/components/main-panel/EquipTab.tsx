import { useState, useCallback } from 'react';
import { ChevronDown, Wrench, Headphones } from 'lucide-react';
import { cn } from '@/lib/utils';

interface EquipRow {
  name: string;
  status: string;
  statusColor: string;
}

interface EquipSection {
  key: string;
  label: string;
  color: string;
  rows: EquipRow[];
}

const SECTIONS: EquipSection[] = [
  {
    key: 'broad',
    label: '방송',
    color: '#f3732c',
    rows: [{ name: '데모름', status: '정상', statusColor: '#3b82f6' }],
  },
  {
    key: 'display',
    label: '전광판',
    color: '#ffb200',
    rows: [{ name: '데모름', status: '정상', statusColor: '#3b82f6' }],
  },
  {
    key: 'gate',
    label: '차단기',
    color: '#e91e8f',
    rows: [
      { name: '데모름', status: '점검요망', statusColor: '#d97706' },
      { name: '데모름', status: '점검요망', statusColor: '#d97706' },
      { name: '데모름', status: 'OPEN', statusColor: '#ef4444' },
    ],
  },
];

export function EquipTab() {
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
            <Wrench className="h-4 w-4 cursor-pointer text-gray-400 transition-colors hover:text-gray-600" />
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
                    {['장비명', '상태', '점검', 'A/S'].map((h, i) => (
                      <th
                        key={h}
                        className={cn('py-2 text-[11px] font-medium', i === 0 ? 'pl-2 text-left' : 'text-center')}
                        style={{ color: section.color }}
                      >
                        {h}
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {section.rows.map((row, i) => (
                    <tr key={i} className="border-b border-gray-50 transition-colors hover:bg-gray-50/50">
                      <td className="py-2.5 pl-2 text-left font-medium text-slate-700">{row.name}</td>
                      <td className="text-center">
                        <span style={{ color: row.statusColor, fontWeight: 600 }}>{row.status}</span>
                      </td>
                      <td className="text-center">
                        <Wrench className="mx-auto h-4 w-4 cursor-pointer text-gray-400 transition-colors hover:text-violet-500" />
                      </td>
                      <td className="text-center">
                        <Headphones className="mx-auto h-4 w-4 cursor-pointer text-gray-400 transition-colors hover:text-violet-500" />
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}

export default EquipTab;
