# Rôles & Permissions — Transtechno Cameroun

> Système : **Filament Shield** (bezhansalleh/filament-shield) +
> **Spatie Laravel Permission** (spatie/laravel-permission)

---

## 1. Ce que fait chaque permission

Quand tu ouvres un rôle dans Filament et que tu vois la liste des permissions
pour une ressource (ex. `Cotation`), voici ce que chaque case signifie :

| Nom affiché | Clé interne | Ce que ça permet |
|---|---|---|
| **Voir** | `view_quotation` | Ouvrir la fiche d'**un** enregistrement |
| **Voir tout** | `view_any_quotation` | Accéder à la **liste** (page index) |
| **Créer** | `create_quotation` | Ouvrir le formulaire de création |
| **Mettre à jour** | `update_quotation` | Modifier un enregistrement existant |
| **Supprimer** | `delete_quotation` | Supprimer **un seul** enregistrement |
| **Supprimer tout** | `delete_any_quotation` | Actions de suppression en **masse** |
| ~~Restaurer~~ | `restore_quotation` | ⚠️ INUTILE — voir §3 |
| ~~Restaurer tout~~ | `restore_any_quotation` | ⚠️ INUTILE — voir §3 |
| ~~Forcer la suppression~~ | `force_delete_quotation` | ⚠️ INUTILE — voir §3 |
| ~~Forcer la suppression de tout~~ | `force_delete_any_quotation` | ⚠️ INUTILE — voir §3 |
| ~~Répliquer~~ | `replicate_quotation` | ⚠️ INUTILE — voir §4 |
| ~~Réordonner~~ | `reorder_quotation` | ⚠️ INUTILE — voir §4 |

### Différence "Voir" vs "Voir tout"

- Sans `view_any` → la ressource **n'apparaît pas dans le menu** latéral.
- Avec `view_any` mais sans `view` → l'utilisateur voit la liste mais
  ne peut pas ouvrir une fiche.
- En pratique : toujours cocher les deux ensemble.

---

## 2. Le Guard "web" — qu'est-ce que c'est ?

Un **guard** est le canal d'authentification que Laravel utilise pour identifier
un utilisateur.

Ce projet n'a qu'un seul guard : `web` (sessions navigateur, cookies).
C'est le seul canal utilisé.

**Conséquence pratique :** quand tu crées un rôle dans l'admin, le champ
"Nom du Guard" est pré-rempli à `web` et **ne doit jamais être changé**.
Si quelqu'un le laisse vide ou met autre chose, le rôle ne fonctionnera pas.

> Un second guard (`api`) n'existe que si on ajoute une API REST séparée —
> ce qui n'est pas le cas ici. Filament Auth utilise exclusivement `web`.

---

## 3. Restaurer / Forcer la suppression — PERMISSIONS INUTILES ici

Ces permissions ne servent que si un modèle utilise `SoftDeletes`
(suppression douce : l'enregistrement n'est pas vraiment effacé, juste marqué
`deleted_at`).

**Aucun modèle du projet n'implémente SoftDeletes.**

→ Les permissions `restore_*`, `restore_any_*`, `force_delete_*`,
`force_delete_any_*` sont générées automatiquement par Filament Shield mais
**ne sont jamais vérifiées** par le code. Tu peux les cocher ou les décocher,
ça n'a aucun effet.

**Conseil :** laisser toutes ces cases **décochées** pour simplifier l'interface.
Si SoftDeletes est ajouté plus tard sur un modèle, re-générer les permissions
avec `php artisan shield:generate --all`.

---

## 4. Répliquer / Réordonner — PERMISSIONS INUTILES ici

| Permission | Nécessite |
|---|---|
| `replicate_*` | Un bouton "Dupliquer" (`ReplicateAction`) dans le Resource |
| `reorder_*` | Un glisser-déposer de lignes (`->reorderable()`) dans la table |

**Aucun Resource du projet n'implémente ces fonctionnalités.**
Idem que §3 : généré automatiquement, jamais vérifié, sans effet.

---

## 5. L'onglet "Pages" — utile ou non ?

L'onglet **Pages** liste les pages Filament personnalisées qui ne sont pas
des Resources (ex. : la page Dashboard elle-même, une page de rapport, etc.).

Pour ce projet, il permet de contrôler qui voit **les widgets du dashboard** :
`DashboardStats`, `LatestQuotations`, `OperationsPanel`, etc.
Ces permissions sont **déjà actives** et correctement assignées par rôle.

→ L'onglet est utile si tu veux ajouter une page custom (rapport PDF, import…)
et la restreindre à certains rôles. Pour l'administration quotidienne,
tu n'en as pas besoin.

---

## 6. Rôles existants et leur périmètre

| Rôle | Accès |
|---|---|
| `super_admin` | Tout — bypass automatique de toutes les policies |
| `commercial` | Cotations, clients, produits, catalogue, demandes pôle |
| `technicien_sav` | SAV & Maintenance, parc installé, fiche client (lecture) |
| `editeur_contenu` | Actualités, catégories, médiathèque, messages contact |
| `magasinier` | Catalogue produits (lecture + mise à jour stock), widgets |
| ~~`client_b2b`~~ | ~~Aucune permission assignée — rôle vide, non utilisé~~ — **Supprimé** |

---

## 7. Incohérences détectées — ✅ CORRIGÉES (juin 2026)

### 7.1 ✅ `client_b2b` — rôle supprimé

Rôle vide (0 utilisateur, 0 permission). **Supprimé** via tinker.
Si un espace client B2B est créé à l'avenir, recréer le rôle depuis l'interface.

### 7.2 ✅ `commercial` — `force_delete_*` retiré

Les 16 permissions `force_delete_*` ont été révoquées du rôle `commercial`.
Un commercial ne peut plus déclencher de suppression définitive (même si SoftDeletes est activé plus tard).

### 7.3 ✅ `editeur_contenu` — accès aux messages de contact retiré

Les permissions `view_contact::message` et `view_any_contact::message` ont été
révoquées de `editeur_contenu`. Un éditeur de contenu ne voit plus les messages
des clients depuis le formulaire de contact.

### 7.4 ✅ `replicate_*` et `reorder_*` révoqués de tous les rôles

26 permissions révoquées de tous les rôles (hors super_admin).
Ces cases apparaissent encore **décochées** dans l'interface Shield — c'est normal,
elles sont toujours dans la table `permissions` mais n'appartiennent à aucun rôle.
Pour les masquer complètement de l'UI, supprimer les entrées correspondantes
dans la table `permissions` (elles seront recréées par `shield:generate --all`).

---

## 8. Comment mettre à jour les permissions après un changement de code

Si on ajoute un nouveau Resource, une nouvelle Page ou un nouveau Widget :

```bash
# Régénère les permissions pour toutes les ressources
php artisan shield:generate --all

# Puis mettre à jour les rôles dans l'admin Filament
```

Le guard sera toujours `web`, les nouveaux enregistrements apparaîtront
dans l'interface de gestion des rôles.

---

## 9. Permissions minimales recommandées par rôle

### `commercial`
Cocher : `view`, `view_any`, `create`, `update`, `delete`, `delete_any`
Décocher : tout le reste (restore, force_delete, replicate, reorder)

### `technicien_sav`
Cocher : `view`, `view_any`, `create`, `update` sur sav_ticket + equipment_inventory + client (lecture seule)
Décocher : delete, et tout le reste

### `editeur_contenu`
Cocher : `view`, `view_any`, `create`, `update`, `delete` sur news, media, news_category
Décocher : contact_message si non voulu, et tout le reste

### `magasinier`
Cocher : `view`, `view_any`, `update` sur product uniquement + widgets
Décocher : tout le reste
