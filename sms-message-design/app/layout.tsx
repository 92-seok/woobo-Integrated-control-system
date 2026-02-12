import React from "react"
import type { Metadata, Viewport } from 'next'
import { Inter, Noto_Sans_KR } from 'next/font/google'

import './globals.css'

const inter = Inter({ subsets: ['latin'], variable: '--font-inter' })
const notoSansKR = Noto_Sans_KR({ subsets: ['latin'], variable: '--font-noto-sans-kr' })

export const metadata: Metadata = {
  title: '지능형 통합관제 시스템',
  description: '지능형 통합관제 시스템 - SMS 관리',
}

export const viewport: Viewport = {
  themeColor: '#1a1d2e',
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="ko">
      <body className={`${inter.variable} ${notoSansKR.variable} font-sans antialiased`}>{children}</body>
    </html>
  )
}
