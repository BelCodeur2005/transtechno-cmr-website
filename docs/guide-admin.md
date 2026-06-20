# Guide Administrateur — Transtechno Cameroun

> **À qui s'adresse ce guide ?** À toute personne ayant un compte dans l'interface admin du site transtechno-cmr.com — commerciaux, techniciens SAV, éditeurs de contenu, responsable magasin.

---

## Sommaire

1. [Accès et connexion](#1-accès-et-connexion)
2. [Tableau de bord](#2-tableau-de-bord)
3. [Cotations — de la demande au devis envoyé](#3-cotations)
4. [SAV & Maintenance — de l'ouverture à la clôture](#4-sav--maintenance)
5. [Catalogue & Produits](#5-catalogue--produits)
6. [Actualités](#6-actualités)
7. [Médiathèque](#7-médiathèque)
8. [Messages Contact & Demandes Pôle](#8-messages-contact--demandes-pôle)
9. [CRM Clients](#9-crm-clients)
10. [Parc Installé](#10-parc-installé)
11. [Utilisateurs & Rôles](#11-utilisateurs--rôles)
12. [Rôles et droits — ce que chaque rôle peut faire](#12-rôles-et-droits)

---

## 1. Accès et connexion

**URL admin :** `https://transtechno-cmr.com/admin`

Saisissez votre email et votre mot de passe, puis cliquez sur **"Se connecter"**.

Si vous avez oublié votre mot de passe, contactez le super-administrateur — il peut en définir un nouveau depuis **Système → Utilisateurs**. Il n'y a pas de lien "mot de passe oublié" en autonome.

Une fois connecté, vous arrivez sur le **Tableau de bord**.

> Si vous obtenez l'erreur "Ces identifiants ne correspondent pas à nos enregistrements", vérifiez que votre compte a bien un rôle assigné (sans rôle, l'accès à l'admin est refusé même avec un email/mot de passe corrects).

---

## 2. Tableau de bord

Le tableau de bord est la page d'accueil de l'admin. Il se compose de :

### Cartes KPI (en haut)

| Carte | Ce que ça mesure | Précision |
|---|---|---|
| **Cotations en attente** | Nombre de demandes au statut **Nouveau** ou **En cours** | Cliquer dessus ouvre la liste des cotations |
| **Produits au catalogue** | Nombre total de produits actifs | Sous-titre : nombre de réactifs ABM actifs |
| **Interventions SAV (semaine)** | Tickets SAV dont la date d'ouverture est dans la semaine en cours | Sous-titre : urgences actuellement en cours |
| **CA Mensuel (devis gagnés)** | Somme des totaux TTC des cotations au statut "Gagné" ce mois | Comparé au mois précédent avec indicateur de tendance |

Les chiffres se mettent à jour automatiquement à chaque chargement de page.

### Widget "Dernières demandes de cotation"

Tableau des **6 demandes les plus récentes** (toutes cotations confondues, quel que soit le statut). Colonnes : Référence, Projet/Structure, Statut, Date. Cliquer sur une ligne ouvre directement la fiche de traitement. Bouton **"Voir tout"** en haut à droite pour aller à la liste complète.

### Widget "Panel Opérations" (colonne de droite)

Deux sections :

**Alertes stock** — liste les produits nécessitant une attention :
- Produits au statut **Rupture de stock**
- Produits avec **Quantité indicative ≤ 5 unités**

**Activité récente** — fil d'événements mélangé (cotations, tickets SAV, messages contact) triés du plus récent au plus ancien. Utile pour avoir un aperçu rapide de l'activité du site sans aller dans chaque section.

> Si vous ne voyez pas certains widgets, c'est normal — l'accès est restreint selon votre rôle (voir §12).

---

## 3. Cotations

**Navigation :** menu latéral → **Commercial → Cotations**

Le badge rouge à côté de "Cotations" indique le nombre de demandes au statut **Nouveau** — c'est la file d'attente prioritaire à traiter.

### 3.1 D'où vient une demande de cotation ?

Deux sources :
- **Formulaire public "Demander un devis"** sur le site (le client sélectionne des produits, remplit ses infos et envoie). La demande arrive automatiquement au statut **Nouveau**.
- **Création manuelle** par un commercial (appel téléphonique, visite client). Cliquer sur **"Nouvelle cotation"** en haut à droite de la liste.

### 3.2 Lire et traiter une demande (workflow complet)

#### Étape 1 — Trouver la demande nouvelle

Dans la liste des cotations, les demandes **Nouvelles** sont triées en tête (tri par date décroissante). Utiliser le filtre **Statut = Nouveau** pour n'afficher que celles-ci. Cliquer sur la ligne pour ouvrir la fiche.

#### Étape 2 — Section "Suivi de la demande" (en haut de la fiche)

Remplir immédiatement :

| Champ | Action |
|---|---|
| **Statut** | Passer à **"En cours"** pour signifier que quelqu'un traite cette demande |
| **Priorité** | Mettre "Urgent" ou "Critique" si le client a coché "Besoin urgent" dans son formulaire |
| **Commercial assigné** | Sélectionner le commercial qui gère ce dossier |

La **Référence** (#TR-2026-XXX) est générée automatiquement à la création. Ne jamais la modifier.

#### Étape 3 — Section "Demande du client" (lire attentivement)

Cette section est toujours visible (non collapsible) pour forcer sa lecture avant tout traitement.

- **Spécifications / message du client** : le texte libre saisi par le client dans son formulaire. C'est sa demande principale — lire en entier avant de répondre.
- **Services demandés** : cases cochées par le client (Installation & mise en service, Formation, Contrat maintenance, Livraison urgente). Ces choix influencent le prix final et les engagements à prendre.

> Si "Installation & mise en service" est coché ET que la cotation passe à **Gagné**, les équipements sont automatiquement enregistrés dans le Parc Installé du client (à condition que la cotation soit liée à un client CRM — voir §9).

#### Étape 4 — Section "Établissement"

Informations sur le client : nom du contact, nom de la structure, ville, secteur (privé/public/ONG). Ces données viennent du formulaire public ou ont été saisies manuellement. Vérifier la cohérence avec le dossier CRM si le client existe déjà.

#### Étape 5 — Section "Articles demandés"

Tableau des produits demandés, avec quantité et prix unitaire :

| Champ | Description |
|---|---|
| **Produit** | Sélectionner dans le catalogue. Si le produit n'est pas au catalogue, laisser vide et saisir dans "Description manuelle". |
| **Description manuelle** | Nom libre pour un article hors catalogue ou sur-mesure |
| **Quantité** | Ajuster selon la demande réelle du client |
| **Prix unitaire (FCFA)** | **OBLIGATOIRE avant envoi du devis** — saisir le prix de vente pour chaque ligne |

> ⚠️ Si tous les articles sont à 0 FCFA, une alerte orange apparaît en haut de la fiche. Le devis ne peut pas être envoyé sérieusement sans prix remplis.

Ajouter des lignes avec le bouton **"Ajouter un article"** en bas du tableau.

#### Étape 6 — Section "Récapitulatif financier"

| Champ | Description |
|---|---|
| **Sous-total HT** | Calculé automatiquement (somme des lignes × quantités) |
| **Frais de logistique / transport** | Saisir le montant en FCFA (transport douanier, livraison sur site…) |
| **Remise globale (%)** | Pourcentage de remise appliqué sur le sous-total HT |
| **TVA 19.25%** | Cochée par défaut pour tous les clients |
| **Exonération TVA Minsanté** | Cocher si le client est un établissement public agréé Minsanté — la TVA passe à 0% |
| **TOTAL GÉNÉRAL** | Calculé automatiquement : Sous-total HT + Transport − Remise + TVA |

#### Étape 7 — Générer et envoyer le devis PDF

En haut de la fiche, bouton **"Télécharger le devis PDF"**. Ce PDF contient :
- Les coordonnées Transtechno (logo, adresse, agrément Minsanté)
- Les informations de l'établissement client
- Le tableau des articles avec quantités et prix
- Le récapitulatif financier complet (HT, transport, remise, TVA, total TTC)

Procédure :
1. Vérifier que tous les prix sont remplis.
2. Télécharger le PDF.
3. L'envoyer par email au client (adresse email fournie dans "Établissement").
4. Revenir dans la fiche → passer le statut à **"Devis Envoyé"**.

#### Étape 8 — Suivi après envoi

| Scénario | Statut à mettre |
|---|---|
| Client accepte le devis | **Gagné** |
| Client refuse ou ne répond plus | **Perdu** |
| Client demande des modifications | **En cours** + modifier les articles/prix + re-télécharger le PDF |
| Délai de réponse urgent | **Urgent** ou **Critique** |

#### Section "Lien CRM & parc" (collapsée en bas)

- **Client CRM** : lier cette cotation à un client existant dans la base CRM. Cette liaison permet l'historique complet et le déclenchement automatique du Parc Installé.
- Si le client n'existe pas encore dans le CRM, créer d'abord sa fiche dans **Commercial → Clients**, puis revenir ici et lier.

#### Section "Notes internes" (collapsée en bas)

Notes visibles uniquement par l'équipe admin — jamais affichées au client. Utile pour consigner des informations de négociation, des contraintes techniques, ou des remarques de suivi commercial.

### 3.3 Filtres de la liste des cotations

| Filtre | Usage |
|---|---|
| **Ville** | Filtrer par ville du client (Douala, Yaoundé, Garoua…) |
| **Statut** | Voir uniquement les cotations à un statut précis |
| **Structure** | Rechercher par nom d'établissement |
| **Recherche texte** | Chercher par référence (#TR-…) ou nom de contact |

### 3.4 Exports Excel

Deux boutons d'export disponibles en haut de la liste des cotations :

**"Export Pipeline"** — exporte toutes les cotations dans un fichier Excel. Avant le téléchargement, un formulaire permet de filtrer par :
- Statut (Nouveau, En cours, Gagné…)
- Ville
- Année

Utile pour le suivi commercial mensuel ou trimestriel.

**"Export CA"** — exporte un récapitulatif du chiffre d'affaires (cotations gagnées) pour une année donnée. Utile pour le reporting financier.

---

## 4. SAV & Maintenance

**Navigation :** menu latéral → **Technique → SAV & Maintenance**

Le badge orange à côté indique le nombre de tickets **Ouverts ou En cours** — c'est la file d'intervention active.

### 4.1 D'où vient un ticket SAV ?

- **Formulaire public "Services & SAV"** sur le site. Le client décrit la panne, le type d'intervention souhaité, et l'équipement concerné.
- **Création manuelle** par un technicien ou commercial (suite à un appel téléphonique).

### 4.2 Traiter un ticket SAV (workflow complet)

#### Étape 1 — Section "Dispatch" (en haut)

À remplir immédiatement à la réception du ticket :

| Champ | Action |
|---|---|
| **Statut** | Laisser "Ouvert" tant qu'aucun technicien n'est encore assigné |
| **Priorité** | "Urgent" si le client l'a signalé, "Critique" si c'est un équipement vital (réanimation, bloc opératoire) |
| **Technicien assigné** | Sélectionner le technicien qui va intervenir |

Dès qu'un technicien est assigné, passer le statut à **"En cours"**.

> Le bouton **"Appeler [numéro]"** en vert en haut de la fiche permet d'appeler directement le client depuis votre navigateur ou téléphone. Le numéro est extrait automatiquement du dossier CRM lié, ou à défaut du formulaire public.

#### Étape 2 — Section "Demande client"

Cette section contient deux sous-parties :

**Coordonnées du client** :
- Nom, structure, ville, téléphone, email.
- Le bouton **"Appeler"** est également présent ici pour déclencher l'appel directement.

**Description de la panne** :
- Texte libre saisi par le client. Lire attentivement pour comprendre la nature du problème.
- Ce champ peut être modifié pour ajouter des précisions notées lors d'un appel de pré-diagnostic.

#### Étape 3 — Section "Client CRM" (collapsée)

Lier le ticket à un client CRM existant pour enrichir son historique d'interventions. Si le client n'est pas encore dans le CRM, le créer d'abord dans **Commercial → Clients**.

#### Étape 4 — Section "Équipements concernés"

Trois options disponibles :

1. **Lier au parc installé** : si l'équipement est déjà enregistré dans le Parc Installé du client, le sélectionner ici. Le numéro de série et la date d'installation se remplissent automatiquement.
2. **Saisir manuellement** : numéro de série, modèle, date d'installation approximative.
3. **Non identifié** : laisser vide si l'équipement n'est pas encore connu (à compléter après pré-diagnostic).

#### Étape 5 — Section "Pré-diagnostic"

Notes techniques rédigées avant l'intervention physique. Consigner :
- Ce qui a été tenté à distance (appel téléphonique guidé, test simple).
- Le diagnostic probable.
- Le matériel ou les pièces à emporter.

#### Étape 6 — Section "Dates"

| Champ | Description |
|---|---|
| **Date d'ouverture** | Remplie automatiquement à la création du ticket. |
| **Date de résolution** | Se remplit automatiquement quand le statut passe à "Résolu" ou "Clôturé". Peut être corrigée manuellement. |

#### Étape 7 — Clôturer le ticket

1. L'intervention physique est terminée.
2. Ouvrir la section **"Rapport d'intervention"** (collapsée en bas de la fiche).
3. Remplir le rapport : actions effectuées, pièces remplacées, durée totale de l'intervention, résultat.
4. Passer le statut à **"Résolu"** (résolution technique confirmée) ou **"Clôturé"** (client a validé la remise en service).

### 4.3 Export Excel SAV

Bouton **"Rapport SAV Excel"** en haut de la liste → génère un fichier Excel de tous les tickets SAV pour une année donnée (à sélectionner dans le formulaire). Ce rapport peut être utilisé pour le suivi des interventions ou remis au client comme historique de maintenance.

---

## 5. Catalogue & Produits

**Navigation :** menu latéral → **Catalogue & Produits → Produits**

### 5.1 Créer un produit

Cliquer sur **"Nouveau produit"** en haut à droite. Le formulaire est organisé en 6 onglets :

#### Onglet "Général"

| Champ | Description |
|---|---|
| **Nom du produit** | Nom complet affiché sur le site. Ex. : "Moniteur patient Mindray MEC-1200" |
| **Slug** | Identifiant URL, généré automatiquement depuis le nom. Ne pas modifier après publication (cela casserait les liens existants). |
| **Référence** | Code fabricant ou référence interne. Ex. : BS-050108-A02 |
| **Type de produit** | "Équipement", "Réactif (ABM)", ou "Consommable". Ce choix rend visible ou cache l'onglet "Détails Réactif". |
| **Marque** | Sélectionner dans la liste des marques (BIOBASE, Mindray, HUGER…) |
| **Catégorie** | Catégorie principale du catalogue (ex. Cardiologie, Laboratoire, Bloc Opératoire…) |
| **Pôles d'expertise** | Regroupement commercial — choix multiple (ex. Monitoring & Soins critiques, Hémodialyse…) |
| **Description courte** | 1 à 2 phrases affichées dans la grille du catalogue et dans les résultats de recherche |
| **Description longue** | Texte complet avec présentation, avantages, contexte d'utilisation — affiché sur la fiche produit |
| **Prix (FCFA)** | Prix indicatif. Laisser vide si le prix ne doit pas apparaître. |
| **Prix visible au public** | Activer pour que le prix s'affiche sur la fiche produit du site. Si désactivé, le prix reste dans l'admin uniquement. |
| **Produit vedette** | Activer pour mettre ce produit en avant dans le catalogue et sur la page d'accueil (voir §5.3 et §5.4) |
| **Statut** | "Brouillon" = invisible sur le site · "Actif" = visible · "Archivé" = retiré du catalogue |
| **Ordre d'affichage** | Nombre entier : les produits avec un nombre bas apparaissent en premier dans les listes (0, 1, 2…). Les produits vedettes sont triés par cet ordre sur la page d'accueil. |

#### Onglet "Spécifications"

Tableau de caractéristiques techniques en format clé → valeur. Ajouter autant de lignes que nécessaire avec le bouton **"Ajouter une caractéristique"**.

Exemples :
- "Plage de mesures" → "0–4.3 OD"
- "Poids" → "4.6 kg"
- "Alimentation" → "220V / 50Hz"
- "Dimensions" → "140×300×130 mm"

Ces spécifications s'affichent dans un tableau structuré sur la fiche produit publique.

#### Onglet "Médias"

Gérer les images et documents liés au produit.

- Cliquer sur **"Ajouter un média"**.
- Choisir le type : **Image** (photo produit) ou **PDF** (fiche technique, notice).
- Pour les images : cliquer sur le sélecteur pour choisir une image déjà dans la Médiathèque, ou y uploader une nouvelle.
- Cocher **"Image de couverture"** sur la photo principale — c'est elle qui s'affiche dans la grille du catalogue, sur la page d'accueil, et en premier sur la fiche produit. Un seul média peut être couverture à la fois.

#### Onglet "Stock"

| Champ | Description |
|---|---|
| **Statut du stock** | "En stock (Douala)" · "En transit" · "Rupture" — détermine le badge affiché sur le site public (voir §5.3) |
| **Quantité indicative** | Nombre d'unités disponibles, usage interne uniquement. Si ≤ 5, une alerte apparaît dans le widget "Alertes stock" du tableau de bord. |

#### Onglet "Détails Réactif (ABM)" *(visible uniquement si Type = Réactif)*

| Champ | Description |
|---|---|
| **Température de stockage** | Ambiante / +2–8°C / -20°C / -80°C — impacte les conditions logistiques |
| **Conditionnement** | Ex. "Kit 100 réactions", "50 µg" |
| **Volume / Quantité** | Ex. "1 mL", "100 µg" |
| **IVD certifié** | Cocher si certifié pour diagnostic in vitro. Sinon, usage Recherche uniquement. |
| **N° lot (exemple)** | Numéro de lot de référence pour exemple ou documentation |
| **Note de conservation** | Durée de conservation typique. Ex. "12 mois à -20°C" |

#### Onglet "Produits similaires"

Sélectionner jusqu'à 6 produits à proposer en bas de la fiche produit, dans la section "Vous aimerez aussi". Chercher par nom ou référence.

Si laissé vide, le site sélectionne automatiquement des produits de la même catégorie, en priorisant les produits vedettes.

### 5.2 Modifier un produit existant

Dans la liste des produits, cliquer sur la ligne du produit concerné, puis sur l'icône **"Modifier"** (crayon) à droite, ou cliquer directement sur le nom du produit.

### 5.3 Badges affichés sur le site public

Chaque produit affiche un badge coloré dans le catalogue. Ce badge est contrôlé par deux champs : **"Produit vedette"** (onglet Général) et **"Statut du stock"** (onglet Stock).

| Badge | Couleur | Condition |
|---|---|---|
| **Top Vente** | Rouge | Toggle **"Produit vedette"** activé |
| **Disponible** | Vert | Vedette désactivée + Stock = "En stock (Douala)" |
| **Sur commande** | Bleu | Vedette désactivée + Stock = "En transit" |
| **Indisponible** | Gris | Vedette désactivée + Stock = "Rupture" |

> Le badge **"Top Vente"** est prioritaire : si "Produit vedette" est activé, ce badge s'affiche quel que soit le statut du stock. Dans le tri par défaut du catalogue, les produits vedettes remontent automatiquement en tête de liste.

### 5.4 Équipements phares sur la page d'accueil

La section "Équipements Phares" de la page d'accueil affiche automatiquement jusqu'à **4 produits vedettes**, sélectionnés selon ces conditions :

- Statut = **Actif**
- Type = **Équipement** ou **Consommable** (les réactifs n'apparaissent pas dans cette section)
- Toggle **"Produit vedette"** = activé
- Triés par **Ordre d'affichage** (du plus petit au plus grand)

Les 2 premiers s'affichent en **grandes cartes** (mise en valeur maximum), les 2 suivants en **petites cartes** sur le côté.

> Si aucun produit ne remplit ces conditions (aucun vedette actif), la section disparaît entièrement de la page d'accueil.

**Pour gérer les équipements phares :**
1. Aller dans **Catalogue & Produits → Produits**.
2. Ouvrir le produit voulu → onglet **Général** → activer **"Produit vedette"** → s'assurer que le statut est **Actif**.
3. Régler **"Ordre d'affichage"** : `1` = 1re grande carte, `2` = 2e grande carte, `3` = 1re petite carte, `4` = 2e petite carte.
4. Vérifier qu'une **image de couverture** est définie (onglet Médias) — sans image, la carte s'affiche sans photo.

### 5.5 Gérer les catégories et marques

- **Catalogue & Produits → Catégories** : ajouter, renommer ou désactiver les catégories du catalogue (33 catégories pour les équipements, 5 pour les réactifs ABM).
- **Catalogue & Produits → Marques** : ajouter un nouveau fournisseur ou partenaire, uploader son logo.
- **Catalogue & Produits → Pôles** : gérer les pôles d'expertise transversaux (Monitoring & Soins critiques, Hémodialyse, Imagerie Médicale…).

---

## 6. Actualités

**Navigation :** menu latéral → **Communication → Actualités**

### 6.1 Créer un article

Cliquer sur **"Nouvel article"** en haut à droite.

#### Zone principale (colonne gauche)

| Champ | Description |
|---|---|
| **Titre** | Titre de l'article affiché sur le site et dans la liste des actualités |
| **Slug (URL)** | Généré automatiquement depuis le titre à la création. Ex. : `transtechno-cmr.com/actualites/nouveau-partenariat-biobase`. Ne pas modifier après publication — cela casserait les liens partagés. |
| **Extrait / résumé** | 1 à 3 phrases affichées dans la liste des actualités. Maximum 500 caractères. Rédiger un résumé accrocheur qui donne envie de cliquer. |
| **Contenu** | Éditeur de texte riche : titres (H2, H3), paragraphes, listes à puces, gras, italique, images intégrées. |

#### Panneau latéral (colonne droite)

| Champ | Description |
|---|---|
| **Image de couverture** | Photo principale affichée en en-tête de l'article et en vignette dans la liste. Sélectionner depuis la Médiathèque. |
| **Auteur** | Utilisateur du système signataire de l'article (liste des comptes admin). Pré-rempli avec votre propre compte. Si l'auteur n'a pas de compte, créer un compte utilisateur d'abord. |
| **Catégorie** | Innovation Médicale · Partenariats · Événements · Conseils Techniques · Communiqués. Peut être créée directement depuis ce champ sans changer de page. |
| **Statut** | **Brouillon** = non visible sur le site · **Publié** = visible immédiatement · **Planifié** = visible à la date de publication choisie |
| **Date de publication** | Pour un article **Planifié**, choisir une date et heure future. Le statut "Planifié" doit être sélectionné manuellement — la date seule ne change pas le statut. |
| **Tags / mots-clés** | Étiquettes thématiques pour la navigation interne. Peuvent être créés directement depuis ce champ. |

> **Attention :** choisir une date future ne passe pas automatiquement le statut à "Planifié". Il faut explicitement sélectionner **"Planifié"** dans le menu déroulant Statut ET choisir la date.

### 6.2 Modifier ou dépublier un article

Dans la liste des actualités, cliquer sur l'icône **"Modifier"** (crayon) :
- Pour **dépublier** temporairement : changer le statut de "Publié" à "Brouillon". L'article reste en base mais disparaît du site.
- Pour **supprimer** définitivement : bouton **"Supprimer"** (poubelle) — action irréversible.

### 6.3 Gérer les catégories d'actualités

**Communication → Catégories d'actualités** : créer, renommer, ou désactiver les catégories. Chaque catégorie a un slug (URL) généré automatiquement.

---

## 7. Médiathèque

**Navigation :** menu latéral → **Communication → Médiathèque**

La médiathèque est la bibliothèque centrale de toutes les images du site. Elle est aussi accessible directement depuis n'importe quel formulaire qui a un champ image (produit, actualité…).

### 7.1 Uploader une image

1. Cliquer sur **"Nouveau média"** en haut à droite.
2. Glisser-déposer ou sélectionner un fichier depuis votre ordinateur.
   - Formats acceptés : **PNG, JPG, WEBP**
   - Taille maximum : **2 MB** par fichier
3. Remplir les métadonnées (très important) :

| Champ | Description | Pourquoi c'est important |
|---|---|---|
| **Texte alternatif (Alt)** | Description textuelle de l'image, ex. "Moniteur patient Mindray en salle de réanimation" | Accessibilité pour malvoyants + référencement Google Images |
| **Titre** | Nom court pour retrouver l'image dans la médiathèque, ex. "Moniteur Mindray MEC-1200 face" | Facilite la recherche interne |
| **Légende** | Texte optionnel affiché sous l'image sur le site | Contexte supplémentaire pour le lecteur |
| **Description** | Notes internes sur cette image (usage, source, droits…) | Gestion interne uniquement |

### 7.2 Retrouver une image

La médiathèque dispose d'une barre de recherche. Chercher par **titre** ou **texte alternatif**. Si l'image est introuvable, vérifier qu'elle a bien un titre renseigné — une image sans titre est difficile à retrouver.

### 7.3 Insérer une image dans un produit ou un article

Depuis n'importe quel formulaire avec un champ image :
1. Cliquer sur **"Sélectionner depuis la médiathèque"** (bouton dans le champ image).
2. La médiathèque s'ouvre en popup.
3. Chercher l'image souhaitée par titre.
4. Cliquer sur l'image pour la sélectionner (bordure bleue).
5. Cliquer sur **"Insérer l'image"**.

### 7.4 Formats et bonnes pratiques

- Préférer le format **WEBP** pour les photos produit (meilleure compression, qualité identique).
- Dimensions recommandées pour les images produit : **800×800 pixels** minimum (carré ou paysage).
- Toujours renseigner le **texte alternatif** — c'est obligatoire pour le référencement.
- Ne pas uploader la même image deux fois : chercher d'abord dans la médiathèque avant d'uploader.

---

## 8. Messages Contact & Demandes Pôle

### 8.1 Messages du formulaire "Contactez-nous"

**Navigation :** menu latéral → **Communication → Messages**

Ce sont les messages envoyés via le formulaire général de contact du site (page "/contact").

| Colonne | Description |
|---|---|
| **Nom** | Nom de l'expéditeur |
| **Email / Téléphone** | Coordonnées de contact pour répondre |
| **Sujet** | Objet du message |
| **Message** | Texte complet du message |
| **Date** | Date et heure d'envoi |

Cliquer sur un message pour le lire en entier. Ces messages ne créent **pas** de cotation automatiquement — il faut traiter manuellement : appeler le contact, ou créer une cotation depuis **Commercial → Cotations → Nouvelle cotation**.

### 8.2 Demandes Pôle (Hémodialyse & Traitement des Eaux)

**Navigation :** menu latéral → **Commercial → Demandes Pôle**

Ces demandes viennent du formulaire de contact spécialisé sur la page "Pôle Hémodialyse". Elles concernent des projets d'équipement complets : générateurs de dialyse, traitement de l'eau, production de dialysats.

Ces demandes sont plus complexes que les cotations classiques et nécessitent généralement :
1. Un appel téléphonique de qualification (type d'établissement, volume de patients, budget).
2. La création d'une cotation dédiée avec les équipements identifiés.
3. Un suivi projet personnalisé.

---

## 9. CRM Clients

**Navigation :** menu latéral → **Commercial → Clients**

### 9.1 À quoi sert le CRM ?

Chaque client (hôpital, clinique, labo, ONG…) peut avoir une fiche CRM. Celle-ci centralise :
- Ses coordonnées complètes.
- L'historique de toutes ses cotations.
- Le parc d'équipements installés dans sa structure.

Le CRM n'est pas obligatoire pour traiter une cotation, mais il est fortement recommandé pour les clients récurrents.

### 9.2 Créer une fiche client

Cliquer sur **"Nouveau client"** :

| Champ | Description |
|---|---|
| **Nom de la structure** | Ex. : "Clinique Pasteur de Douala", "Hôpital Régional de Garoua" |
| **Type** | Hôpital public / Clinique privée / Laboratoire / ONG / Autres |
| **Ville** | Ville d'implantation (Douala, Yaoundé, Garoua, Bafoussam…) |
| **Secteur** | Privé / Public / ONG / Recherche |
| **Contact principal** | Nom du décideur ou du responsable des achats |
| **Téléphone** | Numéro direct du contact principal |
| **Email** | Email de contact pour les devis et correspondances |
| **Adresse** | Adresse complète de l'établissement |
| **Notes** | Informations libres : historique relationnel, particularités, interlocuteurs secondaires… |

### 9.3 Voir l'historique d'un client

Sur la fiche client, deux sections affichent l'historique :
- **Cotations liées** : toutes les demandes de cotation associées à ce client, avec leur statut et montant.
- **Parc installé** : équipements déployés dans cette structure (voir §10).

### 9.4 Lier une cotation à un client

Dans la fiche de cotation → section **"Lien CRM & parc"** (collapsée en bas de la fiche) → champ **"Client CRM"** → sélectionner le client dans la liste.

Cette liaison est nécessaire pour que le Parc Installé soit alimenté automatiquement quand la cotation passe à "Gagné".

---

## 10. Parc Installé

**Navigation :** menu latéral → **Technique → Parc Installé**

### 10.1 À quoi sert le Parc Installé ?

Le Parc Installé est le registre de tous les équipements déployés chez les clients. Il permet de savoir :
- Quel équipement est installé chez quel client.
- La date d'installation et le numéro de série.
- L'historique des interventions SAV sur cet équipement.

C'est la source de vérité pour les techniciens SAV : avant une intervention, ils consultent le parc pour connaître l'équipement exact et son historique.

### 10.2 Alimentation automatique

Quand une cotation passe au statut **"Gagné"** ET que le client a coché **"Installation & mise en service"** dans son formulaire, les équipements de la cotation sont **automatiquement enregistrés** dans le parc du client lié.

> **Condition indispensable :** la cotation doit être liée à un client CRM (section "Lien CRM & parc" dans la fiche cotation). Si aucun client CRM n'est lié, l'enregistrement automatique ne se déclenche pas et doit être fait manuellement.

### 10.3 Ajouter manuellement un équipement

Cliquer sur **"Nouvel équipement installé"** :

| Champ | Description |
|---|---|
| **Client** | Sélectionner le client CRM concerné |
| **Produit** | Sélectionner l'équipement depuis le catalogue |
| **Numéro de série** | Numéro gravé sur l'appareil (indispensable pour le SAV) |
| **Date d'installation** | Date de mise en service effective |
| **Notes** | Observations techniques : version firmware, configuration particulière, accessoires installés… |

---

## 11. Utilisateurs & Rôles

**Navigation :** menu latéral → **Système → Utilisateurs & Rôles**

### 11.1 Créer un compte utilisateur

1. **Système → Utilisateurs** → cliquer sur **"Nouvel utilisateur"**.
2. Remplir : **Nom**, **Email**, **Mot de passe**.
3. Assigner un **Rôle** dans la liste (voir §12 pour les rôles disponibles).
4. Cliquer sur **"Créer"**.

L'utilisateur peut alors se connecter sur `https://transtechno-cmr.com/admin` avec son email et son mot de passe.

> Sans rôle assigné, l'utilisateur ne pourra pas accéder à l'interface admin même avec des identifiants corrects.

### 11.2 Modifier un utilisateur existant

Cliquer sur la ligne de l'utilisateur → icône **"Modifier"** :
- **Changer le nom ou l'email** : modifier directement les champs.
- **Réinitialiser le mot de passe** : saisir un nouveau mot de passe dans le champ "Mot de passe" et sauvegarder. L'ancien mot de passe est immédiatement remplacé.
- **Changer de rôle** : modifier le champ "Rôles" — le changement prend effet à la prochaine connexion de l'utilisateur.

### 11.3 Gérer les rôles et permissions

**Système → Rôles** → cliquer sur un rôle pour voir ou modifier ses permissions.

#### Créer un nouveau rôle

1. Cliquer sur **"Nouveau rôle"**.
2. **Nom du rôle** : ex. `responsable_achat` (sans espaces, en minuscules avec tirets bas).
3. **Nom du Guard** : laisser **"web"** — ne jamais changer ce champ (voir §12 pour l'explication).
4. Cocher les permissions souhaitées dans les onglets (Ressources, Widgets, Pages).
5. Cliquer sur **"Créer"**.

#### Onglet "Ressources"

Chaque ressource (Cotations, SAV, Produits…) dispose de 6 permissions utiles :

| Permission | Ce qu'elle autorise |
|---|---|
| **Voir** | Ouvrir la fiche d'un enregistrement individuel |
| **Voir tout** | Accéder à la liste (page index) — **indispensable pour que la ressource apparaisse dans le menu** |
| **Créer** | Ouvrir le formulaire de création et enregistrer un nouvel élément |
| **Mettre à jour** | Modifier un enregistrement existant |
| **Supprimer** | Supprimer un enregistrement unique |
| **Supprimer tout** | Supprimer plusieurs enregistrements en masse |

> Les permissions "Restaurer", "Forcer la suppression", "Répliquer", "Réordonner" sont générées automatiquement par le système mais **n'ont aucun effet** dans cette application. Les laisser décochées.

#### Onglet "Widgets"

Contrôle quels widgets du tableau de bord sont visibles pour ce rôle. Chaque widget (KPI stats, cotations récentes, panel opérations…) peut être activé ou désactivé par rôle.

#### Onglet "Pages"

Contrôle l'accès aux pages admin personnalisées (Médiathèque, Paramètres…). En général, ne pas modifier sauf besoin spécifique.

---

## 12. Rôles et droits

### Rôles prédéfinis

| Rôle | Accès |
|---|---|
| **super_admin** | Accès total — bypass automatique de toutes les permissions, toutes les sections visibles |
| **commercial** | Cotations, Clients CRM, Catalogue (lecture + modification), Demandes Pôle, Médiathèque |
| **technicien_sav** | SAV & Maintenance, Parc Installé, Clients (lecture seule) |
| **editeur_contenu** | Actualités, Catégories d'actualités, Médiathèque |
| **magasinier** | Catalogue produits (lecture + mise à jour du stock uniquement) |

### Règle clé : "Voir tout" est indispensable

Sans la permission **"Voir tout"** d'une ressource, **la ressource n'apparaît pas dans le menu latéral**. Un utilisateur avec seulement "Voir" peut ouvrir une fiche si on lui envoie le lien direct, mais ne verra pas l'entrée dans le menu.

Toujours cocher **"Voir"** ET **"Voir tout"** ensemble.

### Qu'est-ce que le "Guard" ?

Le Guard est le canal d'authentification utilisé par le système. Ce projet n'a qu'un seul canal : `web` (connexion via navigateur avec session). Le champ "Nom du Guard" dans le formulaire de création de rôle doit **toujours rester à "web"**. Ne jamais le changer ou le laisser vide — le rôle ne fonctionnerait plus.

### Que faire si un utilisateur ne voit pas une section ?

1. Aller dans **Système → Rôles** → cliquer sur le rôle de l'utilisateur.
2. Trouver la ressource concernée dans la liste.
3. Vérifier que **"Voir"** ET **"Voir tout"** sont cochés.
4. Sauvegarder.
5. L'utilisateur doit **se déconnecter puis se reconnecter** pour que le changement soit pris en compte.

---

## Annexe — Statuts et codes couleur

### Cotations

| Statut | Signification | Action recommandée |
|---|---|---|
| **Nouveau** | Demande reçue, non encore traitée | Assigner un commercial, passer à "En cours" immédiatement |
| **En cours** | Commercial assigné, traitement en cours | Remplir les prix, préparer le devis |
| **Devis Envoyé** | PDF envoyé au client | Attendre la réponse, relancer après 5 jours sans retour |
| **Urgent** | À traiter en priorité absolue | Traiter avant toutes les autres demandes |
| **Gagné** | Client a accepté le devis | Déclencher la livraison, enregistrer dans le parc si installation |
| **Perdu** | Client a refusé ou ne répond plus | Archiver, noter la raison en note interne |
| **Critique** | Urgence vitale (équipement médical essentiel) | Intervention immédiate requise |

### SAV

| Statut | Signification | Action recommandée |
|---|---|---|
| **Ouvert** | Ticket reçu, aucun technicien assigné | Assigner un technicien, passer à "En cours" |
| **En cours** | Technicien assigné, intervention planifiée ou en cours | Suivre l'avancement, rédiger le pré-diagnostic |
| **Résolu** | Panne résolue techniquement par le technicien | Rédiger le rapport d'intervention |
| **Clôturé** | Client a validé la remise en service | Aucune action requise — dossier clos |

### Produits — badges publics

| Badge | Couleur | Condition dans l'admin |
|---|---|---|
| **Top Vente** | Rouge | "Produit vedette" activé (onglet Général) |
| **Disponible** | Vert | Vedette off + Stock = "En stock (Douala)" |
| **Sur commande** | Bleu | Vedette off + Stock = "En transit" |
| **Indisponible** | Gris | Vedette off + Stock = "Rupture" |

---

*Guide rédigé pour Transtechno Cameroun — version juin 2026.*
