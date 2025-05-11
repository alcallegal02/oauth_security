# oauth_security

## Configuración previa en Keycloak

Accedemos al login de keycloak (http://localhost:8080) con las credenciales usuario admin y contraseña admin:

![image](https://github.com/user-attachments/assets/84c9be3f-2695-41cf-aff6-9ecc9d020b61)

Creamos un nuevo Realm:

![image](https://github.com/user-attachments/assets/2afe3978-05c5-4193-9e5c-84088baa595b)

Vamos ahora a crear un nuevo cliente OAuth:

![image](https://github.com/user-attachments/assets/7dd9767b-84c5-4fe9-886d-938d5fc8b98d)

Dejamos "Client authentication" en "Off" (esto lo configura como cliente público, lo cual es común para PKCE y demostraciones donde no se maneja un client_secret de forma segura en el lado del cliente PHP directamente):

![image](https://github.com/user-attachments/assets/a925ee9b-5fd4-45b0-8d1d-b29dc29625e8)

Añadimos las URLs de callback para ambas versiones:

![image](https://github.com/user-attachments/assets/8379ba6e-d20b-4e2c-a571-b7d6890a5398)

Creamos por último un usuario de prueba:

![image](https://github.com/user-attachments/assets/17a471f2-3d2e-40d0-a540-d9cb0cbb60f8)

![image](https://github.com/user-attachments/assets/525b0600-be17-4a04-9bb4-eb157100dae0)

## Demostración de la Vulnerabilidad

Nos centraremos en dos vulnerabilidades principales con la versión vulnerable:

- Ausencia de state: Permite ataques CSRF.
- Exposición del code en la URL y en la respuesta HTML: El código de autorización es visible.

Accedemos a la URL(http://localhost:8000/vulnerable/oauth_login.php) y nos logueamos con el usuario que hemos creado previamente en keycloak:

![image](https://github.com/user-attachments/assets/836d61b3-96bc-4258-8d18-aea5044ed559)

![image](https://github.com/user-attachments/assets/1152dedc-2389-4a04-bc02-0e12c1256a11)

Como vemos expone el código.

Esta vulnerabilidad CSRF aquí significa que un atacante podría engañar a un usuario ya autenticado en Keycloak para que visite una URL maliciosa. Esta URL maliciosa iniciaría un flujo OAuth hacia la aplicación vulnerable (myapp) pero usando el contexto de autenticación del usuario. Si el atacante pudiera de alguna manera predecir o interceptar un code válido (quizás de otro flujo), podría intentar inyectarlo.

Sin embargo, el impacto más directo de la falta de state es que la aplicación cliente no tiene forma de verificar que la respuesta de redirección de Keycloak corresponde a una solicitud que ella misma inició. Un atacante podría intentar iniciar un flujo de autorización y luego engañar al usuario para que complete ese flujo, haciendo que el code se envíe al redirect_uri del atacante si este pudiera registrar uno, o si el redirect_uri es vulnerable.

## Mitigación

Una vez que hemos comprobar la vulnerabilidad, vamos a mitigarla. Para elo abrimos al dirección del login mitigado (http://localhost:8000/mitigated/oauth_login.php), este acript ahora debería incluir los parámetros state, code_challenge, y code_challenge_method=S256. Iniciamos sesión con el usuario y contraseña creadoa anteriormente y nos redirigirá al callback mitigado:

![image](https://github.com/user-attachments/assets/1dfc2f62-1c7a-495c-9d31-88a4790bfee1)

Como vemos la autenticación sería exitosa.

Para comprobar que funciona correctamente esta verificación de la autenticación podemos modificar los parámetros de la URL:

![image](https://github.com/user-attachments/assets/8e247d18-f270-4265-8db2-add7c29b65d4)

Como vemos nos muestra error porque el estado de la URL no coincide con el estado con el que se guardó la sesión
