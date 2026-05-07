# Implémentation Complète : Gestion des Employés avec Dialogs et Toasts

## 📋 Vue d'ensemble

Cette implémentation fournit un système complet de gestion des employés avec :
- ✅ Formulaires en dialogs modaux (PrimeVue)
- ✅ Notifications toast pour toutes les actions CRUD
- ✅ Soft delete avec historique des employés supprimés
- ✅ Restauration et suppression définitive
- ✅ Commentaires en français

---

## 🏗️ Architecture

### Backend (Laravel)

#### Contrôleur : `app/Http/Controllers/EmployeeController.php`

**Méthodes principales :**

1. **`index()`** - Affiche la liste des employés (non supprimés)
   - Utilise `withoutTrashed()` pour exclure les soft-deleted
   - Rend la vue `Employees/IndexDialog`
   - Supporte recherche, filtres par statut et poste

2. **`create()`** - Redirige vers index avec paramètre `openCreateDialog`
   - Ouvre automatiquement le dialog de création

3. **`store(Request $request)`** - Crée un nouvel employé
   - Affiche toast de succès : "L'employé X a été créé avec succès"
   - Affiche toast d'erreur en cas d'exception

4. **`edit(Employee $employee)`** - Redirige vers index avec paramètre `editEmployeeId`
   - Ouvre automatiquement le dialog de modification

5. **`update(Request $request, Employee $employee)`** - Met à jour un employé
   - Affiche toast de succès : "L'employé X a été mis à jour avec succès"

6. **`destroy(Employee $employee)`** - Soft delete (suppression logique)
   - Affiche toast de succès : "L'employé X a été supprimé avec succès"
   - L'employé reste en base de données avec `deleted_at` défini

7. **`changeStatus(Request $request, Employee $employee)`** - Change le statut
   - Affiche toast de succès avec le nouveau statut

8. **`trash(Request $request)`** - Affiche les employés supprimés
   - Utilise `onlyTrashed()` pour récupérer uniquement les soft-deleted
   - Supporte recherche et filtres

9. **`restore(Request $request, $id)`** - Restaure un employé supprimé
   - Utilise `withTrashed()->findOrFail($id)` pour récupérer l'employé supprimé
   - Appelle `restore()` pour annuler la suppression logique
   - Affiche toast de succès

10. **`forceDelete(Request $request, $id)`** - Supprime définitivement
    - Utilise `withTrashed()->findOrFail($id)`
    - Appelle `forceDelete()` pour supprimer complètement de la base de données
    - **Action irréversible !**

### Routes : `routes/web.php`

```php
// Routes pour soft delete
Route::get('/employees-trash', [EmployeeController::class, 'trash'])
    ->name('employees.trash');

// Routes avec ID (pas de model binding) pour accéder aux soft-deleted
Route::patch('/employees/{id}/restore', [EmployeeController::class, 'restore'])
    ->name('employees.restore');
Route::delete('/employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])
    ->name('employees.forceDelete');

// Routes standard CRUD
Route::resource('employees', EmployeeController::class);
Route::patch('/employees/{employee}/status', [EmployeeController::class, 'changeStatus'])
    ->name('employees.changeStatus');
```

---

## 🎨 Frontend (Vue 3 + PrimeVue)

### 1. Layout : `resources/js/Layouts/AuthenticatedLayout.vue`

**Système de toasts :**

```javascript
// Composant Toast PrimeVue
<Toast position="top-right" />

// Service toast
const toast = useToast();

// Affiche les messages flash du serveur
const displayFlashToasts = () => {
    const flash = page.props.flash;
    
    if (flash.success) {
        toast.add({
            severity: 'success',
            summary: 'Succès',
            detail: flash.success,
            life: 4000,
        });
    }
    // ... error, info, warning
};

// Surveille les changements de page.props
watch(() => page.props, () => {
    displayFlashToasts();
}, { deep: true });
```

**Types de toasts :**
- ✅ **success** (vert) - Création, modification, suppression, restauration
- ❌ **error** (rouge) - Exceptions serveur
- ℹ️ **info** (bleu) - Messages informatifs
- ⚠️ **warning** (orange) - Avertissements

### 2. Page principale : `resources/js/Pages/Employees/IndexDialog.vue`

**Fonctionnalités :**

- **DataTable PrimeVue** avec colonnes :
  - Matricule
  - Nom complet + Email
  - Poste
  - Téléphone
  - Salaire (formaté en XOF)
  - Statut (Tag coloré)
  - Actions (Voir, Modifier, Changer statut, Supprimer)

- **Filtres** :
  - Recherche par terme
  - Filtre par statut (Actif, Inactif, Suspendu)
  - Filtre par poste

- **Dialogs modaux** :
  - Dialog de création d'employé
  - Dialog de modification d'employé
  - Dialog de confirmation de suppression
  - Dialog de confirmation de changement de statut

- **Pagination** :
  - Synchronisée avec Laravel
  - 10 employés par page (configurable)

**Ouverture automatique des dialogs :**

```javascript
onMounted(() => {
    // Ouvre le dialog de création si le serveur a envoyé openCreateDialog
    if (page.props.flash?.openCreateDialog) {
        createDialog.value = true;
    }

    // Ouvre le dialog de modification si le serveur a envoyé editEmployeeId
    if (page.props.flash?.editEmployeeId) {
        const employeeId = page.props.flash.editEmployeeId;
        const employee = props.employees.data.find(e => e.id === employeeId);
        if (employee) {
            employeeToEdit.value = employee;
            editDialog.value = true;
        }
    }
});
```

### 3. Composant formulaire : `resources/js/Components/EmployeeForm.vue`

**Sections :**

1. **Informations personnelles**
   - Prénom (requis)
   - Nom (requis)
   - Date de naissance
   - Email (requis)
   - Téléphone

2. **Adresse**
   - Textarea pour l'adresse complète

3. **Informations professionnelles**
   - Poste (requis, dropdown)
   - Salaire de base (requis, InputNumber avec devise XOF)
   - Statut (requis, dropdown)

**Validation :**
- Affiche les erreurs sous chaque champ
- Classe `p-invalid` sur les champs en erreur
- Boutons désactivés pendant l'envoi

### 4. Page trash : `resources/js/Pages/Employees/Trash.vue`

**Affiche les employés supprimés avec :**

- Colonnes : Matricule, Nom, Poste, Statut, Date de suppression
- Filtres : Recherche, Filtre par poste
- Actions :
  - **Restaurer** - Annule la suppression logique
  - **Supprimer définitivement** - Suppression irréversible

**Dialogs de confirmation :**
- Confirmation avant restauration
- Confirmation avant suppression définitive (avec avertissement)

---

## 🔄 Flux de données

### Création d'employé

```
1. Utilisateur clique "Nouvel Employé"
   ↓
2. openCreateDialog = true (dialog s'ouvre)
   ↓
3. Utilisateur remplit le formulaire et clique "Enregistrer"
   ↓
4. form.post(route('employees.store'))
   ↓
5. EmployeeController::store() crée l'employé
   ↓
6. redirect()->with('success', 'Message...')
   ↓
7. AuthenticatedLayout détecte le changement de page.props
   ↓
8. displayFlashToasts() affiche le toast de succès
   ↓
9. DataTable se rafraîchit automatiquement
```

### Suppression d'employé

```
1. Utilisateur clique "Supprimer"
   ↓
2. Dialog de confirmation s'ouvre
   ↓
3. Utilisateur confirme
   ↓
4. router.delete(route('employees.destroy', id))
   ↓
5. EmployeeController::destroy() effectue soft delete
   ↓
6. redirect()->with('success', 'Message...')
   ↓
7. Toast de succès s'affiche
   ↓
8. Employé disparaît de la liste principale
```

### Restauration d'employé

```
1. Utilisateur va à "Employés Supprimés"
   ↓
2. Clique "Restaurer" sur un employé
   ↓
3. Dialog de confirmation s'ouvre
   ↓
4. Utilisateur confirme
   ↓
5. router.patch(route('employees.restore', id))
   ↓
6. EmployeeController::restore() appelle restore()
   ↓
7. redirect()->with('success', 'Message...')
   ↓
8. Toast de succès s'affiche
   ↓
9. Employé réapparaît dans la liste principale
```

---

## 📦 Configuration PrimeVue

### `resources/js/app.js`

```javascript
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import Tooltip from "primevue/tooltip";
import Aura from "@primeuix/themes/aura";

app.use(PrimeVue, {
    theme: {
        preset: Aura,
    },
})
.use(ToastService);

app.directive("tooltip", Tooltip);
```

### Composants PrimeVue utilisés

- `Button` - Boutons d'action
- `DataTable` - Tableau avec pagination
- `Column` - Colonnes du tableau
- `Dialog` - Dialogs modaux
- `InputText` - Champs texte
- `InputNumber` - Champs numériques
- `Dropdown` - Listes déroulantes
- `Calendar` - Sélecteur de date
- `Textarea` - Zones de texte multiligne
- `Tag` - Badges de statut
- `Toolbar` - Barre d'outils pour filtres
- `Toast` - Notifications toast

---

## 🗄️ Modèle : `app/Models/Employee.php`

**Traits utilisés :**

```php
use SoftDeletes;

protected $dates = ['deleted_at'];
```

**Scopes utilisés :**

```php
// Recherche par terme
public function scopeSearch($query, $term)
{
    return $query->where('first_name', 'like', "%{$term}%")
                 ->orWhere('last_name', 'like', "%{$term}%")
                 ->orWhere('email', 'like', "%{$term}%");
}

// Filtre par statut
public function scopeByStatus($query, $status)
{
    return $query->where('status', $status);
}

// Filtre par poste
public function scopeByPosition($query, $positionId)
{
    return $query->where('position_id', $positionId);
}
```

---

## 🎯 Points clés

### 1. Soft Delete vs Force Delete

- **Soft Delete** (`destroy()`) : Définit `deleted_at`, l'employé reste en base
- **Force Delete** (`forceDelete()`) : Supprime complètement de la base de données

### 2. Récupération des employés supprimés

```php
// Exclure les soft-deleted
Employee::withoutTrashed()

// Récupérer uniquement les soft-deleted
Employee::onlyTrashed()

// Récupérer tous (y compris soft-deleted)
Employee::withTrashed()
```

### 3. Routes avec ID au lieu de model binding

Les routes de restauration et suppression définitive utilisent `{id}` au lieu de `{employee}` car le model binding ne peut pas récupérer les soft-deleted :

```php
// ❌ Ne fonctionne pas pour les soft-deleted
Route::patch('/employees/{employee}/restore', ...)

// ✅ Fonctionne avec withTrashed()
Route::patch('/employees/{id}/restore', ...)
```

### 4. Messages flash et toasts

Tous les messages flash sont définis côté serveur et affichés automatiquement côté client :

```php
// Backend
return redirect()->with('success', 'Message...');

// Frontend (automatique)
// Le layout détecte le changement et affiche le toast
```

---

## 🚀 Utilisation

### Démarrer l'application

```bash
# Terminal 1 : Serveur Laravel
php artisan serve

# Terminal 2 : Vite dev server
npm run dev
```

### Accéder à la gestion des employés

```
http://localhost:8000/employees
```

### Actions disponibles

1. **Créer** : Cliquer "Nouvel Employé" → Remplir le formulaire → "Enregistrer"
2. **Modifier** : Cliquer "Modifier" → Modifier les données → "Enregistrer"
3. **Voir** : Cliquer "Voir" → Affiche la page de détails
4. **Changer statut** : Sélectionner un nouveau statut dans le dropdown
5. **Supprimer** : Cliquer "Supprimer" → Confirmer
6. **Restaurer** : Aller à "Employés Supprimés" → Cliquer "Restaurer" → Confirmer
7. **Supprimer définitivement** : Aller à "Employés Supprimés" → Cliquer "Supprimer" → Confirmer

---

## 📝 Commentaires en français

Tous les fichiers contiennent des commentaires en français expliquant :

- Le rôle de chaque fonction
- Le flux de données
- Les cas d'usage
- Les points importants

---

## ✅ Checklist de vérification

- [x] Dialogs modaux pour création/modification
- [x] Toasts PrimeVue pour toutes les actions
- [x] Soft delete avec historique
- [x] Restauration et suppression définitive
- [x] Filtres et recherche
- [x] Pagination
- [x] Validation des formulaires
- [x] Commentaires en français
- [x] Gestion des erreurs
- [x] Confirmations avant actions destructives

---

## 🐛 Dépannage

### Les toasts ne s'affichent pas

1. Vérifier que `<Toast position="top-right" />` est dans `AuthenticatedLayout.vue`
2. Vérifier que `ToastService` est enregistré dans `app.js`
3. Vérifier que le serveur envoie les messages flash
4. Vérifier la console du navigateur pour les erreurs

### Les dialogs ne s'ouvrent pas

1. Vérifier que `createDialog` et `editDialog` sont des `ref(false)`
2. Vérifier que `v-model:visible` est lié correctement
3. Vérifier que les fonctions `openCreateDialog()` et `openEditDialog()` sont appelées

### Les employés supprimés ne réapparaissent pas

1. Vérifier que `restore()` est appelé sur le modèle
2. Vérifier que la route utilise `{id}` et non `{employee}`
3. Vérifier que le contrôleur utilise `withTrashed()->findOrFail($id)`

---

## 📚 Fichiers modifiés/créés

- ✅ `app/Http/Controllers/EmployeeController.php` - Logique CRUD complète
- ✅ `routes/web.php` - Routes pour soft delete
- ✅ `resources/js/Pages/Employees/IndexDialog.vue` - Page principale avec dialogs
- ✅ `resources/js/Components/EmployeeForm.vue` - Formulaire réutilisable
- ✅ `resources/js/Pages/Employees/Trash.vue` - Page des employés supprimés
- ✅ `resources/js/Layouts/AuthenticatedLayout.vue` - Système de toasts
- ✅ `resources/js/app.js` - Configuration PrimeVue

---

**Implémentation complétée le 7 mai 2026** ✨
