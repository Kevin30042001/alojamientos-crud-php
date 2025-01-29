document.addEventListener('DOMContentLoaded', function() {
    // Validar que el precio máximo no sea menor que el mínimo
    const minPriceInput = document.getElementById('min_price');
    const maxPriceInput = document.getElementById('max_price');

    function validatePrices() {
        if (minPriceInput.value && maxPriceInput.value) {
            const minPrice = parseFloat(minPriceInput.value);
            const maxPrice = parseFloat(maxPriceInput.value);
            
            if (maxPrice < minPrice) {
                maxPriceInput.value = minPrice;
            }
        }
    }

    minPriceInput.addEventListener('change', validatePrices);
    maxPriceInput.addEventListener('change', validatePrices);

    // Limpiar filtros
    const clearFilters = document.getElementById('clearFilters');
    if (clearFilters) {
        clearFilters.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'index.php';
        });
    }

    // Actualizar filtros al escribir (con debounce)
    let timeout = null;
    const searchInput = document.getElementById('search');
    
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            document.querySelector('form').submit();
        }, 500);
    });
});