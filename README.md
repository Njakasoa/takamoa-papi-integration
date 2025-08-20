# Takamoa Papi Integration

## Documentation d’utilisation

### 1. Installation et activation
1. Téléversez le dossier `takamoa-papi-integration` dans `wp-content/plugins`.
2. Activez le plugin depuis **Extensions ▸ Installées**. L’activation crée trois tables personnalisées (`payments`, `tickets`, `designs`) et initialise les options (URLs de redirection, prestataires disponibles, durée de validité, etc.)

### 2. Configuration initiale
1. Dans le menu **Takamoa Papi**, ouvrez l’onglet principal pour renseigner votre **clé API Papi.mg**.
2. L’onglet **Options** permet de définir :
   - URLs de succès/échec après paiement
   - Durée de validité du lien (en minutes)
   - Prestataires de paiement proposés (MVOLA, Orange Money, etc.)
   - Champs optionnels affichés dans le formulaire
   - Mode test et motif associé

### 3. Formulaire côté public
- Le shortcode `[takamoa_papi_form]` ajoute le formulaire Vue.js sur n’importe quelle page.
- Attributs disponibles :
  - `amount` : montant à payer
  - `reference` : référence personnalisée (supporte `{{TIMESTAMP}}`)
  - `payment` : `yes` (paiement en ligne) ou `no` (simple inscription)
- Le formulaire collecte nom, email, téléphone, description et propose le choix du fournisseur lorsqu’il y en a plusieurs.

### 4. Flux de paiement
1. À la soumission, le plugin crée un **lien de paiement** via l’API Papi et enregistre la transaction en base.
2. Si `payment="yes"`, le lien s’ouvre automatiquement et le statut est vérifié toutes les 2 secondes jusqu’à réussite ou échec.
3. Un email de confirmation d’inscription puis, le cas échéant, de paiement réussi est envoyé automatiquement.

### 5. Gestion des paiements
- L’onglet **Paiements** liste les transactions (DataTables) et permet :
  - de consulter les détails,
  - de renvoyer la notification,
  - de générer un billet PDF basé sur un design choisi.

### 6. Génération et gestion des billets
- Lorsqu’un paiement est validé, l’admin peut générer un **billet PDF** (QR code + design) et l’envoyer par email.
- L’onglet **Billets** affiche les références, statuts et liens de téléchargement ; un bouton permet de renvoyer le billet au participant.

### 7. Scanner et validation
- L’onglet **Scanner billets** active la caméra (HTML5‑Qrcode) pour lire le QR code.
- Les handlers AJAX renvoient les informations du billet puis marquent le billet comme *VALIDATED* si la référence est conforme.

### 8. Designs de billets
- L’onglet **Design** permet d’ajouter des modèles (image, dimensions, taille/position du QR code) pour personnaliser les billets envoyés aux participants.

### 9. Endpoints et notifications
- Le plugin enregistre trois endpoints publics : `/paiementreussi`, `/paiementechoue` et `/papi-notify` (notification serveur-à-serveur).

### 10. Désinstallation
- Supprimer le plugin supprime les options enregistrées. Les tables personnalisées restent en base pour conserver l’historique, sauf suppression manuelle.

## Résumé
Le plugin **Takamoa Papi Integration** offre une solution complète pour :
- créer des formulaires d’inscription avec paiement en ligne Papi.mg,
- suivre l’état des transactions,
- générer, envoyer et valider des billets personnalisés,
- administrer l’ensemble via une interface WordPress dédiée.

## Testing
Aucun test automatisé n’a été exécuté pour cette revue — l’analyse repose uniquement sur l’inspection statique du code.
