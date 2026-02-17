<?php
/**
 * Exception Error Page Template (HTML)
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Exception</title>
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
		.error-message {
			background-color: #ffebee;
			border-left: 4px solid #d32f2f;
			padding: 15px;
			border-radius: 3px;
			line-height: 1.6;
		}
		.trace {
			background-color: #f9f9f9;
			border: 1px solid #ddd;
			padding: 15px;
			margin-top: 20px;
			border-radius: 3px;
			font-family: 'Courier New', monospace;
			font-size: 12px;
			overflow-x: auto;
		}
	</style>
</head>
<body>
	<div class="error-container">
		<h1>Exception Error</h1>
		<div class="error-message">
			<?php echo $message; ?>
		</div>
		<?php if (ENVIRONMENT === 'development' && method_exists($exception, 'getTrace')): ?>
			<h4>Stack Trace</h4>
			<div class="trace">
				<?php echo nl2br(htmlspecialchars($exception->getTraceAsString())); ?>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>
