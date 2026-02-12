import { Routes, Route, Navigate } from 'react-router-dom';
import { SmsPage } from '@/pages/SmsPage';
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
      <Route path="/sms" element={<SmsPage />} />
      {/* <Route path="*" element={<Navigate to="/" replace />} /> */}
    </Routes>
  );
}

export default App;
