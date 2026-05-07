# 🔧 Commandes à exécuter

## 🚀 Démarrer l'application

### Terminal 1 : Serveur Laravel

```bash
php artisan serve
```

**Résultat attendu** :
```
Laravel development server started: http://127.0.0.1:8000
```

### Terminal 2 : Serveur Vite

```bash
npm run dev
```

**Résultat attendu** :
```
  VITE v5.x.x  ready in xxx ms

  ➜  Local:   http://localhost:5173/
  ➜  press h to show help
```

---

## 🌐 Accéder à l'application

### URL principale
```
http://localhost:8000
```

### Gestion des employés
```
http://localhost:8000/employees
```

### Employés supprimés
```
http://localhost:8000/employees-trash
```

---

## 🧪 Tester les fonctionnalités

### 1. Créer un employé

```bash
# Cliquer sur "Nouvel Employé"
# Remplir le formulaire
# Cliquer "Enregistrer"
# Vérifier le toast vert
```

### 2. Modifier un employé

```bash
# Cliquer "Modifier" sur un employé
# Modifier les données
# Cliquer "Enregistrer"
# Vérifier le toast vert
```

### 3. Supprimer un employé

```bash
# Cliquer "Supprimer" sur un employé
# Confirmer dans le dialog
# Vérifier le toast vert
# Vérifier que l'employé disparaît
```

### 4. Restaurer un employé

```bash
# Aller à "Employés Supprimés"
# Cliquer "Restaurer" sur un employé
# Confirmer dans le dialog
# Vérifier le toast vert
# Vérifier que l'employé réapparaît
```

---

## 🔍 Vérifier les erreurs

### Console du navigateur
```
F12 → Console
```

**Vérifier qu'il n'y a pas d'erreurs rouges**

### Logs Laravel
```bash
tail -f storage/logs/laravel.log
```

**Vérifier qu'il n'y a pas d'erreurs**

### Logs Vite
```
Vérifier le terminal où npm run dev est exécuté
```

**Vérifier qu'il n'y a pas d'erreurs rouges**

---

## 🧹 Nettoyer le cache

### Vite cache
```bash
# Supprimer le cache Vite
rm -r node_modules/.vite

# Ou sur Windows
Remove-Item -Path "node_modules/.vite" -Recurse -Force
```

### Laravel cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Navigateur
```
F12 → Application → Clear site data
```

---

## 🔄 Redémarrer l'application

### Arrêter les serveurs
```bash
# Terminal 1 : Ctrl+C
# Terminal 2 : Ctrl+C
```

### Redémarrer
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

---

## 📦 Installer les dépendances (si nécessaire)

### NPM
```bash
npm install
```

### Composer
```bash
composer install
```

---

## 🗄️ Base de données

### Créer les tables
```bash
php artisan migrate
```

### Remplir avec des données de test
```bash
php artisan db:seed
```

### Réinitialiser la base de données
```bash
php artisan migrate:fresh --seed
```

---

## 📝 Fichiers importants

### Configuration
- `config/app.php` - Configuration générale
- `config/database.php` - Configuration base de données
- `.env` - Variables d'environnement

### Code
- `app/Http/Controllers/EmployeeController.php` - Logique CRUD
- `routes/web.php` - Routes
- `resources/js/Pages/Employees/IndexDialog.vue` - Page principale
- `resources/js/Components/EmployeeForm.vue` - Formulaire

### Documentation
- `QUICK_START.md` - Guide de démarrage
- `IMPLEMENTATION_SUMMARY.md` - Documentation technique
- `VERIFICATION_CHECKLIST.md` - Checklist
- `FINAL_STATUS.md` - Statut final

---

## 🆘 Dépannage rapide

### Les toasts ne s'affichent pas
```bash
# Redémarrer Vite
npm run dev
```

### Les dialogs ne s'ouvrent pas
```bash
# Rafraîchir la page
F5
```

### Erreur "Employé non trouvé"
```bash
# Vérifier les logs
tail -f storage/logs/laravel.log
```

### Erreur Vite
```bash
# Nettoyer le cache
rm -r node_modules/.vite

# Redémarrer
npm run dev
```

---

## ✅ Checklist de démarrage

- [ ] Serveur Laravel en cours d'exécution
- [ ] Serveur Vite en cours d'exécution
- [ ] Pas d'erreurs dans la console (F12)
- [ ] Pas d'erreurs dans le terminal Vite
- [ ] Page `http://localhost:8000/employees` charge
- [ ] Tableau affiche les employés
- [ ] Bouton "Nouvel Employé" fonctionne
- [ ] Dialog de création s'ouvre
- [ ] Toast s'affiche après création

---

**Vous êtes prêt à commencer ! 🚀**
