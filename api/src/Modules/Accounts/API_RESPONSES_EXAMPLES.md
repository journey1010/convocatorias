# Ejemplos de Respuestas API - Personal Data Extra

## 1. Crear Información Personal

### Solicitud
```http
POST /accounts/personal-data/
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: multipart/form-data

{
  "department_id": 1,
  "province_id": 1,
  "district_id": 1,
  "address": "Calle Principal 123, Apartamento 4B",
  "birthday": "1990-05-15",
  "genere": 1,
  "have_cert_disability": true,
  "file_cert_disability": [PDF file],
  "have_cert_army": false,
  "file_cert_army": null,
  "have_cert_professional_credentials": true,
  "file_cert_professional_credentials": [PDF file],
  "is_active_cert_professional_credentials": true
}
```

### Respuesta Exitosa (201 Created)
```json
{
  "id": 1,
  "user_id": 5,
  "department_id": 1,
  "province_id": 1,
  "district_id": 1,
  "address": "Calle Principal 123, Apartamento 4B",
  "birthday": "1990-05-15",
  "genere": 1,
  "have_cert_disability": true,
  "file_cert_disability": "personal_data_certs/disability_1707213234_507a1c2b.pdf",
  "have_cert_army": false,
  "file_cert_army": null,
  "have_cert_professional_credentials": true,
  "file_cert_professional_credentials": "personal_data_certs/professional_credentials_1707213234_507a1c2c.pdf",
  "is_active_cert_professional_credentials": true
}
```

## 2. Ver Información Personal

### Solicitud
```http
GET /accounts/personal-data/
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Respuesta Exitosa (200 OK)
```json
{
  "id": 1,
  "user_id": 5,
  "department_id": 1,
  "province_id": 1,
  "district_id": 1,
  "address": "Calle Principal 123, Apartamento 4B",
  "birthday": "1990-05-15",
  "genere": 1,
  "have_cert_disability": true,
  "file_cert_disability": "personal_data_certs/disability_1707213234_507a1c2b.pdf",
  "have_cert_army": false,
  "file_cert_army": null,
  "have_cert_professional_credentials": true,
  "file_cert_professional_credentials": "personal_data_certs/professional_credentials_1707213234_507a1c2c.pdf",
  "is_active_cert_professional_credentials": true
}
```

### Respuesta No Encontrada (404 Not Found)
```json
{
  "message": "Información personal no encontrada",
  "status": 404
}
```

## 3. Actualizar Información Personal

### Solicitud
```http
PATCH /accounts/personal-data/
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: multipart/form-data

{
  "department_id": 2,
  "province_id": 2,
  "district_id": 2,
  "address": "Nueva Dirección 456, Piso 2",
  "birthday": "1990-05-15",
  "genere": 1,
  "have_cert_disability": false,
  "file_cert_disability": null,
  "have_cert_army": true,
  "file_cert_army": [PDF file],
  "have_cert_professional_credentials": true,
  "file_cert_professional_credentials": [PDF file],
  "is_active_cert_professional_credentials": false
}
```

### Respuesta Exitosa (200 OK)
```json
{
  "id": 1,
  "user_id": 5,
  "department_id": 2,
  "province_id": 2,
  "district_id": 2,
  "address": "Nueva Dirección 456, Piso 2",
  "birthday": "1990-05-15",
  "genere": 1,
  "have_cert_disability": false,
  "file_cert_disability": null,
  "have_cert_army": true,
  "file_cert_army": "personal_data_certs/army_1707213400_507a1c2d.pdf",
  "have_cert_professional_credentials": true,
  "file_cert_professional_credentials": "personal_data_certs/professional_credentials_1707213400_507a1c2e.pdf",
  "is_active_cert_professional_credentials": false
}
```

## 4. Descargar Certificado

### Solicitud - Certificado de Discapacidad
```http
GET /accounts/personal-data/certificate/disability
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Solicitud - Certificado Militar
```http
GET /accounts/personal-data/certificate/army
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Solicitud - Credenciales Profesionales
```http
GET /accounts/personal-data/certificate/professional_credentials
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Respuesta Exitosa (200 OK)
```
[Binary PDF content]
Content-Type: application/pdf
Content-Disposition: attachment; filename="disability_1707213234_507a1c2b.pdf"
```

## Errores Comunes

### Error: Usuario no autenticado (401)
```json
{
  "message": "Usuario no autenticado",
  "status": 401
}
```

### Error: El usuario ya tiene información registrada (409)
```json
{
  "message": "Este usuario ya tiene información personal registrada",
  "status": 409
}
```

### Error: No tiene permiso (403)
```json
{
  "message": "No tienes permiso para crear información de otro usuario",
  "status": 403
}
```

### Error: Validación fallida - Archivo inválido (422)
```json
{
  "message": "El archivo debe ser un PDF válido",
  "status": 422
}
```

### Error: Validación fallida - Archivo muy grande (422)
```json
{
  "message": "El archivo no debe exceder 4 MB",
  "status": 422
}
```

### Error: Validación fallida - Campos requeridos (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "department_id": ["El departamento es requerido"],
    "address": ["La dirección es requerida"],
    "birthday": ["La fecha de nacimiento es requerida"],
    "file_cert_disability": ["El certificado de discapacidad es requerido"]
  },
  "status": 422
}
```

### Error: Tipo de certificado inválido (400)
```json
{
  "message": "Tipo de certificado inválido",
  "status": 400
}
```

### Error: Archivo de certificado no existe (404)
```json
{
  "message": "El archivo solicitado no existe",
  "status": 404
}
```

## Flujos Típicos

### 1. Nuevo usuario completa su información personal
```
POST /accounts/personal-data/ → 201 Created
  └─ Respuesta contiene ID y rutas de archivos
```

### 2. Usuario visualiza su información
```
GET /accounts/personal-data/ → 200 OK
  └─ Respuesta contiene toda la información
```

### 3. Usuario descarga un certificado
```
GET /accounts/personal-data/certificate/disability → 200 OK
  └─ Descarga archivo PDF
```

### 4. Usuario actualiza su información y certificados
```
PATCH /accounts/personal-data/ → 200 OK
  └─ Respuesta contiene información actualizada
  └─ Archivos antiguos se eliminan automáticamente
```

### 5. Usuario intenta crear info personal siendo ciudadano diferente (error)
```
POST /accounts/personal-data/ (con user_id diferente) → 403 Forbidden
  └─ "No tienes permiso para crear información de otro usuario"
```

### 6. Usuario intenta crear segunda entrada de información (error)
```
POST /accounts/personal-data/ (segundo intento) → 409 Conflict
  └─ "Este usuario ya tiene información personal registrada"
```

## Notas Técnicas

- Los certificados se almacenan en `storage/app/private/` para acceso seguro
- El API proporciona rutas relativas a los archivos, no URLs públicas
- Los archivos solo se pueden descargar a través del API autenticado
- Los certificados se eliminan automáticamente al actualizar o establecer `have_cert_*` a false
- Todas las operaciones se ejecutan en transacciones para garantizar consistencia
