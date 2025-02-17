export const getPriorityColor = (priority) => {
    console.log('Getting color for priority:', priority);
    const colors = {
        'low': {
            bg: 'bg-blue-100',
            text: 'text-blue-800',
            light: 'bg-blue-50'
        },
        'medium': {
            bg: 'bg-amber-100',
            text: 'text-amber-800',
            light: 'bg-amber-50'
        },
        'high': {
            bg: 'bg-orange-100',
            text: 'text-orange-800',
            light: 'bg-orange-50'
        },
        'critical': {
            bg: 'bg-red-100',
            text: 'text-red-800',
            light: 'bg-red-50'
        }
    };

    const color = colors[priority] || colors['medium'];
    console.log('Returning color:', color);
    return color;
};
