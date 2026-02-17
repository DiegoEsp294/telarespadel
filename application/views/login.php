<!-- Login Section -->
<style>
    .auth-container {
        min-height: calc(100vh - 300px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f5f5 0%, #e8f0f5 100%);
        padding: 40px 20px;
    }

    .auth-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 51, 102, 0.1);
        width: 100%;
        max-width: 400px;
        padding: 40px;
    }

    .auth-card h1 {
        color: #003366;
        text-align: center;
        margin: 0 0 10px;
        font-size: 28px;
    }

    .auth-card .subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        color: #003366;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group input {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: inherit;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #FF6600;
        box-shadow: 0 0 5px rgba(255, 102, 0, 0.2);
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background-color: #FF6600;
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background-color: #FF8533;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
    }

    .error-message {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        color: #721c24;
        padding: 12px;
        margin-bottom: 20px;
    }

    .success-message {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        color: #155724;
        padding: 12px;
        margin-bottom: 20px;
    }

    .auth-link {
        text-align: center;
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }

    .auth-link a {
        color: #FF6600;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        .auth-card {
            padding: 30px 20px;
        }

        .auth-card h1 {
            font-size: 24px;
        }
    }
</style>

<section class="auth-container">
    <div class="auth-card">
        <h1><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h1>
        <p class="subtitle">Accede a tu cuenta de Telares Padel</p>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo site_url('auth/login'); ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo set_value('email'); ?>" 
                       placeholder="tu@email.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Mínimo 6 caracteres">
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-lock"></i> Entrar
            </button>
        </form>

        <div class="auth-link">
            ¿No tienes cuenta? <a href="<?php echo site_url('auth/registro'); ?>">Registrate aquí</a>
        </div>

        <div class="auth-link">
            <a href="<?php echo base_url(); ?>">← Volver al inicio</a>
        </div>
    </div>
</section>
