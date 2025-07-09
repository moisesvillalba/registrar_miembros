# 📋 Sistema de Registro Integral

![Banner del Sistema](https://via.placeholder.com/800x200/1e3a8a/FFFFFF?text=Sistema+de+Registro)

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

## 🛠️ Instalación Paso a Paso

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
git clone https://github.com/moisesvillalba/registro.git

# Entrar al directorio
cd registro
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

## 🔒 Características de Seguridad

- 🛡️ Protección contra inyección SQL
- 🔍 Validación de entradas
- 🧼 Sanitización de datos
- 🚫 Protección contra CSRF


## 📞 Contacto y Soporte

- 📧 Email: moisesvillalba@gmail.com
- 🐙 GitHub: [Abrir Issues](https://github.com/moisesvillalba/registro/issues)
