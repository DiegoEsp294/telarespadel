<?php
/**
 * Exception Error Page Template (CLI)
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

Exception Error

<?php echo $message; ?>

<?php if (ENVIRONMENT === 'development' && method_exists($exception, 'getTrace')): ?>
Stack Trace:
<?php echo $exception->getTraceAsString(); ?>
<?php endif; ?>
