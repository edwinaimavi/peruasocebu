# Regla de Codificacion y Textos

Para todos los modulos actuales y nuevos de PeruAsoCebu:

- Guardar archivos `.php`, `.blade.php`, `.js`, `.css` y `.json` como UTF-8.
- Mantener `<meta charset="UTF-8">` o `<meta charset="utf-8">` en el `<head>` antes de CSS y JS.
- Usar base de datos MySQL con `utf8mb4` y `utf8mb4_unicode_ci`.
- No usar `utf8_encode()` ni `utf8_decode()`.
- No guardar nombres, razones sociales, direcciones, notas o descripciones como HTML escapado (`&amp;`, `&ntilde;`, etc.).
- En controladores JSON usar `response()->json()` normal.
- Si se usa `json_encode()` manualmente, pasar `JSON_UNESCAPED_UNICODE`.
- En DataTables, devolver texto limpio desde backend para campos normales y escapar en frontend con `$.fn.dataTable.render.text()`.
- Reservar HTML en backend solo para badges, botones, iconos y acciones, y declararlo en `rawColumns()`.
- Para textos con tildes dentro de JavaScript que hayan dado problemas, usar escapes Unicode (`Atenci\u00f3n`, `d\u00edgitos`).
- Antes de terminar un modulo, probar `ñ`, `Ñ`, tildes, `ü` y `&` en listados, modales, alertas y respuestas AJAX.
