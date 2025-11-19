# Aplicativo de Clima

Una aplicación web sencilla que muestra información del clima en tiempo real utilizando la API de OpenWeatherMap.

## Configuración Requerida

Para que la aplicación funcione correctamente, es necesario crear un archivo `.env` en el directorio raíz del proyecto con la siguiente información:

```
# Configuracion de variables de entorno
# API Key de OpenWeatherMap - obtenida desde https://openweathermap.org/api
API_CLIMA=tu_api_key_de_openweather

# Configuracion de la aplicacion
CIUDAD_DEFECTO=nombre_de_la_ciudad
PAIS_DEFECTO=codigo_pais
IDIOMA=es
```

### Obtener API Key

1. Regístrate en [OpenWeatherMap](https://openweathermap.org/api)
2. Obtén tu API key gratuita
3. Coloca la key en la variable `API_CLIMA` del archivo `.env`

### Configurar Ciudad y País

Especifica el nombre de la ciudad (`CIUDAD_DEFECTO`), el código del país (`PAIS_DEFECTO`) y el idioma (`IDIOMA`) para personalizar la información climática mostrada en el archivo `.env`.

## Uso

1. Clona el repositorio
2. Crea el archivo `.env` con tu API key (`API_CLIMA`), ciudad (`CIUDAD_DEFECTO`), país (`PAIS_DEFECTO`) e idioma (`IDIOMA`)
3. Abre `index.html` en tu navegador

## Archivos del Proyecto

- `index.html`: Estructura principal de la aplicación
- `styles.css`: Estilos de la interfaz
- `script.js`: Lógica JavaScript para obtener datos del clima
- `clima.php`: Backend para procesar solicitudes de la API