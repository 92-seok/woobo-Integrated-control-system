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
        'flex h-screen flex-col bg-sidebar text-sidebar-foreground transition-all duration-300 ease-in-out',
        collapsed ? 'w-[72px]' : 'w-[240px]'
      )}
    >
      <div className="flex items-center gap-3 px-5 py-6">
        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sidebar-primary">
          <Bell className="h-5 w-5 text-sidebar-primary-foreground" />
        </div>
        {!collapsed && (
          <div className="flex flex-col overflow-hidden">
            <span className="line-clamp-2 text-xs text-sidebar-accent-foreground mb-2">
              Intelligent Integrated Control System
            </span>
            <span className="line-clamp-2 text-sm font-semibold text-sidebar-foreground">
              지능형 통합관제 시스템
            </span>
          </div>
        )}
      </div>

      <div className="mx-4 h-px bg-sidebar-border" />

      <nav className="flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">
        {navItems.map((item) => (
          <NavLink
            key={item.label}
            to={item.to}
            className={({ isActive }) =>
              cn(
                'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150',
                isActive
                  ? 'bg-sidebar-primary text-sidebar-primary-foreground'
                  : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground'
              )
            }
            title={collapsed ? item.label : undefined}
          >
            <item.icon className="h-[18px] w-[18px] shrink-0 text-white" />
            {!collapsed && <span className="truncate text-white">{item.label}</span>}
          </NavLink>
        ))}
      </nav>

      <div className="flex flex-col gap-1 px-3 pb-4">
        <div className="mx-1 mb-2 h-px bg-sidebar-border" />
        <button
          type="button"
          onClick={onToggle}
          className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
        >
          <ChevronLeft
            className={cn(
              'h-[18px] w-[18px] shrink-0 transition-transform duration-300',
              collapsed && 'rotate-180'
            )}
          />
          {!collapsed && <span>사이드바 접기</span>}
        </button>
        <button
          type="button"
          className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
        >
          <LogOut className="h-[18px] w-[18px] shrink-0" />
          {!collapsed && <span>로그아웃</span>}
        </button>
      </div>
    </aside>
  );
}
