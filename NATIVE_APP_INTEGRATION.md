# Integración con App Nativa React Native

Esta documentación explica cómo funciona la integración entre el sitio web de Karma y la aplicación móvil React Native.

## Descripción General

El sitio web de Karma puede ser cargado dentro de un WebView de React Native. Cuando esto ocurre, el sitio web notifica automáticamente a la aplicación nativa sobre eventos de autenticación (login y logout) para que la app pueda guardar la sesión localmente.

## Archivos Modificados/Creados

### Archivos JavaScript

1. **`public/js/app/nativeApp.js`** - Módulo principal de comunicación (standalone, no requiere build)
2. **`resources/js/utils/nativeApp.js`** - Versión con ES6 modules (para desarrollo futuro)
3. **`resources/js/app.js`** - Archivo principal que importa el módulo

### Archivos PHP

1. **`app/Http/Controllers/Auth/GoogleAuthController.php`**
   - Modificado método `callback()` para crear token y enviar datos de sesión

2. **`app/Http/Controllers/Api/AuthController.php`**
   - Modificado método `login()` para incluir explícitamente `user_id` en la respuesta

### Vistas

1. **`resources/views/layouts/app.blade.php`**
   - Agregado script de comunicación con app nativa
   - Agregados meta tags con datos de autenticación
   - Agregado listener para detectar logout

## Cómo Funciona

### 1. Detección de WebView

El módulo JavaScript detecta automáticamente si el sitio está corriendo dentro de una app React Native verificando la existencia de `window.ReactNativeWebView`:

```javascript
function isRunningInNativeApp() {
    return typeof window !== 'undefined' &&
           typeof window.ReactNativeWebView !== 'undefined';
}
```

### 2. Login Exitoso (Google OAuth)

Cuando un usuario hace login con Google:

1. El `GoogleAuthController` crea un token de API
2. Guarda el token en la sesión con la clave `native_app_token`
3. Redirige al dashboard con un flag `native_app_login`
4. El layout detecta este flag y ejecuta:

```javascript
window.NativeAppBridge.notifyLoginSuccess(userId, userToken);
```

5. Esto envía un mensaje a la app nativa:

```json
{
    "action": "loginSuccess",
    "userId": "123",
    "userToken": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### 3. Logout

Cuando el usuario hace logout:

1. El formulario de logout intercepta el evento `submit`
2. Antes de enviar el formulario, ejecuta:

```javascript
window.NativeAppBridge.notifyLogout();
```

3. Esto envía un mensaje a la app nativa:

```json
{
    "action": "logout"
}
```

### 4. API Login (para uso desde JavaScript)

Si tu aplicación usa el endpoint de API `/api/auth/login`, la respuesta incluye:

```json
{
    "success": true,
    "data": {
        "user": { ... },
        "user_id": "123",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer"
    }
}
```

Desde JavaScript puedes notificar a la app nativa manualmente:

```javascript
fetch('/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
})
.then(response => response.json())
.then(data => {
    if (data.success && window.NativeAppBridge) {
        window.NativeAppBridge.notifyLoginSuccess(
            data.data.user_id,
            data.data.token
        );
    }
});
```

## API del Bridge

El objeto `window.NativeAppBridge` expone las siguientes funciones:

### `isRunningInNativeApp()`
Retorna `true` si el sitio está corriendo en un WebView de React Native.

```javascript
if (window.NativeAppBridge.isRunningInNativeApp()) {
    console.log('Running in native app!');
}
```

### `notifyLoginSuccess(userId, userToken)`
Notifica a la app nativa de un login exitoso.

**Parámetros:**
- `userId` (string): ID del usuario
- `userToken` (string): Token de autenticación

**Retorna:** `boolean` - true si el mensaje fue enviado exitosamente

```javascript
window.NativeAppBridge.notifyLoginSuccess('123', 'token...');
```

### `notifyLogout()`
Notifica a la app nativa de un logout.

**Retorna:** `boolean` - true si el mensaje fue enviado exitosamente

```javascript
window.NativeAppBridge.notifyLogout();
```

### `autoDetectAndNotify()`
Detecta automáticamente el estado de autenticación desde meta tags y notifica si es necesario.

```javascript
window.NativeAppBridge.autoDetectAndNotify();
```

## Lado de React Native

En tu aplicación React Native, debes configurar el WebView para escuchar mensajes:

```jsx
import { WebView } from 'react-native-webview';

function MyWebView() {
    const handleMessage = (event) => {
        const message = JSON.parse(event.nativeEvent.data);

        if (message.action === 'loginSuccess') {
            // Guardar userId y userToken en AsyncStorage
            saveUserSession(message.userId, message.userToken);
        } else if (message.action === 'logout') {
            // Limpiar sesión local
            clearUserSession();
        }
    };

    return (
        <WebView
            source={{ uri: 'https://your-site.com' }}
            onMessage={handleMessage}
        />
    );
}
```

## Testing

### En Navegador Normal
- El script detectará que no está en WebView
- Los mensajes no se enviarán pero se registrarán en console

### En WebView de React Native
1. Carga tu sitio en el WebView
2. Haz login
3. Verifica que la app reciba el mensaje `loginSuccess`
4. Haz logout
5. Verifica que la app reciba el mensaje `logout`

## Logs de Debug

El módulo genera logs en la consola del navegador:

```
[NativeApp] Bridge initialized - Running in React Native WebView
[App] Fresh login detected, notifying native app
[NativeApp] Notifying login success
[NativeApp] Message sent successfully: {action: "loginSuccess", ...}
```

Para ver estos logs en tu app React Native, habilita el debug del WebView.

## Notas Importantes

1. **Seguridad**: El token se transmite a través del WebView. Asegúrate de usar HTTPS en producción.

2. **Persistencia del Token**: El token se guarda en la sesión de Laravel temporalmente. La app nativa es responsable de guardarlo de forma permanente.

3. **Expiración del Token**: Los tokens de Passport/Sanctum pueden expirar. Implementa lógica de refresh en tu app nativa.

4. **Compatibilidad**: Esta integración es completamente transparente para usuarios de navegador web normal.

## Troubleshooting

### Los mensajes no se envían
- Verifica que `window.ReactNativeWebView` esté definido
- Revisa los logs de la consola
- Asegúrate de que el script `nativeApp.js` se cargue correctamente

### Token no está disponible
- Verifica que el usuario haya hecho login recientemente
- Revisa que la sesión de Laravel esté activa
- Comprueba que Passport esté configurado correctamente

### Error "createToken() method not found"
- Asegúrate de que Laravel Passport esté instalado y configurado
- Verifica que el modelo User use el trait `HasApiTokens`
