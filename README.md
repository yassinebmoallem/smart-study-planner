# 📚 Smart Study Planner

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/fr/docs/Web/JavaScript)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.4-FF6384?style=flat-square&logo=chartdotjs&logoColor=white)](https://www.chartjs.org/)
[![Licence](https://img.shields.io/badge/licence-MIT-green?style=flat-square)](LICENSE)
[![Statut](https://img.shields.io/badge/statut-actif-brightgreen?style=flat-square)]()

Application web full-stack PHP/MySQL qui genere automatiquement un **planning de revisions personnalise** par matiere, adapte les sessions selon le niveau de difficulte, et permet un suivi de progression en temps reel — dans une interface calme et confortable, pensee pour reduire le stress des examens.

---

## 📋 Table des Matieres

- [A propos](#-a-propos)
- [Problematique et Solution](#-problematique-et-solution)
- [Fonctionnalites](#-fonctionnalites)
- [Technologies Utilisees](#️-technologies-utilisees)
- [Architecture du Projet](#-architecture-du-projet)
- [Installation](#-installation)
- [Utilisation](#-utilisation)
- [Structure de la Base de Donnees](#️-structure-de-la-base-de-donnees)
- [Algorithme de Planification](#-algorithme-de-planification)
- [Captures d'Ecran](#-captures-decran)
- [Defis Techniques Rencontres](#-defis-techniques-rencontres)
- [Ameliorations Futures](#-ameliorations-futures)
- [Auteur](#-auteur)
- [Licence](#-licence)

---

## 📖 A propos

**Smart Study Planner** est une application web academique developpee en PHP/MySQL qui aide les etudiants a organiser efficacement leurs revisions. Contrairement a un simple agenda, elle genere automatiquement un planning intelligent, matiere par matiere, en tenant compte de la difficulte du cours, des heures disponibles et de la date d'examen.

### Contexte de developpement

- Projet realise dans le cadre du cursus **L2 SIG** (Systemes d'Information et de Gestion)
- Objectif : proposer une solution concrete au probleme de desorganisation des revisions
- Version **v8** — resultat d'iterations successives sur le design et les fonctionnalites

---

## 💡 Problematique et Solution

### Le Probleme

Chaque saison d'examens, les etudiants reproduisent le meme cycle : procrastination, revisions intensives la veille, stress et mauvais resultats. Les outils classiques (agendas, to-do lists) ne comprennent pas le contexte academique — ils ne savent pas que *Thermodynamique* demande deux fois plus d'effort que *Langue francaise*, ni qu'il ne reste que 14 jours avant les partiels.

### La Solution

**Smart Study Planner** prend en entree vos matieres, vos dates d'examen et votre estimation de difficulte, puis construit automatiquement un planning session par session, adapte a votre progression reelle. Si vous prenez du retard, une seule action suffit pour regenerer intelligemment le reste du planning.

---

## ✨ Fonctionnalites

### Pour tous les utilisateurs
- ✅ Inscription securisee avec validation des donnees
- ✅ Connexion avec gestion de session PHP
- ✅ Interface responsive (desktop, tablette, mobile)
- ✅ Citations motivantes quotidiennes rotatives
- ✅ Design calme et confortable (palette vert doux)

### Gestion des Matieres
- ✅ Ajout d'une matiere avec :
  - Nom de la matiere
  - Niveau de difficulte (Facile / Moyen / Difficile)
  - Date de l'examen
  - Heures totales a etudier
  - Heures par semaine disponibles
- ✅ Modification et suppression de matiere
- ✅ Barre de progression visuelle par matiere
- ✅ Indicateur de jours restants avant l'examen

### Planning Intelligent
- ✅ Generation automatique d'un planning jour par jour a la creation
- ✅ Regeneration intelligente selon la progression reelle
- ✅ Vue hebdomadaire detaillee du planning par matiere
- ✅ Suivi des sessions par case a cocher (AJAX — sans rechargement de page)

### Tableau de Bord
- ✅ Sessions du jour avec statut en temps reel
- ✅ Resume des matieres actives et examens a venir
- ✅ Compteur d'heures restantes pour la journee

### Calendrier
- ✅ Vue mensuelle interactive
- ✅ Affichage des sessions d'etude et des dates d'examen
- ✅ Navigation entre les mois (precedent / suivant)

### Statistiques
- ✅ Graphique d'activite quotidienne (30 derniers jours)
- ✅ Graphique d'activite hebdomadaire (8 semaines)
- ✅ Compteur de jours consecutifs (streak)
- ✅ Taux de progression global sur l'ensemble des matieres

---

## 🛠️ Technologies Utilisees

### Backend

| Technologie | Role |
|---|---|
| **PHP 8.0+** | Logique serveur, routage, gestion des sessions |
| **MySQLi** | Execution des requetes et gestion de la connexion |
| **MySQL 5.7+** | Stockage relationnel (utilisateurs, matieres, sessions) |

### Frontend

| Technologie | Role |
|---|---|
| **HTML5** | Structure semantique des pages |
| **CSS3** (systeme custom) | Variables CSS, flexbox, grid, design complet |
| **JavaScript Vanilla** | Appels AJAX, mises a jour live, apercu du planning |
| **Chart.js 4.4** | Graphiques interactifs (statistiques) |
| **Plus Jakarta Sans** | Typographie Google Fonts — claire et lisible |

### Outils de Developpement

| Outil | Usage |
|---|---|
| **phpMyAdmin** | Administration de la base de donnees |
| **XAMPP / Laragon** | Serveur local Apache + MySQL |
| **VS Code** | Editeur de code principal |

---

## 🏗️ Architecture du Projet

```
smart-study-planner-v8/
│
├── index.php              # Page d'accueil : onglets connexion + inscription
├── dashboard.php          # Tableau de bord principal
├── add_subject.php        # Formulaire d'ajout / modification d'une matiere
├── plan.php               # Vue detaillee du planning pour une matiere
├── calendar.php           # Calendrier mensuel interactif
├── stats.php              # Page des statistiques et graphiques
├── regenerate.php         # Regeneration du planning selon la progression
├── delete_subject.php     # Suppression d'une matiere (cascade)
├── toggle_session.php     # Endpoint AJAX : cocher / decocher une session
├── logout.php             # Destruction de la session + redirection
│
├── auth.php               # Garde de session (redirection si non connecte)
├── connexion.php          # Connexion MySQLi a la base de donnees
├── planner.php            # ⭐ Coeur de l'algorithme de planification
├── _helpers.php           # Fonctions utilitaires (formatHours, diffLabel...)
├── _icons.php             # Bibliotheque d'icones SVG (Feather icons inline)
├── _sidebar.php           # Composant de navigation laterale reutilisable
│
├── style.css              # Systeme de design complet (variables, composants)
├── script.js              # JS : AJAX, mise a jour live, animations
│
└── database.sql           # Schema MySQL : utilisateurs, matieres, sessions_etude
```

---

## 📦 Installation

### Prerequis

- **PHP 8.0** ou superieur
- **MySQL 5.7+** ou MariaDB 10.3+
- Un serveur local : **XAMPP**, **Laragon**, **WAMP**, ou le serveur integre PHP

---

### Option 1 — Serveur integre PHP (le plus rapide)

```bash
# Etape 1 — Cloner ou copier le dossier du projet
git clone https://github.com/yassinebmoallem/smart-study-planner.git
cd smart-study-planner-v8

# Etape 2 — Creer la base de donnees
# Ouvrir MySQL et executer :
mysql -u root -p < database.sql

# Etape 3 — Configurer les identifiants de connexion
# Editer connexion.php avec votre hote, utilisateur, mot de passe et nom de BDD

# Etape 4 — Demarrer le serveur
php -S localhost:8080

# Etape 5 — Ouvrir le navigateur
# Aller sur : http://localhost:8080
```

---

### Option 2 — XAMPP / WAMP / Laragon

```
1. Copier le dossier du projet dans :
   - XAMPP   →  C:/xampp/htdocs/smart-study-planner/
   - WAMP    →  C:/wamp64/www/smart-study-planner/
   - Laragon →  C:/laragon/www/smart-study-planner/

2. Demarrer Apache et MySQL depuis le panneau de controle.

3. Creer la base de donnees :
   Ouvrir phpMyAdmin → Nouvelle base → nommer "smart_study"
   → Importer → selectionner database.sql → Executer

4. Ouvrir le navigateur sur :
   http://localhost/smart-study-planner/
```

---

### Configuration de la Base de Donnees

Ouvrir `connexion.php` et mettre a jour les lignes suivantes :

```php
$host   = "localhost";
$dbname = "smart_study";
$user   = "root";
$pass   = "";          // votre mot de passe MySQL
```

---

## 🚀 Utilisation

### Premiere utilisation

1. **Lancer l'application** via le navigateur
2. **Creer un compte** :
   - Cliquer sur l'onglet "Inscription"
   - Remplir les champs : nom complet, email, mot de passe
   - Cliquer sur "Creer mon compte"
3. **Se connecter** :
   - Entrer email et mot de passe
   - Cliquer sur "Se connecter"
   - Redirection automatique vers le tableau de bord

### Ajouter une matiere

1. Cliquer sur **"Nouvelle matiere"** depuis le tableau de bord
2. Renseigner les informations :
   - Nom de la matiere (ex : Mathematiques, Physique...)
   - Niveau de difficulte (Facile / Moyen / Difficile)
   - Date de l'examen
   - Heures totales a etudier
   - Heures disponibles par semaine
3. Un apercu du planning s'affiche en temps reel
4. Cliquer sur **"Generer mon planning"**
5. Redirection automatique vers le planning detaille

### Suivre sa progression

1. Depuis le **tableau de bord**, cocher les sessions realisees
2. La barre de progression se met a jour instantanement (AJAX)
3. En cas de retard, cliquer sur **"Regenerer"** pour redistribuer les heures restantes

---

## 🗄️ Structure de la Base de Donnees

### Table : `utilisateurs`

| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique |
| nom | VARCHAR(100) | NOT NULL | Nom complet de l'utilisateur |
| email | VARCHAR(150) | UNIQUE, NOT NULL | Adresse email |
| mot_de_passe | VARCHAR(255) | NOT NULL | Mot de passe hache (bcrypt) |
| cree_le | DATETIME | DEFAULT NOW() | Date d'inscription |

### Table : `matieres`

| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique |
| utilisateur_id | INT | FOREIGN KEY NOT NULL | Reference vers utilisateurs(id) |
| nom | VARCHAR(150) | NOT NULL | Nom de la matiere |
| difficulte | ENUM | NOT NULL | 'facile', 'moyen' ou 'difficile' |
| date_examen | DATE | NOT NULL | Date de l'examen |
| heures_totales | FLOAT | NOT NULL | Total d'heures a etudier |
| heures_par_semaine | FLOAT | NOT NULL | Disponibilite hebdomadaire |
| heures_completees | FLOAT | NOT NULL | Heures effectivement realisees |
| cree_le | DATETIME | DEFAULT NOW() | Date de creation |

### Table : `sessions_etude`

| Colonne | Type | Contraintes | Description |
|---|---|---|---|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Identifiant unique |
| matiere_id | INT | FOREIGN KEY NOT NULL | Reference vers matieres(id) |
| date_session | DATE | NOT NULL | Date de la session |
| heures | FLOAT | NOT NULL | Duree de la session (en heures) |
| fait | TINYINT(1) | NOT NULL DEFAULT 0 | 0 = a faire / 1 = realisee |

### Relations et integrite

- **1:N** — Un utilisateur peut avoir plusieurs matieres
- **1:N** — Une matiere peut avoir plusieurs sessions d'etude
- **CASCADE** — La suppression d'une matiere supprime automatiquement toutes ses sessions

```
utilisateurs (1) ──── (N) matieres (1) ──── (N) sessions_etude
```

---

## 🤖 Algorithme de Planification

Le moteur de planification est dans `planner.php`. Il utilise une approche **rule-based** — deterministe, rapide et entierement explicable.

### Fonctionnement — Etape par Etape

```
Entrees :
  Nom de la matiere, niveau de difficulte, date d'examen,
  heures totales a etudier, heures deja realisees

Etape 1 — Calcul des jours restants
  joursRestants = dateExamen - aujourd'hui  (en jours)
  Si joursRestants <= 0 → arret (examen deja passe)

Etape 2 — Calcul du travail restant
  heuresRestantes = heuresTotales - heuresRealisees

Etape 3 — Calcul de la base quotidienne
  baseJournaliere = heuresRestantes / joursRestants

Etape 4 — Application du multiplicateur de difficulte
  ┌─────────────┬───────────────┬──────────────────────────────────┐
  │ Difficulte  │ Multiplicateur│ Effet                            │
  ├─────────────┼───────────────┼──────────────────────────────────┤
  │ difficile   │   x 1.25     │ Sessions plus longues, plus dense│
  │ moyen       │   x 1.00     │ Rythme standard                  │
  │ facile      │   x 0.80     │ Sessions allegees                │
  └─────────────┴───────────────┴──────────────────────────────────┘
  heuresJournalieres = baseJournaliere × multiplicateur

Etape 5 — Plafonnement de la duree de session
  heuresJournalieres = clamp(valeur, min=0.5h, max=6h)
  (Evite le surmenage et les micro-sessions irrealistes)

Etape 6 — Suppression des sessions en attente
  DELETE toutes les sessions futures (fait = 0) de cette matiere

Etape 7 — Insertion des sessions quotidiennes
  Pour chaque jour de aujourd'hui jusqu'a dateExamen :
    sessionHeures = min(heuresJournalieres, heuresRestantes)
    INSERT session (matiere_id, date, heures, fait=0)
    heuresRestantes -= sessionHeures
    si heuresRestantes <= 0 → arret

Sortie :
  N lignes dans sessions_etude, une par jour, couvrant
  toutes les heures restantes jusqu'a la date d'examen.
```

### Regeneration Intelligente

Lorsque l'etudiant clique sur **Regenerer**, le meme algorithme se relance mais lit la valeur mise a jour de `heures_completees` — seules les **heures restantes** sont redistribuees sur les **jours restants**. Cela permet a l'etudiant de rattraper proprement les jours manques.

---

## 🔧 Defis Techniques Rencontres

### 1. Mise a jour en temps reel sans rechargement de page
**Probleme** : Cocher une session declenchait un rechargement complet de la page, degradant l'experience utilisateur.

**Solution implementee** :
- Creation d'un endpoint dedie `toggle_session.php` qui retourne du JSON
- Appel `fetch()` asynchrone depuis `script.js` au clic sur la case
- Mise a jour du DOM (barre de progression, compteur) sans rechargement
- Gestion des erreurs reseau avec `try/catch`

**Resultat** : Interface fluide et reactive, comparable a une application SPA

---

### 2. Algorithme de regeneration adaptative
**Probleme** : Recalculer un planning quand l'etudiant a deja realise une partie des sessions sans ecraser les donnees existantes.

**Solution architecturale** :
- Suppression uniquement des sessions avec `fait = 0` (futures, non realisees)
- Relecture de `heures_completees` pour ne redistribuer que le travail restant
- Calcul base sur les jours restants reels (pas les jours initiaux)

**Resultat** : Le planning se regenere intelligemment en respectant ce qui a deja ete fait

---

### 3. Gestion de la progression circulaire (SVG dynamique)
**Probleme** : Afficher une jauge circulaire SVG dont la valeur change en temps reel sans librairie externe.

**Solution technique** :
- Calcul du `stroke-dasharray` cote PHP au chargement initial
- Recalcul en JavaScript apres chaque appel AJAX via `updateProgressCircle(data)`
- Formule : `dashArray = round(226.2 x progress / 100)` (circonference du cercle r=36)

**Resultat** : Jauge animee sans aucune dependance JavaScript externe

---

### 4. Calcul du streak de jours consecutifs
**Probleme** : Calculer dynamiquement le nombre de jours d'etude consecutifs depuis la base de donnees.

**Solution SQL + PHP** :
- Requete SQL triee par date decroissante sur les sessions realisees (`fait = 1`)
- Boucle PHP comparant chaque date a la date courante en remontant jour par jour
- Arret des qu'un jour sans session est detecte

**Resultat** : Compteur de streak precis, mis a jour a chaque connexion

---

### 5. Systeme de design coherent avec variables CSS
**Probleme** : Maintenir une coherence visuelle sur 10+ pages sans framework CSS externe.

**Solution de design** :
- Palette complete definie via variables CSS (`--primary`, `--surface-0`, `--shadow-md`...)
- Composants reutilisables (`.stat-card`, `.subject-card`, `.diff-badge`)
- Sidebar extraite dans `_sidebar.php` et incluse sur chaque page
- Icones SVG centralisees dans `_icons.php` via une fonction `icon($name, $size)`

**Resultat** : Charte graphique uniforme et facilement modifiable depuis un seul fichier

---

## 🔮 Ameliorations Futures

### Court terme (Sprint 1-2)
- [ ] Mode sombre — bascule entre theme clair et theme sombre
- [ ] Rappels par email — notification avant les sessions du jour
- [ ] Export PDF — planning hebdomadaire imprimable
- [ ] Timer Pomodoro integre par session d'etude

### Moyen terme (Mois 1-3)
- [ ] Support multilingue — selecteur Arabe / Francais / Anglais
- [ ] Application web progressive (PWA) — installable et utilisable hors-ligne
- [ ] Suggestions IA — recommandation d'heures optimales selon les performances passees
- [ ] Synchronisation Google Agenda — export des sessions vers un calendrier externe

### Long terme (Trimestre 2-4)
- [ ] Salles d'etude partagees — partager un planning et reviser avec des amis
- [ ] Version mobile native (React Native ou Flutter)
- [ ] Tableau de bord enseignant — suivi collectif d'une promotion
- [ ] Generation de rapports de progression academique

---

## 👤 Auteur

**Yassine Ben Moallem (Oshino)**
Etudiant Developpeur — L2 SIG
Sidi Henri, Tunisie

| Plateforme | Lien |
|---|---|
| Email | yassine.bmaalem@gmail.com |
| LinkedIn | [Yassine Ben Maallem](https://www.linkedin.com/in/yassine-ben-maalem/?skipRedirect=true) |
| GitHub | [@yassinebmoallem](https://github.com/yassinebmoallem) |

---

## 📊 Statistiques du Projet

| Metrique | Valeur |
|---|---|
| **Langage principal** | PHP 8.0 |
| **Base de donnees** | MySQL 5.7+ |
| **Pages de l'application** | 7 |
| **Fichiers PHP** | 19 |
| **Nombre de tables SQL** | 3 |
| **Version actuelle** | v8 |
| **Interface** | Responsive (desktop + mobile) |

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forker le projet
2. Creer une branche feature (`git checkout -b feature/NouvelleFeature`)
3. Committer les modifications (`git commit -m 'Ajout de NouvelleFeature'`)
4. Pousser vers la branche (`git push origin feature/NouvelleFeature`)
5. Ouvrir une Pull Request

### Conventions de contribution
- Code commente en francais
- Respect des conventions de nommage PHP (snake_case pour les variables)
- Documentation mise a jour pour toute nouvelle fonctionnalite

---

## 📄 Licence

Ce projet est sous licence MIT — voir le fichier [LICENSE](LICENSE) pour plus de details.

---

## 🙏 Remerciements

- [Chart.js](https://www.chartjs.org/) — Bibliotheque de graphiques open-source
- [Feather Icons](https://feathericons.com/) — Bibliotheque d'icones SVG minimalistes
- [Google Fonts — Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans) — Typographie principale
- [PHP Documentation](https://www.php.net/docs.php) — Reference langage
- Tous les camarades et enseignants qui ont teste et donne leur avis

---

<div align="center">

**⭐ Si ce projet vous a ete utile, laissez une etoile — ca compte vraiment !**

*Developpe avec patience, PHP, et beaucoup trop de cafe ☕*

*Version 1.0 — 2026*

</div>
