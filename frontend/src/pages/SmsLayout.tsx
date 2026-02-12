import { Outlet, NavLink } from 'react-router-dom';

export function SmsLayout() {
  return (
    <div className="w-full min-w-0 min-h-[calc(100vh-2rem)] bg-slate-50/60 p-6 pb-10 box-border">
      <nav className="flex gap-1 border-b border-slate-200 mb-6">
        <NavLink
          to="/sms/send"
          className={({ isActive }) =>
            `px-4 py-2.5 rounded-t-lg text-sm font-medium transition-colors ${isActive ? 'bg-white text-[#2b3280] border border-slate-200 border-b-white -mb-px shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'}`
          }
        >
          문자발송
        </NavLink>
        <NavLink
          to="/sms/list"
          className={({ isActive }) =>
            `px-4 py-2.5 rounded-t-lg text-sm font-medium transition-colors ${isActive ? 'bg-white text-[#2b3280] border border-slate-200 border-b-white -mb-px shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'}`
          }
        >
          발송내역
        </NavLink>
        <NavLink
          to="/sms/address"
          className={({ isActive }) =>
            `px-4 py-2.5 rounded-t-lg text-sm font-medium transition-colors ${isActive ? 'bg-white text-[#2b3280] border border-slate-200 border-b-white -mb-px shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'}`
          }
        >
          연락처관리
        </NavLink>
      </nav>
      <Outlet />
    </div>
  );
}
