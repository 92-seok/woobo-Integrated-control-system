import { useState } from 'react';
import { Outlet } from 'react-router-dom';
import { AppSidebar } from './AppSidebar';
import { cn } from '@/lib/utils';
import { Menu } from 'lucide-react';

export function Layout() {
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  return (
    <div className="bg-background flex h-screen overflow-hidden">
      {/* 모바일 오버레이 */}
      {mobileMenuOpen && (
        <div
          className="bg-foreground/40 fixed inset-0 z-40 backdrop-blur-sm md:hidden"
          onClick={() => setMobileMenuOpen(false)}
          onKeyDown={(e) => {
            if (e.key === 'Escape') setMobileMenuOpen(false);
          }}
          role="button"
          tabIndex={0}
          aria-label="메뉴 닫기"
        />
      )}
      {/* 데스크톱 사이드바 */}
      <div className="hidden md:block">
        <AppSidebar collapsed={sidebarCollapsed} onToggle={() => setSidebarCollapsed(!sidebarCollapsed)} />
      </div>

      {/* 모바일 사이드바 */}
      <div
        className={cn(
          'fixed inset-y-0 left-0 z-50 transform transition-transform duration-300 md:hidden',
          mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
        )}
      >
        <AppSidebar onToggle={() => setMobileMenuOpen(false)} />
      </div>

      {/* 오른쪽 콘텐트 전체 */}
      <main className="flex flex-1 flex-col overflow-hidden">
        {/* 모바일용 상단 햄버거 버튼 */}
        <div className="border-border flex items-center border-b bg-white px-4 py-3 md:hidden">
          <button
            type="button"
            onClick={() => setMobileMenuOpen(true)}
            className="text-muted-foreground hover:bg-muted rounded-md p-2"
            aria-label="메뉴 열기"
          >
            <Menu className="h-5 w-5" />
          </button>
        </div>

        {/* 각 페이지 컴포넌트 */}
        <Outlet />
      </main>
    </div>
  );
}

export default Layout;
