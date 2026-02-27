import { Routes, Route } from 'react-router-dom';
import { Layout } from '@/components/layout/Layout';
import { SmsPage } from '@/pages/SmsPage';
import { MainPage } from '@/pages/MainPage';
import { BroadPage } from '@/pages/BroadPage';
import './App.css';

function App() {
  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<MainPage />} />
        <Route path="/broad" element={<BroadPage />} />
        <Route path="/sms" element={<SmsPage />} />
      </Route>
    </Routes>
  );
}

export default App;
