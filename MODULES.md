# Modules du back-office Transtechno Cameroun

Ce document explique chaque onglet / module de l'administration, qui l'utilise, depuis quel formulaire public les données arrivent, et comment les gérer.

---

## 1. Messages (`/admin/contacts`)

**Qui remplit ça ?**
Tout visiteur du site public qui passe par la page **Contactez-nous** (`/contact`).

**Formulaire public :** `/contact`
Champs : Nom · Email · Téléphone · Structure · Type de structure · Sujet · Message

**Ce que ça crée :** un enregistrement `Contact` en base + email interne de notification.

**Comment le gérer :**
- Lire le message dans la liste
- Répondre directement par téléphone ou email (aucun workflow automatisé, c'est un simple formulaire de contact)
- Pas de statut ni de suivi — si un message nécessite un suivi long, ouvrir manuellement un Ticket SAV ou une Cotation

---

## 2. Demandes de projet / Pôle (`/admin/pole-project-requests`)

**Qui remplit ça ?**
Visiteurs des pages spécialisées :
- **Pôle Hémodialyse** (`/pole-hemodialyse`) — projets de centres de dialyse, contrats de maintenance, dialysats
- **Réactifs ABM** (`/reactifs/abm`) — demandes de réactifs biologie moléculaire, PCR, culture cellulaire…

**Formulaires publics :** `/pole-hemodialyse` et `/reactifs/abm`
Champs communs : Nom · Structure · Fonction · Téléphone · Email · Type de projet/demande · Message

**Ce que ça crée :** un enregistrement `PoleProjectRequest` avec un champ `pole_type` (`hemodialyse` ou `abm`).

**Comment le gérer :**
- Filtrer par `pole_type` pour séparer les demandes Hémodialyse des demandes ABM
- Contacter le prospect (téléphone ou email)
- Si la demande aboutit à un devis → créer manuellement une Cotation dans le back-office
- Pas de conversion automatique en cotation pour l'instant

---

## 3. Cotations (`/admin/quotations`)

**Qui remplit ça ?**
Visiteurs qui ont sélectionné des produits dans le catalogue ou décrit un besoin sans produit précis.

**Formulaires publics :**
- **Récapitulatif panier** (`/cotation`) — produits du catalogue + éventuellement articles hors-catalogue
- **Demande libre** (`/demande-cotation`) — description textuelle d'un besoin, sans produit du catalogue

**Ce que ça crée :** une `Quotation` avec ses `QuotationItems` (lignes de produits) + historique de statut.

**Cycle de vie d'une cotation :**

```
Nouveau → En cours → Devis Envoyé → Gagné
                              ↘ Perdu
```

**Comment la gérer (écran `traitement_cotation`) :**
1. Ouvrir la cotation → lire les articles demandés et les spécifications du client
2. Saisir le prix unitaire HT sur chaque ligne
3. Remplir : Remise globale (%) · Frais logistique/transport · Assigner un commercial
4. Cocher "Exonération TVA Minsanté" si le client est un hôpital public éligible (sinon TVA 19,25 %)
5. Changer le statut → "Devis Envoyé"
6. Générer le PDF (`/cotation/{id}/devis`) et l'envoyer au client

**Services associés :** si le client a coché Installation / Formation / Maintenance dans le récapitulatif, ces cases s'affichent dans le formulaire admin et apparaissent sur le PDF en chips "Services associés inclus".

**Priorités :** Normal · Critique · Urgent (défini par le client ou modifiable par l'admin)

---

## 4. Tickets SAV & Maintenance (`/admin/sav-tickets`)

**Qui remplit ça ?**
- **Depuis le site public :** visiteurs qui cliquent sur "Ouvrir un ticket SAV" depuis la page Services (`/services-sav`) → redirigés vers le formulaire dédié `/sav/demande`
- **Depuis le back-office :** un technicien crée manuellement un ticket après un appel téléphonique

**Formulaire public :** `/sav/demande`
Champs : Nom · Établissement · Ville · Téléphone · Email · Type d'intervention · Marque équipement · Modèle · N° série · Description panne · Priorité (Normal / Critique / Urgent)

**Ce que ça crée :** un `SavTicket` avec référence auto `SAV-2026-XXX` + email interne + accusé de réception au client.

**Cycle de vie d'un ticket :**

```
Ouvert → En cours → Résolu → Clôturé
```

Quand le statut passe à "Résolu" ou "Clôturé", la date `resolved_at` est remplie automatiquement.

**Comment le gérer :**
1. Voir la liste filtrée par statut / priorité (badge rouge = nombre de tickets ouverts/en cours)
2. Ouvrir le ticket → lire la description (pour les tickets publics : contact, équipement, panne)
3. Remplir le **Pré-diagnostic à distance** (notes de l'appel téléphonique)
4. Assigner un technicien
5. Lier à un **Client** et à un **Équipement du parc** si disponibles
6. Changer le statut → "En cours" lors du départ sur site
7. Remplir le **Rapport d'intervention** (cause, actions, pièces remplacées) avant de clore
8. Passer en "Résolu" puis "Clôturé"

**Export :** bouton "Rapport SAV Excel" → 3 feuilles : détail tickets · par statut · par technicien

---

## 5. Parc installé (`/admin/equipment-inventory`)

**Qui remplit ça ?**
Uniquement les **techniciens / admins** dans le back-office. Il n'y a pas de formulaire public pour ça.

**Ce que c'est :** la liste de tous les équipements vendus et installés chez les clients, avec leur numéro de série, leur état et leur historique de maintenance.

**Structure :**
- Lié à un `Client` (l'établissement qui a acheté)
- Lié à un `Product` (la fiche produit du catalogue)
- Champs : N° série · Date d'installation · État (En service / En panne / En maintenance / Réformé) · Notes

**Comment le gérer :**
1. Après livraison d'un équipement → créer l'entrée dans le parc avec le numéro de série
2. Lier les futurs `SavTickets` à cet équipement pour avoir l'historique complet des interventions
3. Utiliser les filtres par Client ou par état pour voir les équipements en panne ou à maintenir

---

## Récapitulatif : qui crée quoi depuis où

| Module admin | Formulaire public | Qui remplit | Modèle Laravel |
|---|---|---|---|
| Messages | `/contact` | Tout visiteur | `Contact` |
| Demandes projet | `/pole-hemodialyse` et `/reactifs/abm` | Prospect spécialisé | `PoleProjectRequest` |
| Cotations | `/cotation` et `/demande-cotation` | Client qui veut un devis | `Quotation` + `QuotationItem` |
| Tickets SAV | `/sav/demande` | Client avec panne | `SavTicket` |
| Parc installé | *(back-office uniquement)* | Technicien / Admin | `EquipmentInventory` |

---

## Flux typique d'une vente complète

```
1. Visiteur ajoute des produits au panier
          ↓
2. Remplit le récapitulatif (/cotation) → Cotation "Nouveau" créée
          ↓
3. Admin traite la cotation → saisit les prix → génère le PDF
          ↓
4. Client accepte → statut "Gagné"
          ↓
5. Livraison + installation → entrée dans le Parc installé
          ↓
6. Si panne ultérieure → Client ouvre un Ticket SAV (/sav/demande)
          ↓
7. Technicien intervient → remplit rapport → ticket "Clôturé"
```
