# Guía de Configuración de Base de Datos
## Torneos Telares Padel

---

## 📋 Requisitos Previos

- **XAMPP** instalado (con MySQL/MariaDB)
- **PHP 5.3+** con soporte para MySQLi
- **phpMyAdmin** (incluido en XAMPP) o acceso a MySQL por línea de comandos

---

## 🚀 Pasos de Instalación

### **Paso 1: Iniciar XAMPP**

1. Abre **XAMPP Control Panel**
2. Inicia los servicios:
   - **Apache** (servidor web)
   - **MySQL** (base de datos)

### **Paso 2: Acceder a phpMyAdmin**

**Opción A: A través del navegador**
```
http://localhost/phpmyadmin/
```

**Opción B: Por línea de comandos**
```bash
# Windows cmd o PowerShell
mysql -u root -p
```
(Presiona Enter cuando pida contraseña, si no has configurado una)

### **Paso 3: Crear la Base de Datos e Importar Datos**

#### **Opción A: Usando phpMyAdmin (Recomendado)**

1. Abre phpMyAdmin: `http://localhost/phpmyadmin/`
2. Haz clic en **"Nueva"** en la parte superior izquierda
3. En el campo **"Crear nueva base de datos"**, escribe: `torneos_telares`
4. Selecciona el collation: **utf8_general_ci**
5. Haz clic en **"Crear"**
6. Ahora importa el script SQL:
   - Ve a la pestaña **"Importar"**
   - Haz clic en **"Elegir archivo"**
   - Selecciona: `database/setup.sql`
   - Haz clic en **"Importar"**

#### **Opción B: Usando línea de comandos (MySQL)**

```bash
# Conectarse a MySQL
mysql -u root -p

# En el intérprete de MySQL, ejecuta:
CREATE DATABASE torneos_telares;
USE torneos_telares;
SOURCE C:/xampp/htdocs/torneos-telares-padel/database/setup.sql;

# Verifica que se creó correctamente:
SHOW TABLES;
```

### **Paso 4: Configurar la Conexión en CodeIgniter**

El archivo `application/config/database.php` ya está configurado con los valores por defecto:

```php
$db['default'] = array(
    'hostname' => 'localhost',      // Host del servidor MySQL
    'username' => 'root',           // Usuario MySQL (por defecto)
    'password' => '',               // Contraseña (vacía por defecto)
    'database' => 'torneos_telares', // Nombre de la BD
    'dbdriver' => 'mysqli',         // Driver de la BD
);
```

**Si tu configuración es diferente, edita estos parámetros en `application/config/database.php`**

---

## ✅ Verificar la Instalación

### **Test 1: Verificar que phpMyAdmin ve la BD**

1. Abre `http://localhost/phpmyadmin/`
2. En el panel izquierdo deberías ver **"torneos_telares"**
3. Al hacer clic, deberías ver las tablas:
   - `torneos`
   - `participantes` (opcional)
   - `inscripciones` (opcional)

### **Test 2: Ver los datos de prueba**

1. En phpMyAdmin, selecciona la BD `torneos_telares`
2. Haz clic en la tabla **"torneos"**
3. Deberías ver **6 torneos** de prueba

### **Test 3: Probar la aplicación web**

1. Abre tu navegador
2. Ve a: `http://localhost/torneos-telares-padel/`
3. En la sección **"Nuestros Torneos"** deberías ver los torneos cargados desde la BD

---

## 📊 Estructura de la Base de Datos

### **Tabla: `torneos`**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID único del torneo |
| `nombre` | VARCHAR(255) | Nombre del torneo |
| `descripcion` | TEXT | Descripción detallada |
| `fecha_inicio` | DATE | Fecha de inicio |
| `fecha_fin` | DATE | Fecha de fin |
| `categoria` | VARCHAR(50) | Categoría (Mixto, Masculino, etc) |
| `participantes` | VARCHAR(100) | Cantidad de participantes |
| `estado` | ENUM | Estado: proxima, en_curso, finalizado |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Fecha de última actualización |

### **Tabla: `participantes`** (Opcional para futuro)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID único |
| `nombre` | VARCHAR(255) | Nombre del jugador |
| `apellido` | VARCHAR(255) | Apellido |
| `email` | VARCHAR(255) | Email |
| `telefono` | VARCHAR(20) | Teléfono |
| `nivel_juego` | ENUM | Nivel: principiante, intermedio, avanzado, profesional |
| `created_at` | TIMESTAMP | Fecha de registro |

### **Tabla: `inscripciones`** (Opcional para futuro)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID único |
| `torneo_id` | INT | ID del torneo |
| `participante1_id` | INT | ID del participante 1 |
| `participante2_id` | INT | ID del participante 2 (pareja) |
| `estado` | ENUM | confirmada, pendiente, cancelada |
| `fecha_inscripcion` | TIMESTAMP | Fecha de inscripción |

---

## 🔧 Resolver Problemas Comunes

### **Error: "Unknown database 'torneos_telares'"**

**Solución:**
- Verifica que la BD fue creada: `http://localhost/phpmyadmin/`
- Asegúrate de haber ejecutado el script `database/setup.sql`

### **Error: "No such file or directory" en setup.sql**

**Solución:**
- Verifica que el archivo existe en: `database/setup.sql`
- Usa la ruta completa en la línea de comandos:
  ```bash
  mysql -u root -p torneos_telares < "C:\xampp\htdocs\torneos-telares-padel\database\setup.sql"
  ```

### **Error de conexión en la aplicación web**

**Causas y soluciones:**
1. **MySQL no está corriendo**
   - Abre XAMPP Control Panel y inicia MySQL
   
2. **Credenciales incorrectas en database.php**
   - Abre `application/config/database.php`
   - Verifica que coincidan con tu usuario MySQL
   
3. **Tabla no tiene datos**
   - Re-importa el script `database/setup.sql`

### **¿Cómo resetear los datos?**

Para volver a cargar los datos de prueba:

```bash
# En phpMyAdmin o línea de comandos:
DROP DATABASE torneos_telares;
# Luego vuelve a ejecutar el proceso de Paso 3
```

---

## 📝 Agregar Nuevos Torneos (Manualmente)

Si deseas agregar torneos directamente en la BD:

### **Opción A: phpMyAdmin**

1. Abre phpMyAdmin
2. Selecciona la DB `torneos_telares`
3. Haz clic en la tabla `torneos`
4. Haz clic en **"Insertar"**
5. Completa los campos y haz clic en **"Continuar"**

### **Opción B: SQL directo**

```sql
INSERT INTO torneos (nombre, descripcion, fecha_inicio, fecha_fin, categoria, participantes, estado) 
VALUES (
    'Tu Torneo',
    'Descripción del torneo',
    '2026-02-28',
    '2026-03-15',
    'Mixto',
    '32 parejas',
    'proxima'
);
```

---

## 🎓 Próximos Pasos

Con la BD configurada, puedes:

1. **Agregar un formulario de inscripción** que guarde participantes e inscripciones
2. **Crear un panel de administración** para gestionar torneos
3. **Implementar búsqueda y filtros** de torneos
4. **Agregar exportación a PDF** de resultados

---

## 📞 Soporte

Si encuentras problemas:

1. Verifica los **logs de PHP**: `application/logs/`
2. Abre la **consola de PHP**: `http://localhost/phpmyadmin/`
3. Revisa los **errores de MySQL** en XAMPP Control Panel

---

**Última actualización:** Febrero 2026  
**Versión:** 1.0
