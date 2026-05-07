# Guide de Démarrage Rapide

## 🚀 Démarrer l'application

### 1. Démarrer le serveur Laravel

```bash
php artisan serve
```

L'application sera accessible à `http://localhost:8000`

### 2. Démarrer le serveur Vite (dans un autre terminal)

```bash
npm run dev
```

Vite compilera les fichiers Vue et CSS en temps réel.

---

## 📍 Accéder à la gestion des employés

```
http://localhost:8000/employees
```

---

## 🎯 Actions principales

### ✅ Créer un employé

1. Cliquer sur le bouton **"Nouvel Employé"** (en haut à droite)
2. Un dialog modal s'ouvre avec le formulaire
3. Remplir les champs requis :
   - Prénom
   - Nom
   - Email
   - Poste
   - Salaire de base
   - Statut
4. Cliquer **"Enregistrer"**
5. Un toast vert s'affiche : "L'employé X a été créé avec succès"
6. Le nouvel employé apparaît dans le tableau

### ✏️ Modifier un employé

1. Cliquer sur le bouton **"Modifier"** (dans la colonne Actions)
2. Un dialog modal s'ouvre avec les données de l'employé
3. Modifier les champs souhaités
4. Cliquer **"Enregistrer"**
5. Un toast vert s'affiche : "L'employé X a été mis à jour avec succès"

### 👁️ Voir les détails d'un employé

1. Cliquer sur le bouton **"Voir"** (dans la colonne Actions)
2. La page de détails s'ouvre

### 🔄 Changer le statut d'un employé

1. Cliquer sur le dropdown **"Statut"** (dans la colonne Actions)
2. Sélectionner un nouveau statut (Actif, Inactif, Suspendu)
3. Un dialog de confirmation s'ouvre
4. Cliquer **"Confirmer"**
5. Un toast vert s'affiche avec le nouveau statut

### 🗑️ Supprimer un employé (soft delete)

1. Cliquer sur le bouton **"Supprimer"** (dans la colonne Actions)
2. Un dialog de confirmation s'ouvre
3. Cliquer **"Supprimer"**
4. Un toast vert s'affiche : "L'employé X a été supprimé avec succès"
5. L'employé disparaît de la liste principale
6. L'employé est maintenant dans "Employés Supprimés"

### 🔍 Rechercher des employés

1. Utiliser le champ **"Rechercher..."** (en haut du tableau)
2. Taper le nom, prénom ou email
3. Le tableau se filtre automatiquement

### 📊 Filtrer par statut

1. Utiliser le dropdown **"Statut"** (en haut du tableau)
2. Sélectionner un statut (Actif, Inactif, Suspendu)
3. Le tableau se filtre automatiquement

### 🏢 Filtrer par poste

1. Utiliser le dropdown **"Poste"** (en haut du tableau)
2. Sélectionner un poste
3. Le tableau se filtre automatiquement

---

## 🗑️ Gestion des employés supprimés

### Accéder à l'historique

1. Cliquer sur le bouton **"Employés Supprimés"** (en haut à droite)
2. La page "Historique des Employés Supprimés" s'ouvre
3. Affiche tous les employés supprimés avec la date de suppression

### ↩️ Restaurer un employé

1. Cliquer sur le bouton **"Restaurer"** (dans la colonne Actions)
2. Un dialog de confirmation s'ouvre
3. Cliquer **"Restaurer"**
4. Un toast vert s'affiche : "L'employé X a été restauré avec succès"
5. L'employé réapparaît dans la liste principale

### 🔴 Supprimer définitivement

1. Cliquer sur le bouton **"Supprimer"** (dans la colonne Actions)
2. Un dialog de confirmation s'ouvre avec un avertissement
3. Cliquer **"Supprimer définitivement"**
4. Un toast vert s'affiche : "L'employé X a été supprimé définitivement"
5. **⚠️ L'employé est complètement supprimé de la base de données (irréversible)**

---

## 🎨 Types de notifications (Toasts)

### ✅ Succès (Vert)

Affichée après :
- Création d'un employé
- Modification d'un employé
- Suppression d'un employé
- Restauration d'un employé
- Changement de statut

**Durée** : 4 secondes

### ❌ Erreur (Rouge)

Affichée en cas de :
- Exception serveur
- Validation échouée
- Erreur de base de données

**Durée** : 5 secondes

### ℹ️ Information (Bleu)

Affichée pour les messages informatifs

**Durée** : 4 secondes

### ⚠️ Avertissement (Orange)

Affichée pour les avertissements

**Durée** : 4 secondes

---

## 📋 Champs du formulaire

### Informations personnelles

| Champ | Type | Requis | Notes |
|-------|------|--------|-------|
| Prénom | Texte | ✅ | |
| Nom | Texte | ✅ | |
| Date de naissance | Date | ❌ | Max : aujourd'hui |
| Email | Email | ✅ | Format email valide |
| Téléphone | Texte | ❌ | |

### Adresse

| Champ | Type | Requis | Notes |
|-------|------|--------|-------|
| Adresse | Textarea | ❌ | Adresse complète |

### Informations professionnelles

| Champ | Type | Requis | Notes |
|-------|------|--------|-------|
| Poste | Dropdown | ✅ | Sélectionner dans la liste |
| Salaire de base | Nombre | ✅ | Devise : XOF |
| Statut | Dropdown | ✅ | Actif, Inactif, Suspendu |

---

## 🔧 Dépannage rapide

### Les toasts ne s'affichent pas

**Solution** : Vérifier que le serveur Vite est en cours d'exécution

```bash
npm run dev
```

### Les dialogs ne s'ouvrent pas

**Solution** : Rafraîchir la page (F5)

### Les employés supprimés ne réapparaissent pas après restauration

**Solution** : Rafraîchir la page ou revenir à la liste principale

### Erreur "Employé non trouvé"

**Solution** : L'employé a peut-être été supprimé définitivement. Vérifier dans "Employés Supprimés"

---

## 📞 Support

Pour toute question ou problème, consulter le fichier `IMPLEMENTATION_SUMMARY.md` pour plus de détails techniques.

---

**Dernière mise à jour** : 7 mai 2026
