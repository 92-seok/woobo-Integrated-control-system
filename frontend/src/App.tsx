import { Routes, Route } from 'react-router-dom';
import { Layout } from '@/components/layout/Layout';
import { SmsPage } from '@/pages/SmsPage';
import { MainPage } from '@/pages/MainPage';
import { BroadPage } from '@/pages/BroadPage';
import { GatePage } from '@/pages/GatePage';
import { ParkingPage } from '@/pages/ParkingPage';
import { ReportPage } from '@/pages/ReportPage';
import { DataPage } from '@/pages/DataPage';
import './App.css';

function App() {
  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<MainPage />} />
        <Route path="/data" element={<DataPage />} />
        <Route path="/broad" element={<BroadPage />} />
        <Route path="/sms" element={<SmsPage />} />
        <Route path="/gate" element={<GatePage />} />
        <Route path="/parking" element={<ParkingPage />} />
        <Route path="/report" element={<ReportPage />} />
      </Route>
    </Routes>
  );
}

export default App;
