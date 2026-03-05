# Conectar este proyecto al repositorio controlflota

Este directorio sigue siendo **montajes-campana** en tu disco; el nombre del repo remoto puede ser **controlflota** sin problema.

## 1. Inicializar Git y conectar el remoto

En la raíz del proyecto (esta carpeta), ejecutá en la terminal:

```powershell
cd c:\wamp64\www\montajes-campana

# Crear el repositorio local
git init

# Añadir el remoto (reemplazá la URL por la que te dieron)
git remote add origin https://github.com/TU_ORG_O_USUARIO/controlflota.git

# Ver qué se va a subir (revisá que no aparezca .env ni vendor)
git status
```

## 2. Primer commit y push

```powershell
git add .
git commit -m "Código inicial: Montajes Campaña → controlflota"
git branch -M main
git push -u origin main
```

Si el remoto ya tiene commits (por ejemplo un README creado en GitHub), puede ser necesario hacer antes:

```powershell
git pull origin main --allow-unrelated-histories
```

y luego `git push -u origin main`.

---

## Cursor: qué se conserva

- La carpeta **`.cursor/`** está en el `.gitignore`, así que **no se sube** al repo.
- Todo lo que tengas ahí (planes, reglas, etc.) **sigue en tu máquina** y no se pierde.
- Si en el futuro querés versionar reglas compartidas con el equipo, podés quitar la línea `.cursor/` del `.gitignore`.

El nombre de la carpeta local puede seguir siendo `montajes-campana`; Git solo usa el remoto **controlflota** para push/pull.
