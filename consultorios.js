function agregarConsultorio() {
    const form = document.getElementById('formNuevoConsultorio');
    const data = new FormData(form);

    console.log('Datos enviados:', Array.from(data.entries())); // Muestra los datos en la consola

    fetch('guardarConsultorio.php', {
        method: 'POST',
        body: data,
    })
        .then(response => response.text())
        .then(data => {
            console.log('Respuesta del servidor:', data); // da  la respuesta del servidor
            alert(data);
            actualizarConsultorios();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al guardar el consultorio.');
        });
}


function actualizarConsultorios() {
    fetch('obtener_consultorios.php') // debuelve la consulta JSON O HTML 
        .then(response => response.text()) // va a acambiar a json si deveulve en
        .then(data => {
            const container = document.getElementById('consultoriosContainer');
            container.innerHTML = data; // Inserta el contenido devuelto por PHP
        })
        .catch(error => console.error('Error al actualizar consultorios:', error));
}
