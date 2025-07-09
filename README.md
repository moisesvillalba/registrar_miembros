# 📋 Sistema de Registro Integral

## 🌟 Introducción

### ¿Qué es este Sistema?
El Sistema de Registro Integral es una aplicación web diseñada para simplificar y optimizar el proceso de registro y gestión de miembros. Imagina tener una herramienta que:
- 🖊️ Recopila información de manera estructurada
- 📊 Organiza datos de forma inteligente
- 🔒 Mantiene tu información segura

### Casos de Uso
✅ **Para Organizaciones**:
- Registros de miembros
- Gestión de información institucional
- Control de datos personales y profesionales

✅ **Para Administradores**:
- Panel de control centralizado
- Búsqueda y filtrado de registros
- Gestión eficiente de información

## 🚀 Características Principales

| Característica | Descripción | Beneficio |
|---------------|-------------|-----------|
| 📝 Formulario Multipaso | Registro dividido en 4 secciones | Experiencia de usuario intuitiva |
| 🖥️ Panel Administrativo | Gestión completa de registros | Control total de la información |
| 📤 Subida de Documentos | Almacenamiento de archivos | Respaldo de documentación |
| 📱 Diseño Responsivo | Funciona en todos los dispositivos | Accesibilidad universal |

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor Web**: Apache (XAMPP)
- **Frameworks CSS**: Bootstrap/CSS personalizado

## 📁 Estructura del Proyecto

```
registrar_miembros/
├── assets/                 # Archivos estáticos (CSS, JS, imágenes)
├── config/                 # Configuraciones de base de datos
├── includes/               # Archivos PHP incluidos
├── uploads/                # Archivos subidos por usuarios
├── dashboard.php           # Panel de administración
├── index.php              # Página principal/formulario
├── login.php              # Página de inicio de sesión
├── process.php            # Procesamiento de formularios
├── list.php               # Listado de miembros
├── edit.php               # Edición de registros
├── delete.php             # Eliminación de registros
├── install.php            # Instalador de la base de datos
└── README.md              # Este archivo
```

## 🚀 Instalación Paso a Paso

### Requisitos Previos
**Necesitarás**:
- 🌐 Servidor web (Apache/Nginx)
- 🐘 PHP 7.4+
- 🗃️ MySQL 5.7+ o MariaDB 10.3+
- 🌍 Navegador web moderno

### Guía de Instalación

#### 1. Descargar el Código 📦
```bash
# Clonar repositorio
git clone https://github.com/moisesvillalba/registrar_miembros.git
# Entrar al directorio
cd registrar_miembros
```

#### 2. Configuración de Base de Datos 🗄️
```sql
-- Crear base de datos
CREATE DATABASE sistema_registro;
-- Crear usuario (sustituir valores)
CREATE USER 'usuario_registro'@'localhost' IDENTIFIED BY 'contraseña_segura';
GRANT ALL PRIVILEGES ON sistema_registro.* TO 'usuario_registro'@'localhost';
```

#### 3. Configurar Conexión 🔗
Editar `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'sistema_registro';
private $username = 'usuario_registro';
private $password = 'contraseña_segura';
```

#### 4. Ejecutar Instalador 🚀
- Abrir `http://tu-dominio.com/install.php`
- Seguir instrucciones del instalador

## 🔐 Acceso Inicial

| Tipo | Credenciales |
|------|--------------|
| 👤 Usuario | `admin` |
| 🔑 Contraseña | `Admin123` |

> ⚠️ **Importante**: Cambiar contraseña inmediatamente

## 🎯 Uso del Sistema

### Para Usuarios (Registro)

1. **Acceder al formulario**: Ve a la página principal
2. **Completar datos**: Llena todos los campos requeridos
3. **Subir documentos**: Adjunta fotos y documentos necesarios
4. **Enviar**: Haz clic en "Registrar" para enviar

### Para Administradores

1. **Iniciar sesión**: Usa `login.php` con tus credenciales
2. **Ver dashboard**: Accede al panel de control
3. **Gestionar registros**: 
   - Ver listado completo
   - Editar información
   - Eliminar registros
   - Exportar datos

## 📊 Funcionalidades Detalladas

### Formulario de Registro
- Datos personales completos
- Validación en tiempo real
- Carga múltiple de archivos
- Campos condicionales

### Panel de Administración
- Estadísticas resumidas
- Búsqueda y filtros
- Paginación de resultados
- Acciones masivas

### Seguridad
- Autenticación de usuarios
- Validación de datos
- Protección contra SQL injection
- Manejo seguro de archivos

## 🎨 Personalización

### Modificar Estilos
En `assets/css/styles.css`:
```css
:root {
  --primary-color: #1e3a8a;     /* Color principal */
  --primary-light: #3b82f6;     /* Tono claro */
  --success-color: #10b981;     /* Color de éxito */
}
```

### Personalización Avanzada
- **Estilos**: Modifica `assets/css/style.css`
- **Scripts**: Edita `assets/js/main.js`
- **Plantillas**: Personaliza archivos PHP principales

## 🚧 Solución de Problemas

### Errores Comunes 🛠️

1. **Error de Conexión de Base de Datos**
   - ✅ Verificar credenciales
   - ✅ Comprobar servicio MySQL
   - ✅ Revisar permisos de usuario

2. **Problemas de Subida de Archivos**
   - ✅ Verificar permisos de carpeta `/uploads`
   - ✅ Límite de tamaño: 5MB
   - ✅ Formatos permitidos: JPG, PNG, PDF

3. **Sesión expira rápidamente**
   - ✅ Aumenta session.gc_maxlifetime en php.ini
   - ✅ Verifica configuración de cookies

### Logs de Error
- Apache: `xampp/apache/logs/error.log`
- PHP: Habilita `display_errors` en desarrollo

## 🔒 Características de Seguridad

- 🛡️ Protección contra inyección SQL
- 🔍 Validación de entradas
- 🧼 Sanitización de datos
- 🚫 Protección contra CSRF

## 🤝 Contribuir

1. Fork el repositorio
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
3. Commit tus cambios: `git commit -m 'Añadir nueva funcionalidad'`
4. Push a la rama: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request

## 📝 Changelog

### v2.0.0 (2025-07-09)
- Migración a repositorio independiente
- Refactorización completa del código
- Mejoras en la interfaz de usuario
- Optimización de consultas a base de datos

### v1.0.0 (2025-07-03)
- Versión inicial del sistema
- Funcionalidades básicas de CRUD
- Sistema de autenticación

## 📞 Contacto y Soporte

- 📧 Email: moisesvillalba@gmail.com
- 🐙 GitHub: [Abrir Issues](https://github.com/moisesvillalba/registrar_miembros/issues)

## 🤝 Contribuir

1. Fork el repositorio
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
3. Commit tus cambios: `git commit -m 'Añadir nueva funcionalidad'`
4. Push a la rama: `git push origin feature/nueva-funcionalidad`
5. Abre un Pull Request

## 📝 Changelog

### v2.0.0 (2025-07-09)
- Migración a repositorio independiente
- Refactorización completa del código
- Mejoras en la interfaz de usuario
- Optimización de consultas a base de datos

### v1.0.0 (2025-07-03)
- Versión inicial del sistema
- Funcionalidades básicas de CRUD
- Sistema de autenticación

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## 🙏 Agradecimientos

- A la comunidad PHP por las mejores prácticas
- A Bootstrap por el framework CSS
- A todos los colaboradores del proyecto

---

**Desarrollado por Moisés Villalba**