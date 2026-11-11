# 🦷 Historia Clínica Odontológica

Aplicación web para gestionar pacientes e historias clínicas odontológicas
(limpieza, blanqueamiento, extracción de muelas y cordales, endodoncias, calzas, etc.).

**Tecnologías:** PHP 8 (PDO) · MySQL · Bootstrap 5 · AJAX (fetch) · PDF generado en PHP puro.

## Funcionalidades
- Inicio de sesión con contraseñas cifradas (`password_hash`), sesión y token CSRF.
- Panel principal con estadísticas y menú lateral (responsive).
- Registro, búsqueda, edición y eliminación de pacientes (con antecedentes y alergias).
- Catálogo de procedimientos ofrecidos por el odontólogo (nombre, descripción, precio, estado).
- Historia clínica por paciente: atenciones con procedimiento, pieza dental, diagnóstico, observaciones, costo y estado.
- Generación de la historia clínica en **PDF**.

## Instalación local (XAMPP / Laragon)
1. Copie la carpeta en `htdocs`.
2. En phpMyAdmin cree la BD `clinica_dental` e importe `database/clinica_dental.sql`.
3. Copie `config/config.sample.php` a `config/config.php` y ajuste credenciales.
4. Abra `http://localhost/clinica_dental/` → usuario **admin** / contraseña **Admin123\*** (se crea en el primer ingreso; cámbiela luego).

## Despliegue en InfinityFree (gratuito, soporta PHP + MySQL)
1. Cree cuenta en infinityfree.com y un sitio (subdominio gratuito).
2. En *MySQL Databases* cree una base de datos; anote host (`sqlXXX.infinityfree.com`), nombre, usuario y contraseña.
3. Abra phpMyAdmin e importe `database/clinica_dental.sql`.
4. Edite `config/config.php` con esos datos.
5. Suba el contenido del proyecto a la carpeta `htdocs` (File Manager o FTP con FileZilla).
6. Entre a `https://su-sitio.infinityfreeapp.com`.

> Vercel **no ejecuta PHP de forma nativa** ni ofrece MySQL; use InfinityFree, FreeHostia, AwardSpace o 000webhost.

## Subir a GitHub
```bash
git init
git add .
git commit -m "Historia clínica odontológica"
git branch -M main
git remote add origin https://github.com/SU_USUARIO/clinica-dental.git
git push -u origin main
```
(`config/config.php` está en `.gitignore` para no publicar contraseñas.)

## Estructura
```
index.php · dashboard.php · pacientes.php · procedimientos.php · historia.php · logout.php
api/        endpoints AJAX (JSON): login, stats, pacientes, procedimientos, historia
pdf/        historia_pdf.php (PDF de la historia clínica)
lib/        MiniPDF.php (generador PDF sin dependencias)
includes/   init.php, header.php, footer.php
database/   clinica_dental.sql
```
