import { Routes, Route, Navigate } from 'react-router-dom';
import { SmsLayout } from '@/pages/SmsLayout';
import { SendMsg } from '@/pages/SendMsg';
import { SendList } from '@/pages/SendList';
import { AddrControl } from '@/pages/AddrControl';
import './App.css';

function App() {
  return (
    <Routes>
      <Route
        path="/"
        element={
          <div className="p-6">
            <h1 className="text-xl font-semibold">지능형 통합관제 시스템</h1>
            <p className="text-slate-500 mr-2">메인 대시보드 (구현예정)</p>
          </div>
        }
      />
      <Route path="/sms" element={<SmsLayout />}>
        <Route index element={<Navigate to="/sms/send" replace />}></Route>
        <Route path="send" element={<SendMsg />}></Route>
        <Route path="list" element={<SendList />}></Route>
        <Route path="address" element={<AddrControl />}></Route>
      </Route>
      <Route path="*" element={<Navigate to="/" replace />}></Route>
    </Routes>
  );
}

export default App;
