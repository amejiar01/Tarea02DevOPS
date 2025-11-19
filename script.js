// Variables globales
let datosClima = null;

// Elementos del DOM
const elementoCargando = document.getElementById('cargando');
const elementoInformacion = document.getElementById('informacion-clima');
const elementoError = document.getElementById('error');
const elementoTemperatura = document.getElementById('temperatura');
const elementoCiudad = document.getElementById('ciudad');
const elementoFecha = document.getElementById('fecha');

/**
 * Funcion principal para obtener datos del clima
 */
async function obtenerClima() {
    mostrarCargando();

    try {
        const respuesta = await fetch('clima.php');

        if (!respuesta.ok) {
            throw new Error('Error en la respuesta del servidor');
        }

        const datos = await respuesta.json();

        if (datos.error) {
            throw new Error(datos.mensaje || 'Error desconocido');
        }

        datosClima = datos;
        mostrarInformacion();

    } catch (error) {
        console.error('Error al obtener clima:', error);
        mostrarError();
    }
}

/**
 * Muestra el estado de carga
 */
function mostrarCargando() {
    elementoCargando.style.display = 'block';
    elementoInformacion.style.display = 'none';
    elementoError.style.display = 'none';
}

/**
 * Muestra la informacion del clima
 */
function mostrarInformacion() {
    if (!datosClima) return;

    // Actualizar temperatura
    elementoTemperatura.textContent = Math.round(datosClima.temperatura);

    // Actualizar ciudad
    elementoCiudad.textContent = `${datosClima.ciudad}, ${datosClima.pais}`;

    // Actualizar fecha
    elementoFecha.textContent = obtenerFechaHoy();

    // Mostrar informacion
    elementoCargando.style.display = 'none';
    elementoInformacion.style.display = 'block';
    elementoError.style.display = 'none';
}

/**
 * Muestra el estado de error
 */
function mostrarError() {
    elementoCargando.style.display = 'none';
    elementoInformacion.style.display = 'none';
    elementoError.style.display = 'block';
}

/**
 * Obtiene la fecha actual formateada
 * @returns {string} Fecha formateada
 */
function obtenerFechaHoy() {
    const hoy = new Date();
    const opciones = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    return hoy.toLocaleDateString('es-ES', opciones);
}

/**
 * Inicializa la aplicacion cuando se carga la pagina
 */
document.addEventListener('DOMContentLoaded', function () {
    console.log('Aplicacion de clima iniciada');
    obtenerClima();
});

/**
 * Actualiza el clima cada 10 minutos automaticamente
 */
setInterval(obtenerClima, 600000); // 10 minutos = 600000 milisegundos