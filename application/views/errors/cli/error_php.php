<?php
/**
 * Error Page Template (CLI)
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php echo isset($heading) ? $heading : $severity; ?>

<?php echo isset($message) ? $message : 'An error occurred'; ?>

<?php if (ENVIRONMENT === 'development'): ?>
Archivo: <?php echo isset($filepath) ? $filepath : 'Unknown'; ?>
Línea: <?php echo isset($line) ? $line : 'Unknown'; ?>
<?php endif; ?>
