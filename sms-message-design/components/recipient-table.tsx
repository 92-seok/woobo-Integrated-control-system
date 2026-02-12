"use client"

import { useState } from "react"
import { Checkbox } from "@/components/ui/checkbox"
import { cn } from "@/lib/utils"
import { UserPlus, Search } from "lucide-react"

interface Recipient {
  id: string
  name: string
  department: string
  position: string
  alias: string
  phone: string
}

const sampleRecipients: Recipient[] = [
  { id: "1", name: "김철수", department: "관제운영팀", position: "팀장", alias: "철수", phone: "010-1234-5678" },
  { id: "2", name: "이영희", department: "관제운영팀", position: "대리", alias: "영희", phone: "010-2345-6789" },
  { id: "3", name: "박민수", department: "시설관리팀", position: "과장", alias: "민수", phone: "010-3456-7890" },
  { id: "4", name: "정수진", department: "시설관리팀", position: "사원", alias: "수진", phone: "010-4567-8901" },
  { id: "5", name: "최동현", department: "IT운영팀", position: "부장", alias: "동현", phone: "010-5678-9012" },
]

export function RecipientTable() {
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set())
  const [searchQuery, setSearchQuery] = useState("")

  const filteredRecipients = sampleRecipients.filter(
    (r) =>
      r.name.includes(searchQuery) ||
      r.department.includes(searchQuery) ||
      r.position.includes(searchQuery) ||
      r.phone.includes(searchQuery)
  )

  const toggleAll = () => {
    if (selectedIds.size === filteredRecipients.length) {
      setSelectedIds(new Set())
    } else {
      setSelectedIds(new Set(filteredRecipients.map((r) => r.id)))
    }
  }

  const toggleOne = (id: string) => {
    const next = new Set(selectedIds)
    if (next.has(id)) {
      next.delete(id)
    } else {
      next.add(id)
    }
    setSelectedIds(next)
  }

  return (
    <div className="rounded-xl border border-border bg-card">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-border px-5 py-4">
        <div className="flex items-center gap-2">
          <UserPlus className="h-4 w-4 text-primary" />
          <h3 className="text-sm font-semibold text-card-foreground">수신자 선택</h3>
          {selectedIds.size > 0 && (
            <span className="rounded-full bg-primary px-2 py-0.5 text-xs font-medium text-primary-foreground">
              {selectedIds.size}명 선택
            </span>
          )}
        </div>
        <div className="relative">
          <Search className="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
          <input
            type="text"
            placeholder="검색..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="h-8 w-48 rounded-lg border border-border bg-background pl-8 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          />
        </div>
      </div>

      {/* Table */}
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-border bg-muted/50">
              <th className="w-12 px-4 py-3 text-center">
                <Checkbox
                  checked={
                    filteredRecipients.length > 0 &&
                    selectedIds.size === filteredRecipients.length
                  }
                  onCheckedChange={toggleAll}
                  aria-label="전체 선택"
                />
              </th>
              <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                이름
              </th>
              <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                부서명
              </th>
              <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                직책
              </th>
              <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                별칭
              </th>
              <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                연락처
              </th>
            </tr>
          </thead>
          <tbody>
            {filteredRecipients.map((recipient, idx) => (
              <tr
                key={recipient.id}
                className={cn(
                  "border-b border-border transition-colors hover:bg-muted/30",
                  selectedIds.has(recipient.id) && "bg-primary/5",
                  idx === filteredRecipients.length - 1 && "border-b-0"
                )}
              >
                <td className="px-4 py-3 text-center">
                  <Checkbox
                    checked={selectedIds.has(recipient.id)}
                    onCheckedChange={() => toggleOne(recipient.id)}
                    aria-label={`${recipient.name} 선택`}
                  />
                </td>
                <td className="px-4 py-3 text-sm font-medium text-card-foreground">
                  {recipient.name}
                </td>
                <td className="px-4 py-3 text-sm text-muted-foreground">
                  {recipient.department}
                </td>
                <td className="px-4 py-3">
                  <span className="inline-flex rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                    {recipient.position}
                  </span>
                </td>
                <td className="px-4 py-3 text-sm text-muted-foreground">
                  {recipient.alias}
                </td>
                <td className="px-4 py-3 text-sm font-mono text-muted-foreground">
                  {recipient.phone}
                </td>
              </tr>
            ))}
            {filteredRecipients.length === 0 && (
              <tr>
                <td colSpan={6} className="px-4 py-8 text-center text-sm text-muted-foreground">
                  검색 결과가 없습니다.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}
