// tienda.js

// Función para AGREGAR desde Catálogo o Producto (Sin recargar)
function agregarCarrito(event, form) {
    event.preventDefault();

    const formData = new FormData(form);
    const data = {
        accion: 'agregar',
        id: formData.get('id'),
        cantidad: formData.get('cantidad'),
        variante: formData.get('variante') // Enviamos la variante si existe
    };

    // Simulación visual inmediata (Optimistic UI)
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    btn.disabled = true;

    fetch('acciones_carrito.php', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(response => {
        // Restaurar botón
        btn.innerHTML = originalText;
        btn.disabled = false;

        if (response.ok) {
            // 1. Actualizar burbuja del carrito
            actualizarBurbuja(response.total_items);
            
            // 2. Mostrar animación "Pop" en el icono
            animarIconoCarrito();
            
            // 3. Mostrar mensaje Toast bonito
            mostrarToast("¡Añadido a tu bolsa! 🎀");
        }
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = originalText;
        btn.disabled = false;
        mostrarToast("Error al agregar 😢");
    });
}

// Función para SUMAR/RESTAR desde el Carrito (Sin recargar)
function cambiarCantidad(id, accion) {
    const data = { accion: accion, id: id };

    fetch('acciones_carrito.php', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(response => {
        if (response.ok) {
            // Si la cantidad llegó a 0, borramos la fila visualmente con animación
            if (response.row_qty <= 0) {
                const fila = document.getElementById(`producto-fila-${id}`);
                if(fila) {
                    fila.style.transform = 'translateX(100px)';
                    fila.style.opacity = '0';
                    setTimeout(() => {
                        fila.remove();
                        if(response.total_items === 0) location.reload();
                    }, 300);
                }
            } else {
                // Actualizar textos de esa fila específica
                const qtyEl = document.getElementById(`qty-${id}`);
                if(qtyEl) qtyEl.innerText = response.row_qty;
                
                const subtotalEl = document.getElementById(`subtotal-${id}`);
                if(subtotalEl) subtotalEl.innerText = '$' + response.row_subtotal;
            }
            
            // Actualizar totales globales
            const granTotalEl = document.getElementById('gran-total');
            if(granTotalEl) granTotalEl.innerText = '$' + response.gran_total;
            
            const resumenSubtotalEl = document.getElementById('resumen-subtotal');
            if(resumenSubtotalEl) resumenSubtotalEl.innerText = '$' + response.gran_total;
            
            actualizarBurbuja(response.total_items);
        }
    });
}

// Utilidades visuales
function actualizarBurbuja(cantidad) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(b => {
        if (cantidad > 0) {
            b.innerText = cantidad;
            b.classList.remove('hidden');
        } else {
            b.classList.add('hidden');
        }
    });
}

function animarIconoCarrito() {
    const btn = document.getElementById('btn-carrito');
    if(btn) {
        btn.classList.add('scale-125', 'text-brand-accent', 'bg-pink-100');
        setTimeout(() => {
            btn.classList.remove('scale-125', 'text-brand-accent', 'bg-pink-100');
        }, 300);
    }
}

function mostrarToast(mensaje) {
    // Eliminar toast anterior si existe para evitar acumulación fea
    const oldToast = document.getElementById('toast-notification');
    if(oldToast) oldToast.remove();

    // Crear el elemento toast
    const toast = document.createElement('div');
    toast.id = 'toast-notification';
    toast.className = 'fixed bottom-5 right-5 bg-brand-dark text-white px-6 py-4 rounded-2xl shadow-2xl transform translate-y-20 transition-all duration-500 z-50 flex items-center gap-3 font-medium border border-pink-400/30 backdrop-blur-md';
    
    toast.innerHTML = `
        <div class="bg-white/20 p-1 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <span>${mensaje}</span>
    `;
    
    document.body.appendChild(toast);
    
    // Animar entrada
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-20');
    });

    // Ocultar después de 3 segundos
    setTimeout(() => {
        if(toast) {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => toast.remove(), 500);
        }
    }, 3000);
}