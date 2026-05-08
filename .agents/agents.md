# AGENTS.MD — Módulo de Firma de Documentos
## Consultorio de Medicina Alternativa SER S.C.

> Este archivo es la fuente de verdad para cualquier agente o desarrollador que trabaje en este proyecto.
> Léelo completamente antes de modificar cualquier archivo.

---

## 1. Propósito del Proyecto

Este módulo permite la **firma digital de documentos de consentimiento** para pacientes del Consultorio de Medicina Alternativa SER S.C. El flujo cubre tres documentos legales obligatorios antes de recibir tratamiento:

1. **Historia Clínica General (HGC)** — Historial médico del paciente
2. **Aviso de Privacidad (AP)** — Consentimiento de tratamiento de datos personales
3. **Consentimiento Informado (CIN)** — Aceptación de los términos y servicios médicos

Al finalizar, se captura una **fotografía del paciente** para complementar su expediente.

---

## 2. Stack Tecnológico

| Componente | Tecnología |
|---|---|
| Servidor | Apache HTTP Server (Windows) |
| Lenguaje backend | PHP 7.4+ |
| Base de datos | MySQL, extensión **`mysqli`** (NO usar PDO) |
| Frontend | HTML5 + CSS3 + JavaScript Vanilla |
| Firma digital | [SignaturePad v4](https://cdn.jsdelivr.net/npm/signature_pad@4/dist/signature_pad.umd.min.js) |
| Tipografías UI | Segoe UI (sistema) |
| Tipografías documentos | EB Garamond + Source Sans 3 (Google Fonts) |

> [!IMPORTANT]
> La conexión a base de datos usa **`mysqli`** exclusivamente. Si algún archivo usa `PDO`, debe ser refactorizado. Este es un requisito crítico del proyecto.

---

## 3. Flujo del Usuario (Navegación)

```
index.php
    ↓ (POST: codigo/id_paciente)
view_hcg.php         → Firma documento HGC
    ↓ (GET: id_paciente)
view_aviso.php       → Firma Aviso de Privacidad
    ↓ (GET: id_paciente)
view_consentimiento.php → Firma Consentimiento Informado
    ↓ (GET: id_paciente)
fotopaciente.php     → Captura fotografía del paciente
    ↓ (GET: id_paciente)
finalizar_aceptación.php → Resumen con tabla de firmas y foto
```

> [!NOTE]
> `index.php` usa **POST** para iniciar el flujo. Todos los pasos siguientes usan **GET** para pasar el `id_paciente`. Si una página no recibe `id_paciente` válido vía GET, detiene la ejecución mostrando un mensaje de error y no permite continuar.

---

## 4. Estructura de Archivos

```
FirmaDocsSer/
├── .agents/
│   └── agents.md              ← Este archivo
├── .gitignore                 ← Excluye Firmas/* y FotosPacientes/*
├── Firmas/
│   └── .keep                  ← Mantiene la carpeta vacía en Git
├── FotosPacientes/
│   └── .keep                  ← Mantiene la carpeta vacía en Git
├── Static/
│   ├── css/
│   │   └── styles.css         ← ÚNICO archivo CSS del proyecto
│   └── img/
│       ├── logo.png            ← Logo institucional
│       └── favicon.png         ← Ícono del navegador
├── logic/
│   ├── conn.php               ← Conexión mysqli a la BD
│   ├── datos_paciente.php     ← Función reutilizable obtener_datos_paciente()
│   ├── save_firmas.php        ← Procesa y guarda firmas digitales
│   └── save_foto.php          ← Procesa y guarda foto del paciente (responde JSON)
├── index.php                  ← Pantalla de inicio / ingreso de código
├── view_hcg.php               ← Vista y firma de Historia Clínica General
├── view_aviso.php             ← Vista y firma del Aviso de Privacidad
├── view_consentimiento.php    ← Vista y firma del Consentimiento Informado
├── fotopaciente.php           ← Captura de fotografía del paciente
└── finalizar_aceptación.php  ← Pantalla de resumen final
```

---

## 5. Base de Datos

### Servidor y esquema

- **Host:** `localhost`
- **Base de datos:** `ser`
- **Extensión PHP:** `mysqli`

### Tabla principal: `ser.paciente`

Esta tabla es el eje central del módulo. Los campos relevantes son:

| Campo | Tipo | Descripción |
|---|---|---|
| `id_paciente` | INT (PK) | Identificador único del paciente |
| `nombres` | VARCHAR | Nombre(s) del paciente |
| `a_paterno` | VARCHAR | Apellido paterno |
| `a_materno` | VARCHAR | Apellido materno |
| `genero` | VARCHAR | Género del paciente |
| `fecha_nacimiento` | DATE | Fecha de nacimiento |
| `fecha_captura` | DATETIME | Fecha de registro en sistema |
| `foto` | VARCHAR | Nombre de archivo de la foto (sin ruta). Ej: `paciente_20_1778xxx.png` |
| `fecha_foto` | DATETIME | Fecha y hora en que se guardó la foto |
| `url_firma_hgc` | VARCHAR | Ruta relativa de la firma HGC. Ej: `Firmas/HistoriaClinicaGen_20_2026-05-09_01-10.png` |
| `date_firma_hgc` | DATETIME | Timestamp interno de firma HGC |
| `fecha_consetimiento_hgc` | DATETIME | Fecha de consentimiento HGC (mostrada al usuario) |
| `url_firma_avisop` | VARCHAR | Ruta relativa de la firma del Aviso de Privacidad |
| `date_firma_avisop` | DATETIME | Timestamp interno de firma AP |
| `fecha_consetimiento_avisop` | DATETIME | Fecha de consentimiento AP (mostrada al usuario) |
| `url_firma_consent` | VARCHAR | Ruta relativa de la firma del Consentimiento Informado |
| `date_firma_consent` | DATETIME | Timestamp interno de firma CIN |
| `fecha_consetimientoinf` | DATETIME | Fecha de consentimiento Informado (mostrada al usuario) |

### Tablas de soporte (usadas en view_hcg.php)

- **`ser.his_clinica_gen`**: Historia clínica general del paciente. Se relaciona con `paciente` por `id_paciente`.
- **`ser.user`**: Tabla de usuarios/médicos. Se relaciona por el campo `medico` (username).

---

## 6. Archivos de Lógica (`logic/`)

### `conn.php`
Establece la conexión `mysqli`. Es requerido con `require_once` en **todas** las páginas PHP y archivos de lógica.

```php
$mysqli = new mysqli("localhost", "root", "***", "ser");
$mysqli->set_charset("utf8");
```

### `datos_paciente.php`
Define la función `obtener_datos_paciente($mysqli, $id_paciente)`:
- Recibe la conexión y el ID del paciente.
- Retorna un array asociativo con todos los campos del paciente si existe, o `false` si no.
- **Debe ser usado como primera validación** en todos los archivos que requieran datos del paciente.

```php
$datos_paciente = obtener_datos_paciente($mysqli, $id_paciente);
if (!$datos_paciente) { die("...mensaje de error..."); }
```

### `save_firmas.php`
Recibe via POST: `id_paciente`, `documento` (HGC | AP | CIN) y `firma_base64`.
- Decodifica la imagen base64 de la firma.
- **Elimina la firma anterior** del servidor si ya existía (para evitar acumulación de archivos).
- Guarda la nueva imagen `.png` en `/Firmas/` con el nombre `{TipoDoc}_{id}_{Y-m-d_H-i}.png`.
- Actualiza los campos `url_firma_*`, `date_firma_*` y `fecha_consetimiento_*` en la BD.
- Renderiza una página HTML con el resultado y un botón para continuar al siguiente paso.

### `save_foto.php`
Recibe via POST multipart: `id_paciente` y `foto` (archivo de imagen).
- Responde siempre en **JSON** (`Content-Type: application/json`).
- Valida tipo MIME: solo acepta `image/jpeg`, `image/png`, `image/webp`.
- **Elimina la foto anterior** si ya existía en el servidor.
- Guarda la nueva foto en `/FotosPacientes/` con el nombre `paciente_{id}_{timestamp}.ext`.
- Actualiza `foto` y `fecha_foto` en la tabla `paciente`.

---

## 7. Paleta de Colores

| Uso | Color | Hex |
|---|---|---|
| Primario (fondo degradado inicio) | Azul petróleo | `#2d83a0` |
| Primario (fondo degradado fin) | Cian claro | `#00e5ff` |
| Primario oscuro (hover) | Azul oscuro | `#1e5a7a` |
| Encabezados y acentos UI | Azul petróleo | `#2d83a0` |
| Botón finalizar / acción positiva | Verde | `#028a0f` |
| Botón finalizar (hover) | Verde oscuro | `#02660b` |
| Botón limpiar firma | Naranja | `#f57c00` |
| Botón limpiar firma (hover) | Naranja oscuro | `#e65100` |
| Fondo documentos papel | Crema | `#f7f5f0` |
| Texto principal documentos | Negro tinta | `#1a1a1a` |
| Texto secundario | Gris medio | `#333` |
| Bordes | Azul grisáceo | `#c8d3db` |
| Error | Rojo | `#ef5350` / `#c62828` |
| Éxito | Verde claro | `#2e7d32` |

---

## 8. Sistema de Temas CSS (`Static/css/styles.css`)

Todo el CSS del proyecto está centralizado en **un solo archivo**. Los estilos se activan mediante clases en la etiqueta `<body>`:

| Clase en `<body>` | Uso | Páginas |
|---|---|---|
| `body.ui-theme` | Interfaz de aplicación (fondo degradado azul, tarjeta blanca) | `index.php`, `fotopaciente.php`, `finalizar_aceptación.php` |
| `body.doc-theme-paper` | Documentos tipo papel (fondo crema, tipografía editorial) | `view_aviso.php`, `view_consentimiento.php` |
| `body.doc-theme-hcg` | Historia Clínica (fondo blanco, optimizado para impresión) | `view_hcg.php` |

> [!IMPORTANT]
> **Nunca agregar `<style>` directamente a los archivos PHP.** Todo CSS nuevo debe ir en `Static/css/styles.css` bajo la sección del tema correspondiente.

### Componentes UI principales

- **`.container`**: Tarjeta blanca centrada (usada en `index.php`)
- **`.card`**: Tarjeta blanca centrada expandida (usada en `fotopaciente.php`, `finalizar_aceptación.php`)
- **`.pagina`**: Hoja de documento estilo papel (`view_aviso.php`, `view_consentimiento.php`)
- **`.btn` / `.button`**: Botones primarios azules, grandes (táctil-friendly, min-height: 70px)
- **`.btn-guardar`**: Botón guardar (azul)
- **`.btn-limpiar`**: Botón limpiar firma (naranja)
- **`.btn-finalizar`**: Botón finalizar proceso (verde)
- **`.signature-section` / `.signature-box`**: Contenedor del área de firma digital
- **`#firma`**: Canvas de SignaturePad (borde azul, cursor crosshair)

---

## 9. Implementación de Firma Digital (SignaturePad)

La librería SignaturePad v4 se carga vía CDN en todas las páginas con firma:

```html
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4/dist/signature_pad.umd.min.js"></script>
```

**Patrón estándar de implementación en todas las vistas de firma:**

```javascript
const canvas = document.getElementById('firma');
const pad = new SignaturePad(canvas, {
    penColor: '#000000',
    minWidth: 2,
    maxWidth: 4,
    throttle: 16
});

function limpiarFirma() { pad.clear(); }

document.getElementById('firmaForm').addEventListener('submit', function(e) {
    if (pad.isEmpty()) {
        e.preventDefault();
        alert('⚠️ Por favor firme en el espacio designado antes de guardar.');
        return false;
    }
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'firma_base64';
    input.value = pad.toDataURL();
    this.appendChild(input);
});

// Responsive canvas (ajuste para alta densidad de pantalla)
function resizeCanvas() {
    const box = canvas.parentElement;
    const ratio = window.devicePixelRatio || 1;
    canvas.width = Math.min(550 * ratio, box.offsetWidth * ratio);
    canvas.height = 240 * ratio;
    canvas.style.width = Math.min(550, box.offsetWidth) + 'px';
    canvas.style.height = '240px';
    const ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();
```

El canvas debe tener `touch-action: none` en CSS para funcionar correctamente en tablets táctiles.

---

## 10. Convenciones de Programación

### PHP
- **Siempre** usar `require_once 'logic/conn.php'` y `require_once 'logic/datos_paciente.php'` al inicio de cada vista.
- **Validar** `$_GET['id_paciente']` al inicio de cada página. Si no existe o el paciente no se encuentra, detener con `die()` y mensaje descriptivo.
- Usar `htmlspecialchars()` para toda salida de datos de usuario al HTML.
- Usar `intval()` o `real_escape_string()` antes de interpolar variables en consultas SQL.
- Preferir **prepared statements** (`bind_param`) en las operaciones de escritura (INSERT/UPDATE).
- Antes de guardar un nuevo archivo (firma/foto), **eliminar el anterior** con `unlink()` si existe.

### Nombrado de archivos de firma
```
Firmas/HistoriaClinicaGen_{id}_{Y-m-d_H-i}.png
Firmas/AvisoPrivacidad_{id}_{Y-m-d_H-i}.png
Firmas/ConsentimientoInformado_{id}_{Y-m-d_H-i}.png
```

### Nombrado de archivos de foto
```
FotosPacientes/paciente_{id}_{timestamp_unix}.{ext}
```

### Formato de fechas al usuario
Usar la función `formatearFecha()` definida en `finalizar_aceptación.php`:
```php
function formatearFecha($fecha) {
    if (!$fecha) return 'Pendiente';
    $t = strtotime($fecha);
    return $t ? date('d/m/Y \a \l\a\s H:i:s', $t) : $fecha;
}
```
Resultado: `08/05/2026 a las 13:40:56`

---

## 11. Seguridad y Almacenamiento

- Las carpetas `/Firmas/` y `/FotosPacientes/` están **excluidas del control de versiones** (`.gitignore`).
- Solo se conserva un archivo por paciente por tipo de documento: el sistema elimina el anterior automáticamente antes de guardar el nuevo.
- El servidor Apache debe tener **permisos de escritura** (755) sobre las carpetas `/Firmas/` y `/FotosPacientes/`.
- `save_foto.php` solo acepta `image/jpeg`, `image/png` y `image/webp` validado por `mime_content_type()`.
- El parámetro `id_paciente` es validado con `is_numeric()` y `intval()` antes de cualquier operación.

---

## 12. Optimización para Tablet

El sistema está diseñado primariamente para tablets (ej. 11.5" con resolución 2508×1504 px en alta densidad):

- Botones con `min-height: 70px` y `font-size` grande para facilitar la interacción táctil.
- El canvas de SignaturePad escala automáticamente usando `window.devicePixelRatio`.
- La cámara se abre en modo `environment` (cámara trasera) por defecto en `fotopaciente.php`.
- Se usan `@media queries` para ajustar tamaños en pantallas pequeñas (`max-width: 799px`).
- Se desactiva `-webkit-tap-highlight-color` en botones para evitar el destello azul táctil.

---

## 13. Checklist para Nuevas Vistas o Módulos

Antes de crear un nuevo archivo PHP, verificar:

- [ ] Incluye `require_once 'logic/conn.php'` y `require_once 'logic/datos_paciente.php'`
- [ ] Valida `$_GET['id_paciente']` con `die()` si falta o el paciente no existe
- [ ] El `<body>` tiene la clase de tema correcta (`ui-theme`, `doc-theme-paper` o `doc-theme-hgc`)
- [ ] El `<head>` incluye `<link rel="stylesheet" href="Static/css/styles.css">` (no inline `<style>`)
- [ ] El CSS nuevo (si aplica) se agrega a `Static/css/styles.css` bajo la sección del tema correcto
- [ ] El favicon está incluido: `<link rel="icon" type="image/png" href="Static/img/favicon.png">`
- [ ] Los datos de usuario se sanitizan con `htmlspecialchars()` antes de mostrarlos en el HTML
- [ ] Si el módulo guarda archivos al servidor, elimina el archivo previo antes de guardar el nuevo
