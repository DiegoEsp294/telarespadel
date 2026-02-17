<?php
/**
 * Database Error Page Template (HTML)
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Error de Base de Datos</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f5f5f5;
			margin: 0;
			padding: 20px;
		}
		.error-container {
			max-width: 800px;
			margin: 0 auto;
			background-color: white;
			padding: 30px;
			border-radius: 5px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		}
		h1 {
			color: #d32f2f;
			margin-top: 0;
		}
		h4 {
			color: #333;
			margin-top: 20px;
		}
		.error-message {
			background-color: #ffebee;
			border-left: 4px solid #d32f2f;
			padding: 15px;
			border-radius: 3px;
			line-height: 1.6;
		}
		code {
			background-color: #f5f5f5;
			padding: 2px 6px;
			border-radius: 3px;
			font-family: 'Courier New', monospace;
		}
	</style>
</head>
<body>
	<div class="error-container">
		<h1>Error de Base de Datos</h1>
		<div class="error-message">
			<?php echo isset($message) ? $message : 'Ocurrió un error al acceder a la base de datos'; ?>
		</div>
		<?php if (ENVIRONMENT === 'development'): ?>
			<h4>Información del Error</h4>
			<p><strong>Archivo:</strong> <?php echo isset($filepath) ? $filepath : 'Unknown'; ?></p>
			<p><strong>Línea:</strong> <?php echo isset($line) ? $line : 'Unknown'; ?></p>
			<?php if (isset($error)): ?>
				<p><strong>Detalles:</strong></p>
				<pre style="background-color: #f5f5f5; padding: 15px; border-radius: 3px; overflow-x: auto;">
<code><?php echo htmlspecialchars($error); ?></code>
				</pre>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</body>
</html>
