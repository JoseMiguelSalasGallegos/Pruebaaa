// Estado global de la aplicación
let calendar;
let currentEventId = null;
let bookedTimes = {};

// Inicialización del calendario cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    loadEventsFromLocalStorage();
    setupEventListeners();
    updateProximasCitas();
    setupModalCloseButton();
});

// Función de inicialización del calendario
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'es',
        selectable: true,
        editable: true,
        eventClick: handleEventClick,
        select: handleDateSelect,
        eventAdd: handleEventChange,
        eventChange: handleEventChange,
        eventRemove: handleEventChange,
    });
    calendar.render();
}

// Manejadores de eventos del calendario
function handleEventClick(info) {
    openEditModal(info.event);
}

function handleDateSelect(info) {
    openNewModal(info);
}

function handleEventChange() {
    updateProximasCitas();
    saveEventsToLocalStorage();
}

// Configuración de event listeners
function setupEventListeners() {
    document.getElementById('appointmentDate').addEventListener('change', updateAvailableHours);
    document.getElementById('appointmentForm').addEventListener('submit', handleFormSubmit);
    window.addEventListener('click', handleWindowClick);
    window.addEventListener('beforeunload', cleanup);
    addNewAppointmentButtonListener();
}

// Funciones de persistencia
function saveEventsToLocalStorage() {
    try {
        const events = calendar.getEvents().map(event => ({
            id: event.id,
            title: event.title,
            start: event.start.toISOString(),
            type: event.extendedProps.type,
            notes: event.extendedProps.notes
        }));
        localStorage.setItem('appointments', JSON.stringify(events));
    } catch (error) {
        console.error('Error al guardar eventos:', error);
        showError('Error al guardar los eventos');
    }
}

function loadEventsFromLocalStorage() {
    try {
        const storedEvents = localStorage.getItem('appointments');
        if (storedEvents) {
            const events = JSON.parse(storedEvents);
            events.forEach(eventData => {
                calendar.addEvent(eventData);
                updateBookedTimes(eventData);
            });
        }
    } catch (error) {
        console.error('Error al cargar eventos:', error);
        showError('Error al cargar los eventos guardados');
    }
}

// Función para validar el rango de fechas permitido
function validateDateTime(date, time) {
    const selectedDateTime = new Date(`${date}T${time}`);
    const now = new Date();
    const oneYearFromNow = new Date();
    oneYearFromNow.setFullYear(now.getFullYear() + 1);
    
    // Validar que la fecha no sea en el pasado
    if (selectedDateTime < now) {
        throw new Error('No se pueden agendar citas en fechas pasadas');
    }

    // Validar que la fecha no sea más de un año en el futuro
    if (selectedDateTime > oneYearFromNow) {
        throw new Error('No se pueden agendar citas con más de un año de anticipación');
    }

    // Validar que sea dentro del horario de atención (8:00 AM a 1:00 PM)
    const hour = selectedDateTime.getHours();
    const minutes = selectedDateTime.getMinutes();
    const timeInMinutes = hour * 60 + minutes;
    
    if (timeInMinutes < 8 * 60 || timeInMinutes > 13 * 60) {
        throw new Error('Las citas solo pueden ser agendadas entre 8:00 AM y 1:00 PM');
    }

    // Validar que sea un día de la semana (no fines de semana)
    const dayOfWeek = selectedDateTime.getDay();
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        throw new Error('No se pueden agendar citas en fines de semana');
    }

    return true;
}

function isTimeSlotAvailable(date, time, eventId = null) {
    if (!bookedTimes[date]) return true;
    
    if (eventId && bookedTimes[date].includes(time)) {
        const event = calendar.getEventById(eventId);
        if (event && 
            event.start.toISOString().split('T')[0] === date && 
            event.start.toTimeString().slice(0, 5) === time) {
            return true;
        }
    }
    
    return !bookedTimes[date].includes(time);
}

// Funciones de actualización de UI
function updateAvailableHours() {
    const selectedDate = document.getElementById('appointmentDate').value;
    const timeSelect = document.getElementById('time');
    const now = new Date();
    const selectedDateTime = new Date(selectedDate);
    
    Array.from(timeSelect.options).forEach(option => {
        if (option.value === '') return;
        
        const optionDateTime = new Date(`${selectedDate}T${option.value}`);
        const isBooked = bookedTimes[selectedDate]?.includes(option.value);
        const isPast = optionDateTime < now;
        
        option.disabled = isBooked || isPast;
    });
}

function updateProximasCitas() {
    const proximasCitasList = document.getElementById('proximas-citas-list');
    if (!proximasCitasList) return;

    proximasCitasList.innerHTML = '';
    const now = new Date();

    const events = calendar.getEvents()
        .filter(event => new Date(event.start) >= now)
        .sort((a, b) => new Date(a.start) - new Date(b.start))
        .slice(0, 5);

    if (events.length === 0) {
        proximasCitasList.innerHTML = '<div class="cita-item"><div class="nombre">No hay citas próximas</div></div>';
        return;
    }

    events.forEach(event => {
        const citaEl = document.createElement('div');
        citaEl.className = 'cita-item';
        citaEl.innerHTML = `
            <div class="nombre">${event.title}</div>
            <div class="fecha">${formatDateToLocal(event.start)}</div>
            ${event.extendedProps.notes ? `<div class="notes">${event.extendedProps.notes}</div>` : ''}
        `;
        proximasCitasList.appendChild(citaEl);
    });
}

// Funciones de manejo de modal
function openNewModal(info) {
    currentEventId = null;
    document.getElementById('modalTitle').textContent = 'Nueva Cita';
    document.getElementById('appointmentForm').reset();
    
    const dateStr = info.startStr.split('T')[0];
    document.getElementById('appointmentDate').value = dateStr;
    
    updateAvailableHours();
    document.getElementById('deleteButton').style.display = 'none';
    openModal();
}

function openEditModal(event) {
    currentEventId = event.id;
    document.getElementById('modalTitle').textContent = 'Editar Cita';
    document.getElementById('patientName').value = event.title;

    const dateTime = event.start;
    const dateStr = dateTime.toISOString().split('T')[0];
    const timeStr = dateTime.toTimeString().slice(0, 5);

    document.getElementById('appointmentDate').value = dateStr;
    document.getElementById('time').value = timeStr;
    document.getElementById('notes').value = event.extendedProps.notes || '';

    updateAvailableHours();
    document.getElementById('deleteButton').style.display = 'block';
    openModal();
}

function openModal() {
    document.getElementById('appointmentModal').style.display = 'flex';
}

function openNewModalFromButton() {
    try {
        currentEventId = null;
        document.getElementById('modalTitle').textContent = 'Nueva Cita';
        document.getElementById('appointmentForm').reset();
        
        // Obtener el siguiente horario disponible
        const nextAvailable = getNextAvailableTime();
        const availableSlot = findNextAvailableSlot(nextAvailable.date);
        
        if (!availableSlot) {
            throw new Error('No hay horarios disponibles en los próximos 14 días');
        }
        
        // Establecer los valores en el formulario
        document.getElementById('appointmentDate').value = availableSlot.date;
        document.getElementById('time').value = availableSlot.time;
        
        // Actualizar las horas disponibles en el selector
        updateAvailableHours();
        
        // Ocultar el botón de eliminar ya que es una nueva cita
        document.getElementById('deleteButton').style.display = 'none';
        
        // Abrir el modal
        openModal();
        
    } catch (error) {
        showError(error.message);
    }
}

function closeModal() {
    // Verificamos si el formulario tiene cambios sin guardar
    const hasUnsavedChanges = 
        document.getElementById('patientName').value.trim() !== '' || 
        document.getElementById('notes').value.trim() !== '';
    const isDeleting = currentEventId && !calendar.getEventById(currentEventId)
    if (isDeleting || !hasUnsavedChanges) {
        resetModal();
        return;
    }

    if (hasUnsavedChanges) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Los cambios no guardados se perderán",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                resetModal();
            }
        });
    } else {
        resetModal();
    }
}

// Función para obtener el siguiente horario disponible
function getNextAvailableTime() {
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinutes = now.getMinutes();
    
    // Lista de horarios disponibles (de tu select en el HTML)
    const availableHours = [
        "08:00", "08:30", "09:00", "09:30", "10:00",
        "10:30", "11:00", "11:30", "12:00", "12:30"
    ];
    
    // Si es después del último horario disponible, retornar el primer horario del siguiente día
    if (currentHour >= 13) {
        return {
            date: new Date(now.setDate(now.getDate() + 1)).toISOString().split('T')[0],
            time: availableHours[0]
        };
    }
    
    // Encontrar el próximo horario disponible
    const currentTime = `${currentHour.toString().padStart(2, '0')}:${currentMinutes.toString().padStart(2, '0')}`;
    const nextTime = availableHours.find(time => time > currentTime);
    
    return {
        date: now.toISOString().split('T')[0],
        time: nextTime || availableHours[0]
    };
}

// Función para verificar si un horario está disponible
function findNextAvailableSlot(startDate) {
    const date = new Date(startDate);
    let attempts = 0;
    const maxAttempts = 14; // Buscar hasta 14 días adelante
    
    while (attempts < maxAttempts) {
        const dateStr = date.toISOString().split('T')[0];
        const bookedTimesForDate = bookedTimes[dateStr] || [];
        
        // Verificar cada horario disponible
        const availableHours = [
            "08:00", "08:30", "09:00", "09:30", "10:00",
            "10:30", "11:00", "11:30", "12:00", "12:30"
        ];
        
        for (const time of availableHours) {
            if (!bookedTimesForDate.includes(time)) {
                return {
                    date: dateStr,
                    time: time
                };
            }
        }
        
        // Si no hay horarios disponibles, probar el siguiente día
        date.setDate(date.getDate() + 1);
        attempts++;
    }
    
    return null;
}

function addNewAppointmentButtonListener() {
    const newAppointmentButton = document.querySelector('.nueva-cita-btn');
    if (newAppointmentButton) {
        newAppointmentButton.addEventListener('click', openNewModalFromButton);
    }
}

function setupModalCloseButton() {
    const closeButton = document.querySelector('.modal-close');
    if (closeButton) {
        closeButton.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    }
}

function resetModal() {
    const modal = document.getElementById('appointmentModal');
    const form = document.getElementById('appointmentForm');
    
    modal.style.display = 'none';
    form.reset();
    currentEventId = null;
}

// Manejo del formulario
async function handleFormSubmit(e) {
    e.preventDefault();
    
    try {
        const date = document.getElementById('appointmentDate').value;
        const time = document.getElementById('time').value;
        const patientName = document.getElementById('patientName').value.trim();
        
        if (!patientName) {
            throw new Error('El nombre del paciente es requerido');
        }
        
        validateDateTime(date, time);
        
        if (!isTimeSlotAvailable(date, time, currentEventId)) {
            throw new Error('Horario no disponible');
        }
        
        const eventData = {
            id: currentEventId || Date.now().toString(),
            title: patientName,
            start: formatEventDateTime(date, time),
            notes: document.getElementById('notes').value
        };

        if (currentEventId) {
            updateExistingEvent(currentEventId, date, time);
        }

        updateBookedTimes(eventData);
        calendar.addEvent(eventData);
        saveEventsToLocalStorage();
        
        showSuccess('Cita guardada exitosamente');
        resetModal(); // Cambiamos closeModal() por resetModal()
        
    } catch (error) {
        showError(error.message);
    }
}

// Funciones auxiliares
function updateExistingEvent(eventId, newDate, newTime) {
    const existingEvent = calendar.getEventById(eventId);
    if (existingEvent) {
        const oldDate = existingEvent.start.toISOString().split('T')[0];
        const oldTime = existingEvent.start.toTimeString().slice(0, 5);

        removeBookedTime(oldDate, oldTime);
        existingEvent.remove();
    }
}

function updateBookedTimes(eventData) {
    const date = new Date(eventData.start).toISOString().split('T')[0];
    const time = new Date(eventData.start).toTimeString().slice(0, 5);
    
    if (!bookedTimes[date]) {
        bookedTimes[date] = [];
    }
    if (!bookedTimes[date].includes(time)) {
        bookedTimes[date].push(time);
    }
}

function removeBookedTime(date, time) {
    if (bookedTimes[date]) {
        bookedTimes[date] = bookedTimes[date].filter(t => t !== time);
        if (bookedTimes[date].length === 0) {
            delete bookedTimes[date];
        }
    }
}

// Funciones de formato
function formatEventDateTime(date, time) {
    return `${date}T${time}:00`;
}

function formatDateToLocal(date) {
    return new Date(date).toLocaleString('es-ES', {
        month: '2-digit',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Notificaciones
function showNotification(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}

function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: message,
        showConfirmButton: false,
        timer: 2000
    });
}

function showError(message) {
    const date = document.getElementById('appointmentDate').value;
    const time = document.getElementById('time').value;
    
    let formattedMessage = message;
    if (message.includes('fechas pasadas') || message.includes('año de anticipación')) {
        formattedMessage = `${message}\nFecha seleccionada: ${formatDate(date)} ${formatTime(time)}`;
    }
    
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: formattedMessage
    });
}

// Función para eliminar citas
function deleteAppointment() {
    if (currentEventId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const event = calendar.getEventById(currentEventId);
                if (event) {
                    const date = event.start.toISOString().split('T')[0];
                    const time = event.start.toTimeString().slice(0, 5);

                    removeBookedTime(date, time);
                    event.remove();
                    saveEventsToLocalStorage();
                    updateProximasCitas();
                    showNotification('Cita eliminada exitosamente');
                    resetModal();
                }
            }
        });
    }
}

// Manejadores de eventos de ventana
function handleWindowClick(event) {
    const modal = document.getElementById('appointmentModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Limpieza de recursos
function cleanup() {
    window.removeEventListener('click', handleWindowClick);
}

document.addEventListener('DOMContentLoaded', function() {
    const mobileCalendarToggle = document.getElementById('mobileCalendarToggle');
    const mainContent = document.querySelector('.main-content');

    if (mobileCalendarToggle && mainContent) {
        mobileCalendarToggle.addEventListener('click', function() {
            mainContent.classList.toggle('mobile-calendar-open');
        });

        // Añadir un botón de cierre dentro del calendario móvil
        const closeCalendarBtn = document.createElement('button');
        closeCalendarBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeCalendarBtn.classList.add('close-mobile-calendar');
        closeCalendarBtn.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            z-index: 1002;
        `;
        
        closeCalendarBtn.addEventListener('click', function() {
            mainContent.classList.remove('mobile-calendar-open');
        });

        mainContent.insertBefore(closeCalendarBtn, mainContent.firstChild);
    }
});

function retroceder() {
    window.location.href = 'inicioMedico.html';
}