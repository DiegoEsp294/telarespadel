# 🔧 Guía de Instalación - Telares Padel

## Paso 1: Descargar CodeIgniter 3

1. Ve a: https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip
2. Descarga el archivo ZIP de CodeIgniter 3.1.13
3. Extrae el archivo en una carpeta temporal

## Paso 2: Copiar la carpeta `system`

1. Busca la carpeta `system` dentro del archivo descargado
2. Cópiala a: `c:\xampp\htdocs\torneos-telares-padel\system\`

Tu estructura debería verse así:
```
c:\xampp\htdocs\torneos-telares-padel\
├── system/                    ← Carpeta copiada de CodeIgniter
├── application/               ← YA EXISTE
├── assets/                    ← YA EXISTE
├── index.php                  ← YA EXISTE
├── .htaccess                  ← YA EXISTE
└── README.md                  ← YA EXISTE
```

## Paso 3: Configurar Apache (XAMPP)

1. Abre el archivo: `C:\xampp\apache\conf\httpd.conf`
2. Busca la línea que dice: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Elimina la `#` al inicio (déscomentar)
4. Guarda el archivo
5. Reinicia Apache desde el panel de control de XAMPP

## Paso 4: Habilitar mod_rewrite en .htaccess

El archivo `.htaccess` ya está configurado en el proyecto. Si aún así tienes problemas:

1. Ve a `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Agrega o modifica tu virtual host:
```apache
<VirtualHost *:80>
    DocumentRoot "C:\xampp\htdocs\torneos-telares-padel"
    ServerName localhost
    ServerAlias localhost
    <Directory "C:\xampp\htdocs\torneos-telares-padel">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Reinicia Apache

## Paso 5: Acceder a la Web

Abre tu navegador y ve a:
```
http://localhost/torneos-telares-padel/
```

Si ves la página principal con el diseño azul y naranja, ¡está funcionando! 🎉

## Paso 6: Personalizar tu Logo

1. Prepara una imagen PNG o JPG con tu logo (recomendado: 200x100px)
2. Guárdala en: `assets/images/logo.png`
3. Recarga la página web

## Paso 7: Configurar Información del Club

Edita el archivo: `application/controllers/Home.php`

Busca la función `index()` y modifica:

```php
// Datos del club
$data['club_nombre'] = 'Telares Padel';          // Nombre del club
$data['club_descripcion'] = '...';               // Descripción
$data['club_info'] = array(
    'ubicacion' => 'Tu localidad, [Provincia]',  // Tu ubicación
    'telefono' => '+54 (XXX) XXXX-XXXX',        // Tu teléfono
    'email' => 'info@telarespadel.com',         // Tu email
    'facebook' => 'https://facebook.com/...',   // Tu Facebook
    'instagram' => '@telarespadel'               // Tu Instagram
);
```

## ✅ Verificación

Si todo está bien, deberías ver:

- ✓ Navbar con logo y menú de navegación
- ✓ Hero section atractivo
- ✓ Información del club
- ✓ Listado de 4 torneos de ejemplo
- ✓ Sección de servicios
- ✓ Formulario de contacto
- ✓ Footer con información
- ✓ Diseño responsivo (prueba en móvil)

## 🐛 Solución de Problemas

### "Error: Cannot find system folder"
- Asegúrate de haber copiado la carpeta `system` correctamente

### "404 Not Found" o "Page not found"
- Verifica que mod_rewrite esté habilitado
- Recarga Apache
- Intenta acceder a: `http://localhost/torneos-telares-padel/index.php`

### El CSS no se ve bien
- Limpia el caché del navegador (Ctrl + Shift + Delete)
- Verifica que la carpeta `assets` exista

### Las imágenes no aparecen
- Coloca tu logo en: `assets/images/logo.png`
- El archivo debe existir con ese nombre exacto

## 📞 Ayuda Adicional

Si tienes problemas, verifica:
1. PHP 7.4+ está instalado: `php -v` en la terminal
2. MySQL está corriendo (si lo usarás)
3. XAMPP Apache está iniciado
4. El puerto 80 está disponible

---

¡Listo! Tu web para Telares Padel está funcionando. 🏓
