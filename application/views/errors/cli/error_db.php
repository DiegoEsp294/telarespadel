<?php
/**
 * Database Error Page Template (CLI)
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

Error de Base de Datos

<?php echo isset($message) ? $message : 'Ocurrió un error al acceder a la base de datos'; ?>

<?php if (ENVIRONMENT === 'development'): ?>
Archivo: <?php echo isset($filepath) ? $filepath : 'Unknown'; ?>
Línea: <?php echo isset($line) ? $line : 'Unknown'; ?>

<?php if (isset($error)): ?>
Detalles:
<?php echo $error; ?>
<?php endif; ?>
<?php endif; ?>
