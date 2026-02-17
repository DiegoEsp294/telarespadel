# 🏆 Vista Detalle de Torneo - Instrucciones

## ✅ Lo que se ha creado

### **1. Nueva Vista: `detalle_torneo.php`**
Una página completa con:
- ✓ Información completa del torneo
- ✓ Estadísticas de inscriptos
- ✓ Tabla con listado de inscriptos confirmados
- ✓ Inscriptos agrupados por categoría
- ✓ Indicador de cupos disponibles
- ✓ Formulario para solicitar inscripción
- ✓ Panel de solicitudes pendientes
- ✓ Diseño responsivo y profesional

### **2. Métodos en Controlador `Home.php`**
- `torneo($id)` - Muestra el detalle de un torneo específico
- `solicitar_inscripcion()` - Procesa la solicitud de inscripción

### **3. Métodos en Modelo `Torneo_model.php`**
- `obtener_inscriptos($torneo_id)` - Lista inscriptos confirmados
- `obtener_inscriptos_por_categoria($torneo_id)` - Agrupa por categoría
- `contar_inscriptos($torneo_id)` - Cuenta total de inscriptos
- `crear_solicitud_inscripcion($data)` - Guarda solicitud
- `obtener_solicitudes($torneo_id)` - Lista solicitudes pendientes

### **4. Nuevas Tablas en BD**
- `inscripciones` - Con campo `categoria`
- `solicitudes_inscripcion` - Registra solicitudes de nuevos usuarios

---

## 🚀 Pasos para implementar

### **Paso 1: Actualizar la Base de Datos**

#### **Opción A: phpMyAdmin (Recomendado)**

1. Abre `http://localhost/phpmyadmin/`
2. Selecciona la BD `torneos_telares`
3. Ve a **"SQL"** en la pestaña superior
4. Ejecuta estos comandos:

```sql
-- Actualizar tabla inscripciones
ALTER TABLE inscripciones ADD COLUMN categoria VARCHAR(100) AFTER estado;

-- Crear tabla solicitudes_inscripcion si no existe
CREATE TABLE IF NOT EXISTS solicitudes_inscripcion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  torneo_id INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100),
  email VARCHAR(255) NOT NULL,
  telefono VARCHAR(20),
  categoria VARCHAR(100),
  compañero VARCHAR(200),
  estado ENUM('pendiente', 'confirmada', 'rechazada') DEFAULT 'pendiente',
  fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (torneo_id) REFERENCES torneos(id) ON DELETE CASCADE,
  INDEX idx_torneo (torneo_id),
  INDEX idx_estado (estado),
  INDEX idx_email (email)
);

-- Modificar tabla inscripciones para permitir NULL
ALTER TABLE inscripciones MODIFY participante1_id INT NULL;
ALTER TABLE inscripciones MODIFY participante2_id INT NULL;
```

#### **Opción B: Línea de comandos MySQL**

```bash
mysql -u root -p torneos_telares < database/setup.sql
```

### **Paso 2: Recargar la aplicación**

1. Accede a: `http://localhost/torneos-telares-padel/`
2. Haz clic en **"Más Información"** de cualquier torneo
3. ¡Debería cargar la nueva vista!

---

## 📋 Funcionalidades de la Vista

### **Sección Superior: Información del Torneo**
- Nombre y estado
- Fechas (con validación de nulas)
- Categoría del torneo

### **Estadísticas**
- Total de inscriptos
- Cupo total disponible
- Desglose por categoría

### **Tabla de Inscriptos**
Muestra:
- Nombres de la pareja
- Categoría (badge)
- Estado (Confirmada, Pendiente, Cancelada)

### **Formulario de Solicitud**
Campos:
- **Nombre** (requerido)
- **Apellido** (opcional)
- **Email** (requerido)
- **Teléfono** (opcional)
- **Categoría** (selector)
- **Compañero/a** (textarea con nombre y teléfono)

Al enviar la solicitud:
- Se guarda en la tabla `solicitudes_inscripcion`
- El usuario recibe confirmación
- El administrador puede ver las solicitudes en el mismo panel

---

## 💡 Elementos Adicionales Implementados

✓ **Manejo de errores:**
  - Validación de campos requeridos
  - Redirección si el torneo no existe
  - Mensajes de éxito/error

✓ **Responsive Design:**
  - Se adapta a móviles
  - Grilla flexible
  - Formulario pegajoso en desktop

✓ **Indicadores visuales:**
  - Estado de inscripción con colores
  - Categorías con badges
  - Cupo disponible destacado

✓ **Mejoras futuras posibles:**
  - Panel de administrador para confirmar solicitudes
  - Envío de emails automáticos
  - Descarga de PDF con listado
  - Generación de hojas de ruta

---

## 🔗 URLs Importantes

| Acción | URL |
|--------|-----|
| Ver detalle torneo | `http://localhost/torneos-telares-padel/home/torneo/1` |
| Enviar solicitud | POST a `home/solicitar_inscripcion` |
| Inicio | `http://localhost/torneos-telares-padel/` |

---

## 🎨 Personalizaciones

Si deseas cambiar colores, estilos o el layout, edita el CSS al inicio de [application/views/detalle_torneo.php](application/views/detalle_torneo.php).

**Colores actuales:**
- Primary: `#003366` (Azul)
- Accent: `#FF6600` (Naranja)
- Success: `#28a745` (Verde)

---

## ❓ Preguntas Frecuentes

### ¿Cómo confirmar una solicitud de inscripción?

Necesitaremos crear un **panel de administrador** para esto. Por ahora, las solicitudes se guardan y puedes verlas usando phpMyAdmin:

```sql
SELECT * FROM solicitudes_inscripcion WHERE estado = 'pendiente';
```

### ¿Los usuarios reciben emails?

De momento no, pero es fácil de implementar usando la librería Email de CodeIgniter.

### ¿Cómo editar los datos de los inscriptos?

Accede a phpMyAdmin y edita directamente la tabla `inscripciones`.

---

**¡Listo!** La vista está completa y funcional. ¿Necesitas agregar algo más?
