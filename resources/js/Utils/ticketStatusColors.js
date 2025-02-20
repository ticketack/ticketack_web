export const getStatusColor = (status) => {
    switch (status.toLowerCase()) {
        case 'nouveau':
        case 'new':
        case 'neu':
            return 'bg-gradient-to-r from-blue-500 to-blue-600 text-white';
        case 'en cours':
        case 'in progress':
        case 'in bearbeitung':
            return 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white';
        case 'résolu':
        case 'resolved':
        case 'gelöst':
            return 'bg-gradient-to-r from-green-500 to-green-600 text-white';
        case 'fermé':
        case 'closed':
        case 'geschlossen':
            return 'bg-gradient-to-r from-gray-500 to-gray-600 text-white';
        default:
            return 'bg-gradient-to-r from-gray-500 to-gray-600 text-white';
    }
};
