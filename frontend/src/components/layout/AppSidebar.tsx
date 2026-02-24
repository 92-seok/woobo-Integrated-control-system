import {
  Monitor,
  Search,
  Radio,
  PanelTop,
  Shield,
  CircleParking,
  MessageSquare,
  SlidersHorizontal,
  FileText,
  Users,
  Bell,
  LogOut,
  ChevronLeft,
} from 'lucide-react';
import { NavLink } from 'react-router-dom';
import { cn } from '@/lib/utils';

const navItems = [
  { icon: Monitor, label: '상황화면', to: '/' },
  { icon: Search, label: '데이터검색', to: '/data' },
  { icon: Radio, label: '방송관리', to: '/broad' },
  { icon: PanelTop, label: '전광판관리', to: '/display' },
  { icon: Shield, label: '차단기관리', to: '/gate' },
  { icon: CircleParking, label: '주차장관리', to: '/parking' },
  { icon: MessageSquare, label: 'SMS관리', to: '/sms' },
  { icon: SlidersHorizontal, label: '임계치관리', to: '/alert' },
  { icon: FileText, label: '보고서', to: '/report' },
  { icon: Users, label: '계정관리', to: '/admin' },
];

interface AppSidebarProps {
  collapsed?: boolean;
  onToggle?: () => void;
}

export function AppSidebar({ collapsed = false, onToggle }: AppSidebarProps) {
  return (
    <aside
      className={cn(
        'border-sidebar-border bg-sidebar text-sidebar-foreground relative flex h-screen flex-col border-r transition-all duration-300 ease-in-out',
        collapsed ? 'w-[72px]' : 'w-[240px]'
      )}
    >
      {/* 로고 영역 */}
      <div className="flex items-center gap-3 px-4 py-10">
        <div className="bg-sidebar-primary/20 relative flex h-10 w-10 shrink-0 items-center justify-center rounded-lg">
          <Bell className="text-sidebar-primary h-7 w-7" />
          <span
            className="border-sidebar absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 bg-emerald-400"
            style={{ animation: 'pulse-dot 2.5s ease-in-out infinite' }}
          />
        </div>
        {!collapsed && (
          <div className="overflow-hidden">
            <p className="font-jakarta text-md leading-tight font-bold text-white">지능형 통합관제 시스템</p>
            <p className="text-sidebar-foreground/40 mt-1 text-[11px] font-bold tracking-wider uppercase">
              Control System
            </p>
          </div>
        )}
      </div>

      {/* 구분선 */}
      <div className="bg-sidebar-border mx-4 h-px" />

      {/* 네비게이션 */}
      <nav className="flex flex-1 flex-col gap-0.5 overflow-y-auto px-2 py-4">
        {navItems.map((item) => (
          <NavLink
            key={item.label}
            to={item.to}
            className={({ isActive }) =>
              cn(
                'group text-md relative flex items-center gap-5 rounded-lg px-5 py-7 font-bold transition-all duration-150',
                isActive
                  ? 'bg-white/15 text-white before:absolute before:top-1/2 before:left-0 before:h-5 before:w-[3px] before:-translate-y-1/2 before:rounded-r-full before:bg-white'
                  : 'text-white/60 hover:bg-white/10 hover:text-white/90'
              )
            }
            title={collapsed ? item.label : undefined}
          >
            <item.icon className="h-[18px] w-[18px] shrink-0" />
            {!collapsed && <span className="truncate">{item.label}</span>}
          </NavLink>
        ))}
      </nav>

      {/* 하단 버튼 */}
      <div className="flex flex-col gap-0.5 px-2 pb-4">
        <div className="bg-sidebar-border mx-1 mb-2 h-px" />
        <button
          type="button"
          onClick={onToggle}
          className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/60 transition-all hover:bg-white/10 hover:text-white/90"
        >
          <ChevronLeft
            className={cn('h-[18px] w-[18px] shrink-0 transition-transform duration-300', collapsed && 'rotate-180')}
          />
          {!collapsed && <span>사이드바 접기</span>}
        </button>
        <button
          type="button"
          className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/60 transition-all hover:bg-red-500/15 hover:text-red-300"
        >
          <LogOut className="h-[18px] w-[18px] shrink-0" />
          {!collapsed && <span>로그아웃</span>}
        </button>
      </div>
    </aside>
  );
}
