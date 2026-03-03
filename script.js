// Medicine data
const medicines = [
    {
        id: '1',
        name: 'crocin',
        subtitle: 'crocin',
        strength: '100 Mg',
        form: 'tablet',
        image: 'https://images.unsplash.com/photo-1646392206581-2527b1cae5cb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtZWRpY2luZSUyMHBpbGxzJTIwdGFibGV0fGVufDF8fHx8MTc3MjQzMzQzNnww&ixlib=rb-4.1.0&q=80&w=1080',
    },
    {
        id: '2',
        name: 'ibuprofen',
        subtitle: 'ibuprofen',
        strength: '200 Mg',
        form: 'tablet',
        image: 'https://images.unsplash.com/photo-1646392206581-2527b1cae5cb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtZWRpY2luZSUyMHBpbGxzJTIwdGFibGV0fGVufDF8fHx8MTc3MjQzMzQzNnww&ixlib=rb-4.1.0&q=80&w=1080',
    },
    {
        id: '3',
        name: 'paracetamol',
        subtitle: 'paracetamol',
        strength: '500 Mg',
        form: 'capsule',
        image: 'https://images.unsplash.com/photo-1646392206581-2527b1cae5cb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtZWRpY2luZSUyMHBpbGxzJTIwdGFibGV0fGVufDF8fHx8MTc3MjQzMzQzNnww&ixlib=rb-4.1.0&q=80&w=1080',
    },
];

// Selected medicines state
let selectedMedicines = ['1'];

// Create medicine card HTML
function createMedicineCard(medicine) {
    const isSelected = selectedMedicines.includes(medicine.id);
    
    return `
        <button class="medicine-card ${isSelected ? 'selected' : ''}" data-id="${medicine.id}">
            <!-- Image Section -->
            <div class="medicine-image-container">
                <img src="${medicine.image}" alt="${medicine.name}" class="medicine-image">
                ${isSelected ? `
                    <div class="checkmark-badge">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.333 4L6 11.333L2.667 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                ` : ''}
            </div>

            <!-- Text Content -->
            <div class="medicine-content">
                <h3 class="medicine-name">${medicine.name}</h3>
                <p class="medicine-subtitle">${medicine.subtitle}</p>
                
                <div class="medicine-details">
                    <div class="medicine-detail-row">
                        <span class="medicine-detail-label">Strength:</span>
                        <span class="medicine-detail-value">${medicine.strength}</span>
                    </div>
                    <div class="medicine-detail-row">
                        <span class="medicine-detail-label">Form:</span>
                        <span class="medicine-detail-value">${medicine.form}</span>
                    </div>
                </div>
            </div>

            <!-- Selection Indicator -->
            <div class="selection-indicator ${!isSelected ? 'hidden' : ''}">
                <div class="selection-indicator-content">
                    <p class="selection-indicator-text">✓ SELECTED</p>
                </div>
            </div>
        </button>
    `;
}

// Render all medicine cards
function renderMedicineCards() {
    const grid = document.getElementById('medicineGrid');
    grid.innerHTML = medicines.map(medicine => createMedicineCard(medicine)).join('');
    
    // Add click event listeners
    const cards = grid.querySelectorAll('.medicine-card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const id = card.getAttribute('data-id');
            toggleMedicine(id);
        });
    });
    
    // Update selected count
    updateSelectedCount();
}

// Toggle medicine selection
function toggleMedicine(id) {
    if (selectedMedicines.includes(id)) {
        // Deselect
        selectedMedicines = selectedMedicines.filter(medId => medId !== id);
    } else {
        // Select only if less than 3 are selected
        if (selectedMedicines.length < 3) {
            selectedMedicines.push(id);
        }
    }
    
    // Re-render cards
    renderMedicineCards();
}

// Update selected count display
function updateSelectedCount() {
    const countElement = document.getElementById('selectedCount');
    countElement.textContent = `Selected: ${selectedMedicines.length} / 3`;
}

// Initialize the application
document.addEventListener('DOMContentLoaded', () => {
    renderMedicineCards();
});