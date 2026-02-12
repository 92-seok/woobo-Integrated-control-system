"use client"

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
} from "lucide-react"
import { cn } from "@/lib/utils"

const navItems = [
  { icon: Monitor, label: "상황화면", href: "#" },
  { icon: Search, label: "데이터검색", href: "#" },
  { icon: Radio, label: "방송관리", href: "#" },
  { icon: PanelTop, label: "전광판관리", href: "#" },
  { icon: Shield, label: "차단기관리", href: "#" },
  { icon: CircleParking, label: "주차장관리", href: "#" },
  { icon: MessageSquare, label: "SMS관리", href: "#", active: true },
  { icon: SlidersHorizontal, label: "임계치관리", href: "#" },
  { icon: FileText, label: "보고서", href: "#" },
  { icon: Users, label: "계정관리", href: "#" },
]

interface AppSidebarProps {
  collapsed?: boolean
  onToggle?: () => void
}

export function AppSidebar({ collapsed = false, onToggle }: AppSidebarProps) {
  return (
    <aside
      className={cn(
        "flex h-screen flex-col bg-sidebar text-sidebar-foreground transition-all duration-300 ease-in-out",
        collapsed ? "w-[72px]" : "w-[240px]"
      )}
    >
      {/* Header */}
      <div className="flex items-center gap-3 px-5 py-6">
        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sidebar-primary">
          <Bell className="h-5 w-5 text-sidebar-primary-foreground" />
        </div>
        {!collapsed && (
          <div className="flex flex-col overflow-hidden">
            <span className="truncate text-sm font-semibold text-sidebar-accent-foreground">
              통합관제
            </span>
            <span className="truncate text-xs text-sidebar-foreground">
              지능형 시스템
            </span>
          </div>
        )}
      </div>

      {/* Separator */}
      <div className="mx-4 h-px bg-sidebar-border" />

      {/* Navigation */}
      <nav className="flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">
        {navItems.map((item) => (
          <a
            key={item.label}
            href={item.href}
            className={cn(
              "group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-150",
              item.active
                ? "bg-sidebar-primary text-sidebar-primary-foreground"
                : "text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
            )}
            title={collapsed ? item.label : undefined}
          >
            <item.icon className="h-[18px] w-[18px] shrink-0" />
            {!collapsed && <span className="truncate">{item.label}</span>}
          </a>
        ))}
      </nav>

      {/* Footer */}
      <div className="flex flex-col gap-1 px-3 pb-4">
        <div className="mx-1 mb-2 h-px bg-sidebar-border" />
        <button
          type="button"
          onClick={onToggle}
          className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
        >
          <ChevronLeft
            className={cn(
              "h-[18px] w-[18px] shrink-0 transition-transform duration-300",
              collapsed && "rotate-180"
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
  )
}
