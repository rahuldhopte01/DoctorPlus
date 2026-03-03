import { useState } from 'react';
import medicineImage from 'figma:asset/6346eb4805b5828a0ab16cee121fa679f39e2321.png';
import { MedicineCard } from './components/MedicineCard';
import { Info } from 'lucide-react';

interface Medicine {
  id: string;
  name: string;
  subtitle: string;
  strength: string;
  form: string;
  image: string;
}

export default function App() {
  // Redirect to index.html for vanilla HTML/CSS/JS version
  if (typeof window !== 'undefined') {
    window.location.href = '/index.html';
  }
  
  return (
    <div className="flex min-h-screen items-center justify-center">
      <p className="text-gray-600">Redirecting to HTML version...</p>
    </div>
  );
}