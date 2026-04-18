<html>
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
                            <meta http-equiv="Pragma" content="no-cache">
                            <meta http-equiv="Expires" content="0">
                            <link rel="icon" type="image/png" href="Static/img/favicon.png">
                            <style>
                                * {
                                    margin: 0;
                                    padding: 0;
                                    box-sizing: border-box;
                                }

                                body {
                                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    min-height: 100vh;
                                    background: linear-gradient(135deg, #2d83a0 0%, #00e5ff 100%);
                                    padding: 20px;
                                }

                                .container {
                                    background: white;
                                    padding: 50px 40px;
                                    border-radius: 12px;
                                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
                                    text-align: center;
                                    max-width: 700px;
                                    width: 100%;
                                }

                                .message {
                                    font-size: 28px;
                                    color: #333;
                                    margin-bottom: 40px;
                                    line-height: 1.7;
                                    font-weight: 600;
                                    letter-spacing: 0.3px;
                                }

                                .code {
                                    font-size: 36px;
                                    color: #2d83a0;
                                    font-weight: 700;
                                    margin: 30px 0;
                                }

                                .logo {
                                    width: 120px;
                                    height: 120px;
                                    margin: 0 auto 30px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                }

                                .logo img {
                                    max-width: 100%;
                                    max-height: 100%;
                                    object-fit: contain;
                                    filter: drop-shadow(0 4px 8px rgba(45, 131, 160, 0.2));
                                }

                                form {
                                    display: flex;
                                    flex-direction: column;
                                    gap: 25px;
                                }

                                .input-field {
                                    width: 100%;
                                    padding: 18px 22px;
                                    font-size: 22px;
                                    border: 2px solid #c8d3db;
                                    border-radius: 10px;
                                    transition: all 0.3s ease;
                                    font-weight: 500;
                                    -webkit-appearance: none;
                                    appearance: none;
                                }

                                .input-field:focus {
                                    border-color: #2d83a0;
                                    outline: none;
                                    box-shadow: 0 0 0 5px rgba(45, 131, 160, 0.2);
                                    transform: translateY(-2px);
                                }

                                .input-field::placeholder {
                                    color: #999;
                                    font-size: 20px;
                                }

                                .button {
                                    background-color: #2d83a0;
                                    color: white;
                                    border: none;
                                    padding: 20px 40px;
                                    font-size: 24px;
                                    font-weight: 700;
                                    border-radius: 10px;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    min-height: 70px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    -webkit-tap-highlight-color: transparent;
                                }

                                .button:hover {
                                    background-color: #1e5a7a;
                                    transform: translateY(-3px);
                                    box-shadow: 0 8px 20px rgba(45, 131, 160, 0.3);
                                }

                                .button:active {
                                    transform: translateY(-1px);
                                }

                                .error-message {
                                    display: none;
                                    background-color: #ffebee;
                                    border: 2px solid #ef5350;
                                    border-radius: 10px;
                                    padding: 18px 20px;
                                    margin-bottom: 30px;
                                    color: #c62828;
                                    font-size: 18px;
                                    font-weight: 600;
                                    line-height: 1.5;
                                }

                                .error-message.show {
                                    display: block;
                                    animation: slideDown 0.3s ease;
                                }

                                @keyframes slideDown {
                                    from {
                                        opacity: 0;
                                        transform: translateY(-10px);
                                    }
                                    to {
                                        opacity: 1;
                                        transform: translateY(0);
                                    }
                                }

                                /* Optimización para tablet 11.5 pulgadas */
                                @media (min-width: 800px) and (max-width: 1600px) {
                                    .container {
                                        padding: 60px 50px;
                                        max-width: 800px;
                                    }

                                    .error-message {
                                        padding: 20px 25px;
                                        font-size: 20px;
                                    }

                                    .logo {
                                        width: 150px;
                                        height: 150px;
                                        margin-bottom: 40px;
                                    }

                                    .message {
                                        font-size: 32px;
                                        margin-bottom: 50px;
                                    }

                                    .input-field {
                                        padding: 20px 25px;
                                        font-size: 24px;
                                        min-height: 60px;
                                    }

                                    .button {
                                        padding: 22px 50px;
                                        font-size: 26px;
                                        min-height: 75px;
                                    }
                                }

                                /* Pantallas más grandes */
                                @media (min-width: 1600px) {
                                    .container {
                                        padding: 70px 60px;
                                    }

                                    .error-message {
                                        padding: 22px 28px;
                                        font-size: 22px;
                                    }

                                    .logo {
                                        width: 180px;
                                        height: 180px;
                                        margin-bottom: 50px;
                                    }

                                    .message {
                                        font-size: 36px;
                                    }

                                    .input-field {
                                        padding: 24px 30px;
                                        font-size: 26px;
                                        min-height: 70px;
                                    }

                                    .button {
                                        padding: 26px 60px;
                                        font-size: 28px;
                                        min-height: 85px;
                                    }
                                }

                                /* Pantallas pequeñas */
                                @media (max-width: 799px) {
                                    .container {
                                        padding: 40px 30px;
                                    }

                                    .error-message {
                                        padding: 16px 18px;
                                        font-size: 16px;
                                    }

                                    .logo {
                                        width: 100px;
                                        height: 100px;
                                        margin-bottom: 25px;
                                    }

                                    .message {
                                        font-size: 24px;
                                        margin-bottom: 30px;
                                    }

                                    .input-field {
                                        padding: 16px 20px;
                                        font-size: 20px;
                                        min-height: 55px;
                                    }

                                    .button {
                                        padding: 18px 35px;
                                        font-size: 22px;
                                        min-height: 65px;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="logo">
                                    <img src="Static/img/logo.png" alt="Logo Clínica">
                                </div>
                                
                                <div id="errorAlert" class="error-message"></div>
                                
                                <p class="message">Ingrese el código proporcionado por el médico</p>
                                <form action="view_hcg.php" method="post" autocomplete="off">
                                    <input class="input-field" type="number" name="codigo" placeholder="Ingrese el código" autocomplete="off" required />
                                    <button class="button" type="submit">Revisar Historia Clínica</button>
                                </form>
                            </div>

                            <script>
                                // Limpiar formulario y almacenamiento al cargar la página
                                document.addEventListener('DOMContentLoaded', function() {
                                    const form = document.querySelector('form');
                                    if (form) {
                                        form.reset();
                                    }
                                    
                                    // Limpiar datos de localStorage y sessionStorage
                                    localStorage.clear();
                                    sessionStorage.clear();
                                });

                                // Detectar errores en la URL
                                const urlParams = new URLSearchParams(window.location.search);
                                const errorCode = urlParams.get('error');
                                const errorAlert = document.getElementById('errorAlert');

                                const errorMessages = {
                                    '1': '⚠️ El código es requerido. Por favor ingrese el código proporcionado por el médico.',
                                    '2': '⚠️ El código debe contener solo números. Verifique e intente nuevamente.',
                                    '3': '⚠️ El código no es válido. Por favor verifique e intente nuevamente.'
                                };

                                if (errorCode && errorMessages[errorCode]) {
                                    errorAlert.textContent = errorMessages[errorCode];
                                    errorAlert.classList.add('show');
                                }
                            </script>
                        </body>
                        </html>