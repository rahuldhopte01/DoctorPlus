interface Medicine {
  id: string;
  name: string;
  subtitle: string;
  strength: string;
  form: string;
  image: string;
}

interface MedicineCardProps {
  medicine: Medicine;
  isSelected: boolean;
  onToggle: () => void;
}

export function MedicineCard({ medicine, isSelected, onToggle }: MedicineCardProps) {
  return (
    <button
      onClick={onToggle}
      className={`group relative flex flex-col overflow-hidden rounded-lg bg-white text-left shadow-sm transition-all duration-200 ${
        isSelected
          ? 'border-2 border-teal-500 shadow-lg shadow-teal-100/50'
          : 'border border-gray-200 hover:border-gray-300 hover:shadow-md'
      }`}
    >
      {/* Image Section */}
      <div className="relative h-56 w-full flex-shrink-0 overflow-hidden bg-gray-100">
        <img
          src={medicine.image}
          alt={medicine.name}
          className="size-full object-cover"
        />
        {isSelected && (
          <div className="absolute right-3 top-3 flex size-8 items-center justify-center rounded-full bg-teal-500 shadow-md">
            <svg
              width="16"
              height="16"
              viewBox="0 0 16 16"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M13.333 4L6 11.333L2.667 8"
                stroke="white"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
          </div>
        )}
      </div>

      {/* Text Content */}
      <div className="flex flex-1 flex-col p-5">
        <h3 className="mb-1 text-xl font-bold text-gray-900">
          {medicine.name}
        </h3>
        <p className="mb-4 text-xs font-medium uppercase tracking-wider text-gray-400">
          {medicine.subtitle}
        </p>
        
        <div className="space-y-1">
          <div className="flex items-center gap-2">
            <span className="text-sm text-gray-600">Strength:</span>
            <span className="text-sm font-semibold text-gray-900">{medicine.strength}</span>
          </div>
          <div className="flex items-center gap-2">
            <span className="text-sm text-gray-600">Form:</span>
            <span className="text-sm font-semibold text-gray-900">{medicine.form}</span>
          </div>
        </div>
      </div>

      {/* Selection Indicator */}
      <div className={`h-12 min-h-12 flex-shrink-0 overflow-hidden ${isSelected ? 'opacity-100' : 'opacity-0'}`}>
        <div className="flex h-full items-center justify-center bg-gradient-to-r from-teal-500 to-teal-600 px-6">
          <p className="text-sm font-bold uppercase tracking-wide text-white">
            ✓ Selected
          </p>
        </div>
      </div>
    </button>
  );
}