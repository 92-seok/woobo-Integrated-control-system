"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Send, Info, FileText } from "lucide-react"

const MAX_CHARS = 70

export function MessageComposer() {
  const [title, setTitle] = useState("")
  const [body, setBody] = useState("")

  const charCount = title.length

  return (
    <div className="rounded-xl border border-border bg-card">
      {/* Header */}
      <div className="flex items-center gap-2 border-b border-border px-5 py-4">
        <FileText className="h-4 w-4 text-primary" />
        <h3 className="text-sm font-semibold text-card-foreground">메세지 입력</h3>
      </div>

      <div className="flex flex-col gap-4 p-5">
        {/* Title */}
        <div className="flex flex-col gap-2">
          <div className="flex items-center justify-between">
            <label htmlFor="msg-title" className="text-xs font-medium text-muted-foreground">
              제목
            </label>
            <span className="text-xs tabular-nums text-muted-foreground">
              <span className={charCount > MAX_CHARS ? "font-semibold text-destructive" : ""}>
                {charCount}
              </span>
              /{MAX_CHARS}(글자)
            </span>
          </div>
          <input
            id="msg-title"
            type="text"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            placeholder="메세지 제목을 입력하세요"
            className="h-10 w-full rounded-lg border border-border bg-background px-4 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          />
        </div>

        {/* Body */}
        <div className="flex flex-col gap-2">
          <label htmlFor="msg-body" className="text-xs font-medium text-muted-foreground">
            내용
          </label>
          <textarea
            id="msg-body"
            value={body}
            onChange={(e) => setBody(e.target.value)}
            placeholder="메세지 내용을 입력하세요"
            rows={6}
            className="w-full resize-none rounded-lg border border-border bg-background px-4 py-3 text-sm leading-relaxed text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          />
        </div>

        {/* Help + Send */}
        <div className="flex items-start justify-between gap-4">
          <div className="flex flex-col gap-2 rounded-lg bg-muted/50 p-3">
            <div className="flex items-center gap-1.5 text-xs font-medium text-muted-foreground">
              <Info className="h-3.5 w-3.5" />
              도움말
            </div>
            <ul className="flex flex-col gap-1 text-xs leading-relaxed text-muted-foreground">
              <li>수신자는 상단의 연락처관리에서 추가 또는 수정할 수 있습니다.</li>
              <li>보낼 문자의 제목과 내용을 입력합니다.</li>
              <li>{"제목은 상단의 '발송내역'에 기록됩니다."}</li>
            </ul>
          </div>

          <Button className="gap-2 bg-primary text-primary-foreground hover:bg-primary/90" size="lg">
            <Send className="h-4 w-4" />
            전송
          </Button>
        </div>
      </div>
    </div>
  )
}
