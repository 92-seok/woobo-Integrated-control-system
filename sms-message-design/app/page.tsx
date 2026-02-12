"use client"

import { useState } from "react"
import { AppSidebar } from "@/components/app-sidebar"
import { RecipientTable } from "@/components/recipient-table"
import { MessageComposer } from "@/components/message-composer"
import { SendHistory } from "@/components/send-history"
import { ContactsManager } from "@/components/contacts-manager"
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs"
import { MessageSquare, Clock, BookUser, Menu } from "lucide-react"

export default function Page() {
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  return (
    <div className="flex h-screen overflow-hidden bg-background">
      {/* Mobile overlay */}
      {mobileMenuOpen && (
        <div
          className="fixed inset-0 z-40 bg-foreground/20 backdrop-blur-sm md:hidden"
          onClick={() => setMobileMenuOpen(false)}
          onKeyDown={(e) => {
            if (e.key === "Escape") setMobileMenuOpen(false)
          }}
          role="button"
          tabIndex={0}
          aria-label="메뉴 닫기"
        />
      )}

      {/* Sidebar - Desktop */}
      <div className="hidden md:block">
        <AppSidebar
          collapsed={sidebarCollapsed}
          onToggle={() => setSidebarCollapsed(!sidebarCollapsed)}
        />
      </div>

      {/* Sidebar - Mobile */}
      <div
        className={`fixed inset-y-0 left-0 z-50 transform transition-transform duration-300 md:hidden ${
          mobileMenuOpen ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        <AppSidebar onToggle={() => setMobileMenuOpen(false)} />
      </div>

      {/* Main Content */}
      <main className="flex flex-1 flex-col overflow-hidden">
        {/* Top Bar */}
        <header className="flex items-center justify-between border-b border-border bg-card px-6 py-4">
          <div className="flex items-center gap-3">
            <button
              type="button"
              onClick={() => setMobileMenuOpen(true)}
              className="rounded-lg p-2 text-muted-foreground hover:bg-muted md:hidden"
              aria-label="메뉴 열기"
            >
              <Menu className="h-5 w-5" />
            </button>
            <div>
              <h1 className="text-lg font-semibold text-foreground">SMS 관리</h1>
              <p className="text-xs text-muted-foreground">문자 발송 및 연락처를 관리합니다</p>
            </div>
          </div>
          <div className="flex items-center gap-3">
            <div className="hidden items-center gap-2 sm:flex">
              <div className="h-2 w-2 rounded-full bg-emerald-500" />
              <span className="text-xs text-muted-foreground">시스템 정상</span>
            </div>
            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-medium text-primary-foreground">
              관
            </div>
          </div>
        </header>

        {/* Content Area */}
        <div className="flex-1 overflow-y-auto p-6">
          <Tabs defaultValue="send" className="flex flex-col gap-6">
            <TabsList className="inline-flex w-fit rounded-lg bg-muted p-1">
              <TabsTrigger value="send" className="gap-2 rounded-md px-4 py-2 text-sm data-[state=active]:bg-card data-[state=active]:text-foreground data-[state=active]:shadow-sm">
                <MessageSquare className="h-4 w-4" />
                문자발송
              </TabsTrigger>
              <TabsTrigger value="history" className="gap-2 rounded-md px-4 py-2 text-sm data-[state=active]:bg-card data-[state=active]:text-foreground data-[state=active]:shadow-sm">
                <Clock className="h-4 w-4" />
                발송내역
              </TabsTrigger>
              <TabsTrigger value="contacts" className="gap-2 rounded-md px-4 py-2 text-sm data-[state=active]:bg-card data-[state=active]:text-foreground data-[state=active]:shadow-sm">
                <BookUser className="h-4 w-4" />
                연락처관리
              </TabsTrigger>
            </TabsList>

            <TabsContent value="send" className="flex flex-col gap-6">
              <RecipientTable />
              <MessageComposer />
            </TabsContent>

            <TabsContent value="history">
              <SendHistory />
            </TabsContent>

            <TabsContent value="contacts">
              <ContactsManager />
            </TabsContent>
          </Tabs>
        </div>
      </main>
    </div>
  )
}
