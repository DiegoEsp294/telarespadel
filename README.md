# Telares Padel - Sistema de Gestión de Torneos

Bienvenido a **Telares Padel**, el sitio web oficial del Club de Padel Telares.

## 📋 Descripción

Este es un sitio web moderno y responsivo desarrollado con **CodeIgniter 3**, **PHP 7.4**, **HTML5** y **CSS3** para un club de padel local. 

### Características principales:
- ✅ Página principal con información del club
- ✅ Listado de torneos con estado y fechas
- ✅ Información de contacto
- ✅ Servicios ofrecidos
- ✅ Diseño responsivo (mobile-friendly)
- ✅ Colores corporativos: Azul (#003366), Naranja (#FF6600) y Blanco
- ✅ Interfaz moderna y animaciones suaves

## 🛠️ Requisitos Técnicos

- PHP 7.4 o superior
- CodeIgniter 3.x
- Servidor Apache con mod_rewrite habilitado
- Base de datos MySQL (opcional, para versiones futuras)

## 📁 Estructura del Proyecto

```
torneos-telares-padel/
├── application/
│   ├── controllers/
│   │   └── Home.php              # Controlador principal
│   ├── models/
│   │   └── Torneo_model.php      # Modelo de torneos
│   ├── views/
│   │   ├── header.php            # Encabezado y navegación
│   │   ├── inicio.php            # Página principal
│   │   └── footer.php            # Pie de página
│   └── config/
│       └── config.php            # Configuración de CodeIgniter
├── assets/
│   ├── css/
│   │   └── style.css             # Estilos CSS
│   ├── js/
│   │   └── script.js             # JavaScript
│   └── images/
│       └── logo.png              # Logo del club
├── .htaccess                      # Configuración de reescritura URL
└── index.php                      # Punto de entrada

```

## 🚀 Instalación

### 1. Descargar CodeIgniter 3
Descarga CodeIgniter 3 desde: https://github.com/bcit-ci/CodeIgniter/releases

### 2. Copiar archivos del sistema
Copia la carpeta `system` de CodeIgniter a:
```
c:\xampp\htdocs\torneos-telares-padel\system\
```

### 3. Configurar Apache
Asegúrate de que mod_rewrite esté habilitado en Apache.

### 4. Acceder a la aplicación
Abre tu navegador y ve a: `http://localhost/torneos-telares-padel/`

## ⚙️ Configuración

### Modificar información del club
Edita el archivo [application/controllers/Home.php](application/controllers/Home.php) y cambia:
- `$data['club_nombre']` - Nombre del club
- `$data['club_info']` - Ubicación, teléfono, email, redes sociales

### Agregar tu logo
Reemplaza el archivo [assets/images/logo.png](assets/images/logo.png) con tu logo del club.

### Personalizar colores
Los colores están definidos en [assets/css/style.css](assets/css/style.css) en la sección de variables:
```css
:root {
    --color-azul: #003366;
    --color-naranja: #FF6600;
    --color-blanco: #FFFFFF;
}
```

## 📱 Secciones de la Web

### 1. **Navbar Fijo**
Navegación principal con logo, nombre del club y menú de opciones.

### 2. **Hero Section**
Banner atractivo con invitación a explorar torneos y contactar.

### 3. **Sección Sobre el Club**
- Descripción del club
- Estadísticas (afiliados, canchas, torneos)
- Información de contacto
- Enlaces a redes sociales

### 4. **Listado de Torneos**
Tarjetas con información de:
- Nombre del torneo
- Fechas (inicio y fin)
- Estado (Próximo, En Curso, Finalizado)
- Categoría de jugadores
- Cantidad de participantes
- Descripción breve

### 5. **Servicios**
Muestra los 6 servicios principales del club con iconos.

### 6. **Formulario de Contacto**
Formulario interactivo para que visitantes se comuniquen con el club.

### 7. **Footer**
Enlaces rápidos, información de contacto y redes sociales.

## 🎨 Sistema de Colores

| Color | Hex | Uso |
|-------|-----|-----|
| Azul Oscuro | #003366 | Encabezados, fondo navbar, iconos |
| Naranja | #FF6600 | Botones, acentos, enlace activo |
| Blanco | #FFFFFF | Texto en oscuro, fondos |
| Gris Claro | #F5F5F5 | Fondos alternos |

## 📦 Dependencias Externas

- **Font Awesome 6.0.0** - Iconos (CDN)
- **Google Fonts** - Tipografía (via Segoe UI)

## 🔄 Próximas Mejoras

- [ ] Integración con base de datos para torneos dinámicos
- [ ] Panel administrativo para gestionar torneos
- [ ] Sistema de inscripción en torneos
- [ ] Visualización de brackets
- [ ] Ranking de jugadores
- [ ] Galería de fotos
- [ ] Blog o noticias
- [ ] App móvil nativa

## 📧 Contacto

Para consultas sobre el sitio web, contacta a: **info@telarespadel.com**

## 📄 Licencia

Este proyecto está disponible para uso interno del Club Telares Padel.

---

**Versión:** 1.0  
**Última actualización:** Febrero 2026
