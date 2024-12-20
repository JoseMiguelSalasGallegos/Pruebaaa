document.addEventListener('DOMContentLoaded', () => {
    cargarPacientes();

    document.getElementById('searchPatient').addEventListener('input', () => {
        const query = document.getElementById('searchPatient').value;
        cargarPacientes(query);
    });
});

async function cargarPacientes(query = '') {
    const response = await fetch(`directorio_pacientes.php?search=${encodeURIComponent(query)}`);
    const pacientes = await response.json();
    const grid = document.getElementById('patientsGrid');
    grid.innerHTML = '';

    pacientes.forEach(paciente => {
        const card = document.createElement('div');
        card.classList.add('patient-card');
        card.innerHTML = `
            <h3>${paciente.Nombre}</h3>
            <p><strong>Teléfono:</strong> ${paciente.Telefono}</p>
            <p><strong>Email:</strong> ${paciente.Email}</p>
            <button onclick="mostrarDetalle(${paciente.id_Paciente})">Ver Detalles</button>
        `;
        grid.appendChild(card);
    });
}

async function mostrarDetalle(id_Paciente) {
    console.log("ID del paciente a enviar:", id_Paciente); // Verifica que se esté enviando el Id

    const response = await fetch('directorio_pacientes.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_Paciente })
    });

    if (!response.ok) {
        throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
    }

    const textResponse = await response.text();
    console.log("Respuesta del servidor:", textResponse); // revisa las respuestas

    const paciente = JSON.parse(textResponse);

    if (paciente.error) {
        throw new Error(paciente.error);
    }

    document.getElementById('patientInfo').classList.add('active');
    document.getElementById('patientName').textContent = paciente.Nombre;

    const contentArea = document.getElementById('contentArea');
    contentArea.innerHTML = `
        <h4>Información General</h4>
        <p><strong>Teléfono:</strong> ${paciente.Telefono}</p>
        <p><strong>Email:</strong> ${paciente.Email}</p>
        <p><strong>Dirección:</strong> ${paciente.Direccion}</p>
        <p><strong>Sexo:</strong> ${paciente.Sexo}</p>
        <p><strong>Edad:</strong> ${paciente.Edad}</p>
    `;
}