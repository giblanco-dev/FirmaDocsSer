<html>
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
                            <meta http-equiv="Pragma" content="no-cache">
                            <meta http-equiv="Expires" content="0">
                            <link rel="icon" type="image/png" href="Static/img/favicon.png">
                            <link rel="stylesheet" href="Static/css/styles.css">
                        </head>
                        <body class="ui-theme">
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