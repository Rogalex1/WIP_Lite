# ✅ Checklist de Vérification

Utilisez cette checklist pour vérifier que tout fonctionne correctement.

---

## 🔧 Configuration

- [ ] Serveur Laravel en cours d'exécution (`php artisan serve`)
- [ ] Serveur Vite en cours d'exécution (`npm run dev`)
- [ ] Pas d'erreurs dans la console du navigateur (F12)
- [ ] Pas d'erreurs dans le terminal Vite

---

## 📄 Fichiers créés/modifiés

### Backend

- [ ] `app/Http/Controllers/EmployeeController.php` - Contient toutes les méthodes CRUD
- [ ] `routes/web.php` - Routes pour soft delete et CRUD
- [ ] `app/Models/Employee.php` - Trait SoftDeletes activé

### Frontend

- [ ] `resources/js/Pages/Employees/IndexDialog.vue` - Page principale avec dialogs
- [ ] `resources/js/Components/EmployeeForm.vue` - Formulaire réutilisable
- [ ] `resources/js/Pages/Employees/Trash.vue` - Page des employés supprimés
- [ ] `resources/js/Layouts/AuthenticatedLayout.vue` - Système de toasts
- [ ] `resources/js/app.js` - Configuration PrimeVue

---

## 🎯 Fonctionnalités à tester

### 1. Affichage de la liste

- [ ] Accéder à `http://localhost:8000/employees`
- [ ] La page charge sans erreur
- [ ] Le tableau affiche les employés
- [ ] Les colonnes sont visibles : Matricule, Nom, Poste, Téléphone, Salaire, Statut, Actions

### 2. Création d'employé

- [ ] Cliquer "Nouvel Employé"
- [ ] Un dialog modal s'ouvre
- [ ] Le formulaire affiche tous les champs
- [ ] Remplir les champs requis
- [ ] Cliquer "Enregistrer"
- [ ] Un toast vert s'affiche : "L'employé X a été créé avec succès"
- [ ] Le nouvel employé apparaît dans le tableau
- [ ] Le dialog se ferme automatiquement

### 3. Modification d'employé

- [ ] Cliquer "Modifier" sur un employé
- [ ] Un dialog modal s'ouvre avec les données de l'employé
- [ ] Modifier un champ
- [ ] Cliquer "Enregistrer"
- [ ] Un toast vert s'affiche : "L'employé X a été mis à jour avec succès"
- [ ] Les modifications sont visibles dans le tableau
- [ ] Le dialog se ferme automatiquement

### 4. Changement de statut

- [ ] Cliquer sur le dropdown "Statut" d'un employé
- [ ] Sélectionner un nouveau statut
- [ ] Un dialog de confirmation s'ouvre
- [ ] Cliquer "Confirmer"
- [ ] Un toast vert s'affiche avec le nouveau statut
- [ ] Le statut change dans le tableau

### 5. Suppression d'employé (soft delete)

- [ ] Cliquer "Supprimer" sur un employé
- [ ] Un dialog de confirmation s'ouvre
- [ ] Cliquer "Supprimer"
- [ ] Un toast vert s'affiche : "L'employé X a été supprimé avec succès"
- [ ] L'employé disparaît du tableau
- [ ] L'employé n'apparaît plus dans la liste principale

### 6. Recherche

- [ ] Taper un nom dans le champ "Rechercher..."
- [ ] Le tableau se filtre automatiquement
- [ ] Seuls les employés correspondants s'affichent
- [ ] Effacer la recherche
- [ ] Tous les employés réapparaissent

### 7. Filtres

- [ ] Sélectionner un statut dans le dropdown "Statut"
- [ ] Le tableau se filtre par statut
- [ ] Sélectionner un poste dans le dropdown "Poste"
- [ ] Le tableau se filtre par poste
- [ ] Réinitialiser les filtres
- [ ] Tous les employés réapparaissent

### 8. Pagination

- [ ] Vérifier que le tableau affiche 10 employés par page
- [ ] Cliquer sur la page 2
- [ ] Les employés suivants s'affichent
- [ ] Cliquer sur "Première page"
- [ ] Revenir à la première page

### 9. Page des employés supprimés

- [ ] Cliquer "Employés Supprimés" (en haut à droite)
- [ ] La page "Historique des Employés Supprimés" s'ouvre
- [ ] Les employés supprimés s'affichent
- [ ] La colonne "Date de suppression" affiche la date
- [ ] Les boutons "Restaurer" et "Supprimer" sont visibles

### 10. Restauration d'employé

- [ ] Cliquer "Restaurer" sur un employé supprimé
- [ ] Un dialog de confirmation s'ouvre
- [ ] Cliquer "Restaurer"
- [ ] Un toast vert s'affiche : "L'employé X a été restauré avec succès"
- [ ] L'employé disparaît de la page "Employés Supprimés"
- [ ] L'employé réapparaît dans la liste principale

### 11. Suppression définitive

- [ ] Cliquer "Supprimer" sur un employé supprimé
- [ ] Un dialog de confirmation s'ouvre avec un avertissement
- [ ] Cliquer "Supprimer définitivement"
- [ ] Un toast vert s'affiche : "L'employé X a été supprimé définitivement"
- [ ] L'employé disparaît de la page "Employés Supprimés"
- [ ] L'employé n'apparaît nulle part (suppression complète)

### 12. Validation du formulaire

- [ ] Essayer de soumettre le formulaire sans remplir les champs requis
- [ ] Des messages d'erreur s'affichent sous les champs
- [ ] Les champs en erreur sont marqués en rouge
- [ ] Remplir les champs requis
- [ ] Les messages d'erreur disparaissent

### 13. Voir les détails d'un employé

- [ ] Cliquer "Voir" sur un employé
- [ ] La page de détails s'ouvre
- [ ] Les informations de l'employé s'affichent
- [ ] Cliquer "Retour" ou utiliser le bouton retour du navigateur
- [ ] Revenir à la liste

---

## 🎨 Vérification de l'interface

### Toasts

- [ ] Les toasts s'affichent en haut à droite
- [ ] Les toasts de succès sont verts
- [ ] Les toasts d'erreur sont rouges
- [ ] Les toasts disparaissent après quelques secondes
- [ ] Les toasts affichent le bon message

### Dialogs

- [ ] Les dialogs sont modaux (fond grisé)
- [ ] Les dialogs sont centrés à l'écran
- [ ] Les dialogs ont un titre
- [ ] Les dialogs ont des boutons d'action
- [ ] Cliquer en dehors du dialog le ferme (optionnel)

### Tableau

- [ ] Le tableau est responsive
- [ ] Les colonnes sont bien alignées
- [ ] Les lignes alternent de couleur (striped)
- [ ] Les boutons d'action sont visibles
- [ ] La pagination fonctionne

### Formulaire

- [ ] Les champs sont bien espacés
- [ ] Les labels sont clairs
- [ ] Les champs requis sont marqués avec *
- [ ] Les erreurs s'affichent en rouge
- [ ] Les boutons sont bien positionnés

---

## 🐛 Dépannage

### Les toasts ne s'affichent pas

**Vérifier :**
- [ ] Le serveur Vite est en cours d'exécution
- [ ] La console du navigateur n'affiche pas d'erreurs
- [ ] `<Toast position="top-right" />` est dans `AuthenticatedLayout.vue`
- [ ] `ToastService` est enregistré dans `app.js`

**Solution :**
```bash
# Redémarrer Vite
npm run dev
```

### Les dialogs ne s'ouvrent pas

**Vérifier :**
- [ ] Les variables `createDialog` et `editDialog` sont des `ref(false)`
- [ ] Les fonctions `openCreateDialog()` et `openEditDialog()` sont appelées
- [ ] Pas d'erreurs dans la console

**Solution :**
```bash
# Rafraîchir la page
F5
```

### Les employés supprimés ne réapparaissent pas

**Vérifier :**
- [ ] La route utilise `{id}` et non `{employee}`
- [ ] Le contrôleur utilise `withTrashed()->findOrFail($id)`
- [ ] La méthode `restore()` est appelée

**Solution :**
```bash
# Vérifier les logs Laravel
tail -f storage/logs/laravel.log
```

### Erreur "Employé non trouvé"

**Cause :** L'employé a peut-être été supprimé définitivement

**Solution :**
- [ ] Vérifier dans "Employés Supprimés"
- [ ] Si absent, l'employé a été supprimé définitivement

---

## 📊 Données de test

Pour tester rapidement, créer un employé avec :

- **Prénom** : Jean
- **Nom** : Dupont
- **Email** : jean.dupont@example.com
- **Poste** : Développeur
- **Salaire** : 500000
- **Statut** : Actif

---

## ✨ Résultat attendu

Après avoir complété cette checklist, vous devriez avoir :

- ✅ Une liste d'employés fonctionnelle
- ✅ Des dialogs modaux pour créer/modifier
- ✅ Des toasts pour les notifications
- ✅ Un système de soft delete avec historique
- ✅ La possibilité de restaurer ou supprimer définitivement
- ✅ Des filtres et une recherche
- ✅ Une pagination
- ✅ Une validation des formulaires

---

**Checklist complétée le** : _______________

**Statut** : ☐ Tous les tests réussis ☐ Problèmes détectés

**Notes** : _________________________________________________________________

---

Pour toute question, consulter `IMPLEMENTATION_SUMMARY.md` ou `QUICK_START.md`.
