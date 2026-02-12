"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Plus, Pencil, Trash2 } from "lucide-react"

interface Contact {
  id: string
  name: string
  department: string
  position: string
  alias: string
  phone: string
}

const sampleContacts: Contact[] = [
  { id: "1", name: "김철수", department: "관제운영팀", position: "팀장", alias: "철수", phone: "010-1234-5678" },
  { id: "2", name: "이영희", department: "관제운영팀", position: "대리", alias: "영희", phone: "010-2345-6789" },
  { id: "3", name: "박민수", department: "시설관리팀", position: "과장", alias: "민수", phone: "010-3456-7890" },
  { id: "4", name: "정수진", department: "시설관리팀", position: "사원", alias: "수진", phone: "010-4567-8901" },
  { id: "5", name: "최동현", department: "IT운영팀", position: "부장", alias: "동현", phone: "010-5678-9012" },
]

export function ContactsManager() {
  const [contacts] = useState<Contact[]>(sampleContacts)

  return (
    <div className="rounded-xl border border-border bg-card">
      <div className="flex items-center justify-between border-b border-border px-5 py-4">
        <h3 className="text-sm font-semibold text-card-foreground">
          연락처 목록
          <span className="ml-2 text-xs font-normal text-muted-foreground">
            ({contacts.length}명)
          </span>
        </h3>
        <Button size="sm" className="gap-1.5 bg-primary text-primary-foreground hover:bg-primary/90">
          <Plus className="h-3.5 w-3.5" />
          연락처 추가
        </Button>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-border bg-muted/50">
              <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                이름
              </th>
              <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                부서명
              </th>
              <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                직책
              </th>
              <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                별칭
              </th>
              <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                연락처
              </th>
              <th className="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                관리
              </th>
            </tr>
          </thead>
          <tbody>
            {contacts.map((contact, idx) => (
              <tr
                key={contact.id}
                className={`border-b border-border transition-colors hover:bg-muted/30 ${idx === contacts.length - 1 ? "border-b-0" : ""}`}
              >
                <td className="px-5 py-3 text-sm font-medium text-card-foreground">
                  {contact.name}
                </td>
                <td className="px-5 py-3 text-sm text-muted-foreground">
                  {contact.department}
                </td>
                <td className="px-5 py-3">
                  <span className="inline-flex rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                    {contact.position}
                  </span>
                </td>
                <td className="px-5 py-3 text-sm text-muted-foreground">
                  {contact.alias}
                </td>
                <td className="px-5 py-3 text-sm font-mono text-muted-foreground">
                  {contact.phone}
                </td>
                <td className="px-5 py-3 text-right">
                  <div className="inline-flex items-center gap-1">
                    <button
                      type="button"
                      className="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                      aria-label={`${contact.name} 수정`}
                    >
                      <Pencil className="h-3.5 w-3.5" />
                    </button>
                    <button
                      type="button"
                      className="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive"
                      aria-label={`${contact.name} 삭제`}
                    >
                      <Trash2 className="h-3.5 w-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
